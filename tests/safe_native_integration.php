<?php
ob_start();
require $argv[1] ?? __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\HttpKernel\Exception\{HttpException, HttpExceptionInterface};
use OmegaGuardian\ProfilerShield\Security\{Settings, PublicErrorMapperInterface, PublicErrorDescriptor, PublicErrorRegistry, SafeNativeRenderer};
use OmegaGuardian\ProfilerShield\Storage\FileStore;

final class SafeNativeFixtureController
{
    public static function health(): Response
    {
        return new JsonResponse(['ok' => true], 200, ['X-Application' => 'safe-native-fixture']);
    }

    public static function directError(): Response
    {
        return new JsonResponse(['error' => ['code' => 'VALIDATION_FAILED', 'fields' => ['email' => 'required']]], 422);
    }

    public static function htmlError(): Response
    {
        return new Response('<html><body>PUBLIC HOST ERROR</body></html>', 404, ['Content-Type' => 'text/html']);
    }

    public static function failure(string $kind): Response
    {
        return match ($kind) {
            'validation' => throw new HttpException(422, 'GPS_PRIVATE_VALIDATION_SENTINEL'),
            'auth' => throw new HttpException(401, 'GPS_PRIVATE_AUTH_SENTINEL', null, ['WWW-Authenticate' => 'Bearer realm="fixture"']),
            'rate' => throw new HttpException(429, 'GPS_PRIVATE_RATE_SENTINEL', null, ['Retry-After' => '37']),
            'method' => throw new HttpException(405, 'GPS_PRIVATE_METHOD_SENTINEL', null, ['Allow' => 'GET, HEAD']),
            'invalid-mapper' => throw new HttpException(422, 'GPS_PRIVATE_INVALID_SENTINEL'),
            'broken-mapper' => throw new HttpException(422, 'GPS_PRIVATE_BROKEN_SENTINEL'),
            'nested' => throw new \RuntimeException('GPS_PRIVATE_OUTER_SENTINEL', 0, new \RuntimeException('GPS_PRIVATE_INNER_SENTINEL')),
            default => throw new \RuntimeException('GPS_PRIVATE_FATAL_SENTINEL'),
        };
    }
}

/** An explicitly trusted application mapper — not an inferred exception type. */
final class SafeNativeFixtureMapper implements PublicErrorMapperInterface
{
    public function supports(\Throwable $error, Request $request): bool
    {
        return str_starts_with($request->getPathInfo(), '/api/throw/')
            && in_array($request->getPathInfo(), ['/api/throw/validation', '/api/throw/invalid-mapper', '/api/throw/broken-mapper'], true)
            && $error instanceof HttpExceptionInterface
            && $error->getStatusCode() === 422;
    }

    public function map(\Throwable $error, Request $request): ?PublicErrorDescriptor
    {
        if ($request->getPathInfo() === '/api/throw/broken-mapper') {
            throw new \RuntimeException('GPS_PRIVATE_MAPPER_SENTINEL');
        }
        if ($request->getPathInfo() === '/api/throw/invalid-mapper') {
            return new PublicErrorDescriptor(500, 'UNSAFE_STATUS', 'Never publish');
        }
        return new PublicErrorDescriptor(422, 'API_DOMAIN_ERROR', 'Request rejected', [
            'X-Error-Code' => 'API_DOMAIN_ERROR',
        ]);
    }
}

final class SafeNativeFixtureKernel extends Symfony\Component\HttpKernel\Kernel
{
    public function __construct(private string $root)
    {
        parent::__construct('dev', true);
    }

    public function getProjectDir(): string { return $this->root; }
    public function getCacheDir(): string { return $this->root.'/cache'; }
    public function getLogDir(): string { return $this->root.'/logs'; }

    public function registerBundles(): iterable
    {
        return [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new OmegaGuardian\ProfilerShield\OmegaGuardianProfilerShieldBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('framework', [
                'secret' => 'disposable-test-secret',
                'router' => ['resource' => __DIR__.'/fixtures/safe_native_routes.php'],
                'session' => ['storage_factory_id' => 'session.storage.factory.mock_file'],
            ]);
            $container->register('fixture.safe_native_mapper', SafeNativeFixtureMapper::class)
                ->addTag('omega_guardian.public_error_mapper', ['priority' => 200]);
        });
    }
}

