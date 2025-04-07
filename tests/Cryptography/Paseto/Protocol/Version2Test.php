<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Cryptography\Paseto\Protocol;

use PhoneBurner\SaltLite\Cryptography\Asymmetric\SignatureKeyPair;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\SignaturePublicKey;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\SignatureSecretKey;
use PhoneBurner\SaltLite\Cryptography\Paseto\Claims\PasetoMessage;
use PhoneBurner\SaltLite\Cryptography\Paseto\Exception\PasetoCryptoException;
use PhoneBurner\SaltLite\Cryptography\Paseto\Paseto;
use PhoneBurner\SaltLite\Cryptography\Paseto\PasetoVersion;
use PhoneBurner\SaltLite\Cryptography\Paseto\Protocol\Version2;
use PhoneBurner\SaltLite\Cryptography\String\VariableLengthBinaryString;
use PhoneBurner\SaltLite\Cryptography\Symmetric\SharedKey;
use PhoneBurner\SaltLite\Filesystem\File;
use PhoneBurner\SaltLite\String\Encoding\Encoding;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class Version2Test extends TestCase
{
    public const PasetoVersion VERSION = PasetoVersion::V2;

    public const string TEST_VECTOR_FILE = __DIR__ . '/../../Fixtures/paseto_test_vectors_v2.json';

    #[Test]
    #[DataProvider('provideTestVectorsEncryptPass')]
    public function paseto_standard_test_vectors_encrypt_pass(array $test_vector): void
    {
        $key = SharedKey::import($test_vector['key'], Encoding::Hex);
        $vector_token = new Paseto($test_vector['token']);

        $message = Version2::decrypt($key, $vector_token, $test_vector['implicit-assertion']);

        self::assertSame($test_vector['payload'], $message->payload);
        self::assertSame($test_vector['footer'], $message->footer);

        $token = Version2::encrypt($key, $message, $test_vector['implicit-assertion']);
        self::assertNotEquals($vector_token, $token);

        $message = Version2::decrypt($key, $token, $test_vector['implicit-assertion']);

        self::assertSame($test_vector['payload'], $message->payload);
        self::assertSame($test_vector['footer'], $message->footer);
    }

    #[Test]
    #[DataProvider('provideTestVectorsSignPass')]
    public function paseto_standard_test_vectors_sign_pass(array $test_vector): void
    {
        $secret_key = SignatureSecretKey::import($test_vector['secret-key'], Encoding::Hex);
        $public_key = SignaturePublicKey::import($test_vector['public-key'], Encoding::Hex);

        $key_pair_seed = VariableLengthBinaryString::import($test_vector['secret-key-seed'], Encoding::Hex);
        $key_pair = SignatureKeyPair::fromSeed($key_pair_seed);

        self::assertEquals($secret_key, $key_pair->secret);
        self::assertEquals($public_key, $key_pair->public);

        $vector_token = new Paseto($test_vector['token']);

        $message = Version2::verify($public_key, $vector_token, $test_vector['implicit-assertion']);

        self::assertSame($test_vector['payload'], $message->payload);
        self::assertSame($test_vector['footer'], $message->footer);

        $token = Version2::sign($key_pair, new PasetoMessage(
            $test_vector['payload'],
            $test_vector['footer'],
        ), $test_vector['implicit-assertion']);

        self::assertEquals($vector_token, $token);

        $message = Version2::verify($key_pair->public, $token, $test_vector['implicit-assertion']);

        self::assertSame($test_vector['payload'], $message->payload);
        self::assertSame($test_vector['footer'], $message->footer);
    }

    #[Test]
    #[DataProvider('provideTestVectorsEncryptFail')]
    public function paseto_standard_test_vectors_encrypt_fail(array $test_vector): void
    {
        $key = SharedKey::import($test_vector['key'], Encoding::Hex);
        $this->expectException(PasetoCryptoException::class);
        Version2::decrypt($key, new Paseto($test_vector['token']), $test_vector['implicit-assertion']);
    }

    #[Test]
    #[DataProvider('provideTestVectorsSignFail')]
    public function paseto_standard_test_vectors_sign_fail(array $test_vector): void
    {
        $secret_key = SignatureSecretKey::import($test_vector['secret-key'], Encoding::Hex);
        $public_key = SignaturePublicKey::import($test_vector['public-key'], Encoding::Hex);

        $key_pair_seed = VariableLengthBinaryString::import($test_vector['secret-key-seed'], Encoding::Hex);
        $key_pair = SignatureKeyPair::fromSeed($key_pair_seed);

        self::assertEquals($secret_key, $key_pair->secret);
        self::assertEquals($public_key, $key_pair->public);

        // Inline the instantiation of the token because we use the self-valid
        // nature of the value object to enforce algorithm lucidity at the engine level
        $this->expectException(PasetoCryptoException::class);
        Version2::verify($public_key, new Paseto($test_vector['token']), $test_vector['implicit-assertion']);
    }

    public static function provideTestVectorsEncryptPass(): \Generator
    {
        $test_vectors = \json_decode(File::read(self::TEST_VECTOR_FILE), true, 512, \JSON_THROW_ON_ERROR);
        foreach ($test_vectors['tests'] as $test_vector) {
            $name = (string)$test_vector['name'];
            if ($name[2] === 'E' && $test_vector['expect-fail'] === false) {
                yield $name => [$test_vector];
            }
        }
    }

    public static function provideTestVectorsEncryptFail(): \Generator
    {
        $test_vectors = \json_decode(File::read(self::TEST_VECTOR_FILE), true, 512, \JSON_THROW_ON_ERROR);
        foreach ($test_vectors['tests'] as $test_vector) {
            $name = (string)$test_vector['name'];
            if ($name[2] === 'F' && $test_vector['expect-fail'] === true && isset($test_vector['key'])) {
                yield $name => [$test_vector];
            }
        }
    }

    public static function provideTestVectorsSignPass(): \Generator
    {
        $test_vectors = \json_decode(File::read(self::TEST_VECTOR_FILE), true, 512, \JSON_THROW_ON_ERROR);
        foreach ($test_vectors['tests'] as $test_vector) {
            $name = (string)$test_vector['name'];
            if ($name[2] === 'S' && $test_vector['expect-fail'] === false) {
                yield $name => [$test_vector];
            }
        }
    }

    public static function provideTestVectorsSignFail(): \Generator
    {
        $test_vectors = \json_decode(File::read(self::TEST_VECTOR_FILE), true, 512, \JSON_THROW_ON_ERROR);
        foreach ($test_vectors['tests'] as $test_vector) {
            $name = (string)$test_vector['name'];
            if ($name[2] === 'F' && $test_vector['expect-fail'] === true && isset($test_vector['secret-key'])) {
                yield $name => [$test_vector];
            }
        }
    }
}
