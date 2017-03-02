<?php
namespace Profil\Model;

use Zend\Db\TableGateway\TableGateway;

/**
 * Beschreibung des MitgliedTable.
 * Legt Methoden fest, um die Datenbank-Tabelle 'mitglied' anzusteuern und zu verwalten.
 * Ein Mitglied hinzufuegen (registrieren), heraussuchen nach mitglied_id oder pseudonym, ein Mitglied loeschen.
 * @author Helbig Christian www.krummbeine.de
 */
class MitgliedTable
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
     * Sucht aus der Datenbank das Mitglied mit uebereinstimmendem Pseudonym (eindeutige Identifikation)
     * @param INT $mitglied_id Die eindeutige ID des Miglieds, das aus der Datenbank geholt werden soll.
     * @throws \Exception Fehlermeldung, falls Holen des Mitglieds nicht erfolgreich war
     * @return Array des Mitglieds, auf das die mitglied_id zugeordnet werden konnte.
     */
    public function getMitgliedById($mitglied_id)
    {
    	// mitglied_id in INT casten.
        $mitglied_id  = (int) $mitglied_id;
        // Mitglied aus der Datenbank holen, auf das die Bedingung 'mitglied_id' => $mitglied_id zutrifft
        $rowset = $this->tableGateway->select(array('mitglied_id' => $mitglied_id));
        $row = $rowset->current();
        if (!$row) {
        	// Es konnte kein Mitglied gefunden werden, dessen mitglied_id uebereinstimmt mit dem Parameter
            return null;
        }
        // Rueckgabe eines Arrays, das alle Daten des Mitglieds enthaelt.
        return $row;
    }

    /**
     * Sucht aus der Datenbank das Mitglied mit uebereinstimmendem Pseudonym (eindeutige Identifikation)
     * @param STRING $pseudonym Das Pseudonym des Mitglieds, das Zurueckgegeben werden soll (uebereinstimmung)
     * @return NULL wenn dem Pseudonym kein Mitglied zugeordnet werden konnte | Array mit Daten des Mitglieds, wenn Pseudonym uebereinstimmt
     */
    public function getMitgliedByPseudonym($pseudonym)
    {
    	// Sucht mittels SQL-Select-Statement nach einem Mitglied, dessen Pseudonym-Attribut mit dem uebergebenen
    	// Parameter uebereinstimmt
    	$rowset = $this->tableGateway->select(array('pseudonym' => $pseudonym));
    	$row = $rowset->current();
    	if (!$row) {
    		// Kein Mitglied wurde gefunden, das die Bedingung 'pseudonym' => $pseudonym erfuellt.
    		// Rueckgabe NULL
    		return null;
    	}
    	// Rueckgabe des Arrays vom Mitglied, das die Bedingung 'pseudonym' => $pseudonym erfuellt.
    	return $row;
    }
    
    /**
     * Legt ein neues Mitglied in der Tabelle mitglied an bzw. aktualisiert die
     * Attribute eines vorhandenen mitglieds.
     * @param Mitglied $mitglied Das Mitglied, das gespeichert werden soll.
     * @throws \Exception Fehlermeldung
     */
    public function saveMitglied(Mitglied $mitglied, $ist_admin)
    {
        // mitglied_id des zu speichernden/aktualisierenden Mitglieds auslesen
        $mitglied_id = (int)$mitglied->mitglied_id;
        
        if ($mitglied_id == 0 && !$ist_admin) {
        	// Zu uebertragene Daten festlegen
        	$data = array(
        			'pseudonym' => $mitglied->pseudonym,
        			'meine_stadt'  => $mitglied->meine_stadt,
        			'beschreibung'  => $mitglied->beschreibung,
        			'geburtstag'  => $mitglied->geburtstag,
        			'facebook_id'  => $mitglied->facebook_id,
        			'passwort' => md5($mitglied->passwort), // Erstmaliges Einfuegen --> Verschluesselung!
        	);
        	
        	// Mitglied existiert noch nicht.
        	// Mitglied wird in Tabelle eingefuegt (neuer Tupel)
            $this->tableGateway->insert($data);
        } 
        else {
        	// Zu uebertragene Daten festlegen
        	$data = array(
        			'pseudonym' => $mitglied->pseudonym,
        			'meine_stadt'  => $mitglied->meine_stadt,
        			'beschreibung'  => $mitglied->beschreibung,
        			'geburtstag'  => $mitglied->geburtstag,
        			'facebook_id'  => $mitglied->facebook_id,
        			'passwort' => md5($mitglied->passwort), // --> Verschluesselung vom Textfeld (wieder)
        	);
        	
        	// Mitglied (der Tupel mit uebereinstimmender mitglied_id) existiert wohl bereits
        	// Mitglied aus Datenbank holen
            if ($this->getMitgliedById($mitglied_id)) {
            	// Mitglied gefunden und existiert
            	// Mitglied aktualisieren, bei dem die mitglied_id uebereinstimmt (Bedingung)
                $this->tableGateway->update($data, array('mitglied_id' => $mitglied_id));
            } 
            else {
            	// Das Mitglied existiert in der Datenbank nicht - fehlerhafte Instanz von Miglied.php-Model
            	// mitglied_id konnte keinem existierenden Tupel zugeordnet werden in der Datenbank.
                throw new \Exception('mitglied_id existiert nicht.');
            }
        }
    }

    /**
     * Loescht ein Mitglied aus der Datenbank
     * @param INT $id die eindeutige mitglied_id des zu loeschenden Mitglieds
     */
    public function deleteMitglied($mitglied_id)
    {
    	// Mitglied loeschen, dessen mitglied_id mit dem Parameter uebereinstimmt.
        $this->tableGateway->delete(array('mitglied_id' => $mitglied_id));
    }
}