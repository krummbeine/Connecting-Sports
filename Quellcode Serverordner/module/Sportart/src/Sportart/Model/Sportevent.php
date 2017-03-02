<?php

namespace Sportart\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;


/**
 * 
 * @author Helbig Christian www.krummbeine.de
 *
 */
class Sportevent implements InputFilterAwareInterface
{
    public $sportevent_id;
    public $titel;
    public $beschreibung;
    public $ort;
    public $adresse;
    public $startdatum;
    public $enddatum;
    public $level;
    public $startuhrzeit;
    public $enduhrzeit;
    public $sportart_id;
    public $mitglied_id;
    public $inputFilter;

    /**
     * 
     * @param unknown $data
     */
    public function exchangeArray($data)
    {
        $this->sportevent_id     = (isset($data['sportevent_id']))     ? $data['sportevent_id']     : null;
        $this->titel  = (isset($data['titel']))  ? $data['titel']  : null;
        $this->beschreibung = (isset($data['beschreibung'])) ? $data['beschreibung'] : null;
        $this->ort = (isset($data['ort'])) ? $data['ort'] : null;
        $this->adresse = (isset($data['adresse'])) ? $data['adresse'] : null;
        $this->startdatum = (isset($data['startdatum'])) ? $data['startdatum'] : null;
        $this->enddatum = (isset($data['enddatum'])) ? $data['enddatum'] : null;
        $this->startuhrzeit = (isset($data['startuhrzeit'])) ? $data['startuhrzeit'] : null;
        $this->enduhrzeit = (isset($data['enduhrzeit'])) ? $data['enduhrzeit'] : null;
        $this->level = (isset($data['level'])) ? $data['level'] : null;
        $this->sportart_id = (isset($data['sportart_id'])) ? $data['sportart_id'] : null;
        $this->mitglied_id = (isset($data['mitglied_id'])) ? $data['mitglied_id'] : null;
    }

    /**
     * 
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * 
     * @param InputFilterInterface $inputFilter
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * 
     * @return \Zend\InputFilter\InputFilter
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
        	
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'sportevent_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'mitglied_id',
            		'required' => true,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'titel',
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
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'beschreibung',
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
                            'max'      => 10000,
                        ),
                    ),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'ort',
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
            
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'adresse',
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
            								'min'      => 2,
            								'max'      => 100,
            						),
            				),
            		),
            )));
            
             $inputFilter->add($factory->createInput(array(
                'name' => 'startdatum',
                'required' => true,                 
                'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'messages' => array(
                                    \Zend\Validator\Callback::INVALID_VALUE => 'The end date should be greater than start date',
                            ),
                            'callback' => function($value, $context = array()) {                                    
                                $endDate = \DateTime::createFromFormat('Y-m-d', $context['enddatum']);
                                $startDate = \DateTime::createFromFormat('Y-m-d', $value);
                                return $endDate >= $startDate;
                            },
                        ),
                    ),                          
                ),
        )));
             

             $inputFilter->add($factory->createInput(array(
             		'name' => 'enddatum',
             		'required' => true,
             		'filters' => array(
             				array('name' => 'StripTags'),
             				array('name' => 'StringTrim'),
             		),
             		'validators' => array(
             				array(
             						'name' => 'Callback',
             						'options' => array(
             								'messages' => array(
             										\Zend\Validator\Callback::INVALID_VALUE => 'The end date should be greater than start date',
             								),
             								'callback' => function($value, $context = array()) {
             									$startDate = \DateTime::createFromFormat('Y-m-d', $context['startdatum']);
             									$endDate = \DateTime::createFromFormat('Y-m-d', $value);
             									return $endDate >= $startDate;
             								},
             						),
             				),
             		),
             )));
             

             $inputFilter->add($factory->createInput(array(
             		'name'     => 'level',
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
             								'max'      => 1,
             						),
             				),
             		),
             )));
             

            $inputFilter->add($factory->createInput(array(
                'name' => 'startuhrzeit',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\Callback::INVALID_VALUE => 'The departure time have musst less than the arrival time',
                            ),
                            'callback' => function($value, $context=array()) {
                                $startTime  = $value;
                                $endTime = $context['startuhrzeit'];
                                $isValid = $startTime >= $endTime;
                                return $isValid;
                            },
                        ),
                    ),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name' => 'enduhrzeit',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'messages' => array(
                                \Zend\Validator\Callback::INVALID_VALUE => 'The departure time have musst greater than the arrival time',
                            ),
                            'callback' => function($value, $context=array()) {
                                $startTime  = $value;
                                $endTime  = $context['startuhrzeit'];
                                $isValid = $startTime >= $endTime;
                                return $isValid;
                            },
                        ),
                    ),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
            		'name'     => 'sportart_id',
            		'required' => true,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            )));
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}