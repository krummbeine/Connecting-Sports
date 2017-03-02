<?php
namespace Sportart;

use Sportart\Model\Sportevent;
use Sportart\Model\SporteventTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ServiceProviderInterface;


class Module implements ServiceProviderInterface
 {
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }



    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

     public function getServiceConfig()
     {
         return array(
             'factories' => array(
             		
             	// SporteventTable
                 'Sportart\Model\SporteventTable' =>  function($sm) {
                     $tableGateway = $sm->get('SporteventTableGateway');
                     $table = new SporteventTable($tableGateway);
                     return $table;
                 },
                 'SporteventTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new Sportevent());
                     //verwendete table aufrufen
                     return new TableGateway('sportevent', $dbAdapter, null, $resultSetPrototype);
                 },
             ),
         );
     }



 }