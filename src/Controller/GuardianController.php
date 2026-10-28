<?php
namespace OmegaGuardian\ProfilerShield\Controller;

use OmegaGuardian\ProfilerShield\Storage\FileStore;
use OmegaGuardian\ProfilerShield\Security\Settings;
use OmegaGuardian\ProfilerShield\Security\Accounts;
use OmegaGuardian\ProfilerShield\Security\Sessions;
use OmegaGuardian\ProfilerShield\Security\Policy;
use OmegaGuardian\ProfilerShield\Security\Audit;
use OmegaGuardian\ProfilerShield\Security\Totp;
use OmegaGuardian\ProfilerShield\Security\LoginThrottle;
use OmegaGuardian\ProfilerShield\Security\LoginRateLimited;
use OmegaGuardian\ProfilerShield\Ui\SecurityPanel;
use OmegaGuardian\ProfilerShield\Util\Html;
use OmegaGuardian\ProfilerShield\I18n\UiTranslator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Interface HTML indépendante de Twig, Doctrine, JavaScript distant ou CDN.
 * Toutes les valeurs utilisateur sont échappées. Tous les POST authentifiés
 * nécessitent un CSRF et les changements de sécurité un mot de passe valide.
 *
 * Cette interface n'est jamais incluse dans le HTML des pages métier.
 */
final class GuardianController
{
    private $store; private $settings; private $accounts; private $sessions;
    private $policy; private $audit; private $totp; private $i18n;
    private $throttle; private $securityPanel;
    private $locale = 'fr_FR';
    const CSRF_COOKIE = '__Host-GuardianLoginCsrf';

    public function __construct(FileStore $store, Settings $settings, Accounts $accounts, Sessions $sessions, Policy $policy, Audit $audit, Totp $totp, ?UiTranslator $i18n = null)
    {
        $this->store=$store; $this->settings=$settings; $this->accounts=$accounts;
        $this->sessions=$sessions; $this->policy=$policy; $this->audit=$audit; $this->totp=$totp; $this->i18n=$i18n ?: new UiTranslator();
        $this->throttle=new LoginThrottle($store,$settings);$this->securityPanel=new SecurityPanel($this->i18n);
    }

    public function entry(Request $request)
    {
        $path = $request->getPathInfo();
        $this->locale = $this->preferredLocale($request);
        try {
            if ($this->settings->all()['portal_closed_outside_hours'] && !$this->policy->inSchedule()) {
                return $this->html('Not Found','Not Found',404);
            }
            if (!$this->policy->ipAllowed($request->getClientIp())) { return $this->html('Not Found','Not Found',404); }
            if (!$this->store->ready()) { return $this->html(($this->i18n->trans('legacy.dae75cff3b98',$this->locale)), ('<div class="panel"><h2>'.Html::e($this->i18n->trans('legacy.330adad04f63',$this->locale)).'</h2><p>'.Html::e($this->i18n->trans('legacy.6ded42ae25e5',$this->locale)).'</p></div>'),503); }
            $session = $this->sessions->current($request);
            if ($request->isMethod('POST')) {
                $action = (string) $request->request->get('action', 'login');
                if ($action === 'login') { return $this->login($request); }
                if (!$session || !$this->sessions->csrf($session, $request->request->get('_csrf'))) {
                    return $this->html(($this->i18n->trans('legacy.58b8bf7de679',$this->locale)),('<div class="panel"><h2>'.Html::e($this->i18n->trans('legacy.fbce9857a9c5',$this->locale)).'</h2><p>'.Html::e($this->i18n->trans('legacy.b1e3658b7499',$this->locale)).'</p></div>'),403);
                }
                $user = $this->accounts->find($session['username']);
                if ($user === null || !empty($user['disabled'])) { return $this->html(($this->i18n->trans('legacy.58b8bf7de679',$this->locale)),('<div class="panel">'.Html::e($this->i18n->trans('legacy.a2cca838dca6',$this->locale)).'</div>'),403); }
                if ($this->accounts->needsPasswordChange($session['username'],$user) && !in_array($action,['password_change','logout'],true)) {return $this->passwordPage($session,true,403);}
                return $this->action($request,$session,$user,$action);
            }
            if (!$session) { return $this->loginForm($request, $path); }
            $user = $this->accounts->find($session['username']);
            if (!$user || !empty($user['disabled'])) { return $this->html(($this->i18n->trans('legacy.58b8bf7de679',$this->locale)),('<div class="panel">'.Html::e($this->i18n->trans('legacy.35edb8267691',$this->locale)).'</div>'),403); }
            if ($this->accounts->needsPasswordChange($session['username'],$user)) {return $this->passwordPage($session,true);}
            return $this->dashboard($request,$session,$user);
        } catch (LoginRateLimited $e) {
            $response=$this->loginForm($request,$path,$this->i18n->trans('security.blocked',$this->locale));$response->setStatusCode(429);$response->headers->set('Retry-After',(string)$e->retryAfter);return $response;
        } catch (\Throwable $e) {
            // Ne pas dévoiler les chemins système, le stockage ou les secrets.
            return $this->html('Service indisponible',('<div class="panel"><h2>'.Html::e($this->i18n->trans('legacy.d884853de7f5',$this->locale)).'</h2><p>'.Html::e($this->i18n->trans('legacy.eba4c22f7e3b',$this->locale)).'</p></div>'),503);
        }
    }

    private function verifySensitive(Request $request,string $username,string $password)
    {
        $ticket=$this->throttle->begin($username,(string)$request->getClientIp());$user=$this->accounts->verify($username,$password);
        return $this->throttle->finish($ticket,$user!==null)?$user:null;
    }
    private function passwordPage(array $session,bool $forced,int $status=200,string $error='')
    {
        return $this->html($this->i18n->trans('security.change_title',$this->locale),$this->securityPanel->passwordForm($this->settings->all(),$session['csrf'],$this->locale,$forced,$error),$status);
    }

    private function loginForm(Request $request, $path, $error = '')
    {
        $csrf = bin2hex(random_bytes(32));
        $form = ('<div class="hero"><div class="emblem">◈</div><h1>'.Html::e($this->i18n->trans('legacy.6a1a8e5d5898',$this->locale)).'</h1><p>'.Html::e($this->i18n->trans('legacy.62cb0afe7c00',$this->locale)).'</p></div>');
        $form.='<form class="auth-language" method="get" action="'.Html::e($path).'"><label for="guardian-login-lang">'.Html::e($this->i18n->trans('i18n.language',$this->locale)).'</label><select id="guardian-login-lang" name="lang">';
        foreach (UiTranslator::AVAILABLE as $value=>$label) {
            $form.='<option value="'.Html::e($value).'"'.($value===$this->locale?' selected':'').'>'.Html::e($label).'</option>';
        }
        $form.='</select><button type="submit">OK</button></form>';
        if ($error) { $form .= '<p class="danger">'.Html::e($error).'</p>'; }
        $form .= '<form class="panel" method="post" action="'.Html::e($path).'" autocomplete="on">'
            .'<input type="hidden" name="action" value="login"><input type="hidden" name="_csrf" value="'.$csrf.'">'
            .'<label>'.Html::e($this->i18n->trans('auth.username',$this->locale)).' <input name="username" autocomplete="username" required maxlength="64"></label>'
            .'<label>'.Html::e($this->i18n->trans('auth.password',$this->locale)).' <input name="password" type="password" autocomplete="current-password" required></label>'
            .'<label>'.Html::e($this->i18n->trans('auth.totp',$this->locale)).' <input name="otp" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code" placeholder="'.Html::e($this->i18n->trans('auth.six_digits',$this->locale)).'"></label>'
            .'<label class="check"><input type="checkbox" name="remember" value="1"> '.Html::e($this->i18n->trans('auth.remember',$this->locale)).'</label>'
            .'<button type="submit">'.Html::e($this->i18n->trans('auth.submit',$this->locale)).' →</button>'
            .'<p class="hint">'.Html::e($this->i18n->trans('auth.privacy',$this->locale)).'</p></form>';
        $response = $this->html(($this->i18n->trans('legacy.61d695041034',$this->locale)),$form,200);
        $response->headers->setCookie(new Cookie(self::CSRF_COOKIE,$csrf,time()+600,'/',null,true,true,false,'Strict'));
        if ($request->query->has('lang') && isset(UiTranslator::AVAILABLE[(string)$request->query->get('lang')])) {
            $response->headers->setCookie(new Cookie('__Host-GuardianLocale',$this->locale,time()+365*86400,'/',null,true,true,false,'Lax'));
        }
        return $response;
    }

    private function login(Request $request)
    {
        $username = strtolower(trim((string)$request->request->get('username','')));
        $password = (string)$request->request->get('password','');
        $csrf = (string)$request->request->get('_csrf','');
        if (strlen($username)>64 || strlen($password)>512) {
            return $this->loginForm($request,$request->getPathInfo(),($this->i18n->trans('legacy.c817fa69ae16',$this->locale)));
        }
        $cookie = $request->cookies->get(self::CSRF_COOKIE,'');
        if (!is_string($cookie) || strlen($csrf)!==64 || !hash_equals($cookie,$csrf)) {
            return $this->loginForm($request,$request->getPathInfo(),($this->i18n->trans('legacy.7a4d88bd0909',$this->locale)));
        }
        $ticket=$this->throttle->begin($username,(string)$request->getClientIp());
        $user = $this->accounts->verify($username,$password);
        $valid = $user !== null;
        if ($valid && !empty($user['totp'])) {
            $counter=$this->totp->verifyWindow($this->totp->decrypt($user['totp']), (string)$request->request->get('otp',''));
            $valid=$counter!==null;
            if ($valid) {
                // Transaction atomique : interdit la réutilisation du même
                // code et son rejeu sur une fenêtre précédente.
                try {
                    $this->accounts->update($username,function ($account) use ($counter) {
                        if (!empty($account['disabled']) || $counter <= (isset($account['last_otp_counter'])?$account['last_otp_counter']:-1)) {
                            throw new \RuntimeException(($this->i18n->trans('legacy.cb8d20ef89e5',$this->locale)));
                        }
                        $account['last_otp_counter']=$counter;
                        return $account;
                    });
                } catch (\RuntimeException $e) { $valid=false; }
            }
        }
        if ($valid && $this->settings->all()['mfa'] && empty($user['totp'])) { $valid = false; }
        $valid=$this->throttle->finish($ticket,$valid);
        if (!$valid) {
            try { $this->audit->record('login_failed',substr($username,0,64)); } catch (\Throwable $ignored) {}
            return $this->loginForm($request,$request->getPathInfo(),($this->i18n->trans('legacy.c817fa69ae16',$this->locale)));
        }
        $response = $this->redirect($request->getPathInfo());
        $this->sessions->begin($username,$response,$request->request->get('remember')==='1');
        $response->headers->setCookie(new Cookie(self::CSRF_COOKIE,'',1,'/',null,true,true,false,'Strict'));
        try { $this->audit->record('login_success',$username); } catch (\Throwable $ignored) {}
        return $response;
    }

