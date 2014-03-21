<?php

namespace Proxies\__CG__\Entities;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class File extends \Entities\File implements \Doctrine\ORM\Proxy\Proxy
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

    public function setRelativePath($relativePath)
    {
        $this->__load();
        return parent::setRelativePath($relativePath);
    }

    public function getRelativePath()
    {
        $this->__load();
        return parent::getRelativePath();
    }

    public function setAbsolutePath($absolutePath)
    {
        $this->__load();
        return parent::setAbsolutePath($absolutePath);
    }

    public function getAbsolutePath()
    {
        $this->__load();
        return parent::getAbsolutePath();
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

    public function setPublicLinkPath($publicLinkPath)
    {
        $this->__load();
        return parent::setPublicLinkPath($publicLinkPath);
    }

    public function getPublicLinkPath()
    {
        $this->__load();
        return parent::getPublicLinkPath();
    }

    public function addDataHistorie(\Entities\DataHistory $dataHistories)
    {
        $this->__load();
        return parent::addDataHistorie($dataHistories);
    }

    public function removeDataHistorie(\Entities\DataHistory $dataHistories)
    {
        $this->__load();
        return parent::removeDataHistorie($dataHistories);
    }

    public function getDataHistories()
    {
        $this->__load();
        return parent::getDataHistories();
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

    public function setFolder(\Entities\Folder $folder = NULL)
    {
        $this->__load();
        return parent::setFolder($folder);
    }

    public function getFolder()
    {
        $this->__load();
        return parent::getFolder();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'name', 'creation_date', 'last_update_date', 'relative_path', 'absolute_path', 'is_public', 'access_key', 'public_link_path', 'data_histories', 'user', 'folder');
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