<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Contracts\Models\URL as URLInterface;
use BoomCMS\Contracts\Repositories\URL as URLRepositoryInterface;
use BoomCMS\Database\Models\URL as Model;
use BoomCMS\Support\Helpers\URL as URLHelper;

class URL implements URLRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param string        $location
     * @param PageInterface $page
     * @param bool          $isPrimary
     *
     * @return URLInterface
     */
    public function create($location, PageInterface $page, $isPrimary = false)
    {
        $site = $page->getSite();
        $unique = URLHelper::makeUnique($site, URLHelper::sanitise($location));

        return $this->model->create([
            Model::ATTR_LOCATION   => $unique,
            Model::ATTR_PAGE_ID    => $page->getId(),
            Model::ATTR_IS_PRIMARY => $isPrimary,
            Model::ATTR_SITE       => $site->getId(),
        ]);
    }

    /**
     * @param URLInterface $url
     *
     * @return $this
     */
    public function delete(URLInterface $url)
    {
        $url->delete();

        return $this;
    }

    /**
     * @param int $id
     *
     * @return URLInterface
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * @param SiteInterface $site
     * @param string        $location
     *
     * @return URLInterface
     */
    public function findBySiteAndLocation(SiteInterface $site, $location)
    {
        return $this->model
            ->where(Model::ATTR_SITE, '=', $site->getId())
            ->where(Model::ATTR_LOCATION, '=', URLHelper::sanitise($location))
            ->first();
    }

    /**
     * Determine whether a URL is already being used by a page in the CMS.
     *
     * @param SiteInterface $site
     * @param string        $path
     *
     * @return bool
     */
    public function isAvailable(SiteInterface $site, $path)
    {
        return !$this->model
            ->where(Model::ATTR_SITE, '=', $site->getId())
            ->where(Model::ATTR_LOCATION, '=', $path)
            ->exists();
    }

    /**
     * @param URLInterface $url
     *
     * @return URLInterface
     */
    public function save(URLInterface $url)
    {
        return $url->save();
    }
}