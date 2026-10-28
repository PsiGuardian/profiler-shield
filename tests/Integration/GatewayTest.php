<?php
namespace OmegaGuardian\ProfilerShield\Tests\Integration;

use OmegaGuardian\ProfilerShield\EventListener\GuardianSubscriber;
use OmegaGuardian\ProfilerShield\Storage\FileStore;
use OmegaGuardian\ProfilerShield\Security\Settings;
use OmegaGuardian\ProfilerShield\Security\Accounts;
use OmegaGuardian\ProfilerShield\Security\Policy;
use OmegaGuardian\ProfilerShield\Security\Audit;
use OmegaGuardian\ProfilerShield\Security\Sessions;
use OmegaGuardian\ProfilerShield\Security\DiagnosticAccess;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/** Exécuter avec les dépendances Symfony réellement installées. */
final class GatewayTest extends TestCase
{
    private $dir;
    private $store;
    private $settings;
    private $accounts;
    private $subscriber;

    protected function setUp(): void
    {
        $_SERVER['GUARDIAN_ENCRYPTION_KEY']=bin2hex(random_bytes(32));
        $this->dir=sys_get_temp_dir().'/guardian-integration-'.bin2hex(random_bytes(8));
        $this->store=new FileStore($this->dir);
        $this->store->initialize();
        $this->settings=new Settings($this->store);
        $this->accounts=new Accounts($this->store);
        $sessions = new Sessions($this->store,$this->settings);
        $policy = new Policy($this->settings,$this->store);
        $access = new DiagnosticAccess($sessions,$this->accounts,$policy);
        $this->subscriber=new GuardianSubscriber(
            $sessions,$policy,$this->settings,new Audit($this->store),$this->accounts,$access
        );
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir.'/*') as $file) { @unlink($file); }
        @rmdir($this->dir);
    }

    private function event($path, array $cookies=array())
    {
        $kernel=$this->createMock(HttpKernelInterface::class);
        $request=Request::create($path,'GET',array(),$cookies);
        return new RequestEvent($kernel,$request,HttpKernelInterface::MAIN_REQUEST);
    }

    public function testPublicRoutesAreNeverBlockedByGuardian(): void
    {
        $event=$this->event('/account/preferences');
        $this->subscriber->guard($event);
        self::assertFalse($event->hasResponse());
        $business=$this->event('/docs/_profiler/guide');
        $this->subscriber->guard($business);
        self::assertFalse($business->hasResponse());
    }

    public function testProfilerIsBlockedWithoutIdentity(): void
    {
        foreach (array('/_profiler/','/_wdt/abc','/index.php/_profiler/token','/_profiler%252Ftoken') as $path) {
            $event=$this->event($path);
            $this->subscriber->guard($event);
            self::assertTrue($event->hasResponse());
            self::assertSame(404,$event->getResponse()->getStatusCode());
        }
    }

    public function testUnauthorizedTokenIsStillBlocked(): void
    {
        $event=$this->event('/_profiler/abc',array(Sessions::COOKIE=>bin2hex(random_bytes(32))));
        $this->subscriber->guard($event);
        self::assertTrue($event->hasResponse());
        self::assertSame(404,$event->getResponse()->getStatusCode());
    }

    public function testAuthenticatedSessionCanAccessProfilerWithinPolicy(): void
    {
        $this->accounts->add('developer','LongTestPassword!2026','developer');
        $manager=new Sessions($this->store,$this->settings);
        $response=new Response();
        $manager->begin('developer',$response);
        $cookies=$response->headers->getCookies();
        $event=$this->event('/_profiler/',array(Sessions::COOKIE=>$cookies[0]->getValue()));
        $this->subscriber->guard($event);
        self::assertFalse($event->hasResponse());
    }

    public function testLockdownRevokesProfilerAuthorization(): void
    {
        $this->accounts->add('developer','LongTestPassword!2026','developer');
        $manager=new Sessions($this->store,$this->settings);
        $response=new Response();
        $manager->begin('developer',$response);
        $cookie=$response->headers->getCookies()[0]->getValue();
        $this->store->change('control',function ($state) { $state['lockdown']=true;return $state; });
        $event=$this->event('/_profiler/',array(Sessions::COOKIE=>$cookie));
        $this->subscriber->guard($event);
        self::assertTrue($event->hasResponse());
        self::assertSame(404,$event->getResponse()->getStatusCode());
    }

    public function testGuardianStorageFaultDoesNotChangeOrdinaryRoutes(): void
    {
        $event=$this->event('/health');
        foreach (glob($this->dir.'/*') as $f) { @unlink($f); }
        @rmdir($this->dir);
        $this->subscriber->guard($event);
        self::assertFalse($event->hasResponse());
    }
}
