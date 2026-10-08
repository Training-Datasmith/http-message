<?php

declare(strict_types=1);

namespace Psr\Http\Message\Tests\Contract;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\Tests\Support\ReflectionTestSupport;
use ReflectionClass;

final class ServerRequestInterfaceTest extends TestCase
{
    private const FQN = ServerRequestInterface::class;

    public function testExtendsRequestInterface(): void
    {
        $reflection = new ReflectionClass(self::FQN);
        $parents = $reflection->getInterfaceNames();
        sort($parents);
        self::assertSame([MessageInterface::class, RequestInterface::class], $parents);
    }

    public function testDeclaresExpectedOwnMethods(): void
    {
        $expected = [
            'getServerParams',
            'getCookieParams',
            'withCookieParams',
            'getQueryParams',
            'withQueryParams',
            'getUploadedFiles',
            'withUploadedFiles',
            'getParsedBody',
            'withParsedBody',
            'getAttributes',
            'getAttribute',
            'withAttribute',
            'withoutAttribute',
        ];
        foreach ($expected as $name) {
            ReflectionTestSupport::assertMethodDeclaredOn(self::FQN, $name, self::FQN);
        }
    }

    public function testMethodSignatures(): void
    {
        $class = new ReflectionClass(self::FQN);
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getServerParams'), [], 'array');
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getCookieParams'), [], 'array');
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('withCookieParams'),
            [['name' => 'cookies', 'type' => 'array']],
            ServerRequestInterface::class
        );
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getQueryParams'), [], 'array');
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('withQueryParams'),
            [['name' => 'query', 'type' => 'array']],
            ServerRequestInterface::class
        );
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getUploadedFiles'), [], 'array');
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('withUploadedFiles'),
            [['name' => 'uploadedFiles', 'type' => 'array']],
            ServerRequestInterface::class
        );
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getParsedBody'), [], null);
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('withParsedBody'),
            [['name' => 'data']],
            ServerRequestInterface::class
        );
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getAttributes'), [], 'array');
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('getAttribute'),
            [
                ['name' => 'name', 'type' => 'string'],
                ['name' => 'default', 'optional' => true, 'default' => null],
            ],
            null
        );
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('withAttribute'),
            [['name' => 'name', 'type' => 'string'], ['name' => 'value']],
            ServerRequestInterface::class
        );
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('withoutAttribute'),
            [['name' => 'name', 'type' => 'string']],
            ServerRequestInterface::class
        );
    }

    public function testNormativeDocblocksAndThrows(): void
    {
        $class = new ReflectionClass(self::FQN);
        ReflectionTestSupport::assertDocMatchesNormativePattern(
            $class->getMethod('withCookieParams'),
            '/MUST NOT update the related Cookie header/s'
        );
        ReflectionTestSupport::assertDocMatchesNormativePattern(
            $class->getMethod('withQueryParams'),
            '/MUST NOT change the URI/s'
        );
        ReflectionTestSupport::assertDocMatchesNormativePattern(
            $class->getMethod('getUploadedFiles'),
            '/empty' . ReflectionTestSupport::DOC_SEPARATOR . 'array MUST be returned if no data is present/s'
        );
        ReflectionTestSupport::assertThrowsTagDocuments(
            $class->getMethod('withUploadedFiles'),
            '\\InvalidArgumentException'
        );
        ReflectionTestSupport::assertThrowsTagDocuments(
            $class->getMethod('withParsedBody'),
            '\\InvalidArgumentException'
        );
    }
}
