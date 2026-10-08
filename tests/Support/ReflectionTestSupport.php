<?php

declare(strict_types=1);

namespace Psr\Http\Message\Tests\Support;

use PHPUnit\Framework\Assert;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

final class ReflectionTestSupport
{
    /**
     * @param list<array{name: string, type?: ?string, class?: ?string, nullable?: bool, optional?: bool, default?: mixed, defaultConstant?: ?string}> $parameters
     */
    public static function assertMethodSignature(
        ReflectionMethod $method,
        array $parameters,
        ?string $returnTypeName,
        bool $returnAllowsNull = false
    ): void {
        Assert::assertTrue($method->isPublic());
        Assert::assertFalse($method->isStatic());
        Assert::assertTrue($method->isAbstract());

        Assert::assertCount(count($parameters), $method->getParameters());

        foreach ($parameters as $index => $expected) {
            $parameter = $method->getParameters()[$index];
            Assert::assertSame($expected['name'], $parameter->getName());
            Assert::assertFalse($parameter->isPassedByReference());
            Assert::assertFalse($parameter->isVariadic());

            $optional = $expected['optional'] ?? false;
            Assert::assertSame($optional, $parameter->isOptional());

            $type = $parameter->getType();
            if (!array_key_exists('type', $expected) && !array_key_exists('class', $expected)) {
                Assert::assertNull($type);
            } elseif (($expected['type'] ?? null) === null && ($expected['class'] ?? null) === null) {
                Assert::assertNull($type);
            } else {
                Assert::assertInstanceOf(ReflectionNamedType::class, $type);
                if (isset($expected['class'])) {
                    Assert::assertSame($expected['class'], $type->getName());
                } else {
                    Assert::assertSame($expected['type'], $type->getName());
                }
                $nullable = $expected['nullable'] ?? false;
                Assert::assertSame($nullable, $type->allowsNull());
            }

            if (array_key_exists('default', $expected)) {
                Assert::assertTrue($parameter->isDefaultValueAvailable());
                Assert::assertSame($expected['default'], $parameter->getDefaultValue());
            }

            if (array_key_exists('defaultConstant', $expected)) {
                Assert::assertTrue($parameter->isDefaultValueConstant());
                Assert::assertSame($expected['defaultConstant'], $parameter->getDefaultValueConstantName());
                Assert::assertSame(constant($expected['defaultConstant']), $parameter->getDefaultValue());
            }

            if (array_key_exists('defaultIsConstant', $expected) && $expected['defaultIsConstant']) {
                Assert::assertTrue($parameter->isDefaultValueConstant());
            }
        }

        $returnType = $method->getReturnType();
        if ($returnTypeName === null) {
            Assert::assertNull($returnType);
            return;
        }

        Assert::assertInstanceOf(ReflectionNamedType::class, $returnType);
        Assert::assertSame($returnTypeName, $returnType->getName());
        Assert::assertSame($returnAllowsNull, $returnType->allowsNull());
    }

    public static function assertMethodDeclaredOn(string $interfaceFqn, string $methodName, string $declaringInterfaceFqn): void
    {
        $reflection = new ReflectionClass($interfaceFqn);
        $method = $reflection->getMethod($methodName);
        Assert::assertSame($declaringInterfaceFqn, $method->getDeclaringClass()->getName());
    }

    public static function assertDocContainsNormativeOrThrows(ReflectionMethod $method, string $needle): void
    {
        $doc = $method->getDocComment();
        Assert::assertNotFalse($doc);
        Assert::assertStringContainsString($needle, $doc);
    }

    public static function assertClassDocContainsNormativeOrThrows(ReflectionClass $class, string $needle): void
    {
        $doc = $class->getDocComment();
        Assert::assertNotFalse($doc);
        Assert::assertStringContainsString($needle, $doc);
    }

    public static function assertThrowsTagDocuments(ReflectionMethod $method, string $exceptionClass): void
    {
        $doc = $method->getDocComment();
        Assert::assertNotFalse($doc);
        Assert::assertStringContainsString('@throws ' . $exceptionClass, $doc);
    }
}