$root = sys_get_temp_dir().'/guardian-safe-native-'.bin2hex(random_bytes(8));
mkdir($root, 0700);
foreach ([
    'GUARDIAN_STORAGE_DIR' => $root.'/vault',
    'GUARDIAN_ENCRYPTION_KEY' => bin2hex(random_bytes(32)),
    'GUARDIAN_LOGIN_PATH' => '/internal/test-'.bin2hex(random_bytes(20)),
    'GUARDIAN_DEBUG_ERRORS_FOR_AUTHENTICATED' => '0',
    'GUARDIAN_SHOW_PHP_VERSION' => '0',
    'GUARDIAN_COLLECTION_BRIDGE' => '0',
] as $key => $value) {
    $_SERVER[$key] = $_ENV[$key] = $value;
    putenv($key.'='.$value);
}

$store = new FileStore($root.'/vault');
$store->initialize();
$settings = new Settings($store);
$kernel = new SafeNativeFixtureKernel($root);
$checks = 0;
$failures = [];
function safeNativeCheck(bool $result, string $description): void
{
    global $checks, $failures;
    ++$checks;
    if (!$result) {
        $failures[] = $description;
    }
}
function safeNativeRequest(string $path, string $accept = 'application/json', string $method = 'GET'): Response
{
    global $kernel;
    $request = Request::create('https://localhost'.$path, $method, [], [], [], [
        'REMOTE_ADDR' => '203.0.113.27', 'HTTPS' => 'on', 'HTTP_ACCEPT' => $accept,
    ]);
    $response = $kernel->handle($request);
    $kernel->terminate($request, $response);
    return $response;
}
function safeNativePrivate(Response $response, string $context): void
{
    $published = (string)$response->getContent().' '.json_encode($response->headers->all());
    safeNativeCheck(strpos($published, 'GPS_PRIVATE_') === false, $context.': exposed private sentinel');
}

