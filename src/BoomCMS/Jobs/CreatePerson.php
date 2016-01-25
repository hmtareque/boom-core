<?php

namespace BoomCMS\Jobs;

use BoomCMS\Auth\Hasher;
use BoomCMS\Auth\RandomPassword;
use BoomCMS\Events\AccountCreated;
use BoomCMS\Exceptions\DuplicateEmailException;
use BoomCMS\Support\Facades\Person;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

class CreatePerson extends Command implements SelfHandling
{
    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $name;

    /**
     * @return void
     */
    public function __construct($email, $name)
    {
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $password = (string) new RandomPassword();
        $hasher = new Hasher();

        try {
            $person = Person::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => $hasher->make($password),
            ]);
        } catch (DuplicateEmailException $e) {
        }

        if (isset($person)) {
            Event::fire(new AccountCreated($person, $password, Auth::user()));

            return $person;
        } else {
            return Person::findByEmail($this->credentials['email']);
        }
    }
}