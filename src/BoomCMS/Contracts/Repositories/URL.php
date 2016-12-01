<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Contracts\Models\URL as URLInterface;

interface URL
{
    /**
     * @param string        $location
     * @param PageInterface $page
     * @param bool          $isPrimary
     *
     * @return URLInterface
     */
    public function create($location, PageInterface $page, $isPrimary = false);

    /**
     * @param URLInterface $url
     *
     * @return $this
     */
    public function delete(URLInterface $url);

    /**
     * @param int $urlId
     *
     * @return URLInterface
     */
    public function find($urlId);

    /**
     * @param string $location
     *
     * @return URLInterface
     */
    public function findByLocation($location);

    /**
     * @param type $location
     *
     * @return bool
     */
    public function isAvailable($location);

    /**
     * Returns the primary URL for the give page.
     *
     * @param PageInterface $page
     *
     * @return URLInterface
     */
    public function page(PageInterface $page);

    /**
     * @param URLInterface $url
     *
     * @return URLInterface
     */
    public function save(URLInterface $url);
}
