<?php

namespace AppBundle\Entity;

/**
 * UserKeys
 */
class UserKeys
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $userKey;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Users
     */
    private $user;


    /**
     * Set type
     *
     * @param string $type
     *
     * @return UserKeys
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set userKey
     *
     * @param string $userKey
     *
     * @return UserKeys
     */
    public function setUserKey($userKey)
    {
        $this->userKey = $userKey;

        return $this;
    }

    /**
     * Get userKey
     *
     * @return string
     */
    public function getUserKey()
    {
        return $this->userKey;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\Users $user
     *
     * @return UserKeys
     */
    public function setUser(\AppBundle\Entity\Users $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\Users
     */
    public function getUser()
    {
        return $this->user;
    }
}
