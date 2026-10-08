<?php

declare(strict_types=1);

namespace Psr\Http\Message\Tests\Contract;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\Tests\Support\ReflectionTestSupport;
use Psr\Http\Message\UploadedFileInterface;
use ReflectionClass;

final class UploadedFileInterfaceTest extends TestCase
{
    private const FQN = UploadedFileInterface::class;

    public function testHasNoParentInterface(): void
    {
        self::assertSame([], (new ReflectionClass(self::FQN))->getInterfaceNames());
    }

    public function testMethodSignatures(): void
    {
        $class = new ReflectionClass(self::FQN);
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getStream'), [], StreamInterface::class);
        ReflectionTestSupport::assertMethodSignature(
            $class->getMethod('moveTo'),
            [['name' => 'targetPath', 'type' => 'string']],
            'void'
        );
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getSize'), [], 'int', true);
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getError'), [], 'int');
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getClientFilename'), [], 'string', true);
        ReflectionTestSupport::assertMethodSignature($class->getMethod('getClientMediaType'), [], 'string', true);
    }

    public function testNormativeDocblocksAndThrows(): void
    {
        $class = new ReflectionClass(self::FQN);
        ReflectionTestSupport::assertClassDocContainsNormativeOrThrows($class, 'immutable');
        ReflectionTestSupport::assertDocContainsNormativeOrThrows(
            $class->getMethod('getStream'),
            'moveTo() method has been called previously'
        );
        ReflectionTestSupport::assertDocContainsNormativeOrThrows(
            $class->getMethod('moveTo'),
            'subsequent calls MUST raise'
        );
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('getStream'), '\\RuntimeException');
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('moveTo'), '\\InvalidArgumentException');
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('moveTo'), '\\RuntimeException');
        $getErrorDoc = $class->getMethod('getError')->getDocComment();
        self::assertNotFalse($getErrorDoc);
        self::assertStringContainsString('MUST return', $getErrorDoc);
        self::assertStringContainsString('UPLOAD_ERR_OK', $getErrorDoc);
    }
}
