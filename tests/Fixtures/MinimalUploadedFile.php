<?php

declare(strict_types=1);

namespace Psr\Http\Message\Tests\Fixtures;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

final class MinimalUploadedFile implements UploadedFileInterface
{
    public function getStream(): StreamInterface
    {
        return new MinimalStream();
    }

    public function moveTo(string $targetPath): void
    {
    }

    public function getSize(): ?int
    {
        return null;
    }

    public function getError(): int
    {
        return UPLOAD_ERR_OK;
    }

    public function getClientFilename(): ?string
    {
        return null;
    }

    public function getClientMediaType(): ?string
    {
        return null;
    }
}
