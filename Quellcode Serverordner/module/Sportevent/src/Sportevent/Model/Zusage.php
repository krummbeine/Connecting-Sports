<?php
namespace Sportevent\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Model f&uuml;r die Datenbank-Entit&auml;t "v_mitglied_sportevent" mit seinen Attributen
 * @author Helbig Christian www.krummbeine.de
 */
class Zusage implements InputFilterAwareInterface
{
	// Attribute der Tabelle v_mitglied_sportevent:
    public $sportevent_id;
    public $mitglied_id;
    // JOIN-Attribute:
    public $pseudonym;
    public $titel;
    public $startdatum;
    public $war_sympathisch;
    public $war_anwesend;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->sportevent_id    = (isset($data['sportevent_id'])) ? $data['sportevent_id'] : null;
        $this->mitglied_id    	= (isset($data['mitglied_id'])) ? $data['mitglied_id'] : null;
        // JOIN-Attribute:
        $this->pseudonym		= (isset($data['pseudonym'])) ? $data['pseudonym'] : null;
        $this->titel			= (isset($data['titel'])) ? $data['titel'] : null;
        $this->startdatum		= (isset($data['startdatum'])) ? $data['startdatum'] : null;
        $this->war_anwesend		= (isset($data['war_anwesend'])) ? $data['war_anwesend'] : null;
        $this->war_sympathisch		= (isset($data['war_sympathisch'])) ? $data['war_sympathisch'] : null;
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
    	throw new \Exception("Nicht benutzt!");
    }
}