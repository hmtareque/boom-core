<?php

namespace BoomCMS\Core\Person;

use BoomCMS\Core\Models\Person as Model;

class Provider
{
    public function create(array $credentials)
    {
        $existing = $this->findByEmail($credentials['email']);

        if ($existing->loaded()) {
            throw new DuplicateEmailException($credentials['email']);
        }

        $model = Model::create($credentials);

        return $this->findAndCache($model);
    }

    public function findAndCache(Model $model)
    {
        if ($model->id) {
            $this->cache[$model->id] = $model;
        }

        return new Person($model->toArray());
    }

    public function findAll()
    {
        $models = Model::all();
        $people = [];

        foreach ($models as $model) {
            $people[] = $this->findAndCache($model);
        }

        return $people;
    }

    /**
     *
     * @return Person
     */
    public function findBy($key, $value)
    {
        $model = Model::where($key, '=', $value)->first();

        return $model ? $this->findAndCache($model) : new Guest();
    }

    public function findById($id)
    {
        return $this->findBy('id', $id);
    }

    public function findByEmail($email)
    {
        return $this->findBy('email', $email);
    }

    public function findByGroupId($groupId)
    {
        $people = [];
        $query = Model::join('people_groups', 'people.id', '=', 'people_groups.person_id')
            ->where('group_id', '=', $groupId);

        foreach ($query->get() as $result) {
            $people[] = $this->findAndCache($result);
        }

        return $people;
    }

    public function findByLogin($login)
    {
        return $this->findByEmail($login);
    }

    public function findByResetPasswordCode($code)
    {
        return $this->findBy('reset_password_code', $code);
    }

    /**
     *
     * @return \Boom\Person\Guest
     */
    public function getEmptyUser()
    {
        return new Guest();
    }
}
