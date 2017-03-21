<?php

namespace Radcliffe\Tests\Larama\Unit\Console\Input;

use PHPUnit\Framework\TestCase;
use Radcliffe\Larama\Console\Input\AliasInput;

/**
 * Tests the replacement argv input class.
 *
 * @group larama
 */
class AliasInputTest extends TestCase
{
    /**
     * Tests get alias.
     *
     * @param string $expected
     *   The expected value.
     * @param array $args
     *   The array of arguments to test.
     *
     * @dataProvider getAliasProvider
     */
    public function testGetAlias($expected, $args)
    {
        $input = new AliasInput($args);
        $this->assertEquals($expected, $input->getAlias());
    }

    /**
     * Tests the exceptions thrown by getAlias.
     *
     * @expectedException \RuntimeException
     */
    public function testGetAliasExceptionBadCharacters()
    {
        new AliasInput(['larama', '@\'', 'help']);
    }

    /**
     * Tests the exceptions thrown by getAlias.
     *
     * @expectedException \RuntimeException
     */
    public function testGetAliasExceptionNoLength()
    {
        new AliasInput(['larama', '@', 'help']);
    }


    /**
     * Provide data for the getAlias method.
     *
     * @return array
     */
    public function getAliasProvider()
    {
        return [
            ['', ['larama', 'help']],
            ['', ['larama', '--verbose', 'help']],
            ['laravel', ['larama', '@laravel', 'help']],
            ['', ['larama', 'help', '@laravel']],
            ['laravel', ['larama', '@laravel', '--verbose', 'help']],
        ];
    }
}
