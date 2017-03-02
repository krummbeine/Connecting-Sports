<?php
namespace Sportart\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Sportart\Model\Sportevent;
use Sportart\Form\SporteventForm;
use Hilfsfunktionen\Authorisation;

class SportartController extends AbstractActionController
{
	// Notwendige Tables
    protected $sporteventTable;
    protected $sportartTable;
    protected $mitgliedTable;
    protected $zusageTable;
    protected $bewertungTable;
    protected $kommentarTable;
    protected $lieblingssportartTable;
    
    public function indexAction()
    {
    	$id = $this->params()->fromRoute('id');
    	if($id)
    		Authorisation::setSportartId($id);
    	
    	// Pruefen, ob der Nutzer eine Sportart ausgewaehlt hat
    	// Wenn nicht, wird dieser auf entsprechende Seite umgeleitet, um dies nachzuholen
    	Authorisation::istSportartAusgewaehlt($this);
    	
    	$mitglied_id = -1;
    	if(NULL !== Authorisation::getMitgliedId())
    		$mitglied_id = Authorisation::getMitgliedId();
    	
    	$sportart = $this->getSportartTable()->getSportart(Authorisation::getSportartId());
    	
        return new ViewModel(array(
            'sportevents' => $this->getSporteventTable()->fetchAll(Authorisation::getSportartId()),
        	'sportart' => $sportart,
        	'mitglied_name' => Authorisation::getMitgliedName(),
        	'mitglied_id' => Authorisation::getMitgliedId(),
        	'ist_lieblingssportart' => $this->getLieblingssportartTable()->getLieblingssportart($id, $mitglied_id),
        ));
    }

    public function addAction()
    {
    	Authorisation::berechtigt(true, false, $this->getMitgliedTable(), $this);
    	
	    $form = new SporteventForm();

	    $form->get('submit')->setValue('Add');
	        
	    $request = $this->getRequest();
	        

	    if ($request->isPost()) {
	    	$sportevent = new Sportevent();

	        $form->setInputFilter($sportevent->getInputFilter());
	        $form->setData($request->getPost());

	        if ($form->isValid()) {
	        	$sportevent->exchangeArray($form->getData());
	            $this->getSporteventTable()->saveSportevent($sportevent, Authorisation::getSportartId(), Authorisation::getMitgliedId());
	
	            // Zu Sportart weiterleiten
	            return $this->redirect()->toRoute('sportart');
	        }
	     }
	    	

	   	 $sportart = $this->getSportartTable()->getSportart(Authorisation::getSportartId());
	     return array(
	       		'form' => $form,
	       		'sportart' => $sportart, 
        );
    }

    public function editAction()
    {
    	Authorisation::berechtigt(true, false, $this->getMitgliedTable(), $this);
    	
	    $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
        	return $this->redirect()->toRoute('sportart', array(
	           'action' => 'add'
 	        ));
        }

	    $sportevent = $this->getSporteventTable()->getSportevent($id);
	    $form  = new SporteventForm();
        $form->bind($sportevent);

	    $form->get('submit')->setAttribute('value', 'Edit');

	    $request = $this->getRequest();
	        

	    if(Authorisation::getMitgliedId() == 0 || Authorisation::getMitgliedId() == $sportevent->mitglied_id)
	    {
	    	if ($request->isPost()) {

	            $form->setInputFilter($sportevent->getInputFilter());
	            $form->setData($request->getPost());
	            
		        if ($form->isValid()) {
		            $this->getSporteventTable()->saveSportevent(
	                		$form->getData(), 
	                		Authorisation::getSportartId(), 
		            		Authorisation::getMitgliedId()
		            );
		
		            // Weiterleiten
		            return $this->redirect()->toRoute('sportart');
		        }
	        }
        }
        else {
        	// Weiterleiten
        	return $this->redirect()->toRoute('sportart');
        }
	
