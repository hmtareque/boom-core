<?php

namespace BoomCMS\Tests\Http\Controllers\People;

use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;
use BoomCMS\Http\Controllers\People\PersonSites as Controller;
use BoomCMS\Tests\Http\Controllers\BaseControllerTest;
use Mockery as m;

class PersonSitesTest extends BaseControllerTest
{
    /**
     * @var string
     */
    protected $className = Controller::class;

    public function store()
    {
        $site = new Site();

        $person = m::mock(Person::class);
        $person->shouldReceive('addSite')->once()->with($site);

        $this->controller->addGroup($person, $site);
    }

    public function testDestroy()
    {
        $site = new Site();
        $person = m::mock(Person::class);

        $person->shouldReceive('removeSite')->with($site);

        $this->controller->destroy($person, $site);
    }
}
