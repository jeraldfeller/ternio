<?php

namespace AppBundle\Entity;

/**
 * GeneratedKeys
 */
class GeneratedKeys
{
    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var \DateTime
     */
    private $dateTime;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set publicKey
     *
     * @param string $publicKey
     *
     * @return GeneratedKeys
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * Get publicKey
     *
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     *
     * @return GeneratedKeys
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
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
}

