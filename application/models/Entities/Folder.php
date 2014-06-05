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
     * @var boolean $is_public
     */
    private $is_public;

    /**
     * @var string $access_key
     */
    private $access_key;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $files;

    /**
     * @var Entities\User
     */
    private $user;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $shares;

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

    /**
     * Add shares
     *
     * @param Entities\Share $shares
     * @return Folder
     */
    public function addShare(\Entities\Share $shares)
    {
        $this->shares[] = $shares;
        return $this;
    }

    /**
     * Remove shares
     *
     * @param Entities\Share $shares
     */
    public function removeShare(\Entities\Share $shares)
    {
        $this->shares->removeElement($shares);
    }

    /**
     * Get shares
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getShares()
    {
        return $this->shares;
    }

    /**
     * Recursively apply share
     * 
     * @return Entities\Folder
     */
    public function recursiveShare($shareToApply, $sharedTo = null) {
        $ci =& get_instance();

        // Apply share to file
        foreach ($this->getFiles()->toArray() as $file) {
            if (is_null($shareToApply)) {
                if ( ($share = $file->searchShareByUser($sharedTo)) !== false) {
                    $file->removeShare($share);
                    $ci->doctrine->em->remove($share);
                }
            }
            else {
                $share = new Share;

                $share->setIsWritable($shareToApply->getIsWritable());
                $share->setUser($shareToApply->getUser());
                $share->setOwner($shareToApply->getOwner());
                $share->setDate(new \DateTime("now", new \DateTimeZone("Europe/Berlin")));
                $share->setFile($file);
                $ci->doctrine->em->persist($share);
            }
        }

        // Apply share to folder
        foreach ($this->getFolders() as $folder) {
            $share = new Share;

            $share->setIsWritable($shareToApply->getIsWritable());
            $share->setUser($shareToApply->getUser());
            $share->setOwner($shareToApply->getOwner());
            $share->setFolder($folder);
            $share->setDate(new \DateTime("now", new \DateTimeZone("Europe/Berlin")));
            $ci->doctrine->em->persist($share);
            $folder->setShare($share);
            $folder->recursiveShare(null, $sharedTo);
        }
        $ci->doctrine->em->persist($this);
        return $this;
    }

    /**
     * JSON serialize
     * 
     * @return public object
     */
    public function jsonSerialize() {
        $excludes = ["user", "parent", "folders", "shares"];
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
            if ($key == "folders") {
                $ci =& get_instance();
                if (is_null($this->parent) || ($ci->uri->segment(2) == "folder" && $ci->uri->segment(3) == "details" && $ci->uri->segment(4) == $this->id) ) {
                    $collectionJson = array();
                    if (!is_null($value)) {
                        foreach ($value->getKeys() as $collectionKey) {
                            $collectionJson[] = $value->current();
                            $value->next();
                        }
                    }
                    $json[$key] = $collectionJson;
                }
            }
        }
        $json["parent"] = (!is_null($this->parent) ? $this->parent->getId() : null);
        return $json;
    }
}