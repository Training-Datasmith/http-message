<?php

declare(strict_types=1);

namespace Psr\Http\Message\Tests\Contract;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\Tests\Fixtures\MinimalMessage;
use Psr\Http\Message\Tests\Fixtures\MinimalRequest;
use Psr\Http\Message\Tests\Fixtures\MinimalResponse;
use Psr\Http\Message\Tests\Fixtures\MinimalServerRequest;
use Psr\Http\Message\Tests\Fixtures\MinimalStream;
use Psr\Http\Message\Tests\Fixtures\MinimalUploadedFile;
use Psr\Http\Message\Tests\Fixtures\MinimalUri;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

final class ImplementabilityTest extends TestCase
{
    public function testMinimalMessageIsInvokableThroughInterface(): void
    {
        $message = new MinimalMessage();
        self::assertInstanceOf(MessageInterface::class, $message);
        /** @var MessageInterface $iface */
        $iface = $message;

        self::assertIsString($iface->getProtocolVersion());
        self::assertInstanceOf(MessageInterface::class, $iface->withProtocolVersion('1.0'));
        self::assertIsArray($iface->getHeaders());
        self::assertIsBool($iface->hasHeader('X'));
        self::assertIsArray($iface->getHeader('X'));
        self::assertIsString($iface->getHeaderLine('X'));
        self::assertInstanceOf(MessageInterface::class, $iface->withHeader('X', 'a'));
        self::assertInstanceOf(MessageInterface::class, $iface->withHeader('X', ['a']));
        self::assertInstanceOf(MessageInterface::class, $iface->withAddedHeader('X', 'b'));
        self::assertInstanceOf(MessageInterface::class, $iface->withAddedHeader('X', ['b']));
        self::assertInstanceOf(MessageInterface::class, $iface->withoutHeader('X'));
        self::assertInstanceOf(StreamInterface::class, $iface->getBody());
        self::assertInstanceOf(MessageInterface::class, $iface->withBody(new MinimalStream()));
    }

    public function testMinimalRequestIsInvokableThroughInterface(): void
    {
        $request = new MinimalRequest();
        self::assertInstanceOf(RequestInterface::class, $request);
        /** @var RequestInterface $iface */
        $iface = $request;
        $uri = new MinimalUri();

        self::assertIsString($iface->getRequestTarget());
        self::assertInstanceOf(RequestInterface::class, $iface->withRequestTarget('/x'));
        self::assertIsString($iface->getMethod());
        self::assertInstanceOf(RequestInterface::class, $iface->withMethod('POST'));
        self::assertInstanceOf(UriInterface::class, $iface->getUri());
        self::assertInstanceOf(RequestInterface::class, $iface->withUri($uri));
        self::assertInstanceOf(RequestInterface::class, $iface->withUri($uri, true));
    }

    public function testMinimalResponseIsInvokableThroughInterface(): void
    {
        $response = new MinimalResponse();
        self::assertInstanceOf(ResponseInterface::class, $response);
        /** @var ResponseInterface $iface */
        $iface = $response;

        self::assertIsInt($iface->getStatusCode());
        self::assertInstanceOf(ResponseInterface::class, $iface->withStatus(404));
        self::assertInstanceOf(ResponseInterface::class, $iface->withStatus(404, 'Not Found'));
        self::assertIsString($iface->getReasonPhrase());
    }