try {
    $kernel->boot();
    safeNativeCheck(class_exists(PublicErrorRegistry::class), 'Registry class autoload');
    safeNativeCheck(class_exists(PublicErrorDescriptor::class), 'Descriptor class autoload');
    safeNativeCheck(interface_exists(PublicErrorMapperInterface::class), 'Mapper interface autoload');

    foreach (['native', 'generic', 'custom'] as $mode) {
        $settings->set(['error_mode' => $mode, 'error_custom_text' => 'Public safe message']);

        $r = safeNativeRequest('/api/health');
        safeNativeCheck($r->getStatusCode() === 200, $mode.' ordinary status');
        safeNativeCheck($r->getContent() === '{"ok":true}', $mode.' ordinary JSON untouched');
        safeNativeCheck($r->headers->get('X-Application') === 'safe-native-fixture', $mode.' ordinary application header untouched');

        $r = safeNativeRequest('/api/direct-error');
        safeNativeCheck($r->getStatusCode() === 422, $mode.' prebuilt error status');
        safeNativeCheck(str_contains($r->getContent(), 'VALIDATION_FAILED'), $mode.' prebuilt code untouched');

        $r = safeNativeRequest('/api/throw/validation');
        $json = json_decode($r->getContent(), true);
        safeNativeCheck($r->getStatusCode() === 422, $mode.' trusted error status');
        safeNativeCheck($json['error']['code'] ?? null === 'API_DOMAIN_ERROR', $mode.' trusted error code');
        safeNativeCheck(($json['error']['code'] ?? '') === 'API_DOMAIN_ERROR', $mode.' trusted code exact');
        safeNativeCheck(($json['error']['safe_message'] ?? '') === 'Request rejected', $mode.' trusted public text');
        safeNativeCheck($r->headers->get('X-Error-Code') === 'API_DOMAIN_ERROR', $mode.' approved custom header');
        safeNativePrivate($r, $mode.' trusted mapper');

        foreach (['broken-mapper', 'invalid-mapper'] as $kind) {
            $r = safeNativeRequest('/api/throw/'.$kind);
            safeNativeCheck($r->getStatusCode() === 422, $mode.' '.$kind.' status');
            $json = json_decode($r->getContent(), true);
            safeNativeCheck(($json['error']['code'] ?? '') === 'REQUEST_FAILED', $mode.' '.$kind.' fail closed');
            safeNativePrivate($r, $mode.' '.$kind);
        }
        foreach (['auth' => [401, 'WWW-Authenticate', 'Bearer'], 'rate' => [429, 'Retry-After', '37'], 'method' => [405, 'Allow', 'GET']] as $kind => [$status, $header, $value]) {
            $r = safeNativeRequest('/api/throw/'.$kind);
            safeNativeCheck($r->getStatusCode() === $status, $mode.' '.$kind.' HTTP status');
            safeNativeCheck(str_contains((string)$r->headers->get($header), $value), $mode.' '.$kind.' protocol header');
            safeNativePrivate($r, $mode.' '.$kind);
        }
        foreach ([
            'application/json' => 'application/json',
            'application/xml' => 'application/xml',
            'application/problem+json' => 'application/problem+json',
            'application/problem+xml' => 'application/problem+xml',
            'text/html;q=0.1, application/json;q=0.9' => 'application/json',
            'application/json;q=0, application/xml;q=1' => 'application/xml',
            'application/*+json' => 'application/json',
        ] as $accept => $mime) {
            $r = safeNativeRequest('/api/throw/nested', $accept);
            safeNativeCheck($r->getStatusCode() === 500, $mode.' '.$accept.' status');
            safeNativeCheck(str_starts_with((string)$r->headers->get('Content-Type'), $mime), $mode.' '.$accept.' MIME');
            if (str_contains($mime, 'xml')) {
                $dom = new \DOMDocument();
                safeNativeCheck(@$dom->loadXML($r->getContent(), LIBXML_NONET), $mode.' '.$accept.' valid XML');
            } else {
                safeNativeCheck(is_array(json_decode($r->getContent(), true)), $mode.' '.$accept.' valid JSON');
            }
            safeNativePrivate($r, $mode.' '.$accept);
        }
        $r = safeNativeRequest('/web/public-error', 'text/html');
        safeNativeCheck($r->getStatusCode() === 404, $mode.' HTML 404 status');
        if ($mode === 'native') {
            safeNativeCheck(str_contains($r->getContent(), 'PUBLIC HOST ERROR'), 'native HTML response kept intact');
        } else {
            safeNativeCheck(!str_contains($r->getContent(), 'PUBLIC HOST ERROR'), $mode.' obfuscates HTML');
        }
        $r = safeNativeRequest('/outside/throw/nested', 'text/html');
        safeNativeCheck($r->getStatusCode() === 500, $mode.' HTML exception status');
        safeNativePrivate($r, $mode.' HTML exception');
    }

    foreach ([
        fn () => new PublicErrorDescriptor(200, 'PUBLIC_ERROR', 'Impossible'),
        fn () => new PublicErrorDescriptor(422, 'PUBLIC_ERROR', 'Allowed', ['X-Debug-Token' => 'private']),
        fn () => new PublicErrorDescriptor(422, 'PUBLIC_ERROR', 'Allowed', ['Allow' => "GET\r\nX-Evil: yes"]),
    ] as $index => $factory) {
        $rejected = false;
        try { $factory(); } catch (\InvalidArgumentException) { $rejected = true; }
        safeNativeCheck($rejected, 'descriptor rejects invalid payload '.$index);
    }
} finally {
    $kernel->shutdown();
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) { $file->isDir() ? @rmdir($file->getPathname()) : @unlink($file->getPathname()); }
    @rmdir($root);
}
$unexpected = ob_get_clean();
if ($unexpected !== '') { fwrite(STDERR, 'Fixture output suppressed: '.strlen($unexpected).' bytes'.PHP_EOL); }
echo json_encode(['checks' => $checks, 'failures' => $failures], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
exit($failures ? 1 : 0);
