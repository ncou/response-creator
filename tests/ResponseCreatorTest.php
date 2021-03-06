<?php

declare(strict_types=1);

namespace Chiron\ResponseCreator\Tests;

use Nyholm\Psr7\Factory\Psr17Factory;
use Chiron\ResponseCreator\ResponseCreator;
use Chiron\Http\Psr\Response;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

//https://github.com/slimphp/Slim-Http/blob/9ce77b2e6f5183bc5464d8fb8c0795aa3e5d070a/tests/ResponseTest.php#L381
//https://github.com/slimphp/Slim-Http/blob/9ce77b2e6f5183bc5464d8fb8c0795aa3e5d070a/src/Response.php#L264

class ResponseCreatorTest extends TestCase
{
    /** @var ResponseCreator */
    private $responseCreator;

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        $this->responseCreator = new ResponseCreator(
            new Psr17Factory(),
            new Psr17Factory()
        );
    }

    // TODO : faire un test avec un objet URI.
    // TODO : faire un test avec l'exception qui se léve !!!! (cad en passant un interger en paramétre par exemple)
    public function testRedirect(): void
    {
        $response = $this->responseCreator->redirect('/foo', 301);
        $responseWithDefaultStatus = $this->responseCreator->redirect('/foo');

        $this->assertSame(301, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertEquals('/foo', $response->getHeaderLine('Location'));

        $this->assertSame(302, $responseWithDefaultStatus->getStatusCode());
        $this->assertTrue($responseWithDefaultStatus->hasHeader('Location'));
        $this->assertEquals('/foo', $responseWithDefaultStatus->getHeaderLine('Location'));
    }

    // TODO : tester avec un objet JsonSerializable.
    // TODO : tester avec un tableau qui contient une key = 'status' pour changer le statuscode de la response.
    // TODO : tester en passant un contentType différent. ex : application/vnd.api+json
    // TODO : tester avec des caractéeres unicode et des slash dans le texte. Ils ne doivent pas être encodés !!!
    public function testJson(): void
    {
        $data = ['foo' => 'bar1&bar2'];

        $response = $this->responseCreator->json($data, 201);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));

        $body = $response->getBody();
        $body->rewind();
        $dataJson = $body->getContents();

        $this->assertEquals('{"foo":"bar1&bar2"}', $dataJson);
        $this->assertEquals($data['foo'], json_decode($dataJson, true)['foo']);

        $response = $this->responseCreator->json([]);
        $this->assertEquals($response->getStatusCode(), 200);
    }

    public function testInvalidJsonThrowsException(): void
    {
        $this->expectException(RuntimeException::class);

        $data = ['foo' => 'bar' . chr(233)];
        $this->assertEquals('bar' . chr(233), $data['foo']);

        $this->responseCreator->json($data, 200);
    }

    public function fileProvider(): array
    {
        return [
            'with resource and content type specified' => [
                'text/plain',
                'resource',
                'Hello World',
                'text/plain',
            ],
            'with resource and content type auto-detection on' => [
                true,
                'resource',
                'Hello World',
                'application/octet-stream',
            ],
            'with resource and content type auto-detection off' => [
                false,
                'resource',
                'Hello World',
                '',
            ],
            'with string and content type specified' => [
                'text/plain',
                'string',
                'Hello World',
                'text/plain',
            ],
            'with string and content type auto-detection on' => [
                true,
                'string',
                'Hello World',
                'text/plain',
            ],
            'with string and content type auto-detection off' => [
                false,
                'string',
                'Hello World',
                '',
            ],
            'with stream and content type specified' => [
                'text/plain',
                'stream',
                'Hello World',
                'text/plain',
            ],
            'with stream and content type auto-detection on' => [
                true,
                'stream',
                'Hello World',
                'application/octet-stream',
            ],
            'with stream and content type auto-detection off' => [
                false,
                'stream',
                'Hello World',
                '',
            ],
        ];
    }

    /**
     * @dataProvider fileProvider
     *
     * @param bool|string $contentType
     * @param string      $openAs
     * @param string      $expectedBody
     * @param string      $expectedContentType
     */
    public function testAttachment($contentType, string $openAs, string $expectedBody, string $expectedContentType): void
    {
        $path = __DIR__ . '/fixtures/plain.txt';

        switch ($openAs) {
            case 'resource':
                $file = fopen($path, 'r');

                break;

            case 'stream':
                $file = (new Psr17Factory())->createStreamFromFile($path);

                break;

            default:
            case 'string':
                $file = $path;

                break;
        }

        $response = $this->responseCreator->file($file, $contentType);

        $this->assertEquals($expectedBody, (string) $response->getBody());
        $this->assertEquals($expectedContentType, $response->getHeaderLine('Content-Type'));

        if (is_resource($file)) {
            fclose($file);
        }
    }

    public function testAttachmentThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->responseCreator->file(1);
    }

    public function fileDownloadProvider(): array
    {
        return [
            'with resource and file name specified' => [
                'plain.txt',
                'resource',
                'attachment; filename="plain.txt"; filename*=UTF-8\'\'plain.txt',
            ],
            'with resource and file name not specified' => [
                null,
                'resource',
                'attachment; filename="plain.txt"; filename*=UTF-8\'\'plain.txt',
            ],
            'with string and file name specified' => [
                'plain.txt',
                'string',
                'attachment; filename="plain.txt"; filename*=UTF-8\'\'plain.txt',
            ],
            'with string and file name not specified' => [
                null,
                'string',
                'attachment; filename="plain.txt"; filename*=UTF-8\'\'plain.txt',
            ],
            'with stream and file name specified' => [
                'plain.txt',
                'stream',
                'attachment; filename="plain.txt"; filename*=UTF-8\'\'plain.txt',
            ],
            'with stream and file name not specified' => [
                null,
                'stream',
                'attachment; filename="plain.txt"; filename*=UTF-8\'\'plain.txt',
            ],
        ];
    }

    /**
     * @dataProvider fileDownloadProvider
     *
     * @param string|null $name
     * @param string      $openAs
     * @param string      $expectedContentDisposition
     */
    public function testWithFileDownload(?string $name, string $openAs, string $expectedContentDisposition): void
    {
        $path = __DIR__ . '/fixtures/plain.txt';

        switch ($openAs) {
            case 'resource':
                $file = fopen($path, 'r');

                break;

            case 'stream':
                $file = (new Psr17Factory())->createStreamFromFile($path);

                break;

            default:
            case 'string':
                $file = $path;

                break;
        }

        $response = $this->responseCreator->attachment($file, $name);

        $this->assertEquals($expectedContentDisposition, $response->getHeaderLine('Content-Disposition'));

        if (is_resource($file)) {
            fclose($file);
        }
    }
}
