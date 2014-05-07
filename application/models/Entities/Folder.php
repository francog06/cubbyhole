<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\Folder
 */
class Folder implements \JsonSerializable
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
     * @var Entities\Share
     */
    private $share;

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
     * Set share
     *
     * @param Entities\Share $share
     * @return Folder
     */
    public function setShare(\Entities\Share $share = null)
    {
        $this->share = $share;
        return $this;
    }

    /**
     * Get share
     *
     * @return Entities\Share 
     */
    public function getShare()
    {
        return $this->share;
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

    /**
     * Get childrens
     * 
     *  @return array
     */
    public function getChildrens() {
        $ci =& get_instance();
        $queryFiles = $ci->doctrine->em->createQueryBuilder()
                    ->add('select', 'f')
                    ->add('from', 'Entities\File f')
                    ->add('where', 'folder_id = :folder')
                    ->setParameter('folder', $this)
                    ->getQuery();

        $files = $query->getArrayResult();

        $queryFolders = $ci->doctrine->em->createQueryBuilder()
                    ->add('select', 'f')
                    ->add('from', 'Entities\Folder f')
                    ->add('where', 'folder_id = :folder')
                    ->setParameter('folder', $this)
                    ->getQuery();

        $folders = $query->getArrayResult();

        return array_merge($files, $folders);
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

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $folders;

    /**
     * @var Entities\Folder
     */
    private $parent;


    /**
     * Add folders
     *
     * @param Entities\Folder $folders
     * @return Folder
     */
    public function addFolder(\Entities\Folder $folders)
    {
        $this->folders[] = $folders;
        return $this;
    }

    /**
     * Remove folders
     *
     * @param Entities\Folder $folders
     */
    public function removeFolder(\Entities\Folder $folders)
    {
        $this->folders->removeElement($folders);
    }

    /**
     * Get folders
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getFolders()
    {
        return $this->folders;
    }

    /**
     * Set parent
     *
     * @param Entities\Folder $parent
     * @return Folder
     */
    public function setParent(\Entities\Folder $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get parent
     *
     * @return Entities\Folder 
     */
    public function getParent()
    {
        return $this->parent;
    }
}