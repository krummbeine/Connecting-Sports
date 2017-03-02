<?php

namespace Sportevent\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

/**
 * Beschreibung des ZusageTable.
 * Legt Methoden fest, um die Datenbank-Tabelle 'v_mitglied_sportevent' anzusteuern und zu verwalten.
 * Eine Zusage hinzufuegen, heraussuchen nach sportevent_id und mitglied_id oder alle eines Sportevents via sportevent_id, 
 * eine Zusage entfernen.
 * @author Helbig Christian www.krummbeine.de
 */
class ZusageTable
{
	// Variable fuer die Datenbank-Verbindung
    protected $tableGateway;

    /**
     * TableGateway festlegen fuer die Datenbank-Verbindung
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Holt alle Zusagen, die zu einem Sportevent gehoeren.
     * @param INT $sportevent_id Das Sportevent, fuer das die Zusagen angezeigt werden sollen.
     * @return array mit den Tupeln des Ergebnisses (Aufbau eines Tuppels: sportevent_id mitglied_id)
     */
    public function fetchAll($sportevent_id, $von_mitglied_id)
    {   
    	// Select-Query an die Datenbank
    	// Holt alle Zusagen aus der Datenbank, die zum uebergebenen Sportevent gehoeren
    	$resultSet = $this->tableGateway->select(function (Select $select) use ($sportevent_id, $von_mitglied_id){
    		// Joine zwei Tabellen, um statt mitglied_id den Namen des Mitglieds (Pseudonym) anzuzeigen
    		$select->join('mitglied', 'v_mitglied_sportevent.mitglied_id = mitglied.mitglied_id', array('pseudonym'));
    		// Joine zwei Tabellen, um zu den Zusage-Attributen die Bewertungs-Attribute auslesen zu können
    		// Verschmelze die Tupel-Mengen mittels sportevent_id UND ueber_mitglied_id eindeutig
    		$select->join(
    			'bewertung',
    			new \Zend\Db\Sql\Expression(
    				"bewertung.von_mitglied_id = '$von_mitglied_id' AND bewertung.ueber_mitglied_id = v_mitglied_sportevent.mitglied_id AND bewertung.sportevent_id = v_mitglied_sportevent.sportevent_id"
    			), 
    			array('war_anwesend', 'war_sympathisch'),
    			$select::JOIN_LEFT
    		);
    		
    		// Sortiert die Zusagen absteigend nach pseudonym
    		$select->order('pseudonym ASC');
    		// Hole nur Zusagen, die die Bedingung 'sportevent_id' => $sportevent_id
    		$select->where(array(
    			'v_mitglied_sportevent.sportevent_id' => $sportevent_id,
    		));
    	});
    	// Alle Zusagen Zurueckgeben, die gefunden wurden und die Bedingung erfuellen
    	// Zusagen Zurueckgeben als Array
    	return $resultSet;
    }
    
    /**
     * Holt alle Zusagen, die von einem Mitglied stammen
     * @param INT $mitglied_id Die eindeutige ID des Mitglieds, von dem die Zusagen stammen
     * @return array mit den Tupeln des Ergebnisses (Aufbau eines Tuppels: sportevent_id mitglied_id)
     */
    public function meineZusagen($mitglied_id)
    {
	    // Select-Query an die Datenbank
	    // Holt alle Zusagen aus der Datenbank, die vom Mitglied gemacht wurden
	    $resultSet = $this->tableGateway->select(function (Select $select) use ($mitglied_id){
	    	// Joine zwei Tabellen, um statt sportevent_id den Titel des Sportevents anzuzeigen & das startdatum auszulesen
	    	$select->join('sportevent', 'v_mitglied_sportevent.sportevent_id = sportevent.sportevent_id', array('titel', 'startdatum'));
	    	// Sortiert die Zusagen absteigend nach dem Startdatum des zugesagten Event
	    	$select->order('sportevent.startdatum DESC');
	    	// Hole nur Zusagen, die die Bedingung 'mitglied_id' => $mitglied_id erfüllen
	    	$select->where(array('v_mitglied_sportevent.mitglied_id' => $mitglied_id));
	    });
    	// Alle Zusagen Zurueckgeben, die gefunden wurden und die Bedingung erfuellen
    	// Zusagen Zurueckgeben als Array
    	return $resultSet;
    }

