<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Site;
use BoomCMS\Database\Models\URL;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Jobs\MakeURLPrimary;
use BoomCMS\Jobs\ReassignURL;
use BoomCMS\Support\Facades\URL as URLFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class Urls extends Controller
{
    protected $viewPrefix = 'boomcms::editor.urls';

    public function __construct(Page $page)
    {
        $this->authorize('editUrls', $page);
    }

    public function create(Page $page)
    {
        return view("$this->viewPrefix.add", [
            'page' => $page,
        ]);
    }

    public function index(Page $page)
    {
        return view($this->viewPrefix.'.list', [
            'page' => $page,
            'urls' => $page->getUrls(),
        ]);
    }

    public function getMove(Page $page, URL $url)
    {
        return view("$this->viewPrefix.move", [
            'url'     => $url,
            'current' => $url->getPage(),
            'page'    => $page,
        ]);
    }

    public function store(Request $request, Site $site, Page $page)
    {
        $location = $request->input('location');
        $url = URLFacade::findBySiteAndLocation($site, $location);

        if ($url && !$url->isForPage($page)) {
            // Url is being used for a different page.
            // Notify that the url is already in use so that the JS can load a prompt to move the url.

            return ['existing_url_id' => $url->getId()];
        }

        if (!$url) {
            URLFacade::create($location, $page);
        }
    }

    public function destroy(Page $page, URL $url)
    {
        if (!$url->isPrimary()) {
            URLFacade::delete($url);
        }
    }

    public function makePrimary(Page $page, URL $url)
    {
        Bus::dispatch(new MakeURLPrimary($url));
    }

    public function postMove(Page $page, URL $url)
    {
        Bus::dispatch(new ReassignURL($url, $page));
    }
}