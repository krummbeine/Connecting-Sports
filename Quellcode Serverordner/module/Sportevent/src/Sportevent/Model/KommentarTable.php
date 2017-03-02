<?php

namespace Sportevent\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

/**
 * Beschreibung des KommentarTable.
 * Legt Methoden fest, um die Datenbank-Tabelle 'kommentar' anzusteuern und zu verwalten.
 * Ein Kommentar hinzufuegen, heraussuchen nach kommentar_id bzw. alle eines Sportevents mit sportevent_id, ein Kommentar entfernen.
 * @author Helbig Christian www.krummbeine.de
 */
class KommentarTable
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
     * Holt alle Kommentare zu einem Sportevent aus der Datenbank
     * @param INT $sportevent_id Eindeutige ID des Sportevents
     * @return array mit Tuppeln (einzelne Kommentare) des Ergebnisses der Anfrage
     */
    public function fetchAll($sportevent_id)
    {    	
    	// Select-Query an die Datenbank
    	$resultSet = $this->tableGateway->select(function (Select $select) use ($sportevent_id){
    		// Joine zwei Tabellen, um statt mitglied_id den Namen des Mitglieds (Pseudonym) anzuzeigen
    		$select->join('mitglied', 'kommentar.mitglied_id = mitglied.mitglied_id', array('pseudonym'));
    		
    		// Kommentare absteigend nach ihrer ID sortiert im Ergebnis-Array einfuegen
    		$select->order('kommentar_id DESC');
    		// Nur Kommentare zum Ergebnis-Array hinzufuegen, deren sportevent_id mit dem Parameter uebereinstimmt
    		$select->where(array('sportevent_id' => $sportevent_id));
    	});
    		 
    	// Das Ergebnis-Array der Anfrage Zurueckgeben
    	return $resultSet;
    }

    /**
     * Holt ein einzelnes Kommentar aus der Datenbank
     * @param INT $kommentar_id Die eindeutige ID des zu holenden Kommentares
     * @throws \Exception Fehler, wenn angefordertes Kommentar nicht existiert
     * @return kommentar-tupel-array Gibt ein Kommentar Zurueck in Form eines Arrays.
     */
    public function getKommentar($kommentar_id)
    {
    	// kommentar_id zu INT casten
        $kommentar_id  = (int) $kommentar_id;
        // Select-Query an die Datenbank:
        // Kommentar suchen, dessen kommentar_id mit dem Parameter uebereinstimmt
        $rowset = $this->tableGateway->select(array('kommentar_id' => $kommentar_id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Fehler bei getKommentar: Kommentar existiert in Datenbank nicht. ID: ".$kommentar_id);
        }
        // Das Array Zurueckgeben, das die Attribute des Kommentares repraesentiert
        return $row;
    }

    /**
     * Ein Kommentar in der Datenbank neu einfuegen bzw. dessen Attribute aktualisieren
     * @param Kommentar $kommentar Das zu speichernde Kommentar
     * @param INT $sportevent_id Die eindeutige sportevent_id fuer das Kommentar (fuer das das Kommentar geschrieben wurde)
     * @param INT $mitglied_id Die eindeutige mitglied_id fuer das Kommentar (Verfasser)
     * @throws \Exception Fehler, wenn Kommentar in der App existiert, aber nicht in der Datenbank auffindbar.
     */
    public function saveKommentar(Kommentar $kommentar, $sportevent_id, $mitglied_id)
    {
    	// Die zu speichernden Attribute fuer das Kommentar festlegen
        $data = array(
            'text' => $kommentar->text,
            'sportevent_id'  => $sportevent_id,
        	'mitglied_id'  => $mitglied_id,
        	'datum' => date("Y-m-d H:i"),
        );

        // Die ID des Kommentar-Parameters auslesen, um zu pruefen, ob es neu in die Datenbank eingefuegt werden muss
        $id = (int)$kommentar->kommentar_id;
        if ($id == 0) {
        	// Kommentar existiert in Datenbank noch nicht -> neu einfuegen
            $this->tableGateway->insert($data);
            // ID des eingefuegtem Kommentares zurueckgeben
            return $this->tableGateway->lastInsertValue;
        }
        else {
        	// Kommentar existiert bereits in Datenbank -> Attribute aktualisieren von bestehendem Tupel
            if ($this->getKommentar($id)) {
            	// Update-Query an die Datenbank und entsprechendes Kommentar aktualisieren, das die
            	// eindeutige kommentar_id des Kommentar-Parameters besitzt
                return $this->tableGateway->update($data, array('kommentar_id' => $id));
            } 
            else {
            	// Fehler: kommentar_id falsch bzw. nicht synchron mit Datenbank /..
                throw new \Exception('Fehler bei saveKommentar: Kommentar existiert in Datenbank nicht.');
            }
        }
    }

    /**
     * Loescht ein Kommentar aus der Datenbank
     * @param INT $kommentar_id Die eindeutige ID des zu loeschenden Kommentares
     */
    public function deleteKommentar($kommentar_id)
    {
    	// Delete-Query an die Datenbank
    	// Loescht das Kommentar, dessen kommentar_id mit der des Parameters uebereinstimmt.
        return $this->tableGateway->delete(array('kommentar_id' => $kommentar_id));
    }
    
    /**
     * Loescht alle Kommentare aus der Datenbank, die einem Mitglied zugeordnet sind
     * @param INT $mitglied_id Die eindeutige ID eines Mitglieds
     */
    public function deleteKommentarByMitgliedId($mitglied_id)
    {
    	// Delete-Query an die Datenbank
    	// Loescht alle Kommentare, deren mitglied_id mit der des Parameters uebereinstimmt.
    	$this->tableGateway->delete(array('mitglied_id' => $mitglied_id));
    }
    
    /**
     * Loescht alle Kommentare aus der Datenbank, die einem Sportevent zugeordnet sind
     * @param INT $sportevent_id Die eindeutige ID eines Sportevents
     */
    public function deleteKommentarBySporteventId($sportevent_id)
    {
    	// Delete-Query an die Datenbank
    	// Loescht alle Kommentare, deren sportevent_id mit der des Parameters uebereinstimmt.
    	$this->tableGateway->delete(array('sportevent_id' => $sportevent_id));
    }
}