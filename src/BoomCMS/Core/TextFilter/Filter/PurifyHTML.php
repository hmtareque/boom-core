<?php

namespace BoomCMS\Core\TextFilter\Filter;

use HTMLPurifier;
use HTMLPurifier_Config;

use Illuminate\Support\Facades\Config;

class PurifyHTML implements \Boom\TextFilter\Filter
{
    public function filterText($text)
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->loadArray(Config::get('boomcms.htmlpurifier'));

        $purifier = new HTMLPurifier($config);

        return $purifier->purify($text);
    }
}
