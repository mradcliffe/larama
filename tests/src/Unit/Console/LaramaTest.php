<?php

namespace Radcliffe\Tests\Larama\Unit\Console;

use PHPUnit\Framework\TestCase;
use Radcliffe\Larama\Config\SiteAlias;
use Radcliffe\Larama\Console\Larama;

/**
 * Tests the larama application for some basic stuff. Not very useful.
 *
 * @group larama
 */
class LaramaTest extends TestCase
{

    public function testGetEnvironment()
    {
        $app = new Larama();
        $this->assertEquals(null, $app->getEnvironment());
    }

    public function testGetAliases()
    {
        $app = new Larama();
        $this->assertTrue(is_array($app->getAliases()));
    }

    /**
     * Asserts that load configuration with a bad alias returns null.
     */
    public function testLoadBadEnvironment()
    {
        $app = new Larama();
        $alias = new SiteAlias('bad', ['approot' => '/tmp/bad']);
        $this->assertNull($app->loadEnvironment($alias));
    }

    /**
     * Asserts that a configuration file is loaded.
     */
    public function testFindConfigFiles()
    {
        $expected = [
            getcwd() . DIRECTORY_SEPARATOR . 'tests/fixtures/valid/site.yml',
        ];
        $app = new Larama();
        $info = $app->findConfigFiles(getcwd() . DIRECTORY_SEPARATOR . 'tests/fixtures/valid');
        $this->assertEquals($expected, $info);
    }

    /**
     * Asserts that an exception is thrown for invalid directory.
     *
     * @expectedException \InvalidArgumentException
     */
     public function testFindConfigFilesInvalid()
     {
         $app = new Larama();
         $app->findConfigFiles(getcwd() . DIRECTORY_SEPARATOR . 'invalid');
     }

    /**
     * Asserts that merge configuration is functional with various parameters.
     *
     * @param \Radcliffe\Larama\Config\SiteAlias[] $expected
     *   An array of site aliases.
     * @param array $info
     *   An array of alias information.
     * @param array $aliases
     *   An array of aliases to seed the app with.
     *
     * @dataProvider mergeProvider
     */
    public function testMergeConfiguration(array $expected, array $info, array $aliases = [])
    {
        $app = new Larama();
        $app->setAliases($aliases);
        $this->assertEquals($expected, $app->mergeConfiguration($info));
    }

    public function testGetConfigDirectories()
    {
        $homedir = \Radcliffe\Larama\Utility::getHomeDirectory();
        $default = [
            '/etc/larama/config',
            $homedir . '/.larama/config',
            $homedir . '/.config/larama',
        ];
        $app = new Larama();
        $this->assertEquals($default, $app->getConfigDirectories());
    }

    /**
     * Asserts getting configuration directories.
     *
     * @param array $expected
     *   An array of configuration directories.
     * @param array $param
     *   An array of configuration directories to pass into the function.
     *
     * @dataProvider setConfigProvider
     */
    public function testSetConfigDirectories($expected, $param)
    {
        $app = new Larama();
        $this->assertEquals($expected, $app->setConfigDirectories($param));
    }

    /**
     * Asserts that YAML parsing is handled correctly.
     *
     * @param array $expected
     *   The expected assertion.
     * @param string $file_name
     *   The file name to test.
     *
     * @dataProvider parseProvider
     */
    public function testParseConfiguration(array $expected, $file_name)
    {
        $app = new Larama();
        $actual = $app->parseConfiguration($file_name);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Create test parameters for testSetConfigDirectories.
     *
     * @return array
     *   The test parameters.
     */
    public function setConfigProvider()
    {
        $homedir = \Radcliffe\Larama\Utility::getHomeDirectory();
        $default = [
            '/etc/larama/config',
            $homedir . '/.larama/config',
            $homedir . '/.config/larama',
        ];
        $test = [__DIR__ . DIRECTORY_SEPARATOR . '/tests/fixtures'];
        return [
            [$default, []],
            [array_merge($test, $default), $test],
        ];
    }

    public function mergeProvider()
    {
        $alias = new SiteAlias('test', []);
        $alias2 = new SiteAlias('test2', []);
        $one_alias = ['aliases' => ['test' => []]];
        $two_aliases = ['aliases' => ['test' => [], 'test2' => []]];

        return [
            [[], [], []],
            [['test' => $alias], $one_alias, []],
            [['test' => $alias, 'test2' => $alias2], $two_aliases, []],
            [['test' => $alias, 'test2' => $alias2], $one_alias, ['test2' => $alias2]],
        ];
    }

    /**
     * Get the test parameters for testParseConfiguration.
     *
     * @return array
     *   An array of test parameters.
     */
    public function parseProvider()
    {
        $cwd = getcwd();
        $site_yaml = [
            'aliases' => [
                'example' => [
                    'approot' => '/var/www/www.example.com',
                    'fqdn' => 'www.example.com',
                    'url' => 'http://www.example.com',
                ],
            ],
        ];

        return [
            [$site_yaml, $cwd . '/tests/fixtures/valid/site.yml'],
            [[], $cwd . '/tests/fixtures/invalid/site.yml'],
            [[], $cwd . '/tests/fixtures/invalid/notexist.yml'],
        ];
    }
}
