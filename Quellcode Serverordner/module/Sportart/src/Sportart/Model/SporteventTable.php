<?php

namespace Sportart\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

/**
 * 
 * @author Helbig Christian www.krummbeine.de
 *
 */
class SporteventTable
{	// Variable fuer Tablegateway
    protected $tableGateway;

    /**
     * 
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

	/**
	 * 
	 * @param sporart $id
	 * @return $resultSet
	 */
    public function fetchAll($sportart_id)
    {
        //select * from sporteventTable
        $resultSet = $this->tableGateway->select(function (Select $select) use ($sportart_id){
            //absteigen reihenfolge ordnen
        	$select->order('startdatum DESC');
            //where bedienung die gegebene sportart_id gleich sportart_id
        	$select->where(array('sportart_id' => $sportart_id));
        });
       
        return $resultSet;
    }

    /**
     * 
     * @param sportevent $id
     * @throws \Exception
     * @return mixed
     */
    public function getSportevent($id)
    {
        $id  = (int) $id;
        //select sportevent_id from sportevent
        $rowset = $this->tableGateway->select(array('sportevent_id' => $id));
        $row = $rowset->current();
        //falls nicht alle ersezt sind dann fehler meldung
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
          return $row;
    }

   /**
    * 
    * @param Sportevent $sportevent,$sportart_id,$mitglied_id
    * @throws \Exception
    */
    public function saveSportevent(Sportevent $sportevent, $sportart_id, $mitglied_id)
    {
        //die form in ein data array speichern
        $data = array(
            'titel' => $sportevent->titel,
            'beschreibung'  => $sportevent->beschreibung,
        	'ort'  => $sportevent->ort,
        	'adresse'  => $sportevent->adresse,
        	'startdatum'  => $sportevent->startdatum,
        	'enddatum'  => $sportevent->enddatum,
            'startuhrzeit'  => $sportevent->startuhrzeit,
            'enduhrzeit'  => $sportevent->enduhrzeit,
        	'level'  => $sportevent->level,
        	'sportart_id'=> $sportart_id, // sportart_id kommt vom Parameter! nicht vom $sportevent !!!
        	'mitglied_id'=>$mitglied_id, // mitglied_id kommt vom Parameter! nicht vom $sportevent !!!
        );
        
        $id = (int)$sportevent->sportevent_id;
        //wenn es kein id gibt,
        if ($id == 0) {
            //ein neue einf&uuml;gen
            $this->tableGateway->insert($data);
        } else {
            //sonst die data mit aktuelle id ablesen
            if ($this->getSportevent($id)) {
                $this->tableGateway->update($data, array('sportevent_id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteSportevent($id)
    {
        //alle zeile loeschen wo sportevent id gleiche uebergegebene id
        $this->tableGateway->delete(array('sportevent_id' => $id));
    }
}