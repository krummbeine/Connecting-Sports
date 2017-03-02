<?php
namespace Profil\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Model fuer die Datenbank-Entitaet "v_sportart_mitglied" mit seinen Attributen
 * @author Helbig Christian www.krummbeine.de
 */
class Lieblingssportart implements InputFilterAwareInterface
{
	// Attribute der Tabelle v_sportart_mitglied:
    public $sportart_id;
    public $mitglied_id;
    public $beschreibung;
    public $level;
    // Attribute fuer JOIN
    public $titel;
    
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->sportart_id    = (isset($data['sportart_id'])) ? $data['sportart_id'] : null;
        $this->mitglied_id    	= (isset($data['mitglied_id'])) ? $data['mitglied_id'] : null;
        $this->beschreibung    	= (isset($data['beschreibung'])) ? $data['beschreibung'] : null;
        $this->level    = (isset($data['level'])) ? $data['level'] : null;
        $this->titel    		= (isset($data['titel'])) ? $data['titel'] : null;
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
    	
    		// sportart_id
    		$inputFilter->add($factory->createInput(array(
    				'name'     => 'sportart_id',
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
    	
    		// Beschreibung
    		$inputFilter->add($factory->createInput(array(
    				'name'     => 'beschreibung',
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
    										'min'      => 0,
    										'max'      => 255,
    								),
    						),
    				),
    		)));
    		
    		// level
    		$inputFilter->add($factory->createInput(array(
    				'name'     => 'level',
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
    										'max'      => 1,
    								),
    						),
    				),
    		)));

    		$this->inputFilter = $inputFilter;
    	}
    	
    	return $this->inputFilter;
    }
}