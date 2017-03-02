<?php
namespace Sportevent\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Sportevent\Model\Kommentar;
use Hilfsfunktionen\Authorisation;

/**
 * Beschreibung vom Controller Kommentar
 * Enthaelt Actions, die per Route aufgerufen werden koennen (definierte Route in config/module.config.php)
 * Mittels dieser Actions koennen Kommentare hinzugefuegt, geloescht und bearbeitet werden
 * Jeweils in Abhaengigkeit vom Nutzer, Login-Status, welche Sportart und Sportevent gewaehlt wurde.
 * @author Helbig Christian www.krummbeine.de
 */
class SporteventController extends AbstractActionController
{
	// Benoetigte Tables fuer die View
	protected $sportartTable;
	protected $sporteventTable;
    protected $kommentarTable;
    protected $mitgliedTable;
    protected $zusageTable;
    
    /**
     * Wird beim Aufruf des Sportevents angezeigt und laedt alle Kommentare
     * @return unknown|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {    	
    	// Die uebergebene "sportevent_id" (id) holen vom Modul Sportart
    	$id = $this->params()->fromRoute('id');
    	// Wenn eine ID uebergeben wurde, wird diese in der sitzung->sportevent_id (Session) gespeichert
    	if($id) {
    		Authorisation::setSporteventId($id);
    		// Die Sportart setzen
    		// Falls der Nutzer ueber das Profil das Sportevent aufruft,
    		// wurde die Auswahl ueber Startseite -> Sportevent-Liste uebergangen
    		// Sportart-ID muss gesetzt werden aus dem zu ladenden Sportevent, damit Pruefung nachher nicht fehlschlaegt.
    		$sportevent = $this->getSporteventTable()->getSportevent(Authorisation::getSporteventId());
    		Authorisation::setSportartId($sportevent->sportart_id);
    	}
    			
    	// Pruefen, ob der Nutzer eine Sportart und ein Sportevent ausgewaehlt hat
    	// Wenn nicht, wird dieser auf entsprechende Seite umgeleitet, um dies nachzuholen
    	Authorisation::istSportartAusgewaehlt($this);
    	Authorisation::istSporteventAusgewaehlt($this);
    	
    	// Zuvor ausgewaehlte Sportart und Sportevent holen
    	$sportart = $this->getSportartTable()->getSportart(Authorisation::getSportartId());
    	$sportevent = $this->getSporteventTable()->getSportevent(Authorisation::getSporteventId());
    	
    	// Verfasser des Events ermitteln:
    	$verfasser = $this->getMitgliedTable()->getMitgliedById((int)$sportevent->mitglied_id);
    	if(isset($verfasser->pseudonym))
    		$verfasser_name = $verfasser->pseudonym;
    	else 
    		$verfasser_name = null;
    	
    	// Die zugehoerigen Kommentare und Zusagen laden, die zum jeweiligen Sportevent gehoeren
    	// Die Kommentare mit der Sportart und dem Sportevent an die View uebergeben
    	// View zeigt Kommentare an, den Namen der Sportart, Informationen zum Event
    	return new ViewModel(array(
    		'kommentare' => $this->getKommentarTable()->fetchAll(Authorisation::getSporteventId()),
    		'zusagen' => $this->getZusageTable()->fetchAll(Authorisation::getSporteventId(), Authorisation::getMitgliedId()),
    		'zugesagt' => $this->getZusageTable()->getZusage(Authorisation::getSporteventId(), Authorisation::getMitgliedId()),
    		'sportart' => $sportart,
    		'sportevent' => $sportevent,
    		'mitglied_name' => Authorisation::getMitgliedName(),
    		'mitglied_id' => Authorisation::getMitgliedId(),
    		'sportevent_verfasser_name' => $verfasser_name,
    	));
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