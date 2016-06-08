<?php

namespace BoomCMS\Chunk\Linkset;

use BoomCMS\Link\Link as LinkObject;

class Link
{
    /**
     * @var array
     */
    protected $attrs;

    /**
     * @var Link
     */
    protected $link;

    public function __construct($attrs)
    {
        $this->attrs = $attrs;
    }

    public function getAssetId()
    {
        return $this->attrs['asset_id'];
    }

    public function getId()
    {
        return $this->attrs['id'];
    }

    /**
     * @return Link
     */
    public function getLink()
    {
        if ($this->link === null) {
            $this->link = LinkObject::factory($this->getUrl());
        }

        return $this->link;
    }

    public function getTarget()
    {
        return $this->getLink();
    }

    public function getTargetPageId()
    {
        return $this->attrs['target_page_id'];
    }

    public function getTitle()
    {
        return (isset($this->attrs['title']) && $this->attrs['title']) ?
            $this->attrs['title'] :
            $this->getLink()->getTitle();
    }

    public function getUrl()
    {
        return $this->attrs['url'];
    }

    public function isInternal()
    {
        return $this->getLink()->isInternal();
    }

    public function isExternal()
    {
        return !$this->isInternal();
    }
}