    private function action(Request $request, array $session, array $user, $action)
    {
        $username=$session['username'];
        if ($action==='logout') {
            $response=$this->redirect($request->getPathInfo());
            $this->sessions->end($request,$response);
            return $response;
        }
        if ($action==='password_change') {
            $current=(string)$request->request->get('current_password','');
            if (!$this->verifySensitive($request,$username,$current)) {return $this->passwordPage($session,$this->accounts->needsPasswordChange($session['username'],$user),400,'security.password_current');}
            $new=(string)$request->request->get('new_password','');$confirm=(string)$request->request->get('password_confirm','');
            if (!hash_equals($new,$confirm)) {return $this->passwordPage($session,$this->accounts->needsPasswordChange($session['username'],$user),400,'security.password_confirmation');}
            try {$this->accounts->setPassword($username,$new);} catch (\InvalidArgumentException $e) {return $this->passwordPage($session,$this->accounts->needsPasswordChange($session['username'],$user),400,$e->getMessage());}
            $this->sessions->invalidateUser($username);$response=$this->redirect($request->getPathInfo());$this->sessions->begin($username,$response);
            $this->audit->record('password_changed',$username,[],true);return $response;
        }
        if ($action==='jit') {
            if (!$this->settings->all()['jit_enabled']) { return $this->notice($request,($this->i18n->trans('legacy.cb9e61e7dc4a',$this->locale))); }
            $minutes=$this->settings->all()['jit_minutes'];
            $this->sessions->modify($request,function ($s) use ($minutes) { $s['jit_until']=time()+60*$minutes;return $s; });
            $this->audit->record('jit_enabled',$username,array('duration'=>$minutes));
            return $this->redirect($request->getPathInfo());
        }
        if ($action==='emergency') {
            if (!$this->settings->all()['emergency_enabled']) { return $this->notice($request,($this->i18n->trans('legacy.b83c9b06ceca',$this->locale))); }
            if (!$this->verifySensitive($request,$username,(string)$request->request->get('password',''))) { return $this->notice($request,($this->i18n->trans('legacy.af354f7fd19e',$this->locale))); }
            $code=trim((string)$request->request->get('code',''));
            $reason=trim((string)$request->request->get('reason',''));
            if (strlen($reason)<8 || strlen($reason)>160) { return $this->notice($request,($this->i18n->trans('legacy.6431312c1dd5',$this->locale))); }
            $matched=false;
            $this->store->change('emergency_codes',function ($state) use ($username,$code,&$matched) {
                if (isset($state[$username])) {
                    foreach ($state[$username] as $k=>$hash) {
                        if (password_verify($code,$hash)) { unset($state[$username][$k]);$matched=true;break; }
                    }
                }
                return $state;
            });
            if (!$matched) { $this->audit->record('emergency_denied',$username);return $this->notice($request,($this->i18n->trans('legacy.0c0ee4fede1b',$this->locale))); }
            // L'audit durable précède l'ouverture exceptionnelle. Si l'audit
            // ou la file échoue, aucun droit supplémentaire n'est accordé.
            $this->audit->record('emergency_granted',$username,array('reason'=>$reason,'duration'=>30),true);
            $this->sessions->modify($request,function ($s) { $s['emergency_until']=time()+1800;return $s; });
            return $this->redirect($request->getPathInfo());
        }
        // L'enrôlement MFA appartient à l'utilisateur, quel que soit son rôle.
        // L'administrateur ne connaît et ne collecte jamais le secret TOTP d'un tiers.
        // Enrôlement TOTP en DEUX étapes, sans redemander le mot de passe :
        // 1. Vérification du mot de passe du compte Guardian en cours.
        // 2. Vérification d'un code à six chiffres, lié à la même session.
        // Le secret provisoire reste chiffré dans le stockage de session,
        // expire en dix minutes et n'est jamais envoyé à un service QR externe.
        if ($action==='mfa_prepare') {
            if (!$this->verifySensitive($request,$username,(string)$request->request->get('password',''))) {
                return $this->notice($request,$this->i18n->trans('mfa.error_password',$this->preferredLocale($request)),'settings');
            }
            if (!\OmegaGuardian\ProfilerShield\Util\Env::get('GUARDIAN_ENCRYPTION_KEY')) {
                return $this->notice($request,$this->i18n->trans('mfa.error_key',$this->preferredLocale($request)),'settings');
            }
            // Générer la clé UNIQUEMENT après vérification de l'identité.
            $secret=$this->totp->generateSecret();
            $encrypted=$this->totp->encrypt($secret);
            $this->sessions->modify($request,function ($s) use ($encrypted) {
                $s['pending_totp']=$encrypted;
                $s['pending_until']=time()+600;
                $s['pending_otp_attempts']=0;
                return $s;
            });
            return $this->redirect($request->getPathInfo().'?view=settings#guardian-mfa');
        }
        if ($action==='mfa_cancel') {
            $this->sessions->modify($request,function ($s) {
                unset($s['pending_totp'],$s['pending_until'],$s['pending_otp_attempts']);
                return $s;
            });
            return $this->redirect($request->getPathInfo().'?view=settings#guardian-mfa');
        }
        if ($action==='mfa_confirm') {
            if (empty($session['pending_totp']) || empty($session['pending_until']) || $session['pending_until']<time()) {
                return $this->notice($request,$this->i18n->trans('mfa.error_expired',$this->preferredLocale($request)),'settings');
            }
            // Un compte ne peut pas tester sans limite les codes pendant
            // l'enrôlement. Aucun nouveau mot de passe n'est demandé ici.
            if ((int)($session['pending_otp_attempts'] ?? 0) >= 5) {
                $this->sessions->modify($request,function ($s) {
                    unset($s['pending_totp'],$s['pending_until'],$s['pending_otp_attempts']);
                    return $s;
                });
                return $this->notice($request,$this->i18n->trans('mfa.error_attempts',$this->preferredLocale($request)),'settings');
            }
            $secret=$this->totp->decrypt($session['pending_totp']);
            $counter=$this->totp->verifyWindow($secret,(string)$request->request->get('otp',''));
            if ($counter===null) {
                $this->sessions->modify($request,function ($s) {
                    $s['pending_otp_attempts']=(int)($s['pending_otp_attempts'] ?? 0)+1;
                    return $s;
                });
                return $this->notice($request,$this->i18n->trans('mfa.error_code',$this->preferredLocale($request)),'settings');
            }
            $encrypted=$session['pending_totp'];
            $this->accounts->update($username,function ($u) use ($encrypted,$counter) {
                $u['totp']=$encrypted;
                // Ce code vient d'être consommé ; le même code ne doit pas
                // permettre une authentification immédiate par rejeu.
                $u['last_otp_counter']=$counter;
                return $u;
            });
            $this->sessions->modify($request,function ($s) {
                unset($s['pending_totp'],$s['pending_until'],$s['pending_otp_attempts']);
                return $s;
            });
            $this->audit->record('mfa_enrolled',$username);
            return $this->redirect($request->getPathInfo().'?view=settings#guardian-mfa');
        }
        if ($user['role']!=='admin') { return $this->notice($request,($this->i18n->trans('legacy.1f8add65114d',$this->locale))); }
        $pass=(string)$request->request->get('password','');
        if (!$this->verifySensitive($request,$username,$pass)) { return $this->notice($request,($this->i18n->trans('legacy.0984fa503cab',$this->locale))); }
        if ($action==='security_policy') {
            try {
                $raw=$request->request->all();$rules=$raw['login_rules']??null;$passwordPolicy=$raw['password_policy']??null;
                if (!is_array($rules) || !array_is_list($rules) || count($rules)>8 || !is_array($passwordPolicy) || array_diff(array_keys($passwordPolicy),['min_length','require_upper','require_lower','require_digit','require_symbol'])) {throw new \InvalidArgumentException('security.invalid_rules');}
                $integer=function($value) {if (!is_string($value) || !preg_match('/^[1-9][0-9]{0,4}$/D',$value)) {throw new \InvalidArgumentException('security.invalid_rules');}return (int)$value;};
                foreach ($rules as &$rule) {if (!is_array($rule) || count($rule)!==4) {throw new \InvalidArgumentException('security.invalid_rules');}foreach($rule as &$value){$value=$integer($value);}unset($value);}unset($rule);
                $policy=['min_length'=>$integer($passwordPolicy['min_length']??null)];
                foreach(['upper','lower','digit','symbol'] as $class) {$value=$passwordPolicy['require_'.$class]??'0';if (!in_array($value,['0','1'],true)) {throw new \InvalidArgumentException('security.invalid_policy');}$policy['require_'.$class]=$value==='1';}
                $this->settings->set(['login_rules'=>$rules,'password_policy'=>$policy]);
                if (($raw['force_existing']??'')==='1') {$this->accounts->forcePasswordChange();}
            } catch (\InvalidArgumentException $e) {return $this->notice($request,$this->i18n->trans($e->getMessage(),$this->locale),'settings');}
            $this->audit->record('authentication_policy_updated',$username,[],true);
            return $this->redirect($request->getPathInfo().'?view=settings#guardian-auth-safety');
        }
        if ($action==='user_create') {
            $newname=(string)$request->request->get('new_username','');
            $newpass=(string)$request->request->get('new_password','');
            $role=(string)$request->request->get('role','developer');
            try { $this->accounts->add($newname,$newpass,$role,true); }
            catch (\Exception $e) { return $this->notice($request,$e->getMessage()); }
            $this->audit->record('user_created',$username,array('result'=>'created'));
            return $this->redirect($request->getPathInfo().'?view=settings');
        }
        if ($action==='user_disable') {
            $target=strtolower(trim((string)$request->request->get('username','')));
            if ($target===$username) { return $this->notice($request,($this->i18n->trans('legacy.17ac6f28e5f0',$this->locale))); }
            if (!$this->accounts->find($target)) { return $this->notice($request,($this->i18n->trans('legacy.0d0ee4af2bdf',$this->locale))); }
            $this->accounts->update($target,function ($u) { $u['disabled']=true;return $u; });
            $this->sessions->invalidateUser($target);
            $this->audit->record('user_disabled',$username);
            return $this->redirect($request->getPathInfo().'?view=settings');
        }
        if ($action==='user_revoke') {
            $target=strtolower(trim((string)$request->request->get('username','')));
            if (!$this->accounts->find($target)) { return $this->notice($request,($this->i18n->trans('legacy.0d0ee4af2bdf',$this->locale))); }
            $this->sessions->invalidateUser($target);
            $this->audit->record('sessions_revoked',$username,array('target'=>$target,'result'=>'revoked'));
            return $this->redirect($request->getPathInfo().'?view=settings');
        }
        if ($action==='user_role') {
            $target=strtolower(trim((string)$request->request->get('username','')));
            $role=(string)$request->request->get('role','');
            if (!in_array($role,array('admin','developer','security'),true)) {return $this->notice($request,($this->i18n->trans('legacy.8e6f254ceb7b',$this->locale)));}
            if ($target===$username) {return $this->notice($request,($this->i18n->trans('legacy.2a1d65ab37c4',$this->locale)));}
            $account=$this->accounts->find($target);
            if (!$account || !empty($account['disabled'])) {return $this->notice($request,($this->i18n->trans('legacy.1595daf9a6a7',$this->locale)));}
            $this->accounts->update($target,function ($value) use ($role) { $value['role']=$role;return $value; });
            $this->sessions->invalidateUser($target);
            $this->audit->record('user_role_changed',$username,array('target'=>$target,'result'=>$role));
            return $this->redirect($request->getPathInfo().'?view=settings');
        }
        if ($action==='settings') {
            $body=$request->request;
            $previous=$this->settings->all();
            // Reject malformed values before PHP casts can turn "12oops" into 12.
            foreach (array('idle_minutes','absolute_hours','remember_days','jit_minutes','audit_retention_days') as $numberKey) {
                if ($body->has($numberKey)) {
                    $value=$body->get($numberKey);
                    if ((!is_string($value) && !is_int($value)) || !preg_match('/^[0-9]+$/D', (string)$value)) {
                        return $this->notice($request, ($this->i18n->trans('legacy.cdf08284d7a9',$this->locale)).$numberKey, 'settings');
                    }
                }
            }
            $rawBody=$body->all();
            if (isset($rawBody['weekdays'])) {
                if (!is_array($rawBody['weekdays'])) { return $this->notice($request, ($this->i18n->trans('legacy.cb30870e122a',$this->locale)), 'settings'); }
                foreach ($rawBody['weekdays'] as $day) {
                    if ((!is_int($day) && !is_string($day)) || !preg_match('/^[1-7]$/D', (string)$day)) {
                        return $this->notice($request, ($this->i18n->trans('legacy.cb30870e122a',$this->locale)), 'settings');
                    }
                }
            }
            $input=array(
                'stealth'=>$body->get('stealth')==='1', 'mfa'=>$body->get('mfa')==='1',
                'schedule_enabled'=>$body->get('schedule_enabled')==='1',
                'portal_closed_outside_hours'=>$body->get('schedule_enabled')==='1' && $body->get('portal_closed_outside_hours')==='1',
                'jit_enabled'=>$body->get('jit_enabled')==='1',
                'emergency_enabled'=>$body->get('emergency_enabled')==='1',
                'idle_minutes'=>(int)$body->get('idle_minutes',$previous['idle_minutes']), 'absolute_hours'=>(int)$body->get('absolute_hours',$previous['absolute_hours']),
                'remember_days'=>(int)$body->get('remember_days',$previous['remember_days']), 'jit_minutes'=>(int)$body->get('jit_minutes',$previous['jit_minutes']),
                'timezone'=>(string)$body->get('timezone',$previous['timezone']),
                'start'=>(string)$body->get('start',$previous['start']), 'end'=>(string)$body->get('end',$previous['end']),
                'weekdays'=>($body->has('schedule_enabled')?array_map('intval',(array)$body->all('weekdays')):$previous['weekdays']),
                'alert_email'=>(string)$body->get('alert_email',$previous['alert_email']),
                'audit_level'=>(string)$body->get('audit_level'),
                'audit_retention_days'=>(int)$body->get('audit_retention_days'),
                'ip_allowlist'=>(string)$body->get('ip_allowlist','')
            );
            // Confirmation côté SERVEUR obligatoire lorsqu'un NOUVEAU fuseau
            // diffère du fuseau du processus PHP-FPM courant. Le JavaScript
            // ne suffit pas : un POST forgé doit être refusé lui aussi.
            $phpZone=\OmegaGuardian\ProfilerShield\Util\ServerClock::phpTimezone();
            $currentZone=$this->settings->all()['timezone'];
            if ($input['timezone'] !== $currentZone && $input['timezone'] !== $phpZone
                && $body->get('timezone_ack') !== '1') {
                return $this->notice($request,$this->i18n->trans('schedule.confirm_required',$this->preferredLocale($request)));
            }
            if ($input['mfa']) {
                foreach ($this->accounts->allPublic() as $account) {
                    if (!$account['mfa']) { return $this->notice($request,($this->i18n->trans('legacy.dad66eff3b47',$this->locale))); }
                }
            }
            try { $this->settings->set($input); }
            catch (\InvalidArgumentException $e) { return $this->notice($request,$e->getMessage()); }
            $this->audit->record('settings_updated',$username);
            return $this->redirect($request->getPathInfo().'?view=settings');
        }
        if ($action==='error_settings') {
            $input=array(
                'error_mode'=>(string)$request->request->get('error_mode','native'),
                'error_reference'=>(string)$request->request->get('error_reference','none')
            );
            // Les champs désactivés par le navigateur ne sont pas transmis.
            foreach (array('error_reference_text', 'error_custom_text') as $field) {
                if ($request->request->has($field)) { $input[$field]=(string)$request->request->get($field); }
            }
            try { $this->settings->set($input); }
            catch (\InvalidArgumentException $e) { return $this->notice($request, $e->getMessage(), 'settings'); }
            $this->audit->record('error_policy_changed',$username);
            return $this->redirect($request->getPathInfo().'?view=settings#guardian-error-pages');
        }
        if ($action==='preset') {
            $name=(string)$request->request->get('preset','');
            if (in_array($name,array('team','strict'),true)) {
                foreach ($this->accounts->allPublic() as $account) {
                    if (!$account['mfa']) { return $this->notice($request,($this->i18n->trans('legacy.de5bb3272b8c',$this->locale))); }
                }
            }
            try { $this->settings->preset($name); }
            catch (\InvalidArgumentException $e) { return $this->notice($request,$e->getMessage()); }
            $this->audit->record('preset_changed',$username);
            return $this->redirect($request->getPathInfo().'?view=settings');
        }
        return $this->notice($request,($this->i18n->trans('legacy.2e011f87bb2a',$this->locale)));
    }

