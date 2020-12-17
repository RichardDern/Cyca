<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class CleanerTest extends TestCase
{
    public function testPlainTextStringDefaultParams()
    {
        $sourceString   = 'hello world';
        $expectedString = 'hello world';

        $this->assertSame($expectedString, \App\Helpers\Cleaner::cleanupString($sourceString));
    }

    public function testPlainTextStringRemoveExtraSpaces()
    {
        $sourceString   = '         hello        world     ';
        $expectedString = 'hello world';

        $this->assertSame($expectedString, \App\Helpers\Cleaner::cleanupString($sourceString, true, true));
    }

    public function testStripTags()
    {
        $sourceString   = '<script type="text/javascript">hello         <span>world</span></script>';
        $expectedString = 'hello world';

        $this->assertSame($expectedString, \App\Helpers\Cleaner::cleanupString($sourceString, true, true));
    }

    public function testHtmlStringDefaultParams()
    {
    }
}
