<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\Share
 */
class Share implements \JsonSerializable
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
     * @var Entities\Folder
     */
    private $folder;

    /**
     * @var Entities\File
     */
    private $file;

    /**
     * @var Entities\User
     */
    private $owner;

    /**
     * @var Entities\User
     */
    private $user;

    /**
     * @var boolean $read
     */
    private $read;

    /**
     * @var boolean $write
     */
    private $write;

    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set date
     *
     * @param datetime $date
     * @return Share
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
     * Set folder
     *
     * @param Entities\Folder $folder
     * @return Share
     */
    public function setFolder(\Entities\Folder $folder = null)
    {
        $this->folder = $folder;
        return $this;
    }

    /**
     * Get folder
     *
     * @return Entities\Folder 
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Set file
     *
     * @param Entities\File $file
     * @return Share
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

    /**
     * Set owner
     *
     * @param Entities\User $owner
     * @return Share
     */
    public function setOwner(\Entities\User $owner = null)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * Get owner
     *
     * @return Entities\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set users
     * 
     * @param \Doctrine\Common\Collections\ArrayCollection
     * @return Share
     */
    public function setUsers(\Doctrine\Common\Collections\ArrayCollection $users)
    {
        $this->users = $users;
    }

    /**
     * Set read
     *
     * @param boolean $read
     * @return Share
     */
    public function setRead($read)
    {
        $this->read = $read;
        return $this;
    }

    /**
     * Get read
     *
     * @return boolean 
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * Set write
     *
     * @param boolean $write
     * @return Share
     */
    public function setWrite($write)
    {
        $this->write = $write;
        return $this;
    }

    /**
     * Get write
     *
     * @return boolean 
     */
    public function getWrite()
    {
        return $this->write;
    }

    /**
     * Set user
     *
     * @param Entities\User $user
     * @return Share
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
        $excludes = [];
        $json = [];

        foreach ($this as $key => $value) {
            if (!in_array($key, $excludes)) {
                if (is_object($value) && strstr(get_class($value), 'Doctrine') !== false) {
                    $collectionJson = array();
                    if (!is_null($value)) {
                        foreach ($value->getKeys() as $collectionKey) {
                            $collectionJson[] = $value->current();
                            $value->next();
                        }
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