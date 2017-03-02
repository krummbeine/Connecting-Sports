<?php
namespace Profil\Form;

use Zend\Form\Form;

class LoginForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('mitglied');
        
        $this->setAttribute('method', 'post');
        
        // Soll nicht angezeigt werden
        $this->add(array(
            'name' => 'mitglied_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        
        // Soll im Login-Formular angezeigt werden
        $this->add(array(
        		'name' => 'pseudonym',
        		'attributes' => array(
        				'type'  => 'text',
        		),
        		'options' => array(
        				'label' => _('Pseudonym '),
        		),
        		'validators' => array(
        				array('StringLength', FALSE, array(3, 255)),
        				array('Regex', FALSE, array('pattern' => '/^[\w.-]*$/'))
        		)
        ));
        
        // Soll nicht angezeigt werden
        $this->add(array(
        		'name' => 'facebook_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
      
        // Soll im Formular Login angezeigt werden
        $this->add(array(
            'name' => 'passwort',
            'attributes' => array(
                'type'  => 'password',
            ),
            'options' => array(
                'label' => _('Passwort '),
            ),
        ));
        
        // Soll nicht angezeigt werden
        $this->add(array(
        		'name' => 'geburtstag',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
        
        // Soll nicht angezeigt werden
        $this->add(array(
        		'name' => 'meine_stadt',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => _('Go'),
                'id' => 'submitbutton',
            ),
        ));
    }
}