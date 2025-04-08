<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Filesystem;

use PhoneBurner\SaltLite\Filesystem\File;
use PhoneBurner\SaltLite\Filesystem\FileMode;
use PhoneBurner\SaltLite\Filesystem\FileStream;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;

final class FileTest extends TestCase
{
    private string $temp_dir;

    private string $test_file_path;

    private string $test_file_content = 'Hello, world!';

    protected function setUp(): void
    {
        $this->temp_dir = \sys_get_temp_dir() . '/salt-lite-test-' . \random_int(100_000, 999_999);
        \mkdir($this->temp_dir, 0777, true);
        $this->test_file_path = $this->temp_dir . '/test-file.txt';
        \file_put_contents($this->test_file_path, $this->test_file_content) ?:
            throw new \RuntimeException('Failed to create test file');
    }

    protected function tearDown(): void
    {
        if (\file_exists($this->test_file_path)) {
            @\unlink($this->test_file_path);
        }

        if (\is_dir($this->temp_dir)) {
            @\rmdir($this->temp_dir);
        }
    }

    #[Test]
    public function read_returns_file_contents(): void
    {
        self::assertSame($this->test_file_content, File::read($this->test_file_path));
    }

    #[Test]
    public function read_accepts_stringable(): void
    {
        $stringable = new class ($this->test_file_path) implements \Stringable {
            public function __construct(private readonly string $path)
            {
            }

            public function __toString(): string
            {
                return $this->path;
            }
        };

        self::assertSame($this->test_file_content, File::read($stringable));
    }

    #[Test]
    public function read_accepts_SplFileInfo(): void
    {
        $file_info = new SplFileInfo($this->test_file_path);
        self::assertSame($this->test_file_content, File::read($file_info));
    }

    #[Test]
    public function read_throws_for_non_existent_file(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to read file at location:');
        File::read($this->temp_dir . '/non-existent-file.txt');
    }

    #[Test]
    public function write_writes_content_to_file(): void
    {
        $new_content = 'New content';
        $bytes_written = File::write($this->test_file_path, $new_content);

        self::assertSame(\strlen($new_content), $bytes_written);
        self::assertSame($new_content, \file_get_contents($this->test_file_path));
    }

    #[Test]
    public function write_accepts_stringable(): void
    {
        $stringable = new class ($this->test_file_path) implements \Stringable {
            public function __construct(private readonly string $path)
            {
            }

            public function __toString(): string
            {
                return $this->path;
            }
        };

        $new_content = 'Stringable path content';
        $bytes_written = File::write($stringable, $new_content);

        self::assertSame(\strlen($new_content), $bytes_written);
        self::assertSame($new_content, \file_get_contents($this->test_file_path));
    }

    #[Test]
    public function write_accepts_spl_file_info(): void
    {
        $file_info = new SplFileInfo($this->test_file_path);
        $new_content = 'SplFileInfo content';
        $bytes_written = File::write($file_info, $new_content);

        self::assertSame(\strlen($new_content), $bytes_written);
        self::assertSame($new_content, \file_get_contents($this->test_file_path));
    }

    #[Test]
    public function stream_returns_file_stream(): void
    {
        $stream = File::stream($this->test_file_path);

        self::assertInstanceOf(FileStream::class, $stream);
        self::assertSame($this->test_file_content, $stream->getContents());
    }

    #[Test]
    public function stream_with_write_mode_allows_writing(): void
    {
        $stream = File::stream($this->test_file_path, FileMode::WriteCreateOrTruncateExisting);

        $new_content = 'New stream content';
        $stream->write($new_content);
        $stream->close();

        self::assertSame($new_content, \file_get_contents($this->test_file_path));
    }

    #[Test]
    public function open_returns_stream_resource(): void
    {
        $stream = File::open($this->test_file_path);

        self::assertIsResource($stream);
        self::assertSame('stream', \get_resource_type($stream));

        \fclose($stream);
    }

    #[Test]
    public function open_with_context_accepts_stream_context(): void
    {
        $context = \stream_context_create([
            'http' => [
                'method' => 'GET',
            ],
        ]);

        $stream = File::open($this->test_file_path, FileMode::Read, $context);

        self::assertIsResource($stream);
        \fclose($stream);
    }

    #[Test]
    public function open_throws_for_invalid_context(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('context must be null or stream-context resource');

        File::open($this->test_file_path, FileMode::Read, 'not a context');
    }

    #[Test]
    public function open_throws_for_non_existent_file(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could Not Create Stream');

        File::open($this->temp_dir . '/non-existent-directory/file.txt', FileMode::Read);
    }

    #[Test]
    public function size_with_stream_resource_returns_file_size(): void
    {
        $stream = File::open($this->test_file_path);

        $size = File::size($stream);

        self::assertSame(\strlen($this->test_file_content), $size);

        \fclose($stream);
    }

    #[Test]
    public function size_with_StreamInterface_returns_file_size(): void
    {
        $stream_mock = $this->createMock(StreamInterface::class);
        $stream_mock->expects($this->once())
            ->method('getSize')
            ->willReturn(42);

        $size = File::size($stream_mock);

        self::assertSame(42, $size);
    }

    #[Test]
    public function size_with_spl_file_info_returns_file_size(): void
    {
        $file_info = new SplFileInfo($this->test_file_path);

        $size = File::size($file_info);

        self::assertSame(\strlen($this->test_file_content), $size);
    }

    #[Test]
    public function size_with_string_path_returns_file_size(): void
    {
        $size = File::size($this->test_file_path);

        self::assertSame(\strlen($this->test_file_content), $size);
    }

    #[Test]
    public function size_throws_for_unsupported_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported Type:stdClass');

        File::size(new \stdClass());
    }

    #[Test]
    public function close_closes_stream_resource(): void
    {
        $stream = File::open($this->test_file_path);

        File::close($stream);

        self::assertFalse(\is_resource($stream));
    }

    #[Test]
    public function close_closes_stream_interface(): void
    {
        $stream_mock = $this->createMock(StreamInterface::class);
        $stream_mock->expects($this->once())
            ->method('close');

        File::close($stream_mock);
    }

    #[Test]
    public function close_handles_unsupported_type_gracefully(): void
    {
        // This should not throw any exception
        File::close('not a stream');
        File::close(null);
        File::close(new \stdClass());

        self::assertTrue(true);
    }
}
