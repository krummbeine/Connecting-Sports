<?php
namespace Sportevent;

use Sportevent\Model\Kommentar;
use Sportevent\Model\KommentarTable;
use Sportevent\Model\Bewertung;
use Sportevent\Model\BewertungTable;
use Sportevent\Model\Zusage;
use Sportevent\Model\ZusageTable;
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
            	
            	// KommentarTable
                'Sportevent\Model\KommentarTable' =>  function($sm) {
                    $tableGateway = $sm->get('KommentarTableGateway');
                    $table = new KommentarTable($tableGateway);
                    return $table;
                },
                'KommentarTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Kommentar());
                    return new TableGateway('kommentar', $dbAdapter, null, $resultSetPrototype);
                },
                
                // ZusageTable
                'Sportevent\Model\ZusageTable' =>  function($sm) {
	                $tableGateway = $sm->get('ZusageTableGateway');
	                $table = new ZusageTable($tableGateway);
	                return $table;
                },
                'ZusageTableGateway' => function ($sm) {
	                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new Zusage());
	                return new TableGateway('v_mitglied_sportevent', $dbAdapter, null, $resultSetPrototype);
                },
                
                // BewertungTable
                'Sportevent\Model\BewertungTable' =>  function($sm) {
                	$tableGateway = $sm->get('BewertungTableGateway');
                	$table = new BewertungTable($tableGateway);
                	return $table;
                },
                'BewertungTableGateway' => function ($sm) {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new Bewertung());
                	return new TableGateway('bewertung', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}