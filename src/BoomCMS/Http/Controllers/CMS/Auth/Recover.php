<?php

namespace BoomCMS\Http\Controllers\CMS\Auth;

use BoomCMS\Core\Auth;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Settings;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Lang;

class Recover extends Controller
{
    public function __construct(Auth\Auth $auth,
        Application $application,
        Request $request)
    {
        $this->auth = $auth;
        $this->app = $application;
        $this->request = $request;
    }

    public function createToken()
    {
        /**
		 * TODO: This is used in setPassword as well.
		 *
		 * It should probably be made into a middleware.
		 */
        $person = $this->auth->getProvider()->findByEmail($this->request->input('email'));

        if ( !$person->isValid()) {
            return $this->showForm([
                'error' => Lang::get('boom::recover.errors.invalid_email')
            ]);
        }
        /* */

        $token = $this->app['auth.password.tokens']->create($person);

        Mail::send('boom::email.recovery', ['token' => $token], function ($message) use ($person) {
            $message
                ->to($person->getEmail(), $person->getName())
                ->from(Settings::get('site.admin.email'), Settings::get('site.name'))
                ->subject('BoomCMS Password Reset');
        });

        return View::make('boom::account.recover.email_sent');
    }

    public function showForm($vars = [])
    {
        return View::make('boom::account.recover.form', $vars);
    }

    public function setPassword()
    {
        if ($this->request->method() === 'GET') {
            return $this->showForm([
                'token' => $this->request->input('token'),
            ]);
        }

        $person = $this->auth->getProvider()->findByEmail($this->request->input('email'));

        if ( !$person->isValid()) {
            return $this->showForm([
                'error' => Lang::get('boom::recover.errors.invalid_email'),
                'token' => $this->request->input('token'),
            ]);
        }

        $tokens = $this->app['auth.password.tokens'];
        $token = $tokens->exists($person, $this->request->input('token'));

        if (!$token) {
            return $this->showForm([
                'error' => Lang::get('boom::recover.errors.invalid_token'),
                'token' => $this->request->input('token'),
            ]);
        }

        if ($this->request->input('password1') != $this->request->input('password2')) {
            return $this->showForm([
                'error' => Lang::get('boom::recover.errors.password_mismatch'),
                'token' => $this->request->input('token'),
            ]);
        }

        if ($this->request->input('password1') && $this->request->input('password2')) {
            $tokens->delete($token);
            $tokens->deleteExpired();
            $person->setEncryptedPassword($this->auth->hash($this->request->input('password1')));
            $this->app['boomcms.person.provider']->save($person);
            $this->auth->login($person);

            return redirect('/');
        } else {
            return $this->showForm([
                'error' => Lang::get('boom::recover.errors.password_mismatch'),
                'token' => $this->request->input('token'),
            ]);
        }
    }
}