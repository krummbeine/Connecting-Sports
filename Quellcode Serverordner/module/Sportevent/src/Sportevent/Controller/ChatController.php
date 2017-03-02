<?php
namespace Sportevent\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Sportevent\Model\Kommentar;
use Hilfsfunktionen\Authorisation;

/**
 * Beschreibung vom Controller Chat
 * Enthaelt Actions, die per Route aufgerufen werden koennen (definierte Route in config/module.config.php)
 * Mittels dieser Actions koennen Kommentare (im Chat) hinzugefuegt, geloescht und bearbeitet werden
 * Jeweils in Abhaengigkeit vom Nutzer, Login-Status, welche Sportart und Sportevent gewaehlt wurde.
 * @author Helbig Christian www.krummbeine.de
 */
class ChatController extends AbstractActionController
{
	// Benoetigte Tables fuer die View
	protected $sportartTable;
	protected $sporteventTable;
    protected $kommentarTable;
    protected $mitgliedTable;
    protected $zusageTable;
    protected $bewertungTable;
    
     /**
     * Gibt alle gespeicherten Kommentare an die View zurück, deren Event_id übereinstimmt
     * Ermöglicht das Laden von Kommentaren, die zu dem Event gehören.
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction() {
    	// Nur gueltig angemeldete Nutzer duerfen den Chat benutzen
    	Authorisation::berechtigt(true, true, $this->getMitgliedTable(), $this);
    	// Nutzer ist gueltig angemeldet
    	
    	return new ViewModel(array(
       		'kommentare' => $this->getKommentarTable()->fetchAll(Authorisation::getSporteventId()),
    		'event_id' => Authorisation::getSporteventId(),
			'mitglied_id' => Authorisation::getMitgliedId(),
			'mitglied_name' => Authorisation::getMitgliedName(),
        ));
    }
    
    /**
     * Fügt eine neues Kommentar hinzu, indem ein neues Kommentar-Objekt mit eindeutiger ID erstellt wird
     * @return Fall 1) response=false (Speichern fehlgeschlagen) Fall 2) response=true mit ID des neuen Kommentar-Obj
     */
    public function createKommentarAction() {
    	// Nur gueltig angemeldete Nutzer duerfen den Chat benutzen
    	Authorisation::berechtigt(true, true, $this->getMitgliedTable(), $this);
    	// Nutzer ist gueltig angemeldet
    	 
    	$request = $this->getRequest();
    	$response = $this->getResponse();
    
    	if ($request->isPost()) {
    		 
    		// Erstellen eines neuen Kommentar-Objektes
    		$neues_kommentar = new Kommentar();
    
    		if (!$kommentar_id = $this->getKommentarTable()->saveKommentar($neues_kommentar, Authorisation::getSporteventId(), Authorisation::getMitgliedId()))
    		{
    			// Speichern fehlgeschlagen
    			$response->setContent(\Zend\Json\Json::encode(array('response' => false)));
    		}
    		else 
    		{
    			// Speichern erfolgreich --> Rückgabe der ID des neuen Kommentars an Json
    			$response->setContent(\Zend\Json\Json::encode(array('response' => true, 'neue_kommentar_id' => $kommentar_id)));
    		}
    	}
    	return $response;
    }
    
    /**
     * Löscht ein Kommentar
     * @return response boolean Gibt zurück, ob das Löschen erfolgreich war (true) bzw. (false)
     */
    public function removeKommentarAction() {
    	// Nur gueltig angemeldete Nutzer duerfen den Chat benutzen
    	Authorisation::berechtigt(true, true, $this->getMitgliedTable(), $this);
    	// Nutzer ist gueltig angemeldet
    	
    	$request = $this->getRequest();
    	$response = $this->getResponse();
    	if ($request->isPost()) {
    		 
    		// Daten-Array von POST empfangen
    		$post_array = $request->getPost();
    		// ID des zu löschenden Kommentares auslesen
    		$kommentar_id = $post_array['id'];
    
    		$kommentar = $this->getKommentarTable()->getKommentar($kommentar_id);
    		// Als Admin (0) angemeldet oder Verfasser des Kommentares (Schreibrecht)
    		if(Authorisation::getMitgliedId() == 0 || $kommentar->mitglied_id == Authorisation::getMitgliedId())
    		{
    			if (!$this->getKommentarTable()->deleteKommentar($kommentar_id))
    				// Fehler beim Löschen des Kommentares
    				$response->setContent(\Zend\Json\Json::encode(array('response' => false)));
    				else {
    					// Löschen erfolgreich
    					$response->setContent(\Zend\Json\Json::encode(array('response' => true)));
    				}
    		}
    		else {
    			$response->setContent(\Zend\Json\Json::encode(array(
    					'response' => 'Zugriff verweigert',
    					'mitglied_id' => $container->mitglied_id,
    					'kommentar_id' => $kommentar->getMitgliedID())));
    		}
    	}
    	return $response;
    }
    
    /**
     * Aktualisiert das Kommentar
     * @return response boolean Gibt zurück, ob das Aktualisieren erfolgreich war (true) bzw. (false)
     */
    public function updateKommentarAction() {
    	// Nur gueltig angemeldete Nutzer duerfen den Chat benutzen
    	Authorisation::berechtigt(true, true, $this->getMitgliedTable(), $this);
    	// Nutzer ist gueltig angemeldet
    	 
    	$request = $this->getRequest();
    	$response = $this->getResponse();
    	if ($request->isPost()) {
    		 
    		// Daten-Array von POST empfangen
    		$post_array = $request->getPost();
    		// ID des zu aktualisierenden Kommentares auslesen
    		$kommentar_id = $post_array['id'];
    
    		$kommentar_inhalt = $post_array['inhalt'];
    		 
    		$kommentar = $this->getKommentarTable()->getKommentar($kommentar_id);
    
    		// Als Admin (0) angemeldet oder Verfasser des Kommentares (Schreibrecht)
    		if(Authorisation::getMitgliedId() == 0 || $kommentar->mitglied_id == Authorisation::getMitgliedId())
    		{
    			$kommentar->text = $kommentar_inhalt;
    			 
    			if (!$this->getKommentarTable()->saveKommentar($kommentar, Authorisation::getSporteventId(), Authorisation::getMitgliedId()))
    			{
    				$response->setContent(\Zend\Json\Json::encode(array('response' => false)));
    			}
    			else 
    			{
    				$response->setContent(\Zend\Json\Json::encode(array('response' => true)));
    			}
    		}
    		else 
    		{
    			$response->setContent(\Zend\Json\Json::encode(array(
    					'response' => 'Zugriff verweigert',
    					'mitglied_id' => Authorisation::getMitgliedId(),
    					'kommentar_id' => $kommentar->getMitgliedID())));
    		}
    	}
    	return $response;
    }
    
    /**
     * Holt das Model fuer die Sportarten
     * @return SportartTable Das Model zum Ansteuern der Datenbank mit der Entitaet "sportart"
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
     * Holt das Model fuer die Sportevents
     * @return SporteventTable Das Model zum Ansteuern der Datenbank mit der Entitaet "sportevent"
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
    /*  
    public function getBewertungTable()
    {
    	if (!$this->bewertungTable) {
    		$sm = $this->getServiceLocator();
    		$this->bewertungTable = $sm->get('Sportevent\Model\BewertungTable');
    	}
    	return $this->bewertungTable;
    }
    */
    

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
}