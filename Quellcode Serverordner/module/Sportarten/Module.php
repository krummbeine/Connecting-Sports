<?php
namespace Sportarten;

use Sportarten\Model\Sportart;
use Sportarten\Model\SportartTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
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
            		
            	// SportartTable
                'Sportarten\Model\SportartTable' =>  function($sm) {
                    $tableGateway = $sm->get('SportartTableGateway');
                    $table = new SportartTable($tableGateway);
                    return $table;
                },
                'SportartTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Sportart());
                    return new TableGateway('sportart', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}