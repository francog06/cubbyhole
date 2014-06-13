<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\Event
 */
class Event implements \JsonSerializable
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
     * @var integer $folder_id
     */
    private $folder_id;

    /**
     * @var integer $file_id
     */
    private $file_id;

    /**
     * @var string $status
     */
    private $status;

    /**
     * @var Entities\User
     */
    private $user;


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
     * @return Event
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
     * Set folder_id
     *
     * @param integer $folderId
     * @return Event
     */
    public function setFolderId($folderId)
    {
        $this->folder_id = $folderId;
        return $this;
    }

    /**
     * Get folder_id
     *
     * @return integer 
     */
    public function getFolderId()
    {
        return $this->folder_id;
    }

    /**
     * Set file_id
     *
     * @param integer $fileId
     * @return Event
     */
    public function setFileId($fileId)
    {
        $this->file_id = $fileId;
        return $this;
    }

    /**
     * Get file_id
     *
     * @return integer 
     */
    public function getFileId()
    {
        return $this->file_id;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Event
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set user
     *
     * @param Entities\User $user
     * @return Event
     */
    public function setUser(\Entities\User $user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return Entities\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * JSON serialize
     * 
     * @return public object
     */
    public function jsonSerialize() {
        $excludes = ["user"];
        $json = [];
        foreach ($this as $key => $value) {
            if (!in_array($key, $excludes)) {
                if (is_object($value) && strstr(get_class($value), 'Doctrine') !== false) {
                    $collectionJson = array();
                    foreach ($value->getKeys() as $collectionKey) {
                        $collectionJson[] = $value->current();
                        $value->next();
                    }
                    $json[$key] = $collectionJson;
                }
                else
                    $json[$key] = $value;
            }
        }
        return $json;
    }
}