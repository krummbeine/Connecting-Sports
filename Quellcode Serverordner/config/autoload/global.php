<?php
// config/autoload/global.php

/**
 * Datenbank-Verbindung definieren
 * Benutzername und Passwort in: database.local.php
 * @author Helbig Christian www.krummbeine.de
 */
return array(
    'db' => array(
        'driver'         => 'Pdo',
        'dsn'            => 'mysql:dbname=ConnectingSports;host=localhost',
        'username' => 'root',
        'password'=> '',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
                    => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
	// Verf&uuml;gbare Sprachen f&uuml;r unsere Anwendung: Deutsch und Englisch
	'locale' => array(
			'default' => 'en_US',
			'available'     => array(
					'de_DE' => 'Deutsch',
					'en_US' => 'English',
			),
	),
);