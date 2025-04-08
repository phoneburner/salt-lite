<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Session;

use PhoneBurner\SaltLite\Http\Session\CsrfToken;
use PhoneBurner\SaltLite\Http\Session\SessionData;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SessionDataTest extends TestCase
{
    #[Test]
    public function constructor_initializes_csrf_token_and_attributes(): void
    {
        $session = new SessionData();

        self::assertInstanceOf(CsrfToken::class, $session->csrf());
        self::assertFalse($session->has('test'));
    }

    #[Test]
    public function regenerateCsrfToken_creates_new_token(): void
    {
        $session = new SessionData();
        $original_token = $session->csrf();

        $new_token = $session->regenerateCsrfToken();

        self::assertInstanceOf(CsrfToken::class, $new_token);
        self::assertNotSame($original_token, $new_token);
        self::assertSame($new_token, $session->csrf());
    }

    #[Test]
    public function clear_regenerates_token_and_removes_all_attributes(): void
    {
        $session = new SessionData();
        $original_token = $session->csrf();
        $session->set('key1', 'value1');
        $session->set('key2', 'value2');

        $session->clear();

        self::assertNotSame($original_token, $session->csrf());
        self::assertFalse($session->has('key1'));
        self::assertFalse($session->has('key2'));
    }

    #[Test]
    public function flash_sets_value_and_marks_as_flash_data(): void
    {
        $session = new SessionData();
        $session->flash('notification', 'Success message');

        self::assertTrue($session->has('notification'));
        self::assertSame('Success message', $session->get('notification'));

        // Serialize and unserialize to simulate the request cycle
        $serialized = \serialize($session->preserialize());
        $unserialized = \unserialize($serialized);

        // Value should still be available after first request cycle
        self::assertTrue($unserialized->has('notification'));
        self::assertSame('Success message', $unserialized->get('notification'));

        // Prepare for next request and verify it will be removed
        $unserialized->preserialize();
        $serialized = \serialize($unserialized);
        $next_request = \unserialize($serialized);

        self::assertFalse($next_request->has('notification'));
    }

    #[Test]
    public function keep_preserves_flash_data_for_next_request(): void
    {
        $session = new SessionData();
        $session->flash('notification', 'Success message');

        // First request cycle
        $session->preserialize();
        $serialized = \serialize($session);
        $unserialized = \unserialize($serialized);

        // Keep the notification for the next request
        $unserialized->keep('notification');

        // Next request cycle
        $unserialized->preserialize();
        $serialized = \serialize($unserialized);
        $next_request = \unserialize($serialized);

        self::assertTrue($next_request->has('notification'));
        self::assertSame('Success message', $next_request->get('notification'));

        // Third request - data should no longer be available
        $next_request->preserialize();
        $serialized = \serialize($next_request);
        $third_request = \unserialize($serialized);

        self::assertFalse($third_request->has('notification'));
    }

    #[Test]
    public function reflash_preserves_all_flash_data_for_next_request(): void
    {
        $session = new SessionData();
        $session->flash('notification', 'Success message');
        $session->flash('error', 'Error message');

        // First request cycle
        $session->preserialize();
        $serialized = \serialize($session);
        $unserialized = \unserialize($serialized);

        // Reflash all data
        $unserialized->reflash();

        // Next request cycle
        $unserialized->preserialize();
        $serialized = \serialize($unserialized);
        $next_request = \unserialize($serialized);

        self::assertTrue($next_request->has('notification'));
        self::assertTrue($next_request->has('error'));
        self::assertSame('Success message', $next_request->get('notification'));
        self::assertSame('Error message', $next_request->get('error'));
    }

    #[Test]
    public function serialization_preserves_session_data(): void
    {
        $session = new SessionData();
        $session->set('regular', 'Regular value');
        $session->flash('flashed', 'Flash value');

        // Simulate end of request
        $session->preserialize();
        $serialized = \serialize($session);
        $unserialized = \unserialize($serialized);

        self::assertInstanceOf(SessionData::class, $unserialized);
        self::assertTrue($unserialized->has('regular'));
        self::assertTrue($unserialized->has('flashed'));
        self::assertSame('Regular value', $unserialized->get('regular'));
        self::assertSame('Flash value', $unserialized->get('flashed'));
        self::assertInstanceOf(CsrfToken::class, $unserialized->csrf());
    }

    #[Test]
    public function unserialized_session_transforms_flash_data_correctly(): void
    {
        $session = new SessionData();
        $session->flash('notification', 'Flash message');

        // Preserialize and serialize the session
        $serialized = \serialize($session->preserialize());
        $unserialized = \unserialize($serialized);

        // Check the unserialized session
        self::assertTrue($unserialized->has('notification'));
        self::assertSame('Flash message', $unserialized->get('notification'));

        // Verify the internal structure is correct
        $reflected_session = new \ReflectionObject($unserialized);
        $csrf_token_prop = $reflected_session->getProperty('csrf_token');
        $csrf_token_prop->setAccessible(true);
        $old_flash_data_prop = $reflected_session->getProperty('old_flash_data');
        $old_flash_data_prop->setAccessible(true);

        self::assertInstanceOf(CsrfToken::class, $csrf_token_prop->getValue($unserialized));
        self::assertIsArray($old_flash_data_prop->getValue($unserialized));
        self::assertArrayHasKey('notification', $old_flash_data_prop->getValue($unserialized));
    }

    #[Test]
    public function set_and_get_methods_store_and_retrieve_values(): void
    {
        $session = new SessionData();

        $session->set('string_key', 'string_value');
        $session->set('int_key', 123);
        $session->set('array_key', ['a', 'b', 'c']);
        $session->set('object_key', new \stdClass());

        self::assertTrue($session->has('string_key'));
        self::assertTrue($session->has('int_key'));
        self::assertTrue($session->has('array_key'));
        self::assertTrue($session->has('object_key'));

        self::assertSame('string_value', $session->get('string_key'));
        self::assertSame(123, $session->get('int_key'));
        self::assertSame(['a', 'b', 'c'], $session->get('array_key'));
        self::assertInstanceOf(\stdClass::class, $session->get('object_key'));
    }

    #[Test]
    public function map_operations_work_as_expected(): void
    {
        $session = new SessionData();

        $session->set('key1', 'value1');

        // Test unset
        $session->unset('key1');
        self::assertFalse($session->has('key1'));

        // Test default value
        self::assertNull($session->find('missing'));
    }

    #[Test]
    public function stringable_keys_are_converted_to_strings(): void
    {
        $session = new SessionData();
        $stringable_key = new class implements \Stringable {
            public function __toString(): string
            {
                return 'stringable_key';
            }
        };

        $session->set($stringable_key, 'value');
        self::assertTrue($session->has('stringable_key'));
        self::assertTrue($session->has($stringable_key));
        self::assertSame('value', $session->get('stringable_key'));
        self::assertSame('value', $session->get($stringable_key));

        // Test flash with stringable
        $session->flash($stringable_key, 'flash_value');
        self::assertSame('flash_value', $session->get($stringable_key));

        // Test keep with stringable
        $session->preserialize();
        $session->keep($stringable_key);
        $serialized = \serialize($session);
        $unserialized = \unserialize($serialized);

        self::assertTrue($unserialized->has('stringable_key'));
        self::assertSame('flash_value', $unserialized->get('stringable_key'));
    }
}
