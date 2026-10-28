<?php
namespace OmegaGuardian\ProfilerShield\Security;
use OmegaGuardian\ProfilerShield\Storage\FileStore;

/** Atomic reservations prevent parallel requests from crossing the threshold. */
final class LoginThrottle
{
    private $clock;
    public function __construct(private FileStore $store, private Settings $settings, ?callable $clock=null) {$this->clock=$clock??fn()=>time();}
    public static function defaults(): array {return [['attempts'=>10,'block_minutes'=>10,'window_minutes'=>30,'escalation_minutes'=>30]];}
    public static function configuration($rules): array
    {
        if (!is_array($rules) || !array_is_list($rules) || count($rules)<1 || count($rules)>8) {throw new \InvalidArgumentException('security.invalid_rules');}
        $previous=0;
        foreach ($rules as $r) {
            if (!is_array($r) || count($r)!==4 || array_diff(array_keys($r),['attempts','block_minutes','window_minutes','escalation_minutes'])) {throw new \InvalidArgumentException('security.invalid_rules');}
            foreach (['attempts'=>100,'block_minutes'=>10080,'window_minutes'=>1440,'escalation_minutes'=>10080] as $key=>$max) {if (!isset($r[$key]) || !is_int($r[$key]) || $r[$key]<1 || $r[$key]>$max) {throw new \InvalidArgumentException('security.invalid_rules');}}
            if ($r['block_minutes']<$previous) {throw new \InvalidArgumentException('security.invalid_rules');}$previous=$r['block_minutes'];
        }
        return $rules;
    }
    private function rule(array $rules,array $bucket): array {return $rules[min(count($rules)-1,$bucket['level'])];}
    private function block(array &$b,array $rules,int $now): void
    {
        $r=$this->rule($rules,$b);$b['blocked_until']=$now+60*$r['block_minutes'];
        $b['repeat_until']=$b['blocked_until']+60*$r['escalation_minutes'];$b['failures']=0;$b['window_started']=0;
    }
    public function begin(string $username,string $ip): array
    {
        $binary=@inet_pton($ip);if ($binary!==false) {$ip=inet_ntop($binary);}
        $now=($this->clock)();$rules=self::configuration($this->settings->all()['login_rules']);$ticket=['id'=>bin2hex(random_bytes(16)),'keys'=>[hash('sha256','account|'.strtolower(trim($username))),hash('sha256','origin|'.$ip)]];$retry=0;
        $this->store->change('attempts',function($state) use($now,$rules,$ticket,&$retry) {
            // Remove malformed entries and inactive buckets, retaining live bans.
            $retention=max(120,60*max(array_column($rules,'window_minutes')));
            foreach ($state as $key=>$b) {if (!isset($b['level'],$b['last'],$b['blocked_until']) || ($b['last']<$now-$retention && $b['blocked_until']<=$now && ($b['repeat_until']??0)<$now)) {unset($state[$key]);}}
            $prepared=[];
            foreach ($ticket['keys'] as $key) {
                $b=$state[$key]??['level'=>0,'failures'=>0,'pending'=>[],'blocked_until'=>0,'repeat_until'=>0,'window_started'=>0,'last'=>$now];
                if ($b['blocked_until']>$now) {$retry=max($retry,$b['blocked_until']-$now);$prepared[$key]=$b;continue;}
                if ($b['blocked_until'] && $b['blocked_until']<=$now) {
                    $repeat=$now<=$b['repeat_until'];$b['level']=$repeat?min($b['level']+1,count($rules)-1):0;
                    if (!$repeat) {$b['repeat_until']=0;}
                    $b['blocked_until']=0;$b['failures']=0;$b['window_started']=0;
                } elseif ($b['repeat_until'] && $now>$b['repeat_until']) {$b['level']=0;$b['failures']=0;$b['window_started']=0;$b['repeat_until']=0;}
                $r=$this->rule($rules,$b);
                if ($b['window_started'] && $now>=$b['window_started']+60*$r['window_minutes']) {$b['failures']=0;$b['window_started']=0;}
                foreach ($b['pending'] as $id=>$expires) {if ($expires<=$now) {unset($b['pending'][$id]);++$b['failures'];}}
                if ($b['failures']>=$r['attempts']) {$this->block($b,$rules,$now);$retry=max($retry,$b['blocked_until']-$now);}
                elseif ($b['failures']+count($b['pending'])>=$r['attempts']) {$retry=max($retry,1);}
                $prepared[$key]=$b;
            }
            if ($retry===0 && count($state)>=10000 && array_diff($ticket['keys'],array_keys($state))) {$retry=60;}
            foreach ($prepared as $key=>$b) {
                if ($retry===0) {$b['pending'][$ticket['id']]=$now+120;$b['last']=$now;}
                // A blocked origin must not create unbounded account buckets.
                if ($retry===0 || isset($state[$key])) {$state[$key]=$b;}
            }
            return $state;
        });
        if ($retry) {throw new LoginRateLimited($retry);}return $ticket;
    }
    public function finish(array $ticket,bool $valid): bool
    {
        $now=($this->clock)();$rules=self::configuration($this->settings->all()['login_rules']);$allowed=$valid;
        $this->store->change('attempts',function($state) use($ticket,$valid,$now,$rules,&$allowed) {
            foreach ($ticket['keys'] as $position=>$key) {
                if (!isset($state[$key]['pending'][$ticket['id']])) {$allowed=false;continue;}
                $b=$state[$key];$expired=$b['pending'][$ticket['id']]<=$now;unset($b['pending'][$ticket['id']]);
                if ($expired || $b['blocked_until']>$now) {$allowed=false;}
                if (!$valid || $expired) {
                    if (!$b['window_started']) {$b['window_started']=$now;}
                    ++$b['failures'];
                    if ($b['blocked_until']<=$now && $b['failures']>=$this->rule($rules,$b)['attempts']) {$this->block($b,$rules,$now);}
                } elseif ($position===0 && $allowed) {$b['failures']=0;$b['window_started']=0;}
                $b['last']=$now;$state[$key]=$b;
            }
            return $state;
        });return $allowed;
    }
}
