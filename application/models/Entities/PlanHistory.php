<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\PlanHistory
 */
class PlanHistory implements \JsonSerializable
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var datetime $subscription_plan_date
     */
    private $subscription_plan_date;

    /**
     * @var datetime $expiration_plan_date
     */
    private $expiration_plan_date;

    /**
     * @var boolean $is_active
     */
    private $is_active;

    /**
     * @var Entities\User
     */
    private $user;

    /**
     * @var Entities\Plan
     */
    private $plan;


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
     * Set subscription_plan_date
     *
     * @param datetime $subscriptionPlanDate
     * @return PlanHistory
     */
    public function setSubscriptionPlanDate($subscriptionPlanDate)
    {
        $this->subscription_plan_date = $subscriptionPlanDate;
        return $this;
    }

    /**
     * Get subscription_plan_date
     *
     * @return datetime 
     */
    public function getSubscriptionPlanDate()
    {
        return $this->subscription_plan_date;
    }

    /**
     * Set expiration_plan_date
     *
     * @param datetime $expirationPlanDate
     * @return PlanHistory
     */
    public function setExpirationPlanDate($expirationPlanDate)
    {
        $this->expiration_plan_date = $expirationPlanDate;
        return $this;
    }

    /**
     * Get expiration_plan_date
     *
     * @return datetime 
     */
    public function getExpirationPlanDate()
    {
        return $this->expiration_plan_date;
    }

    /**
     * Set is_active
     *
     * @param boolean $isActive
     * @return PlanHistory
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
     * Set user
     *
     * @param Entities\User $user
     * @return PlanHistory
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
     * Set plan
     *
     * @param Entities\Plan $plan
     * @return PlanHistory
     */
    public function setPlan(\Entities\Plan $plan = null)
    {
        $this->plan = $plan;
        return $this;
    }

    /**
     * Get plan
     *
     * @return Entities\Plan 
     */
    public function getPlan()
    {
        return $this->plan;
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
                    foreach ($value->getKeys() as $collectionKey) {
                        if (method_exists($value->current(), 'getId'))
                            $collectionJson[] = $value->current()->getId();
                        $value->next();
                    }
                    $json[$key] = $collectionJson;
                }
                else if ($key == "user")
                    $json[$key] = $value->getId();
                else
                    $json[$key] = $value;
            }
        }
        return $json;
    }
}