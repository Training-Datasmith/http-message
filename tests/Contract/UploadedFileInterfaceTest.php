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
        ReflectionTestSupport::assertClassDocMatchesNormativePattern($class, '/considered immutable/s');
        ReflectionTestSupport::assertDocMatchesNormativePattern(
            $class->getMethod('getStream'),
            '/moveTo\(\) method has been called previously, this method MUST raise/s'
        );
        ReflectionTestSupport::assertDocMatchesNormativePattern(
            $class->getMethod('moveTo'),
            '/subsequent calls MUST raise/s'
        );
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('getStream'), '\\RuntimeException');
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('moveTo'), '\\InvalidArgumentException');
        ReflectionTestSupport::assertThrowsTagDocuments($class->getMethod('moveTo'), '\\RuntimeException');
        ReflectionTestSupport::assertDocMatchesNormativePattern(
            $class->getMethod('getError'),
            '/MUST return' . ReflectionTestSupport::DOC_SEPARATOR . 'UPLOAD_ERR_OK/s'
        );
    }
}
