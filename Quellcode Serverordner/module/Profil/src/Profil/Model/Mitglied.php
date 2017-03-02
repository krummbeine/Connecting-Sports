<?php
namespace Profil\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

//Datenbank


class Mitglied //implements InputFilterAwareInterface
{
    public $mitglied_id;
    public $pseudonym;
    public $facebook_id;
    public $passwort;
    public $geburtstag;
    public $meine_stadt;
    public $beschreibung;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->mitglied_id     = (isset($data['mitglied_id']))     ? $data['mitglied_id']     : null;
        $this->pseudonym = (isset($data['pseudonym'])) ? $data['pseudonym'] : null;
        $this->facebook_id  = (isset($data['facebook_id']))  ? $data['facebook_id']  : null;
        $this->passwort  = (isset($data['passwort']))  ? $data['passwort']  : null;
        $this->geburtstag  = (isset($data['geburtstag']))  ? $data['geburtstag']  : null;
        $this->meine_stadt  = (isset($data['meine_stadt']))  ? $data['meine_stadt']  : null;
        $this->beschreibung  = (isset($data['beschreibung']))  ? $data['beschreibung']  : null;
    }

     // Add the following method:
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * Gibt den Views Profil/Registrieren mit Parameter (true)
     * und der View Login mit Parameter (false)
     * die Input Filter mit Validatoren f&uuml;r das Formular.
     * Der Login hat nur Pseudonym und Passwort - Felder, jedoch fehlen die anderen Felder.
     * Damit das Formular nicht IMMER ung&uuml;ltig ist, m&uuml;ssen die required-Attribute gesondert angepasst werden.
     * @param BOOLEAN $alleFelderErforderlich Gibt an, ob es sich um ein Profil/Registrierungs-Formular handelt
     * @return \Zend\InputFilter\InputFilter Gibt den InputFilter f&uuml;r die Validierung Zur&uuml;ck
     */
    public function getInputFilter($alleFelderErforderlich)
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();


            // mitglied_id
            $inputFilter->add($factory->createInput(array(
                'name'     => 'mitglied_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));
            
            // Pseudonym
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'pseudonym',
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

            // facebook_id
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'facebook_id',
            		'required' => false,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            )));
            
            // Passwort
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'passwort',
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
            								'min'      => 6,
            								'max'      => 100,
            						),
            				),
            		),
            )));

            //passwot bestaetigen
            $inputFilter->add($factory->createInput(array(
                'name'     => 'passwortbestaetigen',
                'required' => $alleFelderErforderlich,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'passwort',
                            'messages' => array(
                                \Zend\Validator\Identical::NOT_SAME => 'The two given tokens do not match'
                            )
                        ),
                    ),
                ),
            )));
            
            // geburtstag
            $inputFilter->add($factory->createInput(array(
            		'name' => 'geburtstag',
            		'required' => $alleFelderErforderlich,
            		'filters' => array(
            				array('name' => 'StripTags'),
            				array('name' => 'StringTrim'),
            		),
            )));
            
            // meine_stadt
            $inputFilter->add($factory->createInput(array(
                'name'     => 'meine_stadt',
                'required' => $alleFelderErforderlich,
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
            
            // Beschreibung
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'beschreibung',
            		'required' => $alleFelderErforderlich,
            		'filters'  => array(
            				array('name' => 'StripTags'),
            				array('name' => 'StringTrim'),
            		),
            		'validators' => array(
            				array(
            						'name'    => 'StringLength',
            						'options' => array(
            								'encoding' => 'UTF-8',
            								'min'      => 10,
            								'max'      => 255,
            						),
            				),
            		),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}