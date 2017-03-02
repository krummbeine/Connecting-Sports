<?php

namespace Profil\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

/**
 * Beschreibung des LieblingssportartTable.
 * Legt Methoden fest, um die Datenbank-Tabelle 'v_sportart_mitglied' anzusteuern und zu verwalten.
 * Sportarten, die man mag auswaehlen und mit einer Beschreibung versehen, wird so ermoeglicht.
 * @author Helbig Christian www.krummbeine.de
 */
class LieblingssportartTable
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
     * Holt alle Lieblingssportarten zu einem Mitglied aus der Datenbank
     * @param INT $mitglied_id Die eindeutige ID des Mitglieds, zu dem die Lieblingssportarten ausgelesen werden sollen.
     * @return array mit Tuppeln (einzelne Lieblingssportarten) des Ergebnisses der Anfrage
     */
    public function fetchAll($mitglied_id)
    {    	
    	// Select-Query an die Datenbank
    	$resultSet = $this->tableGateway->select(function (Select $select) use ($mitglied_id){  
    		// Joine zwei Tabellen, um statt sportart_id den Namen der Sportart (Titel) anzuzeigen
    		$select->join('sportart', 'v_sportart_mitglied.sportart_id = sportart.sportart_id', array('titel', 'sportart_id'));
    		// Lieblingssportarten aufsteigend nach Titel sortieren
    		$select->order('titel ASC');
    		// Nur Lieblingssportarten zum Ergebnis-Array hinzufuegen, deren mitglied_id mit dem Parameter uebereinstimmt
    		$select->where(array('mitglied_id' => $mitglied_id));
    	});
    		 
    	// Das Ergebnis-Array der Anfrage Zurueckgeben
    	return $resultSet;
    }

    /**
     * Holt eine einzelne Lieblingssportart-Verknuepfung aus der Datenbank
     * @param INT $sportart_id Die eindeutige sportart_id fuer die Lieblingssportart (Lieblingssportart)
     * @param INT $mitglied_id Die eindeutige mitglied_id fuer die Lieblingssportart (Von diesem Mitglied die Lieblingssportart)
     * @throws \Exception Fehler, wenn angeforderte Lieblingssportart-Vknpf nicht existiert
     * @return Lieblingssportart-tupel-array Gibt eine Lieblingssportart-Verknuepfung zurueck in Form eines Arrays.
     */
    public function getLieblingssportart($sportart_id, $mitglied_id)
    {
    	// IDS zu INT casten
        $sportart_id  = (int) $sportart_id;
        $mitglied_id  = (int) $mitglied_id;
        
        // Select-Query an die Datenbank:
        // Lieblingssportart-Vknpf. suchen, deren 2 eindeutig identifierende IDs mit dem Parameter uebereinstimmen
        $rowset = $this->tableGateway->select(array('sportart_id' => $sportart_id, 'mitglied_id' => $mitglied_id));
        $row = $rowset->current();
        if (!$row) {
        	// Null zurueckgeben, wenn Vknpf nicht existiert fuer IndexAction von SportartController
            return null;
        }
        // Das Array Zurueckgeben, das die Attribute der Lieblingssportart repraesentiert
        return $row;
    }

    /**
     * Ein Lieblingssportart in der Datenbank neu einfuegen bzw. dessen Attribute aktualisieren
     * @param Lieblingssportart $lieblingssportart Das zu speichernde Lieblingssportart
     * @param INT $sportart_id Die eindeutige sportart_id fuer die Lieblingssportart (Lieblingssportart)
     * @param INT $mitglied_id Die eindeutige mitglied_id fuer die Lieblingssportart (Von diesem Mitglied die Lieblingssportart)
     * @throws \Exception Fehler, wenn Lieblingssportart in der App existiert, aber nicht in der Datenbank auffindbar.
     */
    public function saveLieblingssportart(Lieblingssportart $lieblingssportart, $sportart_id, $mitglied_id)
    {
    	$level = $lieblingssportart->level;
    	if($level == null)
    		$level = 1;
    	
    	// Die zu speichernden Attribute fuer das Lieblingssportart festlegen
        $data = array(
            'beschreibung' => $lieblingssportart->beschreibung,
        	'level' => $level,
            'sportart_id'  => $sportart_id,
        	'mitglied_id'  => $mitglied_id,
        );

        // Die ID des Lieblingssportart-Parameters auslesen, um zu pruefen, ob es neu in die Datenbank eingefuegt werden muss
        $id_sportart = (int)$lieblingssportart->sportart_id;
        $id_mitglied = (int)$lieblingssportart->mitglied_id;
        
        if ($id_sportart == 0 && $id_mitglied == 0) {
        	// Lieblingssportart-Verknuepfung existiert in Datenbank noch nicht -> neu einfuegen
            $this->tableGateway->insert($data);
        }
        else {
        	// Lieblingssportart-Verknuepfung existiert bereits in Datenbank -> Attribute aktualisieren von bestehendem Tupel
            if ($this->getLieblingssportart($sportart_id, $mitglied_id)) {
            	// Update-Query an die Datenbank und entsprechende Lieblingssportart-Vknpf aktualisieren, das die
            	// Die Attribute der Verknuepfung aktualisieren, deren 2 zusammen eindeutig identifizierende Attribute
            	// sportart_id und mitglied_id mit den Parameter uebereinstimmen:
                $this->tableGateway->update($data, array('sportart_id' => $sportart_id, 'mitglied_id' => $mitglied_id));
            } 
            else {
            	// Fehler: lieblingssportart_id falsch bzw. nicht synchron mit Datenbank /..
                throw new \Exception('Fehler bei saveLieblingssportart: Lieblingssportart existiert in Datenbank nicht.');
            }
        }
    }

    /**
     * Loescht eine Lieblingssportart-Verknuepfung aus der Datenbank
     * @param INT $sportart_id Die eindeutige ID der Sportart der Vknpf
     * @param INT $mitglied_id Die eindeutige ID des Mitglieds der Vknpf
     */
    public function deleteLieblingssportart($sportart_id, $mitglied_id)
    {
    	// Delete-Query an die Datenbank
    	// Loescht die Lieblingssportart-Vknpf, deren sportart_id und mitglied_id mit der des Parameters uebereinstimmt.
        $this->tableGateway->delete(array('sportart_id' => $sportart_id, 'mitglied_id' => $mitglied_id));
    }
}