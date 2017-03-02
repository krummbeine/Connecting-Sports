<?php
return array(
		
	// Controller-Festlegungen
    'controllers' => array(
        'invokables' => array(
            'Sportevent\Controller\Kommentar' => 'Sportevent\Controller\KommentarController',
        	'Sportevent\Controller\Sportevent' => 'Sportevent\Controller\SporteventController',
        	'Sportevent\Controller\Zusage' => 'Sportevent\Controller\ZusageController',
        	'Sportevent\Controller\Bewertung' => 'Sportevent\Controller\BewertungController',
        	'Sportevent\Controller\Chat' => 'Sportevent\Controller\ChatController',
        ),
    ),
		
	// Routen-Festlegungen
    'router' => array(
        'routes' => array(
        	// Sportevent
            'sportevent' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/sportevent[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Sportevent\Controller\Sportevent',
                        'action'     => 'index',
                    ),
                ),
            ),
        	// Kommentar
        	'kommentar' => array(
        			'type'    => 'segment',
        			'options' => array(
        					'route'    => '/kommentar[/:action][/:id]',
        					'constraints' => array(
        							'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        							'id'     => '[0-9]+',
        					),
        					'defaults' => array(
        							'controller' => 'Sportevent\Controller\Kommentar',
        					),
        			),
        	),
        	// Zusage
        	'zusage' => array(
        			'type'    => 'segment',
       				'options' => array(
       						'route'    => '/zusage[/:action]',
        					'constraints' => array(
       								'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        					),
        					'defaults' => array(
        							'controller' => 'Sportevent\Controller\Zusage',
       						),
       				),
       		),
        	// Bewertung
        	'bewertung' => array(
        			'type'    => 'segment',
       				'options' => array(
        					'route'    => '/bewertung[/:action][/:id]',
        					'constraints' => array(
        							'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
       								'id'     => '[0-9]+',
       						),
       						'defaults' => array(
       								'controller' => 'Sportevent\Controller\Bewertung',
       						),
       				),
       		),
        	// Chat
        	'chat' => array(
        			'type'    => 'segment',
        			'options' => array(
        					'route'    => '/chat[/:action][/:id]',
        					'constraints' => array(
        							'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        							'id'     => '[0-9]+',
        					),
        					'defaults' => array(
        							'controller' => 'Sportevent\Controller\Chat',
        							'action'     => 'index',
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
            'kommentar' => __DIR__ . '/../view',
        	'sportevent' => __DIR__ . '/../view',
        	'chat' => __DIR__ . '/../view',
        ),
    ),
		
);