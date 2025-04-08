<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Configuration;

use PhoneBurner\SaltLite\App\Environment;
use PhoneBurner\SaltLite\Configuration\ConfigurationFactory;
use PhoneBurner\SaltLite\Configuration\ImmutableConfiguration;
use PhoneBurner\SaltLite\Tests\Fixtures\TestEnvironment;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConfigurationFactoryTest extends TestCase
{
    private Environment $environment;
    private \SplFileInfo $root_dir;
    private \SplFileInfo $config_dir;
    private \SplFileInfo $cache_file;

    protected function setUp(): void
    {
        $this->root_dir = new \SplFileInfo(\sys_get_temp_dir() . '/salt-lite-test-' . \random_int(100_000, 999_999));
        \mkdir($this->root_dir->getPathname(), 0777, true);

        $this->config_dir = new \SplFileInfo($this->root_dir->getPathname() . '/config');
        \mkdir($this->config_dir->getPathname(), 0777, true);

        $this->cache_file = new \SplFileInfo($this->root_dir->getPathname() . '/storage/bootstrap/config.cache.php');
        \mkdir(\dirname($this->cache_file->getPathname()), 0777, true);

        $this->environment = new TestEnvironment($this->root_dir->getPathname(), env: ['SALT_ENABLE_CONFIG_CACHE' => 'true']);
    }

    protected function tearDown(): void
    {
        @\unlink($this->cache_file->getPathname());
        foreach ([$this->config_dir, $this->root_dir] as $dir) {
            if (\is_dir($this->config_dir->getPathname())) {
                foreach (\glob($this->config_dir->getPathname() . '/*') ?: [] as $file) {
                    if (\is_file($file)) {
                        @\unlink($file);
                    }
                }
                @\rmdir($this->config_dir->getPathname());
            }
        }
    }

    #[Test]
    public function make_returns_immutable_configuration(): void
    {
        $config = ConfigurationFactory::make($this->environment);
        self::assertInstanceOf(ImmutableConfiguration::class, $config);
    }

    #[Test]
    public function make_loads_configuration_from_files(): void
    {
        $config_file = $this->config_dir->getPathname() . '/test.php';
        \file_put_contents($config_file, '<?php return ["foo" => "bar"];');

        $config = ConfigurationFactory::make($this->environment);
        self::assertSame('bar', $config->get('foo'));
    }

    #[Test]
    public function make_merges_configuration_from_multiple_files(): void
    {
        \file_put_contents($this->config_dir->getPathname() . '/first.php', '<?php return ["foo" => "bar"];');
        \file_put_contents($this->config_dir->getPathname() . '/second.php', '<?php return ["baz" => "qux"];');

        $config = ConfigurationFactory::make($this->environment);
        self::assertSame('bar', $config->get('foo'));
        self::assertSame('qux', $config->get('baz'));
    }

    #[Test]
    public function make_uses_cached_configuration_when_enabled(): void
    {
        $cached_config = ['foo' => 'bar'];
        \file_put_contents($this->cache_file->getPathname(), '<?php return ' . \var_export($cached_config, true) . ';');

        $config = ConfigurationFactory::make($this->environment);
        self::assertSame('bar', $config->get('foo'));
    }

    #[Test]
    public function make_regenerates_cache_when_disabled(): void
    {
        $environment = new TestEnvironment($this->root_dir->getPathname(), env: ['SALT_ENABLE_CONFIG_CACHE' => 'false']);

        $cached_config = ['foo' => 'bar'];
        \file_put_contents($this->cache_file->getPathname(), '<?php return ' . \var_export($cached_config, true) . ';');

        \file_put_contents($this->config_dir . '/test.php', '<?php return ["baz" => "qux"];');

        $config = ConfigurationFactory::make($environment);
        self::assertNull($config->get('foo'));
        self::assertSame('qux', $config->get('baz'));
    }

    #[Test]
    public function make_regenerates_cache_when_invalid(): void
    {
        \file_put_contents($this->cache_file->getPathname(), '<?php return "invalid";');

        \file_put_contents($this->config_dir . '/test.php', '<?php return ["foo" => "bar"];');

        $config = ConfigurationFactory::make($this->environment);
        self::assertSame('bar', $config->get('foo'));
    }
}
