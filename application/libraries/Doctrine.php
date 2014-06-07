<?php

require_once APPPATH . 'config/database.php';
require_once APPPATH . 'libraries/Doctrine/Common/ClassLoader.php';

class Doctrine
{
    public $em = null;
 
    public function __construct()
    { 
        $doctrineClassLoader = new \Doctrine\Common\ClassLoader('Doctrine', APPPATH.'libraries');
        $doctrineClassLoader->register();
 
        $symfonyClassLoader = new \Doctrine\Common\ClassLoader('Symfony', APPPATH.'libraries/Doctrine');
        $symfonyClassLoader->register();
 
        $entityClassLoader = new \Doctrine\Common\ClassLoader('Entities', APPPATH.'models');
        $entityClassLoader->register();
 
        $proxyClassLoader = new \Doctrine\Common\ClassLoader('Proxies', APPPATH.'models');
        $proxyClassLoader->register();
 
        $config = new \Doctrine\ORM\Configuration;
        $cache = new \Doctrine\Common\Cache\ArrayCache;
 
        $config->setProxyDir(APPPATH.'models/Proxies');
        $config->setProxyNamespace('Proxies');
        $config->setAutoGenerateProxyClasses(ENVIRONMENT == 'development');
 
        $yamlDriver = new \Doctrine\ORM\Mapping\Driver\YamlDriver(APPPATH.'models/Mappings');
        $config->setMetadataDriverImpl($yamlDriver);
 
        $CI =& get_instance();
        $CI->load->database();

        $connectionOptions = array(
            'driver' => 'pdo_mysql',
            'user' => $CI->db->username,
            'password' => $CI->db->password,
            'host' => $CI->db->hostname,
            'dbname' => $CI->db->database
        );

        /*
        $connectionOptions = array(
            'driver' => 'pdo_mysql',
            'user' => 'dev',
            'password' => 'yFJ25T3d28d96l1',
            'host' => 'sql.cubbyhole.name',
            'dbname' => 'cubbydev',
            'charset' => 'utf8',
            'driverOptions' => array(
                1002 => 'SET NAMES utf8'
            ),
            'attributes' => array(
                'default_table_collate' => 'utf8_unicode_ci',
                'default_table_charset' => 'utf8'
            )
        );
        */
 
        $em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);
 
        $this->em = $em;
    }
}