    private function dashboard(Request $request, array $session, array $user, $flash = '', $viewOverride = null)
    {
        $cfg=$this->settings->all();
        $view=$viewOverride===null?(string)$request->query->get('view','home'):$viewOverride;
        $csrf=Html::e($session['csrf']);
        $path=Html::e($request->getPathInfo());
        $userName=Html::e($session['username']);
        $allowed=$this->policy->allowed($session,$user);
        $locale=$this->preferredLocale($request);
        $t=function ($key) use ($locale) { return Html::e($this->i18n->trans($key,$locale)); };
        // Interface plein écran : sidebar autonome sans liens vers des domaines
        // externes ni scripts tiers. Le chemin privé reste limité aux liens
        // internes à cette page et n'est jamais publié sur le site métier.
        $items = array(
            array('home','◫',$t('nav.overview'),''),
            array('settings','⚙',$t('nav.settings'),'?view=settings'),
            array('guide','◇',$t('nav.guide'),'?view=guide'),
            array('audit','◷',$t('nav.audit'),'?view=audit'),
            array('support','✉',$t('nav.support'),'?view=support')
        );
        $menu = '<nav class="sidebar-nav" aria-label="'.$t('nav.section').'">';
        foreach ($items as $item) {
            $active = $view === $item[0] ? ' is-active' : '';
            $menu .= '<a class="sidebar-link'.$active.'" href="'.$path.$item[3].'"'.($active ? ' aria-current="page"' : '').'>'
                .'<span class="nav-icon" aria-hidden="true">'.$item[1].'</span><span>'.Html::e($item[2]).'</span>'
                .'</a>';
        }
        $menu .= '</nav>'.\OmegaGuardian\ProfilerShield\Ui\LanguagePicker::render($this->i18n,$locale,$view);
        $body = '<div class="workspace" data-unsaved-label="'.$t('ui.unsaved').'" data-show-password="'.$t('ui.show_password').'" data-hide-password="'.$t('ui.hide_password').'"><aside class="sidebar">'
            .'<a href="'.$path.'" class="sidebar-brand"><span class="brand-icon">Ω</span><span><b>Guardian</b><small>PROFILER SHIELD</small></span></a>'
            .'<div class="sidebar-section-label">'.$t('nav.section').'</div>'.$menu
            .'<div class="sidebar-end"><div class="identity"><span class="identity-avatar">'.Html::e(strtoupper(substr((string)$session['username'],0,1))).'</span>'
            .'<span><b>'.$userName.'</b><small>'.$t('role.'.($user['role']==='security'?'security':$user['role'])).'</small></span></div>'
            .'<form method="post"><input type="hidden" name="_csrf" value="'.$csrf.'"><input type="hidden" name="action" value="logout">'
            .'<button type="submit" class="sidebar-logout">↪ &nbsp; '.$t('nav.logout').'</button></form></div>'
            .'</aside><main class="workspace-main" id="guardian-main"><div class="topbar"><span class="breadcrumb">ΩGUARDIAN <span>/</span> PROFILER SHIELD <span>/</span> '.$t('nav.'.($view==='home'?'overview':$view)).'</span>'
            .'<span class="topbar-right"><span class="status-dot"></span> '.$t('nav.environment').'</span></div>'
            .'<div class="page-content"><div class="heading"><div><span class="eyebrow">ΩGUARDIAN / PROFILER SHIELD</span>'
            .'<h1>'.($view==='home'?$t('dashboard.title'):$t('nav.'.$view)).'</h1><p>'.$t('ui.intro_'.(in_array($view,array('settings','audit','guide','support'),true)?$view:'home')).'</p></div>'
            .'<span class="pill '.($allowed?'pill-ok':'pill-locked').'">'.($allowed?'● &nbsp; '.$t('dashboard.authorized'):'● &nbsp; '.$t('dashboard.locked')).'</span></div>';
        $mfaFlash = $flash!=='' && in_array((string)$request->request->get('action',''),array('mfa_prepare','mfa_confirm','mfa_cancel'),true) ? $flash : '';
        if ($flash!=='' && $mfaFlash==='') {
            // Même gabarit, même formulaire et même session : jamais une page
            // d'erreur autonome lors d'une validation de formulaire.
            $body.='<div class="inline-alert" id="guardian-form-alert" role="alert"><span aria-hidden="true">!</span><p>'.Html::e($flash).'</p></div>';
        }
        if ($view==='settings') {
            if ($user['role'] !== 'admin') { $body.=('<div class="panel"><h2>'.Html::e($this->i18n->trans('legacy.39dc148cbda9',$this->locale)).'</h2><p>'.Html::e($this->i18n->trans('legacy.de1ed505d503',$this->locale)).'</p></div>').$this->personalMfaForm($csrf,$session,$user,$locale,$mfaFlash); }
            else { $body.=$this->securityPanel->render($cfg,$csrf,$locale).$this->settingsView($cfg,$csrf,$path,$locale).$this->errorSettingsForm($cfg,$csrf,$locale).$this->personalMfaForm($csrf,$session,$user,$locale,$mfaFlash).$this->userManager($csrf); }
            $body.=$this->securityPanel->passwordForm($cfg,$session['csrf'],$locale,false);
        } elseif ($view==='guide') { $body.=$this->guide(); }
        elseif ($view==='support') {
            $body.=('<section class="panel support-panel"><span class="section-kicker">'.Html::e($this->i18n->trans('legacy.4a5c53506acc',$this->locale)).'</span>')
                .'<h2>'.$t('support.title').'</h2><p>'.$t('support.lead').'</p>'
                .'<p><a class="button-link" href="mailto:bundle-sec@omegaguardian.fr">'
                .$t('support.email').' · bundle-sec@omegaguardian.fr</a></p>'
                .'<p class="support-privacy">'.$t('support.note').'</p></section>';
        }
        elseif ($view==='audit') {
            $body.=\OmegaGuardian\ProfilerShield\Ui\AuditPanel::render($this->store->read('audit'),$request->query->all(),$this->i18n,$locale);
        } else {
            $body.='<section class="access-hero '.($allowed?'access-open':'access-closed').'"><div class="access-symbol" aria-hidden="true">'.($allowed?'↗':'◇').'</div><div><span class="eyebrow">'.$t('ui.current_access').'</span><h2>'.($allowed?$t('ui.access_open'):$t('ui.access_closed')).'</h2><p>'.($allowed?$t('ui.access_open_help'):$t('ui.access_closed_help')).'</p></div>';
            if ($allowed) { $body.='<a class="button-link" href="/_profiler/" rel="noreferrer">'.$t('ui.open_profiler').' ↗</a>'; }
            else { $body.='<a class="button-link secondary" href="'.$path.'?view=settings">'.$t('ui.review_access').' →</a>'; }
            $body.='</section>';
            $publicAccounts=$this->accounts->allPublic();
            $members=array();
            foreach ($publicAccounts as $account) { $members[$account['name']]=$account; }
            $activity=$user['role']==='admin' ? $this->sessions->activitySummary($members) : null;
            $totalEnabled=count(array_filter($publicAccounts,function ($a) { return !$a['disabled']; }));
            $body.='<div class="cards">'
                .$this->card('shield-check',$t('metrics.public'),$t('metrics.blocked'))
                .$this->card('eye',$t('metrics.stealth'),$cfg['stealth']?$t('status.enabled'):$t('metrics.http403'))
                .$this->card('clock',$t('metrics.schedule'),$cfg['schedule_enabled']?$cfg['start'].'–'.$cfg['end']:$t('status.disabled'))
                .$this->card('heart',$t('metrics.business'),$t('metrics.independent')).'</div>';
            if ($activity !== null) {
                $body.=('<section class="team-overview"><div class="section-head"><div><span class="section-kicker">'.Html::e($this->i18n->trans('legacy.f697621af2a9',$this->locale)).'</span><h2>').$t('team.title').'</h2><p>'.$t('team.caption').'</p></div><span class="team-live"><span class="status-dot"></span>'.$t('team.recent_window').'</span></div>'
                    .'<div class="team-stat-grid">'
                    .$this->teamStat((string)$activity['recent'],$t('team.recent'),$t('team.recent_help'))
                    .$this->teamStat((string)$activity['sessions'],$t('team.sessions'),$t('team.sessions_help'))
                    .$this->teamStat((string)$totalEnabled,$t('team.accounts'),$t('team.accounts_help'))
                    .$this->teamStat((string)$activity['roles']['developer'],$t('team.developers'),$t('team.developers_help'))
                    .'</div><div class="team-two-columns"><div class="team-members"><h3>'.$t('team.active_people').'</h3>';
                if (!$activity['members']) { $body.='<p>'.$t('team.none').'</p>'; }
                foreach ($activity['members'] as $member) {
                    $body.='<div class="team-member"><span class="identity-avatar">'.Html::e(strtoupper(substr($member['name'],0,1))).'</span><span><strong>'.Html::e($member['name']).'</strong><small>'.$t('role.'.$member['role']).'</small></span><time>'.Html::e(gmdate('H:i:s', $member['last'])).' UTC</time></div>';
                }
                $body.='</div><div class="team-roles"><h3>'.$t('team.distribution').'</h3>';
                foreach (array('admin','developer','security') as $roleName) {
                    $c=$activity['roles'][$roleName];
                    $pc=$activity['recent']?round(100*$c/$activity['recent']):0;
                    $body.='<div class="role-row"><div><span>'.$t('role.'.$roleName).'</span><strong>'.$c.'</strong></div><span class="role-track"><span style="width:'.$pc.'%"></span></span></div>';
                }
                $body.='<p class="team-footnote">'.$t('team.privacy').'</p></div></div></section>';
            }
            $body.=('<div class="panel"><h2>'.Html::e($this->i18n->trans('legacy.dca95c2edef3',$this->locale)).'</h2><p>'.Html::e($this->i18n->trans('legacy.adda3fa62ecf',$this->locale)).'</p>');
            $body.=('<div class="grid2"><div><b>'.Html::e($this->i18n->trans('legacy.7eeb816abab3',$this->locale)).'</b><p>').($cfg['mfa']?($this->i18n->trans('legacy.c8883d25bd5d',$this->locale)):($this->i18n->trans('legacy.84b9d6d04178',$this->locale))).('</p></div><div><b>'.Html::e($this->i18n->trans('legacy.c28372f38254',$this->locale)).'</b><p>').($cfg['jit_enabled']?($this->i18n->trans('legacy.361663d37f4a',$this->locale)):($this->i18n->trans('legacy.fe9b1b3182fd',$this->locale))).'</p></div>'
                .('<div><b>'.Html::e($this->i18n->trans('legacy.d583b7851dfa',$this->locale)).'</b><p>').(int)$cfg['remember_days'].(' '.Html::e($this->i18n->trans('legacy.28c658a0ac10',$this->locale)).'</p></div><div><b>'.Html::e($this->i18n->trans('legacy.6959b4159575',$this->locale)).'</b><p>').(int)$cfg['idle_minutes'].(' '.$this->i18n->trans('legacy.5b9e4dbfde71',$this->locale).' ').(int)$cfg['absolute_hours'].(' '.Html::e($this->i18n->trans('legacy.4c65817d66b2',$this->locale)).'</p></div></div></div>');
            $body.=('<div class="panel"><h2>Guardian Policy Advisor</h2><p>'.Html::e($this->i18n->trans('legacy.cf1bf53645d7',$this->locale)).'</p>');
            foreach (\OmegaGuardian\ProfilerShield\Security\PolicyAdvisor::advise($cfg,$this->accounts->allPublic()) as $advice) {
                $body.='<div class="event"><strong>'.Html::e($this->i18n->text($advice['title'],$this->locale)).'</strong><p>'.Html::e($this->i18n->text($advice['detail'],$this->locale)).'</p></div>';
            }
            $body.=('</div><div class="panel"><h2>'.Html::e($this->i18n->trans('legacy.ae7268a07ad6',$this->locale)).'</h2>'); 
            if ($allowed) { $body.=('<p><a class="button-link" href="/_profiler/" rel="noreferrer">'.Html::e($this->i18n->trans('legacy.0cef172d4310',$this->locale)).'</a></p>'); }
            else { $body.=('<p>'.Html::e($this->i18n->trans('legacy.d5dd3700b893',$this->locale)).'</p>'); }
            if ($cfg['jit_enabled'] && in_array($user['role'],array('admin','developer'),true)) {
                $body.='<form method="post"><input type="hidden" name="_csrf" value="'.$csrf.('"><input type="hidden" name="action" value="jit"><button>'.Html::e($this->i18n->trans('legacy.77bf4e88dbc8',$this->locale)).' ').(int)$cfg['jit_minutes'].(' '.Html::e($this->i18n->trans('legacy.24ad3d76496d',$this->locale)).'</button></form>');
            }
            $body.='</div>';
            if ($cfg['emergency_enabled'] && in_array($user['role'],array('admin','developer'),true)) {
                $body.=('<div class="panel"><h2>'.Html::e($this->i18n->trans('legacy.9163e792d407',$this->locale)).'</h2><p>'.Html::e($this->i18n->trans('legacy.95d2ce817c93',$this->locale)).'</p>')
                    .'<form method="post"><input type="hidden" name="_csrf" value="'.$csrf.'"><input type="hidden" name="action" value="emergency">'
                    .('<label>'.Html::e($this->i18n->trans('auth.password',$this->locale)).' <input type="password" name="password" required></label><label>'.Html::e($this->i18n->trans('legacy.22ffdc8aa7ea',$this->locale)).' <input name="code" required></label>')
                    .('<label>'.Html::e($this->i18n->trans('legacy.c24c06c3f7fd',$this->locale)).' <input name="reason" minlength="8" maxlength="160" required></label><button class="amber">'.Html::e($this->i18n->trans('legacy.5c061af459bf',$this->locale)).'</button></form></div>');
            }
        }
        $body.='</div><footer class="workspace-footer"><span>ΩGuardian Profiler Shield</span><span>Security workspace · '.$t('role.'.($user['role']==='security'?'security':$user['role'])).'</span></footer></main></div>';
        $response=$this->html('Centre de sécurité',$body,$flash!==''?400:200);
        if ($request->query->has('lang') && in_array((string)$request->query->get('lang'), array_keys(UiTranslator::AVAILABLE), true)) {
            $response->headers->setCookie(new Cookie('__Host-GuardianLocale',$locale,time()+365*86400,'/',null,true,true,false,'Lax'));
        }
        return $response;
    }

