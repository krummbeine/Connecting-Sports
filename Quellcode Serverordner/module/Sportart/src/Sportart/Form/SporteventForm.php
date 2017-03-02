<?php

namespace Sportart\Form;

use Zend\Form\Form;

/**
 * 
 * @author Helbig Christian www.krummbeine.de
 *
 */
class SporteventForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('sportevent');
        $this->setAttribute('method', 'post');
        
        $this->add(array(
            'name' => 'sportevent_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        
        // Soll nicht angezeigt werden in View!
        $this->add(array(
        		'name' => 'sportart_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
        
        $this->add(array(
        		'name' => 'mitglied_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
        
        $this->add(array(
            'name' => 'titel',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => _('Titel des Events '),
            ),
        ));
        
        $this->add(array(
            'name' => 'beschreibung',
            'attributes' => array(
                'type'  => 'textarea',
            ),
            'options' => array(
                'label' => _('Beschreibung des Events '),
            ),
        ));
        
        $this->add(array(
        		'name' => 'adresse',
        		'attributes' => array(
        				'type'  => 'text',
        		),
        		'options' => array(
        				'label' => _('Adresse (Strasse und Hausnummer) '),
        		),
        ));
        
        $this->add(array(
        		'name' => 'ort',
        		'attributes' => array(
        				'type'  => 'text',
        		),
        		'options' => array(
        				'label' => _('Ort (PLZ Ortsname) '),
        		),
        ));
   
        $this->add(array(
        		'name' => 'startdatum',
        		'attributes' => array(
        				'type'  => 'date',
        		),
        		'options' => array(
        				'label' => _('Startdatum '),
        				'format' => 'Y-m-d'
        		),
        ));
        
        $this->add(array(
        		'name' => 'enddatum',
        		'attributes' => array(
        				'type'  => 'date',		
        		),
        		'options' => array(
        				'label' => _('Enddatum '),
        				'format' => 'Y-m-d'
        		),
        ));

        $this->add(array(
            'name' => 'startuhrzeit',
            'attributes' => array(
                'type'  => 'time',
            ),
            'options' => array(
                'label' => _('Startuhrzeit (20:15) '),
                'format' => 'H:i'
            ),
        ));
        
        $this->add(array(
            'name' => 'enduhrzeit',
            'attributes' => array(
                'type'  => 'time',
            ),
            'options' => array(
                'label' => _('Enduhrzeit (21:15) '),
                'format' => 'H:i'
            ),
        ));
        
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
            	'class' => 'btn btn-primary'
            ),
        ));
    }
}