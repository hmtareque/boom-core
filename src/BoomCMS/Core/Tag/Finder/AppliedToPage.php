<?php

namespace BoomCMS\Core\Tag\Finder;

use BoomCMS\Core\Finder\Filter;
use BoomCMS\Core\Page\Page;
use Illuminate\Database\Eloquent\Builder;

class AppliedToPage extends Filter
{
    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function execute(Builder $query)
    {
        return $query
            ->join('pages_tags', 'tags.id', '=', 'pages_tags.tag_id')
            ->where('pages_tags.page_id', $this->page->getId());
    }
}
