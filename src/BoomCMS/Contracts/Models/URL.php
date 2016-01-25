<?php

namespace BoomCMS\Contracts\Models;

interface URL
{
    /**
     * @return string
     */
    public function __toString();

    /**
     * @return string
     */
    public function getLocation();

    /**
     * @return Page
     */
    public function getPage();

    /**
     * @return int
     */
    public function getPageId();

    /**
     * @return Site
     */
    public function getSite();

    /**
     * @param string $location
     *
     * @return bool
     */
    public function is($location);

    /**
     * @param Page $page
     *
     * @return bool
     */
    public function isForPage(Page $page);

    /**
     * @return bool
     */
    public function isPrimary();

    /**
     * @param string $scheme
     *
     * @return string
     */
    public function scheme($scheme);

    /**
     * @param bool $isPrimary
     *
     * @return $this
     */
    public function setIsPrimary($isPrimary);

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setPageId($id);

    /**
     * @param Site $site
     *
     * @return $this
     */
    public function setSite(Site $site);
}