    /**
     * Dient zur Pruefung, ob eine Zusage existiert
     * @param INT $sportevent_id Eindeutige ID des Sportevents, dem die Zusage gebuehrt.
     * @param INT $mitglied_id Eindeutige ID des Mitglieds, das die Zusage gemacht hat.
     * @return INT 1 wenn Zusage existiert | null, wenn keine solche Zusage gefunden wurde.
     */
    public function getZusage($sportevent_id, $mitglied_id)
    {
    	// Die IDs auf INTEGER casten
        $sportevent_id  = (int) $sportevent_id;
        $mitglied_id  = (int) $mitglied_id;
        
        // via SELECT die Zusage aus der Datenbank holen, welche die Bedingung erfuellt, dass
        // sportevent_id und mitglied_id mit den Parameter uebereinstimmt.
        // Diese Parameter identifizieren zusammen eindeutig die Zusage
        $rowset = $this->tableGateway->select(array('sportevent_id' => $sportevent_id, 'mitglied_id' => $mitglied_id));
        $row = $rowset->current();
        if (!$row) {
        	// Es wurden keine Daten empfangen -> Es existiert keine Zusage, die die Bedingung erfuellt
        	// NULL Zurueckgeben
        	return null;
        }
        // Eine Zusage wurde bezueglich der Parameter gefunden. Return 1
        return 1;
    }

    /**
  	 * Fuegt die Zusage der Tabelle v_mitglied_sportevent hinzu, indem in einem Tupel
  	 * eine Art Verknuepfung durch das Eintragen der sportevent_id und der mitglied_id angelegt wird.
  	 * Diese repraesentiert die Zusage.
     * @param INT $sportevent_id Eindeutige ID des Sportevents, dem die Zusage gebuehrt.
     * @param INT $mitglied_id Eindeutige ID des Mitglieds, das die Zusage machen moechte.
     */
    public function saveZusage($sportevent_id, $mitglied_id)
    {
    	// Pruefen, ob die Zusage bereits existiert
    	if(null == $this->getZusage($sportevent_id, $mitglied_id))
    	{
    		// Es wurde keine solche Zusage gefunden, die die Parameter enthaelt
    		// Eine Zusage kann neu in die Tabelle mit den folgenden Daten eingefuegt werden:
	    	// Die zu speichernden Daten aus den Parameter in ein Array legen:
	        $data = array(
	            'sportevent_id'  => $sportevent_id,
	        	'mitglied_id'  => $mitglied_id,
	        );
	
	        // Die Daten (Zusage) (Verknuepfung der Parameter) in die Tabelle eintragen via INSERT
	        $this->tableGateway->insert($data);
    	}
    }

    /**
     * Entfernt die Zusage aus der Tabelle v_mitglied_sportevent
     * @param INT $sportevent_id Eindeutige ID des Sportevents, dem die Zusage gebuehrt
     * @param INT $mitglied_id Eindeutige ID des Mitglieds, das die Zusage gemacht hat
     */
    public function deleteZusage($sportevent_id, $mitglied_id)
    {
    	// Entfernt die Verknuepfung (den Tupel) aus der Datenbank, bei der die Bedigung zutrifft
    	// sportevent_id und mitglied_id muessen mit Parameter uebereinstimmen, dann wird der Tupel geloescht.
    	// Die Zusage besteht aus einer Verknuepfung dieser beiden Attribute. Existiert diese Verbindung nicht,
    	// gibt es keine Zusage mehr
        $this->tableGateway->delete(array('sportevent_id' => $sportevent_id, 'mitglied_id' => $mitglied_id));
    }
    
    /**
     * Loescht alle Zusagen aus der Datenbank, die einem Mitglied zugeordnet sind
     * @param INT $mitglied_id Die eindeutige ID eines Mitglieds
     */
    public function deleteZusageByMitgliedId($mitglied_id)
    {
    	// Delete-Query an die Datenbank
    	// Loescht alle Zusagen, deren mitglied_id mit der des Parameters uebereinstimmt.
    	$this->tableGateway->delete(array('mitglied_id' => $mitglied_id));
    }
    
    /**
     * Loescht alle Zusagen aus der Datenbank, die einem Sportevent zugeordnet sind
     * @param INT $sportevent_id Die eindeutige ID eines Sportevent
     */
    public function deleteZusageBySporteventId($sportevent_id)
    {
    	// Delete-Query an die Datenbank
    	// Loescht alle Zusagen, deren sportevent_id mit der des Parameters uebereinstimmt.
    	$this->tableGateway->delete(array('sportevent_id' => $sportevent_id));
    }
}