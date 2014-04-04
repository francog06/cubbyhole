<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\DataHistory
 */
class DataHistory
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var datetime $date
     */
    private $date;

    /**
     * @var string $ip
     */
    private $ip;

    /**
     * @var string $country
     */
    private $country;

    /**
     * @var Entities\File
     */
    private $file;


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
     * Set date
     *
     * @param datetime $date
     * @return DataHistory
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return datetime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return DataHistory
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return DataHistory
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set file
     *
     * @param Entities\File $file
     * @return DataHistory
     */
    public function setFile(\Entities\File $file = null)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Get file
     *
     * @return Entities\File 
     */
    public function getFile()
    {
        return $this->file;
    }
}