<?php

declare(strict_types=1);

namespace Psr\Http\Message\Tests\Contract;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class PackageSurfaceTest extends TestCase
{
    private const EXPECTED_INTERFACES = [
        'MessageInterface.php' => 'MessageInterface',
        'RequestInterface.php' => 'RequestInterface',
        'ResponseInterface.php' => 'ResponseInterface',
        'ServerRequestInterface.php' => 'ServerRequestInterface',
        'StreamInterface.php' => 'StreamInterface',
        'UriInterface.php' => 'UriInterface',
        'UploadedFileInterface.php' => 'UploadedFileInterface',
    ];

    public function testSrcContainsOnlyTheSevenInterfaces(): void
    {
        $srcDir = dirname(__DIR__, 2) . '/src';
        $files = glob($srcDir . '/*.php');
        self::assertNotFalse($files);
        sort($files);

        $expectedPaths = [];
        foreach (array_keys(self::EXPECTED_INTERFACES) as $basename) {
            $expectedPaths[] = $srcDir . '/' . $basename;
        }
        sort($expectedPaths);

        self::assertSame($expectedPaths, $files);

        foreach (self::EXPECTED_INTERFACES as $basename => $shortName) {
            $code = file_get_contents($srcDir . '/' . $basename);
            self::assertNotFalse($code);
            self::assertStringContainsString('namespace Psr\Http\Message;', $code);
            self::assertStringContainsString('interface ' . $shortName, $code);
            self::assertNotRegExp('/\b(class|trait)\s+' . $shortName . '\b/', $code);
        }
    }

    public function testAutoloadMapsTheNamespaceToSrc(): void
    {
        $composer = json_decode((string) file_get_contents(dirname(__DIR__, 2) . '/composer.json'), true);
        self::assertIsArray($composer);
        self::assertSame('psr/http-message', $composer['name']);
        self::assertSame(
            ['Psr\\Http\\Message\\' => 'src/'],
            $composer['autoload']['psr-4']
        );
    }

    public function testPhpConstraintIsThePublishedFloor(): void
    {
        $composer = json_decode((string) file_get_contents(dirname(__DIR__, 2) . '/composer.json'), true);
        self::assertIsArray($composer);
        self::assertSame('^7.2 || ^8.0', $composer['require']['php']);

        foreach (self::EXPECTED_INTERFACES as $basename => $shortName) {
            $fqn = 'Psr\\Http\\Message\\' . $shortName;
            self::assertTrue(interface_exists($fqn), $fqn . ' must be loadable on this PHP runtime');
        }
    }

    public function testInterfacesAreAbstractInterfaces(): void
    {
        foreach (self::EXPECTED_INTERFACES as $shortName) {
            $reflection = new ReflectionClass('Psr\\Http\\Message\\' . $shortName);
            self::assertTrue($reflection->isInterface());
        }
    }
}
