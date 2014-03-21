<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\Folder
 */
class Folder
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
    private $files;

    /**
     * @var Entities\User
     */
    private $user;

    public function __construct()
    {
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Folder
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
     * @return Folder
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
     * @return Folder
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
     * @return Folder
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
     * @return Folder
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
     * @return Folder
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
     * @return Folder
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
     * @return Folder
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
     * Add files
     *
     * @param Entities\File $files
     * @return Folder
     */
    public function addFile(\Entities\File $files)
    {
        $this->files[] = $files;
        return $this;
    }

    /**
     * Remove files
     *
     * @param Entities\File $files
     */
    public function removeFile(\Entities\File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set user
     *
     * @param Entities\User $user
     * @return Folder
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
}