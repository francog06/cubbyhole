<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\Share
 */
class Share
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
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $users;

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
     * Add users
     *
     * @param Entities\User $users
     * @return Share
     */
    public function addUser(\Entities\User $users)
    {
        $this->users[] = $users;
        return $this;
    }

    /**
     * Remove users
     *
     * @param Entities\User $users
     */
    public function removeUser(\Entities\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }
}