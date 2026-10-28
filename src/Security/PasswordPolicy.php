<?php
namespace OmegaGuardian\ProfilerShield\Security;

final class PasswordPolicy
{
    public static function defaults(): array
    {
        return ['min_length'=>15,'require_upper'=>false,'require_lower'=>false,'require_digit'=>false,'require_symbol'=>false];
    }

    public static function configuration($policy): array
    {
        if (!is_array($policy) || array_diff(array_keys($policy),array_keys(self::defaults())) || array_diff(array_keys(self::defaults()),array_keys($policy))) {throw new \InvalidArgumentException('security.invalid_policy');}
        if (!is_int($policy['min_length']) || $policy['min_length']<8 || $policy['min_length']>64) {throw new \InvalidArgumentException('security.invalid_policy');}
        foreach (['require_upper','require_lower','require_digit','require_symbol'] as $key) {if (!is_bool($policy[$key])) {throw new \InvalidArgumentException('security.invalid_policy');}}
        return $policy;
    }

    public static function validate($password, array $policy): void
    {
        self::configuration($policy);
        if (!is_string($password) || strlen($password)>512 || preg_match('//u',$password)!==1 || preg_match('/\p{Cc}/u',$password)) {throw new \InvalidArgumentException('security.password_invalid');}
        $length=preg_match_all('/./us',$password);
        if ($length<$policy['min_length']) {throw new \InvalidArgumentException('security.password_short');}
        foreach (['upper'=>'/\p{Lu}/u','lower'=>'/\p{Ll}/u','digit'=>'/\p{Nd}/u','symbol'=>'/[^\p{L}\p{N}\s]/u'] as $class=>$pattern) {
            if ($policy['require_'.$class] && !preg_match($pattern,$password)) {throw new \InvalidArgumentException('security.password_'.$class);}
        }
        if (in_array(strtolower($password),['passwordpassword','passwordpassword1','azertyazertyazerty','123456789012345','qwertyqwertyqwerty'],true)) {throw new \InvalidArgumentException('security.password_common');}
        if (!defined('PASSWORD_ARGON2ID') && strlen($password)>72) {throw new \InvalidArgumentException('security.password_bytes');}
    }

    public static function hash(string $password): string
    {
        $hash=password_hash($password,defined('PASSWORD_ARGON2ID')?PASSWORD_ARGON2ID:PASSWORD_BCRYPT);
        if (!is_string($hash)) {throw new \RuntimeException('security.hash_failed');}
        return $hash;
    }

    public static function generate(array $policy): string
    {
        $groups=['ABCDEFGHJKLMNPQRSTUVWXYZ','abcdefghijkmnopqrstuvwxyz','23456789','!@#$%&*+-=?'];
        $all=implode('',$groups);$chars=[];
        foreach ($groups as $group) {$chars[]=$group[random_int(0,strlen($group)-1)];}
        while (count($chars)<max(32,$policy['min_length'])) {$chars[]=$all[random_int(0,strlen($all)-1)];}
        for ($i=count($chars)-1;$i>0;--$i) {$j=random_int(0,$i);[$chars[$i],$chars[$j]]=[$chars[$j],$chars[$i]];}
        $password=implode('',$chars);self::validate($password,$policy);return $password;
    }
}
