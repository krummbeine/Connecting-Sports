<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Hilfsfunktionen\Authorisation;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
    	// Den Translator initialisieren mit der Sprache, die in der Sitzung eingestellt wurde
    	// Bzw. Authorisation::getSprache() liefert bei keiner eingestellten Sprache automatisch die Standardsprache Deutsch
        $e->getApplication()->getServiceManager()->get('MvcTranslator')->setLocale(Authorisation::getSprache());

        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

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
                	// Namespace "Hilfsfunktionen" registrieren f&uuml;r alle Module
                	// Erm&ouml;glicht Aufruf von eigenen Hilfsfunktionen ohne diese f&uuml;r jedes Modul noch einmal
                	// schreiben zu m&uuml;ssen.
                	'Hilfsfunktionen' => __DIR__ . '/../../vendor/Hilfsfunktionen',
                ),
            ),
        );
    }
}
