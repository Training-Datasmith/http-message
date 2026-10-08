<?php

declare(strict_types=1);

namespace Psr\Http\Message\Tests\Contract;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\Tests\Support\ReflectionTestSupport;
use ReflectionClass;

final class ResponseInterfaceTest extends TestCase
{
    private const FQN = ResponseInterface::class;

    public function testExtendsMessageInterface(): void
    {
        $reflection = new ReflectionClass(self::FQN);
        self::assertSame([MessageInterface::class], $reflection->getInterfaceNames());
    }

    public function testDeclaresExpectedOwnMethods(): void
    {
        $expected = ['getStatusCode', 'withStatus', 'getReasonPhrase'];
        foreach ($expected as $name) {
            ReflectionTestSupport::assertMethodDeclaredOn(self::FQN, $name, self::FQN);
        }
    }

    public function testMethodSignatures(): void
    {
        $class = new ReflectionClass(self::FQN);
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getStatusCode'), [], 'int');
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('withStatus'),
            [
                ['name' => 'code', 'type' => 'int'],
                ['name' => 'reasonPhrase', 'type' => 'string', 'optional' => true, 'default' => ''],
            ],
            ResponseInterface::class
        );
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getReasonPhrase'), [], 'string');
    }

    public function testNormativeDocblocksAndThrows(): void
    {
        $class = new ReflectionClass(self::FQN);
        ReflectionTestSupport::assertDocMatchesNormativePattern(
            $class->getMethod('getReasonPhrase'),
            '/must return an empty string/si'
        );
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('withStatus'), '\\InvalidArgumentException');
    }
}
