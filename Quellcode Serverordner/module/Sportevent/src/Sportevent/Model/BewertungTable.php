<?php

namespace Sportevent\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

/**
 * Beschreibung des BewertungTable.
 * Legt Methoden fest, um die Datenbank-Tabelle 'bewertung' anzusteuern und zu verwalten.
 * @author Helbig Christian www.krummbeine.de
 */
class BewertungTable
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
     * Holt die Mitglieder, die der Nutzer gemocht hat, aus der Datenbank
     * @param INT $von_mitglied_id Eindeutige ID des Mitglieds, von dem die Bewertung kam
     * @return array mit Tuppeln (einzelne Bewertungen) des Ergebnisses der Anfrage
     */
    public function mitgliederDieIchMag($von_mitglied_id)
    {    	
    	// Select-Query an die Datenbank
    	// Holt alle Bewertung-Mitglied-Tupel-Mengen aus der Datenbank, die zum uebergebenen Parameter von_mitglied_id passen
    	$resultSet = $this->tableGateway->select(function (Select $select) use ($von_mitglied_id){
    		// Joine zwei Tabellen, um den Namen des bewerteten Mitglieds zusätzlich zur ueber_mitglied_id zu erhalten
    		$select->join('mitglied', 'bewertung.ueber_mitglied_id = mitglied.mitglied_id', array('pseudonym'));
    		// Sortiert die Mag-Ich-Markierten-Mitglieder aufsteigend nach pseudonym
    		$select->order('pseudonym ASC');
    		// Hole nur Bewertung-Mitglied-Tupel, die die Bedingung 'von_mitglied_id' => $von_mitglied_id erfuellen
    		$select->where(array('von_mitglied_id' => $von_mitglied_id));
    	});
    	// Alle Bewertung-Mitglied-Tupel zurueckgeben, die gefunden wurden und die Bedingung erfuellen
    	// Tupel zurueckgeben als Array
    	return $resultSet;
    }
    

    /**
     * Holt die Mitglieder, die den Nutzer gemocht haben, aus der Datenbank
     * @param INT $ueber_mitglied_id Eindeutige ID des Mitglieds, den Nutzer gemocht haben
     * @return array mit Tuppeln (einzelne Bewertungen) des Ergebnisses der Anfrage
     */
    public function mitgliederDieMichMoegen($ueber_mitglied_id)
    {
    	// Select-Query an die Datenbank
    	// Holt alle Bewertung-Mitglied-Tupel-Mengen aus der Datenbank, die zum uebergebenen Parameter von_mitglied_id passen
    	$resultSet = $this->tableGateway->select(function (Select $select) use ($ueber_mitglied_id){
    		// Joine zwei Tabellen, um den Namen des bewerteten Mitglieds zusätzlich zur von_mitglied_id zu erhalten
    		$select->join('mitglied', 'bewertung.von_mitglied_id = mitglied.mitglied_id', array('pseudonym'));
    		// Sortiert die Mag-Ich-Markierten-Mitglieder aufsteigend nach pseudonym
    		$select->order('pseudonym ASC');
    		// Hole nur Bewertung-Mitglied-Tupel, die die Bedingung 'ueber_mitglied_id' => $ueber_mitglied_id erfuellen
    		$select->where(array('ueber_mitglied_id' => $ueber_mitglied_id));
    	});
    	// Alle Bewertung-Mitglied-Tupel zurueckgeben, die gefunden wurden und die Bedingung erfuellen
    	// Tupel zurueckgeben als Array
   		return $resultSet;
    }
    
    /**
     * Holt die Bewertungen, die uber den Nutzer gemacht wurden, aus der Datenbank
     * @param INT $ueber_mitglied_id Eindeutige ID des Mitglieds, das bewertet wurde
     * @param String $bewertungs_name Der Name der zu pruefenden Spalte fuer die WHERE-Bedingung (z.B. war_anwesend)
     * @param INT $bewertungs_wert Der Wert des zu pruefenden Attributs in der WHERE-Bedinung (z.B. -1, 0, 1)
     * @return INT Summe (Anzahl gefundener Tuppel) enthalten im Array unter WHERE-Bedingung
     */
    public function meineBewertungSumme($ueber_mitglied_id, $bewertungs_name, $bewertungs_wert)
    {
    	// Select-Query an die Datenbank
    	// Holt alle Bewertung-Mitglied-Tupel-Mengen aus der Datenbank, die zum uebergebenen Parameter ueber_mitglied_id passen
    	$resultSet = $this->tableGateway->select(function (Select $select) use ($ueber_mitglied_id, $bewertungs_name, $bewertungs_wert){
    		// Hole nur Tupel, die die Bedingung 'ueber_mitglied_id' => $ueber_mitglied_id erfuellen
    		// UND wo der Parameter $bewertungs_name den Attributwert des Parameters $bewertungs_wert hat:
    		$select->where(array('ueber_mitglied_id' => $ueber_mitglied_id, $bewertungs_name => $bewertungs_wert));
    	});
    	// Die Summe der gefundenen Tuppel zurückgeben
   		return count($resultSet);
    }

    /**
     * Holt eine einzelne Bewertung aus der Datenbank
     * @param INT $bewertung_id Die eindeutige ID der zu holenden Bewertung
     * @throws \Exception Fehler, wenn angeforderte Bewertung nicht existiert
     * @return bewertung-tupel-array Gibt eine Bewertung Zurueck in Form eines Arrays.
     */
    public function getBewertung($bewertung_id)
    {
    	// bewertung_id zu INT casten
        $bewertung_id  = (int) $bewertung_id;
        // Select-Query an die Datenbank:
        // Bewertung suchen, dessen bewertung_id mit dem Parameter uebereinstimmt
        $rowset = $this->tableGateway->select(array('bewertung_id' => $bewertung_id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Fehler bei getBewertung: Bewertung existiert in Datenbank nicht.");
        }
        // Das Array Zurueckgeben, das die Attribute der Bewertung repraesentiert
        return $row;
    }
    
    /**
     * Holt eine einzelne Bewertung aus der Datenbank
     * @param INT $von_mitglied_id Die eindeutige ID des Mitglieds, das die Bewertung erstellt
     * @param INT $sportevent_id Die eindeutige ID des Sportevents, wo die Bewertung erstellt wird
     * @param INT $ueber_mitglied_id Die eindeutige ID des Mitglieds, das bewertet wird
     * @return NULL wenn keine Bewertung gefunden wurde | array mit Attributen der gefundenen Bewertung
     */
    public function getBewertungByKeys($von_mitglied_id, $sportevent_id, $ueber_mitglied_id) 
    {
    	// IDs zu INT casten
    	$von_mitglied_id  = (int) $von_mitglied_id;
    	$sportevent_id  = (int) $sportevent_id;
    	$ueber_mitglied_id  = (int) $ueber_mitglied_id;
    	
    	// Select-Query an die Datenbank:
    	// Bewertung suchen, dessen 3 zusammen eindeutig identifizierenden IDs mit den Parametern uebereinstimmen
    	// von_mitglied_id, sportevent_id, ueber_mitglied_id bilden einen Schluessel zusammen fuer die Identifikation
    	$rowset = $this->tableGateway->select(array(
    			'von_mitglied_id' => $von_mitglied_id,
    			'sportevent_id' => $sportevent_id,
    			'ueber_mitglied_id' => $ueber_mitglied_id,
    	));
    	$row = $rowset->current();
    	if (!$row) {
    		return null;
    	}
    	// Das Array Zurueckgeben, das die Attribute der Bewertung repraesentiert
    	return $row;
    }

    /**
     * Eine Bewertung in der Datenbank neu einfuegen bzw. dessen Attribute aktualisieren
     * @param Bewertung $bewertung Die zu speichernde Bewertung
     * @param Array $daten Ein Array mit Attributen, die in dem Tupel (der Bewertung) gespeichert werden sollen.
     * @throws \Exception Fehler, wenn Kommentar in der App existiert, aber nicht in der Datenbank auffindbar.
     */
    public function saveBewertung(Bewertung $bewertung, $daten)
    {
        // Die ID des Bewertung-Parameters auslesen, um zu pruefen, ob sie neu in die Datenbank eingefuegt werden muss
        $id = (int)$bewertung->bewertung_id;
        if ($id == 0) {
        	// Bewertung existiert in Datenbank noch nicht -> neu einfuegen
            $this->tableGateway->insert($daten);
        }
        else {
        	// Bewertung existiert bereits in Datenbank -> Attribute aktualisieren von bestehendem Tupel
            if ($this->getBewertung($id)) {
            	// Update-Query an die Datenbank und entsprechende Bewertung aktualisieren, die die
            	// eindeutige bewertung_id des Bewertungs-Parameters besitzt
                $this->tableGateway->update($daten, array('bewertung_id' => $id));
            } 
            else {
            	// Fehler: bewertung_id falsch bzw. nicht synchron mit Datenbank /..
                throw new \Exception('Fehler bei saveBewertung: Bewertung existiert in Datenbank nicht.');
            }
        }
    }

    /**
     * Loescht eine Bewertung aus der Datenbank mit 3 zusammen eindeutig identifizierenden Keys (Parameter):
     * @param INT $von_mitglied_id Die eindeutige ID des Mitglieds, das die Bewertung erstellt
     * @param INT $sportevent_id Die eindeutige ID des Sportevents, wo die Bewertung erstellt wird
     * @param INT $ueber_mitglied_id Die eindeutige ID des Mitglieds, das bewertet wird
     */
    public function deleteBewertungByKeys($von_mitglied_id, $sportevent_id, $ueber_mitglied_id)
    {
    	// Delete-Query an die Datenbank
    	// Loescht die Bewertung, deren bewertung_id mit der des Parameters uebereinstimmt.
        $this->tableGateway->delete(array(
        		'von_mitglied_id' => $von_mitglied_id,
        		'sportevent_id' => $sportevent_id,
        		'ueber_mitglied_id' => $ueber_mitglied_id,
        ));
    }
    
    /**
     * Loescht alle Bewertungen aus der Datenbank, die einem Sportevent zugeordnet sind
     * @param INT $sportevent_id Die eindeutige ID eines Sportevents
     */
    public function deleteBewertungBySporteventId($sportevent_id)
    {
    	// Delete-Query an die Datenbank
    	// Loescht alle Bewertungen, deren sportevent_id mit der des Parameters uebereinstimmt.
    	$this->tableGateway->delete(array('sportevent_id' => $sportevent_id));
    }
    
    /**
     * Loescht alle Bewertungen aus der Datenbank, die einem Mitglied zugeordnet sind
     * @param INT $mitglied_id Die eindeutige ID eines Mitglieds
     */
    public function deleteBewertungByMitgliedId($mitglied_id)
    {
    	// Delete-Query an die Datenbank
    	// Loescht alle Bewertungen, deren ueber_mitglied_id mit der des Parameters uebereinstimmt.
    	$this->tableGateway->delete(array('ueber_mitglied_id' => $mitglied_id));
    	// nur die Bewertungen werden irrelevant, die ueber das geloeschte Mitglied waren
    	// vom Mitglied gemachte Bewertungen nicht loeschen. Wichtig fuer Statistiken im Profil.
    }
}

