<?php

declare(strict_types=1);

namespace Psr\Http\Message\Tests\Contract;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\Tests\Support\ReflectionTestSupport;
use Psr\Http\Message\UriInterface;
use ReflectionClass;

final class RequestInterfaceTest extends TestCase
{
    private const FQN = RequestInterface::class;

    public function testExtendsMessageInterface(): void
    {
        $reflection = new ReflectionClass(self::FQN);
        self::assertSame([MessageInterface::class], $reflection->getInterfaceNames());
    }

    public function testDeclaresExpectedOwnMethods(): void
    {
        $expected = [
            'getRequestTarget',
            'withRequestTarget',
            'getMethod',
            'withMethod',
            'getUri',
            'withUri',
        ];
        $reflection = new ReflectionClass(self::FQN);
        $own = [];
        foreach ($expected as $name) {
            $own[] = $name;
            ReflectionTestSupport::assertMethodDeclaredOn(self::FQN, $name, self::FQN);
        }
        self::assertSame($expected, $own);
    }

    public function testInheritedMethodsDeclaredOnMessageInterface(): void
    {
        foreach (['getProtocolVersion', 'withHeader', 'getBody', 'withBody'] as $name) {
            ReflectionTestSupport::assertMethodDeclaredOn(self::FQN, $name, MessageInterface::class);
        }
    }

    public function testMethodSignatures(): void
    {
        $class = new ReflectionClass(self::FQN);
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getRequestTarget'), [], 'string');
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('withRequestTarget'),
            [['name' => 'requestTarget', 'type' => 'string']],
            RequestInterface::class
        );
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getMethod'), [], 'string');
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('withMethod'),
            [['name' => 'method', 'type' => 'string']],
            RequestInterface::class
        );
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getUri'), [], UriInterface::class);
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('withUri'),
            [
                ['name' => 'uri', 'class' => UriInterface::class],
                ['name' => 'preserveHost', 'type' => 'bool', 'optional' => true, 'default' => false],
            ],
            RequestInterface::class
        );
    }

    public function testNormativeDocblocksAndThrows(): void
    {
        $class = new ReflectionClass(self::FQN);
        ReflectionTestSupport::assertClassDocContainsNormativeOrThrows(
            $class,
            'MUST attempt to set the Host header'
        );
        ReflectionTestSupport::assertDocContainsNormativeOrThrows(
            $class->getMethod('getRequestTarget'),
            'MUST return the string "/"'
        );
        $withMethodDoc = $class->getMethod('withMethod')->getDocComment();
        self::assertNotFalse($withMethodDoc);
        self::assertStringContainsString('SHOULD NOT', $withMethodDoc);
        self::assertStringContainsString('modify the given string', $withMethodDoc);
        ReflectionTestSupport::assertDocContainsNormativeOrThrows(
            $class->getMethod('withUri'),
            'MUST update the Host header'
        );
        ReflectionTestSupport::assertDocContainsNormativeOrThrows(
            $class->getMethod('withUri'),
            'MUST NOT update the Host header'
        );
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('withMethod'), '\\InvalidArgumentException');
    }
}
