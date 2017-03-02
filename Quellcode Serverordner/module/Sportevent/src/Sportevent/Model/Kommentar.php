<?php
namespace Sportevent\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Model f&uuml;r die Datenbank-Entit&auml;t "kommentar" mit seinen Attributen
 * @author Helbig Christian www.krummbeine.de
 */
class Kommentar implements InputFilterAwareInterface
{
	// Attribute der Tabelle
    public $kommentar_id;
    public $mitglied_id;
    public $sportevent_id;
    public $text;
    public $datum;
    public $pseudonym;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->kommentar_id     = (isset($data['kommentar_id'])) ? $data['kommentar_id'] : null;
        $this->mitglied_id      = (isset($data['mitglied_id'])) ? $data['mitglied_id'] : null;
        $this->sportevent_id    = (isset($data['sportevent_id'])) ? $data['sportevent_id'] : null;
        $this->text 			= (isset($data['text'])) ? $data['text'] : null;
        $this->datum  			= (isset($data['datum'])) ? $data['datum'] : null;
        $this->pseudonym		= (isset($data['pseudonym'])) ? $data['pseudonym'] : null;
    }

    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Nicht benutzt!");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

			// kommentar_id
            $inputFilter->add($factory->createInput(array(
                'name'     => 'kommentar_id',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));
            
            // mitglied_id
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'mitglied_id',
            		'required' => false,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            )));
            
            // sportevent_id
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'sportevent_id',
            		'required' => false,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            )));

            // text
            $inputFilter->add($factory->createInput(array(
                'name'     => 'text',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));

            // Datum
            $inputFilter->add($factory->createInput(array(
                'name'     => 'datum',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));
			
            
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}