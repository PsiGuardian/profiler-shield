<?php
namespace OmegaGuardian\ProfilerShield\Security;

use OmegaGuardian\ProfilerShield\Storage\FileStore;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sessions indépendantes de PHPSESSID et des sessions métier Symfony.
 *
 * Chaque cookie ne contient qu'un jeton opaque tiré d'un CSPRNG. Le disque
 * ne contient QUE SHA-256(token). Validité et inactivité sont vérifiées à
 * chaque requête sensible, jamais uniquement par l'expiration du navigateur.
 *
 * Les accès concurrents sont protégés par les transactions du FileStore.
 * Une panne de stockage est interceptée par la passerelle de routes
 * sensibles, qui refuse l'accès sans affecter les pages métier.
 */
final class Sessions
{
    const COOKIE = '__Host-GuardianShield';
    const DEVICE_COOKIE = '__Host-GuardianDevice';
    private $store;
    private $settings;

    public function __construct(FileStore $store, Settings $settings)
    {
        $this->store = $store;
        $this->settings = $settings;
    }

    public function begin($username, Response $response, $trusted = false)
    {
        $token = bin2hex(random_bytes(32));
        $csrf = bin2hex(random_bytes(32));
        $now = time();
        $cfg = $this->settings->all();
        $max = $now + 3600 * $cfg['absolute_hours'];
        $id = hash('sha256', $token);
        $accounts=$this->store->read('accounts');$version=$accounts[strtolower($username)]['password_version']??0;
        $this->store->change('sessions', function ($all) use ($id,$username,$csrf,$now,$max,$version) {
            $all[$id] = array('username'=>$username,'csrf'=>$csrf,'created'=>$now,'last'=>$now,'expires'=>$max,'jit_until'=>0,'emergency_until'=>0,'password_version'=>$version);
            return $this->purge($all, $now);
        });
        $this->setCookie($response, self::COOKIE, $token, $max);
        if ($trusted) {
            $this->rememberDevice($username, $response);
        }
    }

    public function current(Request $request)
    {
        $token = $request->cookies->get(self::COOKIE, '');
        if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/D', $token)) {
            return null;
        }
        $id = hash('sha256', $token);
        $now = time();
        $cfg = $this->settings->all();
        $result = null;
        $this->store->change('sessions', function ($all) use ($id,$now,$cfg,&$result) {
            if (isset($all[$id])) {
                $s = $all[$id];
                if (isset($s['created']) && min($s['expires'], $s['created'] + $cfg['absolute_hours'] * 3600) > $now && ($now - $s['last']) <= $cfg['idle_minutes'] * 60) {
                    $s['last'] = $now;
                    $all[$id] = $s;
                    $result = $s;
                } else {
                    unset($all[$id]);
                }
            }
            return $all;
        });
        if ($result) {
            $accounts=$this->store->read('accounts');$account=$accounts[$result['username']]??null;
            if (!$account || !empty($account['disabled']) || ($account['password_version']??0)!==($result['password_version']??0)) {return null;}
        }
        return $result;
    }

    public function modify(Request $request, callable $callback)
    {
        $token = $request->cookies->get(self::COOKIE, '');
        if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/D', $token)) {
            throw new \RuntimeException('Session absente.');
        }
        $id = hash('sha256', $token);
        $this->store->change('sessions', function ($all) use ($id,$callback) {
            if (!isset($all[$id]) || $all[$id]['expires'] <= time()) {
                throw new \RuntimeException('Session invalide.');
            }
            $all[$id] = $callback($all[$id]);
            return $all;
        });
    }

    public function end(Request $request, Response $response)
    {
        $token = $request->cookies->get(self::COOKIE, '');
        if (is_string($token) && preg_match('/^[a-f0-9]{64}$/D', $token)) {
            $id = hash('sha256', $token);
            $this->store->change('sessions', function ($all) use ($id) { unset($all[$id]); return $all; });
        }
        $this->setCookie($response, self::COOKIE, '', 1);
    }

    public function csrf($session, $supplied)
    {
        return is_array($session) && is_string($supplied) && hash_equals($session['csrf'], $supplied);
    }

    public function rememberDevice($username, Response $response)
    {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + $this->settings->all()['remember_days'] * 86400;
        $id = hash('sha256', $token);
        $this->store->change('devices', function ($devices) use ($id,$username,$expiry) {
            foreach ($devices as $k=>$device) { if ($device['expires']<=time()) {unset($devices[$k]);} }
            $devices[$id] = array('username'=>$username,'expires'=>$expiry);
            return $devices;
        });
        $this->setCookie($response, self::DEVICE_COOKIE, $token, $expiry);
    }

    public function recognized(Request $request)
    {
        $token = $request->cookies->get(self::DEVICE_COOKIE, '');
        if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/D', $token)) { return false; }
        $all = $this->store->read('devices');
        $id = hash('sha256', $token);
        return isset($all[$id]) && $all[$id]['expires'] > time();
    }

    public function invalidateUser($username)
    {
        $this->store->change('sessions', function ($all) use ($username) {
            foreach ($all as $id=>$s) {
                if ($s['username'] === $username) { unset($all[$id]); }
            }
            return $all;
        });
    }

    /**
     * Présence administrative indicative, sans transmettre de jetons.
     * Un utilisateur est "actif récemment" si une session valide a eu une
     * requête dans les cinq dernières minutes; ce n'est PAS un WebSocket.
     * Plusieurs sessions du même compte ne comptent qu'un développeur.
     * Les comptes désactivés ne doivent pas figurer dans les chiffres.
     */
    public function activitySummary(array $accounts, $now = null)
    {
        $now=$now===null?time():(int)$now;
        $cfg=$this->settings->all();
        $users=array();
        $sessionCount=0;
        foreach ($this->store->read('sessions') as $session) {
            if (!is_array($session) || !isset($session['username'],$session['expires'],$session['last'])) { continue; }
            if (!isset($session['created']) || min($session['expires'], $session['created'] + $cfg['absolute_hours'] * 3600) <= $now || $now-$session['last'] > $cfg['idle_minutes']*60) { continue; }
            $name=$session['username'];
            if (!isset($accounts[$name]) || !empty($accounts[$name]['disabled'])) { continue; }
            ++$sessionCount;
            if (!isset($users[$name]) || $users[$name]['last'] < $session['last']) {
                $users[$name]=array('name'=>$name,'role'=>$accounts[$name]['role'],'last'=>$session['last']);
            }
        }
        $recent=array();
        $roles=array('admin'=>0,'developer'=>0,'security'=>0);
        foreach ($users as $entry) {
            if ($now-$entry['last']<=300) {
                $recent[]=$entry;
                if (isset($roles[$entry['role']])) { ++$roles[$entry['role']]; }
            }
        }
        usort($recent,function ($a,$b) { return $b['last']-$a['last']; });
        return array('sessions'=>$sessionCount,'users'=>count($users),'recent'=>count($recent),'roles'=>$roles,'members'=>array_slice($recent,0,10));
    }

    private function purge(array $all, $now)
    {
        foreach ($all as $key => $session) {
            if ($session['expires'] <= $now) { unset($all[$key]); }
        }
        return $all;
    }

    private function setCookie(Response $response, $name, $value, $expires)
    {
        // Préfixe __Host : HTTPS requis, Path=/, et aucun attribut Domain.
        $response->headers->setCookie(new Cookie($name, $value, $expires, '/', null, true, true, false, 'Lax'));
    }
}
