<?php

namespace BoomCMS\Contracts\Models;

use Carbon\Carbon;

interface Person
{
    public function addGroup(Group $group);

    /**
     * @param Site $site
     *
     * @return $this
     */
    public function addSite(Site $site);

    /**
     * @param array $sites
     *
     * @return $this
     */
    public function addSites(array $sites);

    public function checkPassword($password);

    public function checkPersistCode($code);

    public function getEmail();

    public function getGroups();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return Carbon
     */
    public function getLastLogin();

    public function getName();

    public function getPassword();

    public function getRememberToken();

    /**
     * @return array
     */
    public function getSites();

    /**
     * @return bool
     */
    public function hasLoggedIn();

    /**
     * @param Site $site
     *
     * @return bool
     */
    public function hasSite(Site $site);

    public function isEnabled();

    public function isSuperUser();

    public function removeGroup(Group $group);

    /**
     * @param Site $site
     *
     * @return $this
     */
    public function removeSite(Site $site);

    /**
     * Set the time of last login.
     *
     * @param Carbon $time
     *
     * @return $this
     */
    public function setLastLogin(Carbon $time);

    public function setEmail($email);

    public function setEnabled($enabled);

    public function setName($name);

    public function setSuperUser($superuser);

    public function setRememberToken($token);
}
