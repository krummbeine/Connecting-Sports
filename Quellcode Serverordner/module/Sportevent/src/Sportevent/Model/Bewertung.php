<?php
namespace Sportevent\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Model f&uuml;r die Datenbank-Entit&auml;t "bewertung" mit seinen Attributen
 * @author Helbig Christian www.krummbeine.de
 */
class Bewertung implements InputFilterAwareInterface
{
	// Attribute der Tabelle
    public $bewertung_id;
    public $von_mitglied_id;
    public $sportevent_id;
    public $war_anwesend;
    public $war_sympathisch;
    public $ueber_mitglied_id;
    // Join-Attribute
    public $pseudonym;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->bewertung_id    = (isset($data['bewertung_id'])) ? $data['bewertung_id'] : null;
        $this->von_mitglied_id    = (isset($data['von_mitglied_id'])) ? $data['von_mitglied_id'] : null;
        $this->sportevent_id    = (isset($data['sportevent_id'])) ? $data['sportevent_id'] : null;
        $this->war_anwesend    = (isset($data['war_anwesend'])) ? $data['war_anwesend'] : null;
        $this->war_sympathisch    = (isset($data['war_sympathisch'])) ? $data['war_sympathisch'] : null;
        $this->ueber_mitglied_id    = (isset($data['ueber_mitglied_id'])) ? $data['ueber_mitglied_id'] : null;
        $this->pseudonym    = (isset($data['pseudonym'])) ? $data['pseudonym'] : null;
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