		return array(
	        'id' => $id,
            'form' => $form,
		);
    }


    public function deleteAction()
    {
    	Authorisation::berechtigt(true, false, $this->getMitgliedTable(), $this);
    	
	    $id = (int) $this->params()->fromRoute('id', 0);

	    if (!$id) {
	        return $this->redirect()->toRoute('sportart');
	    }
	
	    $request = $this->getRequest();
	    if ($request->isPost()) {
	    	$del = $request->getPost('del', 'Nein');
	
	        if ($del == 'Ja' || $del == 'Yes') {
	            	
	          	$sportevent = $this->getSporteventTable()->getSportevent($id);
	            	
	           	// Nur der Admin oder der Verfasser des Sportevents duerfen das Sportevent loeschen
	           	if(Authorisation::getMitgliedId() == 0 || Authorisation::getMitgliedId() == $sportevent->mitglied_id)
	           	{
	                $sportevent_id = (int) $request->getPost('id');
	                // Alle Dinge loeschen, die dem Sportevent zugeordnet wurden (Referenzen)
	                // Zusagen, Bewertungen, Kommentare:
	                $this->getBewertungTable()->deleteBewertungBySporteventId($sportevent_id);
	                $this->getZusageTable()->deleteZusageBySporteventId($sportevent_id);
	                $this->getKommentarTable()->deleteKommentarBySporteventId($sportevent_id);
	                // Das Sportevent nun loeschen:
	                $this->getSporteventTable()->deleteSportevent($sportevent_id);
	           	}
	        }
	
	        // Weiterleiten
	        return $this->redirect()->toRoute('sportart');
        }
	
		return array(
	       'id'    => $id,
			'sportevent' => $this->getSporteventTable()->getSportevent($id)
	    );
    }

    /**
     * 
     * @return sportevent Tabelle
     */
    public function getSporteventTable()
    {
        if (!$this->sporteventTable) {
            $sm = $this->getServiceLocator();
            $this->sporteventTable = $sm->get('Sportart\Model\SporteventTable');
        }
        return $this->sporteventTable;
    }
    
    /**
     *
     * @return sportart Tabelle
     */
    public function getSportartTable()
    {
    	if (!$this->sportartTable) {
    		$sm = $this->getServiceLocator();
    		$this->sportartTable = $sm->get('Sportarten\Model\SportartTable');
    	}
    	return $this->sportartTable;
    }

    /**
     * @return array
     */
    public function getArraySportart(){
        //tabelle des sportart holen
        $sportarts = $this->getSportartTable()->fetchAll();
        $options = array('' => '--  select --');
        //sportart durchsuchen
        foreach($sportarts as $sportart) {
            //id in value setzen und titel nehmen
            $options[$sportart->sportart_id] = $sportart->titel;
        }

        return $options;
    }
    
    /**
     * Holt die PHP-Datei mitgliedTable, die Funktionen enthaelt, um mit der Tabelle mitglied
     * in der Datenbank ConnectingSports zu interagieren. Wird z.B.: fuer Login, Registrieren und Profil benoetigt.
     * @return Profil\Model\MitgliedTable
     */
    public function getMitgliedTable()
    {
    	if (!$this->mitgliedTable) {
    		$sm = $this->getServiceLocator();
    		$this->mitgliedTable = $sm->get('Profil\Model\MitgliedTable');
    	}
    	return $this->mitgliedTable;
    }
    
    /**
     * Holt das Model fuer die Kommentare
     * @return KommentarTable Das Model zum Ansteuern der Datenbank mit der Entitaet "kommentar"
     */
    public function getKommentarTable()
    {
    	if (!$this->kommentarTable) {
    		$sm = $this->getServiceLocator();
    		$this->kommentarTable = $sm->get('Sportevent\Model\KommentarTable');
    	}
    	return $this->kommentarTable;
    }
    
    /**
     * Holt das Model fuer die Zusagen
     * @return ZusageTable Das Model zum Ansteuern der Datenbank mit der Entitaet "v_mitglied_sportevent"
     */
    public function getZusageTable()
    {
    	if (!$this->zusageTable) {
    		$sm = $this->getServiceLocator();
    		$this->zusageTable = $sm->get('Sportevent\Model\ZusageTable');
    	}
    	return $this->zusageTable;
    }
    
    
    /**
     * Holt das Model fuer die Bewertungen
     * @return BewertungTable Das Model zum Ansteuern der Datenbank mit der Entitaet "bewertung"
     */
    public function getBewertungTable()
    {
    	if (!$this->bewertungTable) {
    		$sm = $this->getServiceLocator();
    		$this->bewertungTable = $sm->get('Sportevent\Model\BewertungTable');
    	}
    	return $this->bewertungTable;
    }
    
    /**
     * Holt das Model fuer die Lieblingssportarten-Verknuepfungen
     * @return LieblingssportartTable Das Model zum Ansteuern der Datenbank mit der Entitaet "v_sportart_mitglied"
     */
    public function getLieblingssportartTable()
    {
    	if (!$this->lieblingssportartTable) {
    		$sm = $this->getServiceLocator();
    		$this->lieblingssportartTable = $sm->get('Profil\Model\LieblingssportartTable');
    	}
    	return $this->lieblingssportartTable;
    }
}