    /**
     * Formulaire de configuration autonome et lisible.
     *
     * IMPORTANT : le planificateur visuel n'est qu'une prévisualisation.
     * Les restrictions ne prennent effet qu'après un POST CSRF validé et la
     * confirmation du mot de passe administrateur. Le serveur recalcule
     * chaque autorisation à l'instant de la requête protégée.
     */
    private function settingsView(array $s, $csrf, $path, $locale = 'fr_FR')
    {
        $t=function ($key) use ($locale) { return Html::e($this->i18n->trans($key,$locale)); };
        $check=function ($key) use ($s) { return !empty($s[$key])?' checked':''; };
        $number=function ($key,$label,$help='') use ($s) {
            return '<label class="input-label">'.Html::e($label).'<input type="number" min="1" max="720" name="'.$key.'" value="'.(int)$s[$key].'" required>'
                .($help ? '<small>'.Html::e($help).'</small>':'').'</label>';
        };
        $body='<nav class="settings-index" aria-label="'.$t('ui.sections').'">';
        foreach (array('guardian-auth-safety'=>'security.title','guardian-presets'=>'ui.presets','guardian-access-schedule'=>'schedule.title','guardian-security-options'=>'config.title','guardian-error-pages'=>'errors.title','guardian-mfa'=>'mfa.title','guardian-accounts'=>'ui.accounts') as $id=>$label) {
            $body.='<a href="#'.$id.'">'.$t($label).'</a>';
        }
        $body.=('</nav><section class="panel intro-panel"><div><span class="eyebrow">'.Html::e($this->i18n->trans('legacy.9a12ea2a1b3c',$this->locale)).'</span>')
            .('<h2>'.Html::e($this->i18n->trans('legacy.ed9acba48aed',$this->locale)).'</h2><p>'.Html::e($this->i18n->trans('legacy.1ec7c86a5de2',$this->locale)).'</p></div>')
            .'<div class="intro-orbit" aria-hidden="true">✦</div></section>';
        $body.=('<section class="panel" id="guardian-presets"><div class="section-head"><div><span class="section-kicker">'.Html::e($this->i18n->trans('legacy.0eba34da749d',$this->locale)).'</span><h2>'.Html::e($this->i18n->trans('legacy.51556dfa1adb',$this->locale)).'</h2>')
            .('<p>'.Html::e($this->i18n->trans('legacy.bf333101f931',$this->locale)).'</p></div></div>')
            .'<form method="post" class="quick-preset"><input type="hidden" name="action" value="preset"><input type="hidden" name="_csrf" value="'.$csrf.'">'
            .('<label class="input-label">'.Html::e($this->i18n->trans('legacy.e267d343821f',$this->locale)).'<select name="preset"><option value="solo"').($s['preset']==='solo'?' selected':'').('>'.Html::e($this->i18n->trans('legacy.cd2a76692e65',$this->locale)).'</option><option value="team"').($s['preset']==='team'?' selected':'').('>'.Html::e($this->i18n->trans('legacy.019e75a9e3b8',$this->locale)).'</option><option value="strict"').($s['preset']==='strict'?' selected':'').('>'.Html::e($this->i18n->trans('legacy.113f13b29570',$this->locale)).'</option></select></label>')
            .('<label class="input-label">'.Html::e($this->i18n->trans('common.password',$this->locale)).'<input type="password" name="password" autocomplete="current-password" required></label>')
            .('<button type="submit">'.Html::e($this->i18n->trans('legacy.20852ce2fedf',$this->locale)).'</button></form></section>');
        $body.='<form method="post" class="config-form"><input type="hidden" name="action" value="settings"><input type="hidden" name="_csrf" value="'.$csrf.'">';
        $phpZone=\OmegaGuardian\ProfilerShield\Util\ServerClock::phpTimezone();
        $osZone=\OmegaGuardian\ProfilerShield\Util\ServerClock::operatingSystemTimezone();
        $phpDescription=\OmegaGuardian\ProfilerShield\Util\ServerClock::description($phpZone);
        $configuredDiffers=$s['timezone']!==$phpZone;
        $summaryMessages=array();
        foreach (array('schedule.js.disabled','schedule.js.retained','schedule.js.no_days','schedule.js.zone_missing','schedule.js.allowed','schedule.js.portal_closed') as $messageKey) {
            $summaryMessages[$messageKey]=$this->i18n->trans($messageKey,$locale);
        }
        $scheduleLabels=Html::e(json_encode($summaryMessages, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $body.='<section class="panel schedule-panel" id="guardian-access-schedule" data-locale="'.Html::e($locale).'" data-messages="'.$scheduleLabels.'">'
            .('<div class="section-head"><div><span class="section-kicker">'.Html::e($this->i18n->trans('legacy.5dab55dee8d7',$this->locale)).'</span><h2>').$t('schedule.title').'</h2>'
            .'<p>'.$t('schedule.description').'</p></div>'
            .'<span class="calendar-symbol" aria-hidden="true">▦</span></div>'
            .'<div class="schedule-surface"><div class="schedule-top"><div><h3>'.$t('schedule.week').'</h3><p>'.$t('schedule.week_help').'</p></div><span class="schedule-timezone">◷ &nbsp;'.Html::e($s['timezone']).'</span></div>'
            .'<label class="toggle-row primary-toggle"><span><strong>'.$t('schedule.lock').'</strong><small>'.$t('schedule.lock_help').'</small></span><input class="toggle-native" type="checkbox" name="schedule_enabled" value="1"'.$check('schedule_enabled').'><span class="toggle-track" aria-hidden="true"></span></label>'
            .'<div class="timezone-observer" data-server-timezone="'.Html::e($phpZone).'" data-original-timezone="'.Html::e($s['timezone']).'">'
            .'<div class="timezone-observer-head"><span class="tz-icon">◷</span><div><strong>'.$t('schedule.server_zone').'</strong><span>'.Html::e($phpDescription).'</span></div><span class="tz-status '.($configuredDiffers?'tz-status-warning':'tz-status-ok').'">'.($configuredDiffers?$t('schedule.zone_mismatch'):$t('schedule.zone_matches')).'</span></div>'
            .'<p>'.$t('schedule.server_explain').'</p>'
            .($osZone!==null?'<small>'.$t('schedule.os_zone').' : '.Html::e($osZone).($osZone!==$phpZone?' · '.$t('schedule.os_warning'):'').'</small>':'<small>'.$t('schedule.os_unknown').'</small>')
            .'</div>'
            .'<fieldset class="dependent-fields schedule-dependent" data-enabled-by="schedule_enabled"><legend class="sr-only">'.$t('schedule.title').'</legend><div class="week-selector" role="group" aria-label="'.$t('schedule.allowed_days').'">';
        foreach (array(1=>'days.1',2=>'days.2',3=>'days.3',4=>'days.4',5=>'days.5',6=>'days.6',7=>'days.7') as $day=>$key) {
            $selected=in_array($day,$s['weekdays'],true);
            $body.='<label class="day-tile"><input type="checkbox" name="weekdays[]" value="'.$day.'"'.($selected?' checked':'').'><span class="day-face"><span>'.$t($key).'</span><span class="day-mark" aria-hidden="true">✓</span></span></label>';
        }
        $body.='</div><div class="schedule-time-grid"><label class="input-label">'.$t('schedule.start').'<input type="time" name="start" value="'.Html::e($s['start']).'" required></label>'
            .'<div class="time-separator" aria-hidden="true">→</div><label class="input-label">'.$t('schedule.end').'<input type="time" name="end" value="'.Html::e($s['end']).'" required></label>'
            .'<label class="input-label">'.$t('schedule.timezone').'<input type="text" name="timezone" list="guardian-timezones" value="'.Html::e($s['timezone']).'" required>'
            .'<datalist id="guardian-timezones"><option value="Europe/Paris"><option value="Europe/London"><option value="America/New_York"><option value="America/Montreal"><option value="Asia/Tokyo"><option value="UTC"></datalist></label></div>'
            .'<div class="timezone-confirm" id="guardian-timezone-confirm" hidden><label><input type="checkbox" name="timezone_ack" value="1"><span><strong>'.$t('schedule.confirm_title').'</strong><small>'.$t('schedule.confirm_detail').'</small></span></label></div>'
            .'</fieldset><div class="schedule-switches"><fieldset class="dependent-fields" data-enabled-by="schedule_enabled"><legend class="sr-only">'.$t('schedule.portal').'</legend>';
        $body.='<label class="toggle-row"><span><strong>'.$t('schedule.portal').'</strong><small>'.$t('schedule.portal_help').'</small></span>'
            .'<input class="toggle-native" type="checkbox" name="portal_closed_outside_hours" value="1"'.$check('portal_closed_outside_hours').'><span class="toggle-track" aria-hidden="true"></span></label>'
            .'</fieldset>';
        $body.='</div></div><div class="schedule-summary" aria-live="polite"><div class="summary-icon">◷</div><div>'
            .'<strong>'.$t('schedule.summary').'</strong><p id="guardian-schedule-summary">'.$t('schedule.summary_pending').'</p>'
            .'<small id="guardian-schedule-detail">'.$t('schedule.server').'</small></div></div>'
            .'<p class="schedule-warning" id="guardian-schedule-warning" hidden>'.$t('schedule.invalid').'</p>'
            .'</section>';
        // Chaque option pilote SON panneau secondaire immédiatement dessous.
        // Les fieldssets sont désactivés en DOM si le contrôle est éteint.
        // Le serveur conserve les anciennes valeurs des champs désactivés.
        $toggle=function ($key,$title,$help) use ($check) {
            return '<label class="toggle-row"><span><strong>'.Html::e($this->i18n->text($title,$this->locale)).'</strong><small>'.Html::e($help).'</small></span>'
                .'<input class="toggle-native" type="checkbox" name="'.$key.'" value="1"'.$check($key).' aria-controls="guardian-options-'.$key.'">'
                .'<span class="toggle-track" aria-hidden="true"></span></label>';
        };
        $body.='<section class="panel config-section" id="guardian-security-options"><div class="section-head"><div><span class="section-kicker">03 · '.$t('config.section').'</span><h2>'.$t('config.title').'</h2>'
            .'<p>'.$t('config.description').'</p></div></div>'
            .'<div class="config-card-grid"><div class="config-card">'
            .$toggle('stealth',$t('config.stealth'),$t('config.stealth_help'))
            .'<p class="option-impact">'.$t('config.stealth_impact').'</p></div>'
            .'<div class="config-card">'
            .$toggle('mfa',$t('config.mfa'),$t('config.mfa_help'))
            .'<p class="option-impact">'.$t('config.mfa_impact').'</p><a class="inline-jump" href="#guardian-mfa">'.$t('config.setup_mfa').' ↓</a></div>'
            .'<div class="config-card">'
            .$toggle('jit_enabled',$t('config.jit'),$t('config.jit_help'))
            .'<fieldset id="guardian-options-jit_enabled" class="dependent-fields option-dependent" data-enabled-by="jit_enabled"><legend class="sr-only">'.$t('config.jit').'</legend>'
            .$number('jit_minutes',$t('config.jit_minutes'),$t('config.jit_minutes_help')).'</fieldset></div>'
            .'<div class="config-card">'
            .$toggle('emergency_enabled',$t('config.emergency'),$t('config.emergency_help'))
            .'<fieldset id="guardian-options-emergency_enabled" class="dependent-fields option-dependent" data-enabled-by="emergency_enabled"><legend class="sr-only">'.$t('config.emergency').'</legend>'
            .'<label class="input-label">'.$t('config.alert_email').'<input type="email" name="alert_email" value="'.Html::e($s['alert_email']).'" placeholder="security@example.org"></label>'
            .'<small>'.$t('config.alert_help').'</small></fieldset></div></div>'
            .'<div class="config-subheading"><h3>'.$t('config.sessions').'</h3><p>'.$t('config.sessions_help').'</p></div>'
            .'<div class="grid2 config-session-fields">'
            .$number('idle_minutes',$t('config.idle'),$t('config.idle_help'))
            .$number('absolute_hours',$t('config.absolute'),$t('config.absolute_help'))
            .$number('remember_days',$t('config.remember'),$t('config.remember_help')).'</div>'
            .'<div class="config-subheading"><h3>'.$t('config.audit').'</h3><p>'.$t('config.audit_help').'</p></div>'
            .'<div class="grid2 config-session-fields"><label class="input-label">'.$t('config.audit_level').'<select name="audit_level">';
        foreach (array('minimal'=>$t('config.audit_minimal'),'standard'=>$t('config.audit_standard'),'advanced'=>$t('config.audit_advanced')) as $value=>$label) {
            $body.='<option value="'.$value.'"'.($value===$s['audit_level']?' selected':'').'>'.$label.'</option>';
        }
        $body.='</select></label>'.$number('audit_retention_days',$t('config.audit_retention'),$t('config.audit_retention_help')).'</div>'
            .'<div class="config-subheading"><h3>'.$t('config.network').'</h3><p>'.$t('config.network_help').'</p></div>'
            .'<label class="input-label">'.$t('config.cidr').'<textarea name="ip_allowlist" rows="2" placeholder="192.0.2.0/24, 2001:db8::/32">'.Html::e($s['ip_allowlist']).'</textarea>'
            .'<small>'.$t('config.cidr_help').'</small></label>'
            .'<div class="apply-panel section-save" id="guardian-security-save"><div><strong>'.$t('config.save_title').'</strong><p>'.$t('config.save_help').'</p></div>'
            .'<label class="input-label">'.$t('config.admin_password').'<input type="password" name="password" autocomplete="current-password" required></label>'
            .'<button type="submit">'.$t('config.save').' →</button></div></section></form>';
        return $body;
    }

    /** Pages d'erreur : formulaire autonome, ne partage pas le POST TimeLock. */
    private function errorSettingsForm(array $cfg, $csrf, $locale)
    {
        $t=function ($key) use ($locale) { return Html::e($this->i18n->trans($key,$locale)); };
        $mode=isset($cfg['error_mode']) ? $cfg['error_mode'] : 'native';
        $reference=isset($cfg['error_reference']) ? $cfg['error_reference'] : 'none';
        $out='<section class="panel" id="guardian-error-pages"><div class="section-head"><div>'
            .('<span class="section-kicker">'.Html::e($this->i18n->trans('legacy.d86307343aae',$this->locale)).'</span>')
            .'<h2>'.$t('errors.title').'</h2><p>'.$t('errors.lead').'</p></div></div>'
            .'<form method="post" class="config-form"><input type="hidden" name="action" value="error_settings">'
            .'<input type="hidden" name="_csrf" value="'.Html::e($csrf).'">'
            .'<div class="grid2"><label class="input-label">'.$t('errors.mode').'<select name="error_mode">';
        foreach (array('native','generic','custom') as $value) {
            $out.='<option value="'.$value.'"'.($mode===$value?' selected':'').'>'.$t('errors.'.$value).'</option>';
        }
        $out.='</select></label><label class="input-label">'.$t('errors.reference').'<select name="error_reference">';
        foreach (array('none','random','custom') as $value) {
            $out.='<option value="'.$value.'"'.($reference===$value?' selected':'').'>'.$t('errors.ref_'.$value).'</option>';
        }
        $out.='</select></label></div><p class="option-impact">'.$t('errors.mode_help').'</p><p class="option-impact">'.$t('errors.reference_help').'</p><div class="grid2">'
            .'<label class="input-label" data-error-field="message">'.$t('errors.custom_text').'<textarea name="error_custom_text" rows="2" maxlength="360" aria-describedby="guardian-error-message-help">'
            .Html::e(isset($cfg['error_custom_text'])?$cfg['error_custom_text']:'').'</textarea><small id="guardian-error-message-help">'.$t('errors.custom_help').'</small></label>'
            .'<label class="input-label" data-error-field="reference">'.$t('errors.ref_text').'<input name="error_reference_text" maxlength="60" aria-describedby="guardian-error-reference-help" value="'
            .Html::e(isset($cfg['error_reference_text'])?$cfg['error_reference_text']:'').'"><small id="guardian-error-reference-help">'.$t('errors.ref_text_help').'</small></label></div>'
            .'<aside class="error-preview" data-native-help="'.$t('ui.preview_native').'"><div class="preview-caption"><strong>'.$t('ui.preview').'</strong><span>'.$t('ui.preview_hint').'</span></div><div class="preview-page"><small data-preview-native></small><h3 data-preview-title>Unavailable</h3><p data-preview-message></p><code data-preview-reference></code></div></aside>'
            .'<p class="option-impact">'.$t('errors.warning').'</p>'
            .'<div class="apply-panel"><label class="input-label">'.$t('errors.password')
            .'<input type="password" name="password" required autocomplete="current-password"></label>'
            .'<button type="submit">'.$t('errors.save').' →</button></div></form></section>';
        return $out;
    }

    /**
     * Assistant TOTP intégré au panneau (aucune navigation vers une page isolée).
     *
     * Une seule réauthentification est nécessaire pour ENTRER dans ce parcours.
     * Le second POST est signé CSRF, lié à cette même session et ne contient
     * que le code TOTP. Le secret provisoire n'est pas rendu ailleurs.
     */
    private function personalMfaForm($csrf, array $session, array $user, $locale='fr_FR', $error='')
    {
        $t=function ($key) use ($locale) {return Html::e($this->i18n->trans($key,$locale));};
        $html=('<section class="panel mfa-panel" id="guardian-mfa"><div class="section-head"><div><span class="section-kicker">'.Html::e($this->i18n->trans('legacy.064f7baf66f8',$this->locale)).'</span>')
            .'<h2>'.$t('mfa.title').'</h2><p>'.$t('mfa.lead').'</p></div><span class="mfa-mark" aria-hidden="true">✥</span></div>';
        if ($error!=='') {
            $html.='<div class="inline-alert mfa-inline-alert" id="guardian-form-alert" role="alert"><span aria-hidden="true">!</span><p>'.Html::e($error).'</p></div>';
        }
        if (!empty($user['totp'])) {
            return $html.'<div class="mfa-status mfa-status-ok"><span aria-hidden="true">✓</span><div><strong>'.$t('mfa.active').'</strong><p>'.$t('mfa.active_help').'</p></div></div></section>';
        }
        $pending=!empty($session['pending_totp']) && !empty($session['pending_until']) && (int)$session['pending_until']>time()
            && (int)($session['pending_otp_attempts'] ?? 0)<5;
        if (!$pending) {
            $html.='<div class="mfa-step"><span class="step-index">01</span><div><strong>'.$t('mfa.step1').'</strong><p>'.$t('mfa.step1_help').'</p></div></div>'
                .'<form method="post" class="mfa-prepare-form" action="#guardian-mfa">'
                .'<input type="hidden" name="action" value="mfa_prepare"><input type="hidden" name="_csrf" value="'.Html::e($csrf).'">'
                .'<label class="input-label" for="guardian-password-confirm">'.$t('mfa.current_password').'<input id="guardian-password-confirm" type="password" name="password" autocomplete="current-password" required>'
                .'<small>'.$t('mfa.not_otp').'</small></label><button type="submit">'.$t('mfa.start').' →</button></form>';
        } else {
            $secret=$this->totp->decrypt($session['pending_totp']);
            // L'issuer est volontairement neutre : aucune URL du serveur
            // hébergeant le profiler n'est injectée dans l'application TOTP.
            $issuer='OmegaGuardian';
            $account=$session['username'];
            $uri='otpauth://totp/'.rawurlencode($issuer).':'.rawurlencode($account)
                .'?secret='.rawurlencode($secret).'&issuer='.rawurlencode($issuer).'&algorithm=SHA1&digits=6&period=30';
            $qr='';
            try {
                $svg=\OmegaGuardian\ProfilerShield\Security\LocalQrCode::renderSvg($uri);
                $qr='<img class="mfa-qr" width="224" height="224" alt="'.$t('mfa.qr_alt').'" src="data:image/svg+xml;base64,'.base64_encode($svg).'">';
            } catch (\Throwable $error) {
                // Le code manuel doit rester disponible en cas d'extension
                // XMLWriter absente. On n'envoie jamais l'URI à un tiers.
                $qr='<div class="mfa-qr-error">'.$t('mfa.qr_unavailable').'</div>';
            }
            $html.='<div class="mfa-step"><span class="step-index">02</span><div><strong>'.$t('mfa.step2').'</strong><p>'.$t('mfa.step2_help').'</p></div></div>'
                .'<div class="mfa-setup-grid"><div class="mfa-qr-card"><span class="mfa-qr-caption">'.$t('mfa.local_only').'</span>'.$qr
                .'<small>'.$t('mfa.scan_help').'</small></div>'
                .'<div class="mfa-setup-help"><div class="mfa-privacy-note"><strong>'.$t('mfa.privacy_title').'</strong><p>'.$t('mfa.privacy_help').'</p></div>'
                .'<label class="input-label" for="guardian-secret">'.$t('mfa.manual_secret').'<input id="guardian-secret" class="mfa-secret" type="text" value="'.Html::e($secret).'" readonly spellcheck="false" autocomplete="off"></label>'
                .'<p class="hint">'.$t('mfa.manual_help').'</p></div></div>'
                .'<div class="mfa-step"><span class="step-index">03</span><div><strong>'.$t('mfa.step3').'</strong><p>'.$t('mfa.step3_help').'</p></div></div>'
                .'<form method="post" class="mfa-confirm-form" action="#guardian-mfa"><input type="hidden" name="action" value="mfa_confirm">'
                .'<input type="hidden" name="_csrf" value="'.Html::e($csrf).'">'
                .'<label class="input-label" for="guardian-totp-code">'.$t('mfa.otp_code').'<input id="guardian-totp-code" type="text" name="otp" inputmode="numeric" pattern="[0-9]{6}" minlength="6" maxlength="6" autocomplete="one-time-code" placeholder="000000" required></label>'
                .'<button type="submit">'.$t('mfa.finish').' →</button></form>'
                .'<form method="post" class="mfa-cancel" action="#guardian-mfa"><input type="hidden" name="action" value="mfa_cancel">'
                .'<input type="hidden" name="_csrf" value="'.Html::e($csrf).'"><button type="submit" class="secondary">'.$t('mfa.cancel').'</button></form>';
        }
        return $html.'</section>';
    }

    /** Gestion locale des comptes, sans toucher aux comptes métiers du site. */
    /**
     * Gestion des identités : rôles et sessions toujours validés côté serveur.
     * Changer le rôle révoque les sessions, sans changer le compte métier.
     * La modification de son propre rôle est interdite pour éviter l'auto-lock.
     */
    private function userManager($csrf)
    {
        $html=('<section class="panel" id="guardian-accounts"><div class="section-head"><div><span class="section-kicker">'.Html::e($this->i18n->trans('legacy.3749df7c8224',$this->locale)).'</span><h2>'.Html::e($this->i18n->trans('legacy.96453ddf4874',$this->locale)).'</h2><p>'.Html::e($this->i18n->trans('legacy.d8ca014d151b',$this->locale)).'</p></div></div><div class="list-filters" data-account-filters><label>'.Html::e($this->i18n->trans('auth.username',$this->locale)).'<input type="search" data-account-search autocomplete="off"></label><label>'.Html::e($this->i18n->trans('legacy.a1fc967c5587',$this->locale)).'<select data-account-role><option value="">'.Html::e($this->i18n->trans('ui.all',$this->locale)).'</option><option value="admin">'.Html::e($this->i18n->trans('role.admin',$this->locale)).'</option><option value="developer">'.Html::e($this->i18n->trans('role.developer',$this->locale)).'</option><option value="security">'.Html::e($this->i18n->trans('role.security',$this->locale)).'</option></select></label></div><p data-account-count role="status"></p><div class="account-manager scroll-region" tabindex="0" role="region" aria-label="'.Html::e($this->i18n->trans('legacy.96453ddf4874',$this->locale)).'">');
        foreach ($this->accounts->allPublic() as $u) {
            $name=Html::e($u['name']);
            $role=Html::e($this->i18n->trans('role.'.$u['role'],$this->locale));
            $html.='<div class="account-entry" data-account-name="'.$name.'" data-account-kind="'.Html::e($u['role']).'"><span class="identity-avatar">'.Html::e(strtoupper(substr($u['name'],0,1))).'</span><div class="account-identity"><strong>'.$name.'</strong><span>'.$role.' · '.($u['mfa']?($this->i18n->trans('legacy.72ef39dd6195',$this->locale)):($this->i18n->trans('legacy.1170e20dff75',$this->locale))).($u['disabled']?(' · '.$this->i18n->trans('legacy.c9abb9be49ea',$this->locale)):'').'</span></div>';
            if (!empty($u['password_change_required'])) {$html.='<span class="pill pill-locked">'.Html::e($this->i18n->trans('security.change_title',$this->locale)).'</span>';}
            if (!$u['disabled']) {
                $html.='<form class="account-inline-action" method="post"><input type="hidden" name="_csrf" value="'.$csrf.'"><input type="hidden" name="action" value="user_revoke"><input type="hidden" name="username" value="'.$name.('"><input type="password" name="password" autocomplete="current-password" placeholder="'.Html::e($this->i18n->trans('legacy.54d4fb6034a8',$this->locale)).'" aria-label="'.Html::e($this->i18n->trans('legacy.54d4fb6034a8',$this->locale)).'" required><button type="submit" class="secondary">'.Html::e($this->i18n->trans('legacy.37486918dd39',$this->locale)).'</button></form>');
            }
            $html.='</div>';
        }
        $html.=('</div><div class="grid2"><div><h3>'.Html::e($this->i18n->trans('legacy.a628af9809b4',$this->locale)).'</h3><form method="post">')
            .'<input type="hidden" name="action" value="user_create"><input type="hidden" name="_csrf" value="'.$csrf.'">'
            .('<label>'.Html::e($this->i18n->trans('auth.username',$this->locale)).'<input name="new_username" minlength="3" maxlength="64" required></label>')
            .$this->securityPanel->requirements($this->settings->all(),$this->locale,'creation-policy')
            .('<label>'.Html::e($this->i18n->trans('security.new_password',$this->locale)).'<input type="password" name="new_password" autocomplete="new-password" aria-describedby="creation-policy" maxlength="512" required></label>')
            .('<label>'.Html::e($this->i18n->trans('legacy.a1fc967c5587',$this->locale)).'<select name="role"><option value="developer">'.Html::e($this->i18n->trans('role.developer',$this->locale)).'</option><option value="security">'.Html::e($this->i18n->trans('legacy.d7f3a7609eba',$this->locale)).'</option><option value="admin">'.Html::e($this->i18n->trans('role.admin',$this->locale)).'</option></select></label>')
            .('<label>'.Html::e($this->i18n->trans('legacy.729a3dcad3db',$this->locale)).'<input type="password" name="password" required></label><button>'.Html::e($this->i18n->trans('legacy.a0c6ca6813e8',$this->locale)).'</button></form></div>')
            .('<div><h3>'.Html::e($this->i18n->trans('legacy.61348ab46e50',$this->locale)).'</h3><form method="post">')
            .'<input type="hidden" name="action" value="user_role"><input type="hidden" name="_csrf" value="'.$csrf.'">'
            .('<label>'.Html::e($this->i18n->trans('auth.username',$this->locale)).'<input name="username" required></label>')
            .('<label>'.Html::e($this->i18n->trans('legacy.753ea77345d5',$this->locale)).'<select name="role"><option value="developer">'.Html::e($this->i18n->trans('role.developer',$this->locale)).'</option><option value="security">'.Html::e($this->i18n->trans('role.security',$this->locale)).'</option><option value="admin">'.Html::e($this->i18n->trans('role.admin',$this->locale)).'</option></select></label>')
            .('<p>'.Html::e($this->i18n->trans('legacy.0bc04cef074d',$this->locale)).'</p>')
            .('<label>'.Html::e($this->i18n->trans('legacy.729a3dcad3db',$this->locale)).'<input type="password" name="password" required></label><button>'.Html::e($this->i18n->trans('legacy.c1d2fc2dbff2',$this->locale)).'</button></form>')
            .('<h3>'.Html::e($this->i18n->trans('legacy.34961ab6ab5b',$this->locale)).'</h3><form method="post">')
            .'<input type="hidden" name="action" value="user_disable"><input type="hidden" name="_csrf" value="'.$csrf.'">'
            .('<label>'.Html::e($this->i18n->trans('legacy.ce8fa0e725aa',$this->locale)).'<input name="username" required></label>')
            .('<label>'.Html::e($this->i18n->trans('legacy.729a3dcad3db',$this->locale)).'<input type="password" name="password" required></label>')
            .('<button class="amber">'.Html::e($this->i18n->trans('legacy.e8731fa229d1',$this->locale)).'</button></form></div></div></section>');
        return $html;
    }

    private function guide()
    {
        $items=[];
        foreach (['access','identity','passwords','throttle','simulation','schedule','mfa','sessions','jit','network','errors','audit','recovery','removal'] as $topic) {
            $items[]=[$this->i18n->trans('guide.'.$topic.'_title',$this->locale),$this->i18n->trans('guide.'.$topic,$this->locale)];
        }
        $html=('<div class="panel"><h2>'.Html::e($this->i18n->trans('legacy.f8882843a7ee',$this->locale)).'</h2><p>'.Html::e($this->i18n->trans('legacy.1948b5b009f1',$this->locale)).'</p>');
        foreach ($items as $item) { $html.='<details><summary>'.Html::e($item[0]).'</summary><p>'.Html::e($item[1]).'</p></details>'; }
        return $html.'</div>';
    }

    /** Pictogrammes SVG locaux : aucun CDN ni ressource externe. */
    /** Carte chiffrée sans identifiant de session ni adresse IP exposés. */
    private function teamStat($value,$label,$caption)
    {
        return '<div class="team-stat"><strong>'.Html::e($value).'</strong><span>'.Html::e($label).'</span><small>'.Html::e($caption).'</small></div>';
    }

    private function card($icon,$label,$value)
    {
        $paths = array(
            'shield-check'=>'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>',
            'eye'=>'<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
            'clock'=>'<circle cx="12" cy="12" r="9"/><path d="M12 7v5l4 2"/>',
            'heart'=>'<path d="M3 12h4l2-4 4 8 2-4h6"/>'
        );
        $path = isset($paths[$icon]) ? $paths[$icon] : $paths['shield-check'];
        $svg = '<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round">'.$path.'</svg>';
        return '<div class="metric"><span class="metric-icon">'.$svg.'</span><small>'.Html::e($label).'</small><strong>'.Html::e($value).'</strong></div>';
    }

    private function notice(Request $request,$message,$view=null)
    {
        $message=$this->i18n->trans($message,$this->locale);$message=$this->i18n->text($message,$this->locale);
        // Les erreurs des formulaires Guardian reviennent dans leur panneau
        // avec le formulaire intact, sans redirection vers une page de login.
        try {
            $session=$this->sessions->current($request);
            if ($session) {
                $user=$this->accounts->find($session['username']);
                if ($user && empty($user['disabled'])) {
                    $action=(string)$request->request->get('action','');
                    $target=$view!==null?$view:(in_array($action,array('jit','emergency'),true)?'home':'settings');
                    return $this->dashboard($request,$session,$user,$message,$target);
                }
            }
        } catch (\Throwable $ignored) {
            // Les requêtes de diagnostic restent refusées en cas de panne.
        }
        return $this->html(($this->i18n->trans('legacy.58b8bf7de679',$this->locale)),'<div class="panel"><p>'.Html::e($message).'</p></div>',403);
    }

    private function redirect($path)
    {
        $r=new Response('',303,array('Location'=>$path));
        $r->headers->set('Cache-Control','no-store, private');
        return $r;
    }

    /** Préférence d'interface autonome, sans modifier la locale de l'application. */
    private function preferredLocale(Request $request)
    {
        $candidate = (string) $request->query->get('lang', '');
        if (isset(UiTranslator::AVAILABLE[$candidate])) { return $candidate; }
        $cookie = (string) $request->cookies->get('__Host-GuardianLocale', '');
        if (isset(UiTranslator::AVAILABLE[$cookie])) { return $cookie; }
        if (method_exists($request, 'getPreferredLanguage')) {
            return $this->i18n->normalize($request->getPreferredLanguage(array_keys(UiTranslator::AVAILABLE)));
        }
        return 'fr_FR';
    }

    private function html($title,$body,$status=200)
    {
        // Tous les assets restent embarqués : pas de requête CDN, ni lien
        // public vers une URL privée dans les pages de l'application hôte.
        $css=(string)@file_get_contents(dirname(__DIR__,2).'/assets/guardian.css');
        $js=(string)@file_get_contents(dirname(__DIR__,2).'/assets/guardian.js')."\n".(string)@file_get_contents(dirname(__DIR__,2).'/assets/security.js');
        $nonce=base64_encode(random_bytes(18));
        $dashboard=$title==='Centre de sécurité';
        $container=$dashboard?'workspace-host':'login-shell';
        $html='<!doctype html><html lang="'.Html::e(str_replace('_','-',$this->locale)).'" dir="'.(strpos($this->locale,'ar_')===0?'rtl':'ltr').'"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
            .'<meta name="robots" content="noindex,nofollow,noarchive"><title>'.Html::e($this->i18n->text($title,$this->locale)).' · ΩGuardian</title>'
            .'<style>'.$css.'</style></head><body data-show-password="'.Html::e($this->i18n->trans('ui.show_password',$this->locale)).'" data-hide-password="'.Html::e($this->i18n->trans('ui.hide_password',$this->locale)).'"><div class="'.$container.'">';
        if (!$dashboard) {
            $html.='<div class="auth-intro"><div class="auth-brand"><span class="brand-icon">Ω</span><div><b>Guardian</b><small>PROFILER SHIELD</small></div></div>'
                .('<div class="auth-headline"><span class="eyebrow">'.Html::e($this->i18n->trans('legacy.24ae0eac7a97',$this->locale)).'</span><h2>'.Html::e($this->i18n->trans('legacy.b87feee53025',$this->locale)).' <em>'.Html::e($this->i18n->trans('legacy.b3aeeb096c12',$this->locale)).'</em></h2>')
                .('<p>'.Html::e($this->i18n->trans('legacy.f3bf7b6b0682',$this->locale)).'</p></div>')
                .('<div class="auth-foot">'.Html::e($this->i18n->trans('legacy.3f53945a85ae',$this->locale)).' &nbsp; • &nbsp; '.Html::e($this->i18n->trans('legacy.376c03234f06',$this->locale)).'</div></div><main class="auth-main">');
        }
        $html.=($dashboard?'<a class="skip-link" href="#guardian-main">'.Html::e($this->i18n->trans('ui.skip',$this->locale)).'</a>':'').$body;
        if (!$dashboard) { $html.=('<footer>'.Html::e($this->i18n->trans('legacy.02629590b574',$this->locale)).'</footer></main>'); }
        // Script strictement local : aperçu des horaires et interaction des
        // boutons. Il n'accorde JAMAIS d'autorisation côté serveur.
        $html.='</div><script nonce="'.$nonce.'">'.$js.'</script></body></html>';

        return new Response($html,$status,array(
            'Content-Type'=>'text/html; charset=UTF-8', 'Cache-Control'=>'no-store, private',
            'Referrer-Policy'=>'no-referrer', 'X-Content-Type-Options'=>'nosniff',
            'X-Frame-Options'=>'DENY', 'Content-Security-Policy'=>"default-src 'none'; img-src 'self' data:; style-src 'unsafe-inline'; script-src 'nonce-".$nonce."'; form-action 'self'; base-uri 'none'; frame-ancestors 'none'"
        ));
    }
}
