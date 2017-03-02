<?php
/**
 * Legt die Module fest, die das ZendFramework laden soll.
 * @author Helbig Christian www.krummbeine.de
 */
return array(
    'modules' => array(
        'Application',
        'Sportarten',
		'Sportart',
		'Sportevent',
		'Profil',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            './module',
            './vendor',
        ),
    ),
);
