<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\File
 */
class File
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var datetime $creation_date
     */
    private $creation_date;

    /**
     * @var datetime $last_update_date
     */
    private $last_update_date;

    /**
     * @var string $relative_path
     */
    private $relative_path;

    /**
     * @var string $absolute_path
     */
    private $absolute_path;

    /**
     * @var boolean $is_public
     */
    private $is_public;

    /**
     * @var string $access_key
     */
    private $access_key;

    /**
     * @var string $public_link_path
     */
    private $public_link_path;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $data_histories;

    /**
     * @var Entities\User
     */
    private $user;

    /**
     * @var Entities\Folder
     */
    private $folder;

    public function __construct()
    {
        $this->data_histories = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set creation_date
     *
     * @param datetime $creationDate
     * @return File
     */
    public function setCreationDate($creationDate)
    {
        $this->creation_date = $creationDate;
        return $this;
    }

    /**
     * Get creation_date
     *
     * @return datetime 
     */
    public function getCreationDate()
    {
        return $this->creation_date;
    }

    /**
     * Set last_update_date
     *
     * @param datetime $lastUpdateDate
     * @return File
     */
    public function setLastUpdateDate($lastUpdateDate)
    {
        $this->last_update_date = $lastUpdateDate;
        return $this;
    }

    /**
     * Get last_update_date
     *
     * @return datetime 
     */
    public function getLastUpdateDate()
    {
        return $this->last_update_date;
    }

    /**
     * Set relative_path
     *
     * @param string $relativePath
     * @return File
     */
    public function setRelativePath($relativePath)
    {
        $this->relative_path = $relativePath;
        return $this;
    }

    /**
     * Get relative_path
     *
     * @return string 
     */
    public function getRelativePath()
    {
        return $this->relative_path;
    }

    /**
     * Set absolute_path
     *
     * @param string $absolutePath
     * @return File
     */
    public function setAbsolutePath($absolutePath)
    {
        $this->absolute_path = $absolutePath;
        return $this;
    }

    /**
     * Get absolute_path
     *
     * @return string 
     */
    public function getAbsolutePath()
    {
        return $this->absolute_path;
    }

    /**
     * Set is_public
     *
     * @param boolean $isPublic
     * @return File
     */
    public function setIsPublic($isPublic)
    {
        $this->is_public = $isPublic;
        return $this;
    }

    /**
     * Get is_public
     *
     * @return boolean 
     */
    public function getIsPublic()
    {
        return $this->is_public;
    }

    /**
     * Set access_key
     *
     * @param string $accessKey
     * @return File
     */
    public function setAccessKey($accessKey)
    {
        $this->access_key = $accessKey;
        return $this;
    }

    /**
     * Get access_key
     *
     * @return string 
     */
    public function getAccessKey()
    {
        return $this->access_key;
    }

    /**
     * Set public_link_path
     *
     * @param string $publicLinkPath
     * @return File
     */
    public function setPublicLinkPath($publicLinkPath)
    {
        $this->public_link_path = $publicLinkPath;
        return $this;
    }

    /**
     * Get public_link_path
     *
     * @return string 
     */
    public function getPublicLinkPath()
    {
        return $this->public_link_path;
    }

    /**
     * Add data_histories
     *
     * @param Entities\DataHistory $dataHistories
     * @return File
     */
    public function addDataHistorie(\Entities\DataHistory $dataHistories)
    {
        $this->data_histories[] = $dataHistories;
        return $this;
    }

    /**
     * Remove data_histories
     *
     * @param Entities\DataHistory $dataHistories
     */
    public function removeDataHistorie(\Entities\DataHistory $dataHistories)
    {
        $this->data_histories->removeElement($dataHistories);
    }

    /**
     * Get data_histories
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getDataHistories()
    {
        return $this->data_histories;
    }

    /**
     * Set user
     *
     * @param Entities\User $user
     * @return File
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
     * Set folder
     *
     * @param Entities\Folder $folder
     * @return File
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
}