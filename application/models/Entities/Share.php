<?php

namespace Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities\Share
 */
class Share implements \JsonSerializable
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var datetime $date
     */
    private $date;

    /**
     * @var Entities\Folder
     */
    private $folder;

    /**
     * @var Entities\File
     */
    private $file;

    /**
     * @var Entities\User
     */
    private $owner;

    /**
     * @var Entities\User
     */
    private $user;

    /**
     * @var boolean $is_writable
     */
    private $is_writable;

    public function __construct()
    {
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
     * Set date
     *
     * @param datetime $date
     * @return Share
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return datetime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set folder
     *
     * @param Entities\Folder $folder
     * @return Share
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

    /**
     * Set file
     *
     * @param Entities\File $file
     * @return Share
     */
    public function setFile(\Entities\File $file = null)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Get file
     *
     * @return Entities\File 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set owner
     *
     * @param Entities\User $owner
     * @return Share
     */
    public function setOwner(\Entities\User $owner = null)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * Get owner
     *
     * @return Entities\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set user
     *
     * @param Entities\User $user
     * @return Share
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
     * Set is_writable
     *
     * @param boolean $isWritable
     * @return Share
     */
    public function setIsWritable($isWritable)
    {
        $this->is_writable = $isWritable;
        return $this;
    }

    /**
     * Get is_writable
     *
     * @return boolean 
     */
    public function getIsWritable()
    {
        return $this->is_writable;
    }

    /**
     * Get if file is already shared
     * 
     * @param entity id, email, type
     * @return boolean
     */
    public static function entityAlreadyShared($entity_id, $user_id, $type = "file") {
        if (method_exists(get_class(), $type . "AlreadyShared"))
            return self::{$type . "AlreadyShared"}($entity_id, $user_id);
        else
            throw new Exception("Type not allowed.", 1);
            
    }

    /**
     * Know if file already shared to this email
     * 
     *  @return boolean
     */
    public static function fileAlreadyShared($file_id, $user_id) {
        $ci =& get_instance();
        $query = $ci->doctrine->em->createQueryBuilder()
                    ->add('select', 's')
                    ->add('from', 'Entities\Share s')
                    ->add('where', 's.user_id = :user_id AND s.file_id = :file_id')
                    ->setParameter('user_id', $user_id)
                    ->setParameter('file_id', $file_id)
                    ->getQuery();

        try {
            $share = $query->getSingleResult();
        } catch (Doctrine\ORM\NoResultException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Know if folder already shared to this email
     * 
     *  @return boolean
     */
    public static function folderAlreadyShared($folder_id, $user_id) {
        $ci =& get_instance();
        $query = $ci->doctrine->em->createQueryBuilder()
                    ->add('select', 's')
                    ->add('from', 'Entities\Share s')
                    ->add('where', 's.user_id = :user_id AND s.folder_id = :folder_id')
                    ->setParameter('user_id', $user_id)
                    ->setParameter('folder_id', $folder_id)
                    ->getQuery();

        try {
            $share = $query->getSingleResult();
        } catch (Doctrine\ORM\NoResultException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * JSON serialize
     * 
     * @return public object
     */
    public function jsonSerialize() {
        $excludes = ["user", "owner"];
        $json = [];

        foreach ($this as $key => $value) {
            if (!in_array($key, $excludes)) {
                if (is_object($value) && strstr(get_class($value), 'Doctrine') !== false) {
                    $collectionJson = array();
                    if (!is_null($value)) {
                        foreach ($value->getKeys() as $collectionKey) {
                            $collectionJson[] = $value->current();
                            $value->next();
                        }
                    }
                    $json[$key] = $collectionJson;
                }
                else
                    $json[$key] = $value;
            }
        }

        $json["owner"] = array('id' => $this->owner->getId(), 'email' => $this->owner->getEmail());
        $json["user"] = array('id' => $this->user->getId(), 'email' => $this->user->getEmail());
        return $json;
    }
}