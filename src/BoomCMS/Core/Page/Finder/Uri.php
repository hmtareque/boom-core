<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class Uri extends Filter
{
    protected $uri;

    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    public function execute(Builder $query)
    {
        return $query
            ->join('page_urls', 'pages.id', '=', 'page_urls.page_id')
            ->where('page_urls.location', '=', $this->uri);
    }
}
