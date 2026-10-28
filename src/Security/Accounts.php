<?php
namespace OmegaGuardian\ProfilerShield\Security;

use OmegaGuardian\ProfilerShield\Storage\FileStore;

/**
 * Comptes locaux distincts des identités métier Symfony.
 * Les mots de passe ne sont jamais journalisés ni stockés en clair.
 * Les noms de compte sont normalisés pour prévenir les doublons ambigus.
 */
final class Accounts
{
    private $store;
    public function __construct(FileStore $store) { $this->store = $store; }

    public function find($username)
    {
        $users = $this->store->read('accounts');
        $username = strtolower(trim((string) $username));
        return isset($users[$username]) ? $users[$username] : null;
    }

    public function add($username, $password, $role = 'admin', $temporary = false)
    {
        $username = strtolower(trim((string) $username));
        if (!preg_match('/^[a-z0-9._-]{3,64}$/D', $username)) {
            throw new \InvalidArgumentException('Identifiant : 3 à 64 caractères alphanumériques, . _ -');
        }
        if (!in_array($role, array('admin','developer','security'), true)) {
            throw new \InvalidArgumentException('Rôle invalide.');
        }
        PasswordPolicy::validate($password,(new Settings($this->store))->all()['password_policy']);
        $hash=PasswordPolicy::hash($password);
        $this->store->change('accounts', function ($users) use ($username, $hash, $role, $temporary) {
            if (isset($users[$username])) { throw new \LogicException('Compte existant.'); }
            $users[$username] = array('hash'=>$hash, 'role'=>$role, 'totp'=>'', 'created'=>time(), 'disabled'=>false,'must_change_password'=>(bool)$temporary,'password_version'=>0);
            return $users;
        });
    }

    public function verify($username, $password)
    {
        if (!is_string($password) || strlen($password) > 512 || strlen((string) $username) > 64) { return null; }
        $user = $this->find($username);
        // Vérification factice : réduit la différence de temps utilisateur absent.
        $dummy = '$2y$12$iYXlBfTZZrsPn4rxvOQBW.GdSYbMJzILfnsvaVENl/HcEg/d3up0y';
        $valid=password_verify($password, $user ? $user['hash'] : $dummy);
        return ($valid && $user && empty($user['disabled'])) ? $user : null;
    }

    public function update($username, callable $mutator)
    {
        $username = strtolower($username);
        $this->store->change('accounts', function ($users) use ($username, $mutator) {
            if (!isset($users[$username])) { throw new \RuntimeException('Compte introuvable.'); }
            $users[$username] = $mutator($users[$username]);
            return $users;
        });
    }

    public function setPassword(string $username,string $password): void
    {
        PasswordPolicy::validate($password,(new Settings($this->store))->all()['password_policy']);
        $hash=PasswordPolicy::hash($password);$wasTemporary=false;
        $this->update($username,function($user) use($username,$password,$hash,&$wasTemporary) {
            if (password_verify($password,$user['hash'])) {throw new \InvalidArgumentException('security.password_reuse');}
            $wasTemporary=$this->needsPasswordChange($username,$user);$user['hash']=$hash;$user['must_change_password']=false;
            $user['password_version']=($user['password_version']??0)+1;return $user;
        });
        if ($username==='admin' && $wasTemporary) {@unlink($this->store->root().'/initial-admin-credentials.txt');}
    }

    public function needsPasswordChange(string $username,array $user): bool
    {
        return !empty($user['must_change_password']) || ($username==='admin' && !array_key_exists('must_change_password',$user) && is_file($this->store->root().'/initial-admin-credentials.txt'));
    }

    public function allPublic()
    {
        $result = array();
        foreach ($this->store->read('accounts') as $name => $user) {
            $result[] = array('name'=>$name,'role'=>$user['role'],'mfa'=>!empty($user['totp']),'disabled'=>!empty($user['disabled']),'password_change_required'=>$this->needsPasswordChange($name,$user));
        }
        return $result;
    }

    public function forcePasswordChange(): void
    {
        $this->store->change('accounts',function($users) {
            foreach ($users as &$user) {if (empty($user['disabled'])) {$user['must_change_password']=true;$user['password_version']=($user['password_version']??0)+1;}}
            unset($user);return $users;
        });
    }
}
