<?php
//Controller array
return array(
    'controllers' => array(
        'invokables' => array(
            'Sportart\Controller\Sportart' => 'Sportart\Controller\SportartController',
        ),
    ),
    // The following section is new and should be added to your file
    // router festlegen mit segment type
    'router' => array(
        'routes' => array(
            'sportart' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/sportart[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Sportart\Controller\Sportart',
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
            'sportart' => __DIR__ . '/../view',
        ),
    ),
);