<?php

declare(strict_types=1);

namespace Psr\Http\Message\Tests\Contract;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\Tests\Support\ReflectionTestSupport;
use ReflectionClass;

final class MessageInterfaceTest extends TestCase
{
    private const FQN = MessageInterface::class;

    public function testHasNoParentInterface(): void
    {
        $reflection = new ReflectionClass(self::FQN);
        self::assertSame([], $reflection->getInterfaceNames());
    }

    public function testDeclaresExpectedMethods(): void
    {
        $expected = [
            'getProtocolVersion',
            'withProtocolVersion',
            'getHeaders',
            'hasHeader',
            'getHeader',
            'getHeaderLine',
            'withHeader',
            'withAddedHeader',
            'withoutHeader',
            'getBody',
            'withBody',
        ];
        $actual = array_map(
            static function ($method) {
                return $method->getName();
            },
            (new ReflectionClass(self::FQN))->getMethods()
        );
        sort($expected);
        sort($actual);
        self::assertSame($expected, $actual);
    }

    public function testMethodSignatures(): void
    {
        $class = new ReflectionClass(self::FQN);
        $cases = [
            'getProtocolVersion' => [[], 'string'],
            'withProtocolVersion' => [[['name' => 'version', 'type' => 'string']], MessageInterface::class],
            'getHeaders' => [[], 'array'],
            'hasHeader' => [[['name' => 'name', 'type' => 'string']], 'bool'],
            'getHeader' => [[['name' => 'name', 'type' => 'string']], 'array'],
            'getHeaderLine' => [[['name' => 'name', 'type' => 'string']], 'string'],
            'withHeader' => [[['name' => 'name', 'type' => 'string'], ['name' => 'value']], MessageInterface::class],
            'withAddedHeader' => [[['name' => 'name', 'type' => 'string'], ['name' => 'value']], MessageInterface::class],
            'withoutHeader' => [[['name' => 'name', 'type' => 'string']], MessageInterface::class],
            'getBody' => [[], StreamInterface::class],
            'withBody' => [[['name' => 'body', 'class' => StreamInterface::class]], MessageInterface::class],
        ];

        foreach ($cases as $methodName => [$params, $return]) {
            ReflectionTestSupport::assertMethodSignature(
                $class->getMethod($methodName),
                $params,
                $return
            );
        }
    }

    public function testNormativeDocblocksAndThrows(): void
    {
        $class = new ReflectionClass(self::FQN);
        ReflectionTestSupport::assertClassDocMatchesNormativePattern($class, '/Messages are considered immutable/s');

        ReflectionTestSupport::assertDocMatchesNormativePattern(
            $class->getMethod('getProtocolVersion'),
            '/MUST contain only the HTTP version number/s'
        );
        ReflectionTestSupport::assertDocMatchesNormativePattern(
            $class->getMethod('getHeader'),
            '/MUST' . ReflectionTestSupport::DOC_SEPARATOR . 'return' . ReflectionTestSupport::DOC_SEPARATOR . 'an' . ReflectionTestSupport::DOC_SEPARATOR . 'empty' . ReflectionTestSupport::DOC_SEPARATOR . 'array/s'
        );
        ReflectionTestSupport::assertDocMatchesNormativePattern(
            $class->getMethod('getHeaderLine'),
            '/MUST' . ReflectionTestSupport::DOC_SEPARATOR . 'return' . ReflectionTestSupport::DOC_SEPARATOR . 'an' . ReflectionTestSupport::DOC_SEPARATOR . 'empty' . ReflectionTestSupport::DOC_SEPARATOR . 'string/s'
        );
        ReflectionTestSupport::assertDocMatchesNormativePattern(
            $class->getMethod('withoutHeader'),
            '/MUST be done without case-sensitivity/s'
        );

        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('withHeader'), '\\InvalidArgumentException');
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('withAddedHeader'), '\\InvalidArgumentException');
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('withBody'), '\\InvalidArgumentException');
    }
}
