<?php

namespace Entities;

use Doctrine\Common\Collections;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\Plan
 */
class Plan implements \JsonSerializable
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
     * @var float $price
     */
    private $price;

    /**
     * @var integer $duration
     */
    private $duration;

    /**
     * @var integer $usable_storage_space
     */
    private $usable_storage_space;

    /**
     * @var integer $max_bandwidth
     */
    private $max_bandwidth;

    /**
     * @var integer $daily_data_transfert
     */
    private $daily_data_transfert;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $plan_historys;

    public function __construct()
    {
        $this->plan_historys = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Plan
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
     * Set price
     *
     * @param float $price
     * @return Plan
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return Plan
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set usable_storage_space
     *
     * @param integer $usableStorageSpace
     * @return Plan
     */
    public function setUsableStorageSpace($usableStorageSpace)
    {
        $this->usable_storage_space = $usableStorageSpace;
        return $this;
    }

    /**
     * Get usable_storage_space
     *
     * @return integer 
     */
    public function getUsableStorageSpace()
    {
        return $this->usable_storage_space;
    }

    /**
     * Set max_bandwidth
     *
     * @param integer $maxBandwidth
     * @return Plan
     */
    public function setMaxBandwidth($maxBandwidth)
    {
        $this->max_bandwidth = $maxBandwidth;
        return $this;
    }

    /**
     * Get max_bandwidth
     *
     * @return integer 
     */
    public function getMaxBandwidth()
    {
        return $this->max_bandwidth;
    }

    /**
     * Set daily_data_transfert
     *
     * @param integer $dailyDataTransfert
     * @return Plan
     */
    public function setDailyDataTransfert($dailyDataTransfert)
    {
        $this->daily_data_transfert = $dailyDataTransfert;
        return $this;
    }

    /**
     * Get daily_data_transfert
     *
     * @return integer 
     */
    public function getDailyDataTransfert()
    {
        return $this->daily_data_transfert;
    }

    /**
     * Add plan_historys
     *
     * @param Entities\PlanHistory $planHistorys
     * @return Plan
     */
    public function addPlanHistory(\Entities\PlanHistory $planHistorys)
    {
        $this->plan_historys[] = $planHistorys;
        return $this;
    }

    /**
     * Remove plan_historys
     *
     * @param Entities\PlanHistory $planHistorys
     */
    public function removePlanHistory(\Entities\PlanHistory $planHistorys)
    {
        $this->plan_historys->removeElement($planHistorys);
    }

    /**
     * Get plan_historys
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPlanHistorys()
    {
        return $this->plan_historys;
    }

    /**
     * JSON serialize
     * 
     * @return public object
     */
    public function jsonSerialize() {
        $excludes = ["plan_historys"];
        $json = [];
        foreach ($this as $key => $value) {
            if (!in_array($key, $excludes)) {
                if (is_object($value) && strstr(get_class($value), 'Doctrine') !== false) {
                    $collectionJson = array();
                    foreach ($value->getKeys() as $collectionKey) {
                        if (method_exists($value->current(), 'getId'))
                            $collectionJson[] = $value->current()->getId();
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
     * @var string $description
     */
    private $description;


    /**
     * Set description
     *
     * @param string $description
     * @return Plan
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;

    }

    /**
     * Get default plan
     *
     * @return Entities\Plan
     */
    public static function getDefaultPlan() {
        $ci =& get_instance();
        $query = $ci->doctrine->em->createQueryBuilder()
                    ->add('select', 'p')
                    ->add('from', 'Entities\Plan p')
                    ->where('p.is_default = 1')
                    ->getQuery();

        $plan = null;
        try {
            $plan = $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
        } catch (Exception $e) {
        }

        return $plan;
    }

    /**
     * Get all plans
     * 
     *  @return Doctrine\Common\Collections\Collection 
     */
    public function getAllPlans() {
        $ci =& get_instance();
        $query = $ci->doctrine->em->createQueryBuilder()
                    ->add('select', 'p')
                    ->add('from', 'Entities\Plan p')
                    ->where("p.is_active = 1")
                    ->getQuery();

        $result = $query->getArrayResult();

        return $result;
    }

    /**
     * Get all plans
     * 
     *  @return Doctrine\Common\Collections\Collection 
     */
    public function getAllPlansAdmin() {
        $ci =& get_instance();
        $query = $ci->doctrine->em->createQueryBuilder()
                    ->add('select', 'p')
                    ->add('from', 'Entities\Plan p')
                    ->getQuery();

        $result = $query->getArrayResult();

        return $result;
    }
    /**
     * @var boolean $is_default
     */
    private $is_default;


    /**
     * Set is_default
     *
     * @param boolean $isDefault
     * @return Plan
     */
    public function setIsDefault($isDefault)
    {
        $this->is_default = $isDefault;
        return $this;
    }

    /**
     * Get is_default
     *
     * @return boolean 
     */
    public function getIsDefault()
    {
        return $this->is_default;
    }
    /**
     * @var boolean $is_active
     */
    private $is_active;


    /**
     * Set is_active
     *
     * @param boolean $isActive
     * @return Plan
     */
    public function setIsActive($isActive)
    {
        $this->is_active = $isActive;
        return $this;
    }

    /**
     * Get is_active
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Get plan by id
     * 
     *  @return Entities\Plan 
     */
    public function getPlanById($id=null) {
       if($id == null)
            return null;

        $ci =& get_instance();
        $plan = $ci->doctrine->em->find("Entities\Plan", $id);
        return $plan;
               
    }

    /**
     * Get plan by id
     * 
     *  @return Doctrine\Common\Collections\Collection  
     */
    public function getTotalDownloads($from,$to){
         if(is_null($from) || is_null($to))
            return false;

        $ci =& get_instance();
        $query = $ci->doctrine->em->createQueryBuilder()
                    ->select('d')
                    ->add('from', 'Entities\DataHistory d')
                    ->add("where","d.date >= '".date("Y-m-d",$from)."' AND d.date <= '".date("Y-m-d",$to)."'")
                    ->add("orderBy", "d.date ASC")
                    ->getQuery();

        $result = new Collections\ArrayCollection($query->getResult());
        $plan = $this;

        $download = $result->filter(function($e) use ($plan) {
            return $e->getFile()->getUser()->getActivePlanHistory()->getPlan() == $plan; 
        });

        return $download;
    }

    /**
     * Get plan by id
     * 
     *  @return Doctrine\Common\Collections\Collection  
     */
    public function getTotalShares($from,$to){
         if(is_null($from) || is_null($to))
            return false;

        $ci =& get_instance();
        $query = $ci->doctrine->em->createQueryBuilder()
                    ->select('s')
                    ->add('from', 'Entities\Share s')
                    ->add("where","s.date >= '".date("Y-m-d",$from)."' AND s.date <= '".date("Y-m-d",$to)."'")
                    ->add("orderBy", "s.date ASC")
                    ->getQuery();

        $result = new Collections\ArrayCollection($query->getResult());
        $plan = $this;

        $shares = $result->filter(function($e) use ($plan) {
            return $e->getOwner()->getActivePlanHistory()->getPlan() == $plan; 
        });

        return $shares;
    }
}