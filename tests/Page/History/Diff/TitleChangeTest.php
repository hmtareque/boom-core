<?php

namespace BoomCMS\Tests\Page\History\Diff;

use BoomCMS\Database\Models\PageVersion;
use BoomCMS\Page\History\Diff\TitleChange;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Lang;
use Mockery as m;

class TitleChangeTest extends AbstractTestCase
{
    public function testDescriptionKeyExists()
    {
        $class = new TitleChange(m::mock(PageVersion::class), m::mock(PageVersion::class));

        $this->assertTrue(Lang::has($class->getSummaryKey()));
    }

    public function testNewDescriptionExists()
    {
        $class = new TitleChange(m::mock(PageVersion::class), m::mock(PageVersion::class));

        $this->assertTrue(Lang::has($class->getNewDescriptionKey()));
    }

    public function testOldDescriptionExists()
    {
        $class = new TitleChange(m::mock(PageVersion::class), m::mock(PageVersion::class));

        $this->assertTrue(Lang::has($class->getOldDescriptionKey()));
    }

    public function testGetDescriptionParams()
    {
        $new = new PageVersion([PageVersion::ATTR_TITLE => 'new']);
        $old = new PageVersion([PageVersion::ATTR_TITLE => 'old']);

        $change = new TitleChange($new, $old);

        $newAttrs = ['title' => $new->getTitle()];
        $oldAttrs = ['title' => $old->getTitle()];

        $this->assertEquals($newAttrs, $change->getNewDescriptionParams());
        $this->assertEquals($oldAttrs, $change->getOldDescriptionParams());
    }
}
