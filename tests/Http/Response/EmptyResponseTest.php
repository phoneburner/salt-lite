<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Response;

use PhoneBurner\SaltLite\Http\Domain\HttpHeader;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;
use PhoneBurner\SaltLite\Http\Response\EmptyResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EmptyResponseTest extends TestCase
{
    #[Test]
    public function default_constructor_creates_204_response(): void
    {
        $response = new EmptyResponse();

        self::assertSame(HttpStatus::NO_CONTENT, $response->getStatusCode());
        self::assertSame('', (string)$response->getBody());
        self::assertSame('', $response->getHeaderLine(HttpHeader::CONTENT_LENGTH));
    }

    #[Test]
    #[DataProvider('valid_status_codes')]
    public function can_create_with_custom_status_code(int $status): void
    {
        $response = new EmptyResponse($status);

        self::assertSame($status, $response->getStatusCode());
        self::assertSame('', (string)$response->getBody());
        self::assertSame('', $response->getHeaderLine(HttpHeader::CONTENT_LENGTH));
    }

    #[Test]
    public function can_create_with_custom_headers(): void
    {
        $headers = [
            'X-Custom-Header' => ['custom-value'],
            'X-Another-Header' => ['another-value'],
        ];

        $response = new EmptyResponse(HttpStatus::NO_CONTENT, $headers);

        self::assertSame(HttpStatus::NO_CONTENT, $response->getStatusCode());
        self::assertSame('', (string)$response->getBody());
        self::assertSame('', $response->getHeaderLine(HttpHeader::CONTENT_LENGTH));
        self::assertSame(['custom-value'], $response->getHeader('X-Custom-Header'));
        self::assertSame(['another-value'], $response->getHeader('X-Another-Header'));
    }

    /**
     * @return \Iterator<(int | string), array<int>>
     */
    public static function valid_status_codes(): \Iterator
    {
        yield [HttpStatus::OK];
        yield [HttpStatus::CREATED];
        yield [HttpStatus::ACCEPTED];
        yield [HttpStatus::NO_CONTENT];
        yield [HttpStatus::RESET_CONTENT];
    }
}
