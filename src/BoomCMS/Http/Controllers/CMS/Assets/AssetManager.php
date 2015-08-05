<?php

namespace BoomCMS\Http\Controllers\CMS\Assets;

use BoomCMS\Core\Auth;
use BoomCMS\Core\Asset;
use BoomCMS\Core\Asset\Mimetype\Mimetype;
use BoomCMS\Core\Asset\Mimetype\UnsupportedMimeType;
use BoomCMS\Core\Asset\Finder;
use BoomCMS\Http\Controllers\Controller;

use DateTime;
use ZipArchive;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class AssetManager extends Controller
{
    protected $perpage = 30;

    /**
	 *
	 * @var	string
	 */
    protected $viewPrefix = 'boom::assets.';

    /**
     *
     * @var Asset\Provider
     */
    protected $provider;

    public function __construct(Auth\Auth $auth, Request $request, Asset\Provider $provider)
    {
        $this->auth = $auth;
        $this->request = $request;
        $this->provider = $provider;

        $this->authorization('manage_assets');
    }

    public function delete()
    {
        $collection = new Asset\Collection($this->request->input('assets'));
        $collection->delete();
    }

    public function download()
    {
        $assetIds = array_unique((array) $this->request->input('asset'));
        $assets = [];

        foreach ($assetIds as $assetId) {
            $asset = $this->provider->findById($assetId);

            if ($asset->loaded()) {
                $assets[] = $asset;
            }
        }

        if (count($assets) === 1) {
            return Response::download(
				$assets[0]->getFilename(),
				$assets[0]->getOriginalFilename()
			);
        } else {
            $filename = tempnam(sys_get_temp_dir(), 'boomcms_asset_download');
            $zip = new ZipArchive();
            $zip->open($filename, ZipArchive::CREATE);

            foreach ($assets as $asset) {
                $zip->addFile($asset->getFilename(), $asset->getOriginalFilename());
            }

            $zip->close();

            $response = Response::make()
                ->header("Content-type", "application/zip")
                ->header("Content-Disposition", "attachment; filename=cms_assets.zip")
                ->setContent(file_get_contents($filename));

            unlink($filename);

            return $response;
        }
    }

    /**
	 * Display the asset manager.
	 *
	 */
    public function index()
    {
        return View::make($this->viewPrefix . 'index', [
            'manager' => $this->manager(),
            'person' => $this->person,
        ]);
    }

    public function get(Finder\Finder $finder)
    {
        $finder
            ->addFilter(new Finder\Tag($this->request->input('tag')))
            ->addFilter(new Finder\TitleContains($this->request->input('title')))
            ->addFilter(new Finder\Type($this->request->input('type')));

        $column = 'last_modified';
        $order = 'desc';

        if (strpos($this->request->input('sortby'), '-' ) > 1) {
            list($column, $order) = explode('-', $this->request->input('sortby'));
        }

        $finder->setOrderBy($column, $order);

        $count = $finder->count();

        if ($count === 0) {
            return View::make($this->viewPrefix . 'none_found');
        } else {
            $page = max(1, $this->request->input('page'));
            $perpage = max($this->perpage, $this->request->input('perpage'));
            $pages = ceil($count / $perpage);

            $assets = $finder
                ->setLimit($perpage)
                ->setOffset(($page - 1) * $perpage)
                ->findAll();

            return View::make($this->viewPrefix . 'list', [
                'assets' => $assets,
                'total' => $count,
                'order' =>     $order,
                'pages' => $pages,
                'page' => $page
            ]);
        }
    }

    /**
    * Display the asset manager without topbar etc.
    *
    */
    public function manager()
    {
        return View::make($this->viewPrefix . 'manager');
    }

    public function picker()
    {
        return View::make($this->viewPrefix . 'picker');
    }

    public function replace(Asset\Asset $asset)
    {
        list($validFiles, $errors) = $this->validateFileUpload();

        foreach ($validFiles as $file) {
            $asset->createVersionFromFile($file);
            $this->provider->save($asset);

            return [$asset->getId()];
        }

        if (count($errors)) {
            return new JsonResponse($errors, 500);
        }
    }

    public function revert(Asset\Asset $asset)
    {
        $asset->revertTo($this->request->input('version_id'));
    }

    public function save(Asset\Asset $asset)
    {
        $asset
            ->setTitle($this->request->input('title'))
            ->setDescription($this->request->input('description'))
            ->setCredits($this->request->input('credits'))
            ->setThumbnailAssetId($this->request->input('thumbnail_asset_id'));

        $this->provider->save($asset);
    }

    public function upload()
    {
        $asset_ids = $errors = [];
        $now = new DateTime('now');

        list($validFiles, $errors) = $this->validateFileUpload();

        foreach ($validFiles as $file) {
            $mime = Mimetype::factory($file->getMimeType());

            $asset = Asset\Asset::factory(['type' => $mime->getType()]);
            $asset
                ->setUploadedTime(new DateTime('@' . time()))
                ->setUploadedBy($this->auth->getPerson());

            $asset_ids[] = $this->provider->save($asset)->getId();

            $asset->createVersionFromFile($file);
        }

        if (count($errors)) {
            return new JsonResponse($errors, 500);
        } else {
            return $asset_ids;
        }
    }

    protected function validateFileUpload()
    {
        $validFiles = $errors = [];

        foreach ($this->request->file() as $files) {
            foreach ($files as $i => $file) {
                if ( ! $file->isValid()) {
                    $errors[] = $file->getErrorMessage();
                    continue;
                }

                try {
                    $mime = Mimetype::factory($file->getMimeType());
                } catch (UnsupportedMimeType $e) {
                    $errors[] = "File {$file->getClientOriginalName()} is of an unsuported type: {$e->getMimetype()}";
                    continue;
                }

                $validFiles[] = $file;
            }
        }

        return [$validFiles, $errors];
    }

    public function view(Asset\Asset $asset)
    {
        return View::make("$this->viewPrefix/view", [
            'asset' => $asset,
        ]);
    }
}