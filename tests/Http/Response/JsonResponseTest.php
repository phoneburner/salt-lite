<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Response;

use PhoneBurner\SaltLite\Http\Domain\ContentType;
use PhoneBurner\SaltLite\Http\Domain\HttpHeader;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;
use PhoneBurner\SaltLite\Http\Response\JsonResponse;
use PhoneBurner\SaltLite\Serialization\Json;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class JsonResponseTest extends TestCase
{
    #[Test]
    public function createsJsonResponseWithDefaults(): void
    {
        $data = ['message' => 'Hello, World!', 'success' => true];
        $response = new JsonResponse($data);

        self::assertSame(HttpStatus::OK, $response->getStatusCode());
        self::assertSame(Json::encode($data), (string)$response->getBody());
        self::assertSame(ContentType::JSON, $response->getHeaderLine(HttpHeader::CONTENT_TYPE));
    }

    #[Test]
    public function createsJsonResponseWithCustomStatus(): void
    {
        $data = ['id' => 123, 'name' => 'Test Resource'];
        $response = new JsonResponse($data, HttpStatus::CREATED);

        self::assertSame(HttpStatus::CREATED, $response->getStatusCode());
        self::assertSame(Json::encode($data), (string)$response->getBody());
        self::assertSame(ContentType::JSON, $response->getHeaderLine(HttpHeader::CONTENT_TYPE));
    }

    #[Test]
    public function createsJsonResponseWithCustomHeaders(): void
    {
        $data = ['key' => 'value'];
        $headers = [
            'X-Custom-Header' => ['custom-value'],
            HttpHeader::CACHE_CONTROL => ['no-cache'],
        ];

        $response = new JsonResponse($data, HttpStatus::OK, $headers);

        self::assertSame(HttpStatus::OK, $response->getStatusCode());
        self::assertSame(Json::encode($data), (string)$response->getBody());
        self::assertSame(ContentType::JSON, $response->getHeaderLine(HttpHeader::CONTENT_TYPE));
        self::assertSame(['custom-value'], $response->getHeader('X-Custom-Header'));
        self::assertSame(['no-cache'], $response->getHeader(HttpHeader::CACHE_CONTROL));
    }

    #[Test]
    public function createsJsonResponseWithJsonFlags(): void
    {
        $data = ['special' => 'Characters: ☺'];
        $flags = \JSON_HEX_TAG | \JSON_HEX_APOS;

        $response = new JsonResponse($data, HttpStatus::OK, [], $flags);

        self::assertSame(HttpStatus::OK, $response->getStatusCode());
        self::assertSame(Json::encode($data, $flags), (string)$response->getBody());
        self::assertSame(ContentType::JSON, $response->getHeaderLine(HttpHeader::CONTENT_TYPE));
    }

    #[Test]
    public function contentTypeHeaderCanBeOverridden(): void
    {
        $data = ['test' => true];
        $customContentType = 'application/problem+json';
        $headers = [
            HttpHeader::CONTENT_TYPE => $customContentType,
        ];

        $response = new JsonResponse($data, HttpStatus::OK, $headers);

        self::assertSame(HttpStatus::OK, $response->getStatusCode());
        self::assertSame(Json::encode($data), (string)$response->getBody());
        self::assertSame($customContentType, $response->getHeaderLine(HttpHeader::CONTENT_TYPE));
    }
}
