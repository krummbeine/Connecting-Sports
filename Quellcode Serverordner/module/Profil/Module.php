<?php
namespace Profil;

use Profil\Model\Mitglied;
use Profil\Model\MitgliedTable;
use Profil\Model\Lieblingssportart;
use Profil\Model\LieblingssportartTable;
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
            	
            	// Mitglied
                'Profil\Model\MitgliedTable' =>  function($sm) {
                    $tableGateway = $sm->get('MitgliedTableGateway');
                    $table = new MitgliedTable($tableGateway);
                    return $table;
                },
                'MitgliedTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Mitglied());

                    // Tabellenname der Datenbank 'mitglied'
                    return new TableGateway('mitglied', $dbAdapter, null, $resultSetPrototype);
                },
                
                // Lieblingssportart
                'Profil\Model\LieblingssportartTable' =>  function($sm) {
	                $tableGateway = $sm->get('LieblingssportartTableGateway');
	                $table = new LieblingssportartTable($tableGateway);
	                return $table;
                },
                'LieblingssportartTableGateway' => function ($sm) {
	                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
	                $resultSetPrototype = new ResultSet();
	                $resultSetPrototype->setArrayObjectPrototype(new Lieblingssportart());
	                
	                // Tabellenname der Datenbank 'v_sportart_mitglied'
	                return new TableGateway('v_sportart_mitglied', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}