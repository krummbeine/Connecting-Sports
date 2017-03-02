<?php
return array(
		
	// Controller festlegen
    'controllers' => array(
        'invokables' => array(
            'Profil\Controller\Profil' => 'Profil\Controller\ProfilController',
        	'Profil\Controller\Login' => 'Profil\Controller\LoginController',
        	'Profil\Controller\Registrieren' => 'Profil\Controller\RegistrierenController',
        	'Profil\Controller\Lieblingssportart' => 'Profil\Controller\LieblingssportartController',
        ),
    ),

	// Routen festlegen
    'router' => array(
        'routes' => array(
            'profil' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/profil[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Profil\Controller\Profil',
                        'action'     => 'index',
                    ),
                ),
            ),
        	'login' => array(
        			'type'    => 'segment',
       				'options' => array(
       						'route'    => '/login[/:action][/:id]',
       						'constraints' => array(
       								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
       								'id'     => '[0-9]+',
       						),
       						'defaults' => array(
       								'controller' => 'Profil\Controller\Login',
        							'action'     => 'index',
       						),
       				),
       		),
       		'registrieren' => array(
        			'type'    => 'segment',
        			'options' => array(
        					'route'    => '/registrieren[/:action][/:id]',
        					'constraints' => array(
       								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
       								'id'     => '[0-9]+',
       						),
       						'defaults' => array(
        							'controller' => 'Profil\Controller\Registrieren',
        							'action'     => 'index',
        					),
        			),
        	),
        	'lieblingssportart' => array(
        			'type'    => 'segment',
        			'options' => array(
        					'route'    => '/lieblingssportart[/:action][/:id]',
        					'constraints' => array(
        							'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        							'id'     => '[0-9]+',
        					),
        					'defaults' => array(
        							'controller' => 'Profil\Controller\Lieblingssportart',
        					),
        			),
        	),
        ),
    ),
		
	'service_manager' => array(
		'factories' => array(
			'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
		),
	),
		
	'translator' => array(
		'locale' => 'en_US',
		'translation_file_patterns' => array(
			array(
				'type'     => 'gettext',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.mo',
			),
		),
	),

    'view_manager' => array(
        'template_path_stack' => array(
            'profil' => __DIR__ . '/../view',
        	'login' => __DIR__ . '/../view',
        	'registrieren' => __DIR__ . '/../view',
        	'lieblingssportart' => __DIR__ . '/../view',
        ),
    ),
);