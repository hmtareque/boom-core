<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Plugins
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
abstract class Boom_Core
{
	public static function page_format(Request $request)
	{
		// Change the controller action depending on the request accept header.
		$accepts = $request->accept_type();

		foreach (array_keys($accepts) as $accept)
		{
			switch ($accept)
			{
				case 'application/json':
					$format = 'json';
					break;
				case 'application/rss+xml':
					$format = 'rss';
					break;
				default:
					$format = 'html';
			}
		}

		return $format;
	}

	public static function process_uri(Route $route, array $params, Request $request)
	{
		$page_url = new Model_Page_URL(array('location' => $params['location']));

		if ( ! $page_url->loaded())
		{
			return FALSE;
		}

		$page = ORM::factory('Page')
			->with_current_version(Editor::instance())
			->where('page.id', '=', $page_url->page_id)
			->find();

		if ($page->loaded())
		{
			if ( ! $page_url->is_primary AND $page_url->redirect)
			{
				header('Location: '.$page->url(), NULL, 301);
				exit;
			}

			// The URI matches a page in the CMS so we're going to process it with the Page controller.
			$params['controller'] = 'Page';

			// Add the page model as a paramater for the controller.
			$params['page'] = $page;

			// Change the page format depending on the request headers.
			$params['action'] = Boom::page_format($request);

			return $params;
		}

		return FALSE;
	}
}