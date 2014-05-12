<?php

namespace Proxies\__CG__\Entities;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Plan extends \Entities\Plan implements \Doctrine\ORM\Proxy\Proxy
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

    public function setPrice($price)
    {
        $this->__load();
        return parent::setPrice($price);
    }

    public function getPrice()
    {
        $this->__load();
        return parent::getPrice();
    }

    public function setDuration($duration)
    {
        $this->__load();
        return parent::setDuration($duration);
    }

    public function getDuration()
    {
        $this->__load();
        return parent::getDuration();
    }

    public function setUsableStorageSpace($usableStorageSpace)
    {
        $this->__load();
        return parent::setUsableStorageSpace($usableStorageSpace);
    }

    public function getUsableStorageSpace()
    {
        $this->__load();
        return parent::getUsableStorageSpace();
    }

    public function setMaxBandwidth($maxBandwidth)
    {
        $this->__load();
        return parent::setMaxBandwidth($maxBandwidth);
    }

    public function getMaxBandwidth()
    {
        $this->__load();
        return parent::getMaxBandwidth();
    }

    public function setDailyDataTransfert($dailyDataTransfert)
    {
        $this->__load();
        return parent::setDailyDataTransfert($dailyDataTransfert);
    }

    public function getDailyDataTransfert()
    {
        $this->__load();
        return parent::getDailyDataTransfert();
    }

    public function addPlanHistory(\Entities\PlanHistory $planHistorys)
    {
        $this->__load();
        return parent::addPlanHistory($planHistorys);
    }

    public function removePlanHistory(\Entities\PlanHistory $planHistorys)
    {
        $this->__load();
        return parent::removePlanHistory($planHistorys);
    }

    public function getPlanHistorys()
    {
        $this->__load();
        return parent::getPlanHistorys();
    }

    public function jsonSerialize()
    {
        $this->__load();
        return parent::jsonSerialize();
    }

    public function setDescription($description)
    {
        $this->__load();
        return parent::setDescription($description);
    }

    public function getDescription()
    {
        $this->__load();
        return parent::getDescription();
    }

    public function getAllPlans()
    {
        $this->__load();
        return parent::getAllPlans();
    }

    public function getAllPlansAdmin()
    {
        $this->__load();
        return parent::getAllPlansAdmin();
    }

    public function setIsDefault($isDefault)
    {
        $this->__load();
        return parent::setIsDefault($isDefault);
    }

    public function getIsDefault()
    {
        $this->__load();
        return parent::getIsDefault();
    }

    public function setIsActive($isActive)
    {
        $this->__load();
        return parent::setIsActive($isActive);
    }

    public function getIsActive()
    {
        $this->__load();
        return parent::getIsActive();
    }

    public function getPlanById($id = NULL)
    {
        $this->__load();
        return parent::getPlanById($id);
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'name', 'description', 'price', 'duration', 'usable_storage_space', 'max_bandwidth', 'daily_data_transfert', 'is_default', 'is_active', 'plan_historys');
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