<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\User
 */
class User implements \JsonSerializable
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $email
     */
    private $email;

    /**
     * @var string $password
     */
    private $password;

    /**
     * @var datetime $registration_date
     */
    private $registration_date;

    /**
     * @var string $user_location_ip
     */
    private $user_location_ip;

    /**
     * @var boolean $is_admin
     */
    private $is_admin;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $plan_historys;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $folders;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $files;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $shared_with_me;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $shares;

    public function __construct()
    {
        $this->plan_historys = new \Doctrine\Common\Collections\ArrayCollection();
        $this->folders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
        $this->shared_with_me = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set registration_date
     *
     * @param datetime $registrationDate
     * @return User
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registration_date = $registrationDate;
        return $this;
    }

    /**
     * Get registration_date
     *
     * @return datetime 
     */
    public function getRegistrationDate()
    {
        return $this->registration_date;
    }

    /**
     * Set user_location_ip
     *
     * @param string $userLocationIp
     * @return User
     */
    public function setUserLocationIp($userLocationIp)
    {
        $this->user_location_ip = $userLocationIp;
        return $this;
    }

    /**
     * Get user_location_ip
     *
     * @return string 
     */
    public function getUserLocationIp()
    {
        return $this->user_location_ip;
    }

    /**
     * Set is_admin
     *
     * @param boolean $isAdmin
     * @return User
     */
    public function setIsAdmin($isAdmin)
    {
        $this->is_admin = $isAdmin;
        return $this;
    }

    /**
     * Get is_admin
     *
     * @return boolean 
     */
    public function getIsAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Add plan_historys
     *
     * @param Entities\PlanHistory $planHistorys
     * @return User
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
     * Add folders
     *
     * @param Entities\Folder $folders
     * @return User
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
     * Add files
     *
     * @param Entities\File $files
     * @return User
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
     * Add shared_with_me
     *
     * @param Entities\Share $sharedWithMe
     * @return User
     */
    public function addSharedWithMe(\Entities\Share $sharedWithMe)
    {
        $this->shared_with_me[] = $sharedWithMe;
        return $this;
    }

    /**
     * Remove shared_with_me
     *
     * @param Entities\Share $sharedWithMe
     */
    public function removeSharedWithMe(\Entities\Share $sharedWithMe)
    {
        $this->shared_with_me->removeElement($sharedWithMe);
    }

    /**
     * Get shared_with_me
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSharedWithMe()
    {
        return $this->shared_with_me;
    }

    /**
     * 
     * Has file named `$filename` in his root
     * 
     * @param String $filename
     * @return Boolean
     */
    public function hasFilenameInRoot($filename) {
        $files = $this->files->filter(function($e) use($filename) {
            return $e->getFolder() == null && strtolower($e->getName()) == strtolower($filename);
        });

        return (count($files) > 0);
    }

    /**
     * 
     * Has folder named `$foldername` in his root
     * 
     * @param String $foldername
     * @return Boolean
     */
    public function hasFoldernameInRoot($foldername) {
        $folders = $this->folders->filter(function($e) use($foldername) {
            return $e->getParent() == null && strtolower($e->getName()) == strtolower($foldername);
        });

        return (count($folders) > 0);
    }

    /**
    * Get all users
    *
    * @return Doctrine\Common\Collections\Collection
    */
    public static function getAllUsers()
    {
        $ci =& get_instance();
        $query = $ci->doctrine->em->createQueryBuilder()
                    ->add('select', 'u')
                    ->add('from', 'Entities\User u')
                    ->getQuery();

        $result = $query->getArrayResult();

        return $result;
    }

    /**
    * Get single user by id
    *
    * @return Entities\User
    */
    public static function getUserById($id=null)
    {
        if($id == null)
            return null;

        $ci =& get_instance();
        $result = $ci->doctrine->em->find('Entities\User', $id);

        return $result;
    }

    /**
    * Get active plan
    * @return Entities\PlanHistory
    */
    public function getActivePlanHistory() {
        $ci =& get_instance();

        $query = $ci->doctrine->em->createQueryBuilder()
                ->add('select', 'ph')
                ->add('from', 'Entities\PlanHistory ph')
                ->add('where', 'ph.user = :user AND ph.is_active = :active')
                ->setParameter('user', $this)
                ->setParameter('active', '1')
                ->getQuery();

        $ph = null;
        try {
            $ph = $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
        } catch (Exception $e) {
        }

        return $ph;
    }

    /**
    * Get space used by User
    * @return Integer
    */
    public function getStorageUsed() {
        $totalMbUsed = 0;

        foreach ($this->getFiles()->toArray() as $file) {
            $totalMbUsed += $file->getSize();
        }

        return $totalMbUsed;
    }

    /** 
    * Create key for user
    *
    * @return void
    */
    public function createKey() {
        $ci =& get_instance();
        $data = array(
           'key' => md5(time()),
           'level' => $this->getIsAdmin() ? 1 : 0,
           'ignore_limits' => 0,
           'date_created' => 0,
           'user_id' => $this->getId()
        );

        $ci->db->insert('keys', $data); 
        return $data['key'];
    }

    /**
    * Update key
    *
    * @return void
    */
    public function updateKey() {
        $ci =& get_instance();
        $ci->db->update('keys', array('level' => $this->getIsAdmin() ? 1 : 0), array('user_id' => $this->getId()));
    }

    /**
     * Get user by email
     * 
     * @return Entities\User
     */
    public static function getByEmail($email)
    {
        if (empty($email) || is_null($email)) {
            return null;
        }

        $ci =& get_instance();
        $query = $ci->doctrine->em->createQueryBuilder()
                    ->add('select', 'u')
                    ->add('from', 'Entities\User u')
                    ->add('where', 'u.email = :email')
                    ->setParameter('email', $email)
                    ->getQuery();

        try {
            $user = $query->getSingleResult();
        } catch (Doctrine\ORM\NoResultException $e) {
            return null;
        } catch (Exception $e) {
            return null;
        }
        return $user;
    }

    /**
     * Add shares
     *
     * @param Entities\Share $shares
     * @return User
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
     * JSON serialize
     * 
     * @return public object
     */
    public function jsonSerialize() {
        $excludes = ["plan_historys", "shares", "shared_with_me", "folders", "files"];
        $json = [];

        $this->folders = $this->folders->filter(function($_) {
            return ($_->getParent() == null);
        });

        $this->files = $this->files->filter(function($_) {
            return ($_->getFolder() == null);
        });

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
        $json['activePlanHistory'] = self::getActivePlanHistory();
        return $json;
    }

    /**
    * Get all users by period
    * mktime(0, 0, 0, date("m"), date("d"),   date("Y"));
    *
    * @return Doctrine\Common\Collections\Collection
    */
    public static function getAllUsersByPeriod($from, $to)
    {
        if(is_null($from) || is_null($to))
            return false;

        $ci =& get_instance();
        $query = $ci->doctrine->em->createQueryBuilder()
                    ->select('u')
                    ->add('from', 'Entities\User u')
                    ->add("where","u.registration_date >= '".date("Y-m-d",$from)."' AND u.registration_date <= '".date("Y-m-d",$to)."'")
                    ->getQuery();

        $result = $query->getArrayResult();

        return $result;
    }

     /**
    * Get all ip of users
    * mktime(0, 0, 0, date("m"), date("d"),   date("Y"));
    *
    * @return Doctrine\Common\Collections\Collection
    */
    public static function getAllIpUsers()
    {
        $ci =& get_instance();
        $query = $ci->doctrine->em->createQueryBuilder()
                    ->select('u.user_location_ip')
                    ->add('from', 'Entities\User u')
                    ->getQuery();

        $result = $query->getArrayResult();

        return $result;
    }
}