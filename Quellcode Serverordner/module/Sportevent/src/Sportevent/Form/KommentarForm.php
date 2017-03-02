<?php
// module/Sportevent/src/Sportevent/Form/KommentarForm.php:
namespace Sportevent\Form;

use Zend\Form\Form;

class KommentarForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('kommentar');
        
        $this->setAttribute('method', 'post');
        
        // kommentar_id
        $this->add(array(
            'name' => 'kommentar_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
        
        // mitglied_id
        $this->add(array(
        		'name' => 'mitglied_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
        
        // sportevent_id
        $this->add(array(
        		'name' => 'sportevent_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
        
        // datum
        $this->add(array(
        		'name' => 'datum',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
        
        // text
        $this->add(array(
            'name' => 'text',
            'attributes' => array(
                'type'  => 'textarea',
            ),
            'options' => array(
                'label' => _('Kommentartext '),
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