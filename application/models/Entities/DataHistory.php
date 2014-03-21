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
     * @var string $download_ip
     */
    private $download_ip;

    /**
     * @var string $download_zone
     */
    private $download_zone;

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
     * Set download_ip
     *
     * @param string $downloadIp
     * @return DataHistory
     */
    public function setDownloadIp($downloadIp)
    {
        $this->download_ip = $downloadIp;
        return $this;
    }

    /**
     * Get download_ip
     *
     * @return string 
     */
    public function getDownloadIp()
    {
        return $this->download_ip;
    }

    /**
     * Set download_zone
     *
     * @param string $downloadZone
     * @return DataHistory
     */
    public function setDownloadZone($downloadZone)
    {
        $this->download_zone = $downloadZone;
        return $this;
    }

    /**
     * Get download_zone
     *
     * @return string 
     */
    public function getDownloadZone()
    {
        return $this->download_zone;
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