    public function testMinimalServerRequestIsInvokableThroughInterface(): void
    {
        $request = new MinimalServerRequest();
        self::assertInstanceOf(ServerRequestInterface::class, $request);
        /** @var ServerRequestInterface $iface */
        $iface = $request;

        self::assertIsArray($iface->getServerParams());
        self::assertIsArray($iface->getCookieParams());
        self::assertInstanceOf(ServerRequestInterface::class, $iface->withCookieParams(['a' => 'b']));
        self::assertIsArray($iface->getQueryParams());
        self::assertInstanceOf(ServerRequestInterface::class, $iface->withQueryParams(['q' => '1']));
        self::assertIsArray($iface->getUploadedFiles());
        self::assertInstanceOf(
            ServerRequestInterface::class,
            $iface->withUploadedFiles(['f' => new MinimalUploadedFile()])
        );
        $iface->getParsedBody();
        self::assertInstanceOf(ServerRequestInterface::class, $iface->withParsedBody(null));
        self::assertInstanceOf(ServerRequestInterface::class, $iface->withParsedBody(['k' => 'v']));
        self::assertInstanceOf(ServerRequestInterface::class, $iface->withParsedBody((object) ['k' => 'v']));
        self::assertIsArray($iface->getAttributes());
        $iface->getAttribute('n');
        $iface->getAttribute('n', 1);
        $iface->getAttribute('n', 'x');
        self::assertInstanceOf(ServerRequestInterface::class, $iface->withAttribute('n', 'x'));
        self::assertInstanceOf(ServerRequestInterface::class, $iface->withAttribute('n', 1));
        self::assertInstanceOf(ServerRequestInterface::class, $iface->withAttribute('n', null));
        self::assertInstanceOf(ServerRequestInterface::class, $iface->withoutAttribute('n'));
    }

    public function testMinimalStreamIsInvokableThroughInterface(): void
    {
        $stream = new MinimalStream();
        self::assertInstanceOf(StreamInterface::class, $stream);
        /** @var StreamInterface $iface */
        $iface = $stream;

        self::assertIsString($iface->__toString());
        $iface->close();
        $iface->detach();
        self::assertTrue($iface->getSize() === null || is_int($iface->getSize()));
        self::assertIsInt($iface->tell());
        self::assertIsBool($iface->eof());
        self::assertIsBool($iface->isSeekable());
        $iface->seek(0);
        $iface->seek(0, SEEK_CUR);
        $iface->seek(0, SEEK_END);
        $iface->rewind();
        self::assertIsBool($iface->isWritable());
        self::assertIsInt($iface->write('x'));
        self::assertIsBool($iface->isReadable());
        self::assertIsString($iface->read(1));
        self::assertIsString($iface->getContents());
        $iface->getMetadata();
        $iface->getMetadata(null);
        $iface->getMetadata('uri');
    }

    public function testMinimalUriIsInvokableThroughInterface(): void
    {
        $uri = new MinimalUri();
        self::assertInstanceOf(UriInterface::class, $uri);
        /** @var UriInterface $iface */
        $iface = $uri;

        self::assertIsString($iface->getScheme());
        self::assertIsString($iface->getAuthority());
        self::assertIsString($iface->getUserInfo());
        self::assertIsString($iface->getHost());
        self::assertTrue($iface->getPort() === null || is_int($iface->getPort()));
        self::assertIsString($iface->getPath());
        self::assertIsString($iface->getQuery());
        self::assertIsString($iface->getFragment());
        self::assertInstanceOf(UriInterface::class, $iface->withScheme('https'));
        self::assertInstanceOf(UriInterface::class, $iface->withUserInfo('u'));
        self::assertInstanceOf(UriInterface::class, $iface->withUserInfo('u', 'p'));
        self::assertInstanceOf(UriInterface::class, $iface->withUserInfo('u', null));
        self::assertInstanceOf(UriInterface::class, $iface->withHost('example.com'));
        self::assertInstanceOf(UriInterface::class, $iface->withPort(443));
        self::assertInstanceOf(UriInterface::class, $iface->withPort(null));
        self::assertInstanceOf(UriInterface::class, $iface->withPath('/p'));
        self::assertInstanceOf(UriInterface::class, $iface->withQuery('a=1'));
        self::assertInstanceOf(UriInterface::class, $iface->withFragment('frag'));
        self::assertIsString($iface->__toString());
    }

    public function testMinimalUploadedFileIsInvokableThroughInterface(): void
    {
        $file = new MinimalUploadedFile();
        self::assertInstanceOf(UploadedFileInterface::class, $file);
        /** @var UploadedFileInterface $iface */
        $iface = $file;

        self::assertInstanceOf(StreamInterface::class, $iface->getStream());
        $iface->moveTo('target.bin');
        self::assertTrue($iface->getSize() === null || is_int($iface->getSize()));
        self::assertIsInt($iface->getError());
        self::assertTrue($iface->getClientFilename() === null || is_string($iface->getClientFilename()));
        self::assertTrue($iface->getClientMediaType() === null || is_string($iface->getClientMediaType()));
    }
}
