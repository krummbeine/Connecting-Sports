<?php
namespace Profil\Form;

use Zend\Form\Form;

class LieblingssportartForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('kommentar');
        
        $this->setAttribute('method', 'post');
        
        // mitglied_id
        $this->add(array(
        		'name' => 'mitglied_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
        
        // sportart_id
        $this->add(array(
        		'name' => 'sportart_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
        
        // beschreibung
        $this->add(array(
            'name' => 'beschreibung',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => _('Beschreibung '),
            ),
        ));
        
        // level
        $this->add(array(
        		'name' => 'level',
        		'attributes' => array(
        				'type'  => 'number',
        		),
        		'options' => array(
        				'label' => _('Level (0 Anfaenger, 2 Gelegenheitsspieler, 4 Profisportler) '),
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