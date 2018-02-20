<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Template as TemplateInterface;
use BoomCMS\Contracts\Repositories\Template as TemplateRepositoryInterface;
use BoomCMS\Database\Models\Template as TemplateModel;
use BoomCMS\Foundation\Repository;
use BoomCMS\Support\Helpers;

class Template extends Repository implements TemplateRepositoryInterface
{
    /**
     * @param TemplateModel $model
     */
    public function __construct(TemplateModel $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $attrs
     *
     * @return TemplateInterface
     */
    public function create(array $attrs)
    {
        return $this->model->create($attrs);
    }

    public function findAll()
    {
        return $this->model
            ->orderBy('theme', 'asc')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * @param string $theme
     * @param string $filename
     *
     * @return TemplateInterface
     */
    public function findByThemeAndFilename($theme, $filename)
    {
        return $this->model->where('filename', '=', $filename)
            ->where('theme', '=', $theme)
            ->first();
    }

    /**
     * @return array
     */
    public function findValid()
    {
        $valid = [];
        $templates = $this->findAll();

        foreach ($templates as $template) {
            if ($template->fileExists()) {
                $valid[] = $template;
            }
        }

        return $valid;
    }

    /**
     * Returns the templates with no of pages
     */
    public function findAllWithPageNoOfPages()
    {
        $templates = $this->findAll();

        if($templates->count()>0){
            foreach($templates as $template){
                $template->no_of_pages = count(Helpers::getPages(['template' => $template]));
            }
        }

        return $templates;
    }
}
