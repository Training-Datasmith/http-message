<?php

declare(strict_types=1);

namespace Psr\Http\Message\Tests\Contract;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\Tests\Support\ReflectionTestSupport;
use Psr\Http\Message\UriInterface;
use ReflectionClass;

final class UriInterfaceTest extends TestCase
{
    private const FQN = UriInterface::class;

    public function testHasNoParentInterface(): void
    {
        $parents = (new ReflectionClass(self::FQN))->getInterfaceNames();
        sort($parents);
        $expected = PHP_VERSION_ID >= 80000 ? ['Stringable'] : [];
        self::assertSame($expected, $parents);
    }

    public function testMethodSignatures(): void
    {
        $class = new ReflectionClass(self::FQN);
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getScheme'), [], 'string');
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getAuthority'), [], 'string');
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getUserInfo'), [], 'string');
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getHost'), [], 'string');
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getPort'), [], 'int', true);
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getPath'), [], 'string');
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getQuery'), [], 'string');
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getFragment'), [], 'string');
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('withScheme'),
            [['name' => 'scheme', 'type' => 'string']],
            UriInterface::class
        );
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('withUserInfo'),
            [
                ['name' => 'user', 'type' => 'string'],
                ['name' => 'password', 'type' => 'string', 'nullable' => true, 'optional' => true, 'default' => null],
            ],
            UriInterface::class
        );
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('withHost'),
            [['name' => 'host', 'type' => 'string']],
            UriInterface::class
        );

        $withPort = $class->getMethod('withPort');
        ReflectionTestSupport::assertMethodSignature(
            $withPort,
            [['name' => 'port', 'type' => 'int', 'nullable' => true, 'optional' => false]],
            UriInterface::class
        );

        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('withPath'),
            [['name' => 'path', 'type' => 'string']],
            UriInterface::class
        );
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('withQuery'),
            [['name' => 'query', 'type' => 'string']],
            UriInterface::class
        );
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('withFragment'),
            [['name' => 'fragment', 'type' => 'string']],
            UriInterface::class
        );
        ReflectionTestSupport::assertMethodSignature($class->getMethod('__toString'), [], 'string');
    }

    public function testNormativeDocblocksAndThrows(): void
    {
        $class = new ReflectionClass(self::FQN);
        ReflectionTestSupport::assertClassDocContainsNormativeOrThrows($class, 'immutable');
        ReflectionTestSupport::assertDocContainsNormativeOrThrows(
            $class->getMethod('getScheme'),
            'MUST be normalized to lowercase'
        );
        ReflectionTestSupport::assertDocContainsNormativeOrThrows(
            $class->getMethod('getAuthority'),
            'SHOULD NOT be included'
        );
        ReflectionTestSupport::assertDocContainsNormativeOrThrows(
            $class->getMethod('getHost'),
            'MUST be normalized to lowercase'
        );
        ReflectionTestSupport::assertDocContainsNormativeOrThrows(
            $class->getMethod('getPort'),
            'SHOULD return null'
        );
        ReflectionTestSupport::assertDocContainsNormativeOrThrows(
            $class->getMethod('getPath'),
            'MUST NOT automatically'
        );
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('withScheme'), '\\InvalidArgumentException');
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('withHost'), '\\InvalidArgumentException');
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('withPort'), '\\InvalidArgumentException');
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('withPath'), '\\InvalidArgumentException');
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('withQuery'), '\\InvalidArgumentException');
    }
}
