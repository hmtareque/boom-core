<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Core\Editor\Editor;
use BoomCMS\Core\Page\Page;
use BoomCMS\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Session\SessionManager as Session;
use View as View;

class Controller extends BaseController
{
    /**
     * The current user.
     *
     * @var Boom\Person\Person
     */
    public $person;

    /**
     * @var \BoomCMS\Core\Auth\Auth
     */
    public $auth;

    /**
     * @var Editor
     */
    public $editor;

    /**
     * @var string
     */
    protected $role;

    /**
     * @var Session
     */
    public $session;

    public function __construct(Request $request, Session $session, Editor $editor)
    {
        $this->session = $session;
        $this->request = $request;
        $this->auth = Auth::getFacadeRoot();
        $this->editor = $editor;
        $this->person = $this->auth->getPerson();

        if ($this->role) {
            $this->authorization($this->role);
        }
    }

    /**
     * Checks whether the current user is authorized to perform a particular action.
     *
     * @param string     $role
     * @param Page $page
     */
    public function authorization($role, Page $page = null)
    {
        if (!Auth::isLoggedIn()) {
            abort(401);
        }

        if (!Auth::loggedIn($role, $page)) {
            abort(403);
        }
    }
}
