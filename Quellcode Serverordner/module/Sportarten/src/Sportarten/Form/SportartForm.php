<?php

namespace Sportarten\Form;

use Zend\Form\Form;

/**
 * Das Formular fuer die Sportart (Bearbeiten und Hinzufuegen)
 * @author Helbig Christian www.krummbeine.de
 */
class SportartForm extends Form
{
    public function __construct($name = null)
    {

        parent::__construct('sportarten');
        
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'sportart_id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        //Fuer jedes Element verschiedene Attribute und Optione festlegen:

        //Titel Feld:
        $this->add(array(
            'name' => 'titel',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => _('Titel '),
            ),
        ));

        //Bild_url Feld:
        $this->add(array(
            'name' => 'bild_url',
            'attributes' => array(
                'type'  => 'file',
            ),
            'options' => array(
                'label' => _('Bild-Url '),
            ),
        ));
        
        //Submit-Button
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