<?php

namespace Proxies\__CG__\Entities;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Folder extends \Entities\Folder implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int) $this->_identifier["id"];
        }
        $this->__load();
        return parent::getId();
    }

    public function setName($name)
    {
        $this->__load();
        return parent::setName($name);
    }

    public function getName()
    {
        $this->__load();
        return parent::getName();
    }

    public function setCreationDate($creationDate)
    {
        $this->__load();
        return parent::setCreationDate($creationDate);
    }

    public function getCreationDate()
    {
        $this->__load();
        return parent::getCreationDate();
    }

    public function setLastUpdateDate($lastUpdateDate)
    {
        $this->__load();
        return parent::setLastUpdateDate($lastUpdateDate);
    }

    public function getLastUpdateDate()
    {
        $this->__load();
        return parent::getLastUpdateDate();
    }

    public function setIsPublic($isPublic)
    {
        $this->__load();
        return parent::setIsPublic($isPublic);
    }

    public function getIsPublic()
    {
        $this->__load();
        return parent::getIsPublic();
    }

    public function setAccessKey($accessKey)
    {
        $this->__load();
        return parent::setAccessKey($accessKey);
    }

    public function getAccessKey()
    {
        $this->__load();
        return parent::getAccessKey();
    }

    public function addFile(\Entities\File $files)
    {
        $this->__load();
        return parent::addFile($files);
    }

    public function removeFile(\Entities\File $files)
    {
        $this->__load();
        return parent::removeFile($files);
    }

    public function getFiles()
    {
        $this->__load();
        return parent::getFiles();
    }

    public function setUser(\Entities\User $user = NULL)
    {
        $this->__load();
        return parent::setUser($user);
    }

    public function getUser()
    {
        $this->__load();
        return parent::getUser();
    }

    public function addFolder(\Entities\Folder $folders)
    {
        $this->__load();
        return parent::addFolder($folders);
    }

    public function removeFolder(\Entities\Folder $folders)
    {
        $this->__load();
        return parent::removeFolder($folders);
    }

    public function getFolders()
    {
        $this->__load();
        return parent::getFolders();
    }

    public function setParent(\Entities\Folder $parent = NULL)
    {
        $this->__load();
        return parent::setParent($parent);
    }

    public function getParent()
    {
        $this->__load();
        return parent::getParent();
    }

    public function addShare(\Entities\Share $shares)
    {
        $this->__load();
        return parent::addShare($shares);
    }

    public function removeShare(\Entities\Share $shares)
    {
        $this->__load();
        return parent::removeShare($shares);
    }

    public function getShares()
    {
        $this->__load();
        return parent::getShares();
    }

    public function hasFilenameAlreadyTaken($filename)
    {
        $this->__load();
        return parent::hasFilenameAlreadyTaken($filename);
    }

    public function hasFoldernameAlreadyTaken($foldername)
    {
        $this->__load();
        return parent::hasFoldernameAlreadyTaken($foldername);
    }

    public function isSharedWith($user)
    {
        $this->__load();
        return parent::isSharedWith($user);
    }

    public function recursiveShare($shareToApply, $sharedTo = NULL)
    {
        $this->__load();
        return parent::recursiveShare($shareToApply, $sharedTo);
    }

    public function jsonSerialize()
    {
        $this->__load();
        return parent::jsonSerialize();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'name', 'creation_date', 'last_update_date', 'is_public', 'access_key', 'files', 'folders', 'shares', 'user', 'parent');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields AS $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}