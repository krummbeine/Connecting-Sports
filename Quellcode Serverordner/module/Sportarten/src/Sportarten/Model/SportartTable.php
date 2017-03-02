<?php
namespace Sportarten\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

/**
 * Beschreibung des SportartTables
 * Beschreibt Methoden, um die Datenbank-Tabelle "sportart" anzusteuern.
 * Holen einer Sportart, Speichern und Aktualisieren einer Sportart bzw. Loeschen einer Sportart
 * @author Helbig Christian www.krummbeine.de
 */
class SportartTable
{
    protected $tableGateway;

    /**
     * Den benoetigte TableGateway holen
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Liesst alle in der Datenbank gespeicherten Sportart-Tuppel aus
     * @return Array mit Sportart-Tuppeln
     */
    public function fetchAll()
    {
    	// Select * from sportart
    	$resultSet = $this->tableGateway->select(function (Select $select){
    		// Sportarten aufsteigend nach Titel sortieren
    		$select->order('titel ASC');
    	});
        return $resultSet;
    }

    /**
     * Gibt einen Sportart-Tupel via eindeutiger sportart_id aus der Datenbank zurueck
     * @param INT $sportart_id Die eindeutige ID der Sportart, die geholt werden soll
     * @throws \Exception Wenn sportart_id nicht zugewiesen werden konnte
     * @return Array mit Tuppel der gefundenen Sportart
     */
    public function getSportart($sportart_id)
    {
        $sportart_id  = (int) $sportart_id;
        // Sportart-Tupel-(Array) aus der Datenbank auslesen
        $rowset = $this->tableGateway->select(array('sportart_id' => $sportart_id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("getSportart: Fehler. sportart_id konnte keinem Tuppel zugeordnet werden");
        }
        return $row;
    }

    /**
     * Fuegt einen neuen Sportart-Tupel in die Datenbank ein oder aktualisiert einen vorhandenen
     * @param Sportart $sportart Die Sportart, die in die Datenbank uebertragen werden soll
     * @param String $bild_url Die Url des eventuell hochgeladenen Bildes
     * @throws \Exception
     */
    public function saveSportart(Sportart $sportart, $bild_url)
    {
        $data = array(
            'titel' => $sportart->titel,
        	'bild_url' => $bild_url,
        );

        $id = (int)$sportart->sportart_id;
        
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getSportart($id)) {
                $this->tableGateway->update($data, array('sportart_id' => $id));
            } else {
                throw new \Exception('Aktualisieren des Sportart-Tupels fehlgeschlagen! sportart_id existiert in Datenbank nicht.');
            }
        }
    }

    /**
     * Loescht eine Sportart aus der Datenbank mittels eindeutiger sportart_id
     * @param INT $sportart_id Eindeutige ID der zu loeschenden Sportart
     */
    public function deleteSportart($sportart_id)
    {
        $this->tableGateway->delete(array('sportart_id' => $sportart_id));
    }
}