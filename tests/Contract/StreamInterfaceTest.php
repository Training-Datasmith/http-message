<?php

declare(strict_types=1);

namespace Psr\Http\Message\Tests\Contract;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\Tests\Support\ReflectionTestSupport;
use ReflectionClass;

final class StreamInterfaceTest extends TestCase
{
    private const FQN = StreamInterface::class;

    public function testHasNoParentInterface(): void
    {
        $parents = (new ReflectionClass(self::FQN))->getInterfaceNames();
        sort($parents);
        $expected = PHP_VERSION_ID >= 80000 ? ['Stringable'] : [];
        self::assertSame($expected, $parents);
    }

    public function testDeclaresExpectedMethods(): void
    {
        $expected = [
            '__toString',
            'close',
            'detach',
            'getSize',
            'tell',
            'eof',
            'isSeekable',
            'seek',
            'rewind',
            'isWritable',
            'write',
            'isReadable',
            'read',
            'getContents',
            'getMetadata',
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
        ReflectionTestSupport::assertMethodSignature($class->getMethod('__toString'), [], 'string');
        ReflectionTestSupport::assertMethodSignature($class->getMethod('close'), [], 'void');
        ReflectionTestSupport::assertMethodSignature($class->getMethod('detach'), [], null);
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getSize'), [], 'int', true);
        ReflectionTestSupport::assertMethodSignature($class->getMethod('tell'), [], 'int');
        ReflectionTestSupport::assertMethodSignature($class->getMethod('eof'), [], 'bool');
        ReflectionTestSupport::assertMethodSignature($class->getMethod('isSeekable'), [], 'bool');
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('seek'),
            [
                ['name' => 'offset', 'type' => 'int'],
                ['name' => 'whence', 'type' => 'int', 'optional' => true, 'default' => SEEK_SET],
            ],
            'void'
        );
        ReflectionTestSupport::assertMethodSignature($class->getMethod('rewind'), [], 'void');
        ReflectionTestSupport::assertMethodSignature($class->getMethod('isWritable'), [], 'bool');
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('write'),
            [['name' => 'string', 'type' => 'string']],
            'int'
        );
        ReflectionTestSupport::assertMethodSignature($class->getMethod('isReadable'), [], 'bool');
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('read'),
            [['name' => 'length', 'type' => 'int']],
            'string'
        );
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getContents'), [], 'string');
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('getMetadata'),
            [['name' => 'key', 'type' => 'string', 'nullable' => true, 'optional' => true, 'default' => null]],
            null
        );
    }

    public function testNormativeDocblocksAndThrows(): void
    {
        $class = new ReflectionClass(self::FQN);
        ReflectionTestSupport::assertDocContainsNormativeOrThrows(
            $class->getMethod('__toString'),
            'MUST NOT raise an exception'
        );
        ReflectionTestSupport::assertDocContainsNormativeOrThrows(
            $class->getMethod('__toString'),
            'MUST attempt to seek to the beginning'
        );
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('tell'), '\\RuntimeException');
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('seek'), '\\RuntimeException');
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('rewind'), '\\RuntimeException');
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('write'), '\\RuntimeException');
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('read'), '\\RuntimeException');
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('getContents'), '\\RuntimeException');
    }
}
