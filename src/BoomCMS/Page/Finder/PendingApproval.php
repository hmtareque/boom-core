<?php

namespace BoomCMS\Page\Finder;

use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class PendingApproval extends Filter
{
    public function build(Builder $query)
    {
        return $query
            ->where('pending_approval', '=', true)
            ->orderBy('version:created_at', 'desc');
    }
}
