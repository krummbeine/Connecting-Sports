<?php
namespace Sportevent\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Hilfsfunktionen\Authorisation;

/**
 * Beschreibung vom Controller Zusage
 * Enthaelt Actions, die per Route aufgerufen werden koennen (definierte Route in config/module.config.php)
 * Mittels dieser Actions kann der Nutzer unter bestimmten Bedingungen einem Sportevent zusagen bzw. absagen.
 * @author Helbig Christian www.krummbeine.de
 */
class ZusageController extends AbstractActionController
{
	// Benoetigte Tables fuer die View
	protected $zusageTable;
    protected $mitgliedTable;
    
    /**
     * Prueft, ob ein Nutzer einem Sportevent zusagen kann und traegt - wenn ja - ihn in die Tabelle v_mitglied_sportevent ein.
     * Das Mitglied wird mit dem Sportevent in der Tabelle praktisch verknuepft, die die Zusage repraesentiert.
     * Existiert eine solche Verknuepfung nicht, hat der Nutzer auch nicht zugesagt.
     * @return redirect Weiterleitung bei Fehler zur entsprechenden Seite bzw. zum Profil
     */
    public function zusagenAction()
    {    	
    	Authorisation::berechtigt(true, true, $this->getMitgliedTable(), $this);
    	
    	// Nutzer ist gueltig angemeldet
    	// Die Verknuepfung Mitglied-Sportevent in die Tabelle einfuegen, die die Zusage repraesentiert
        $this->getZusageTable()->saveZusage(Authorisation::getSporteventId(), Authorisation::getMitgliedId());
        
        // Weiterleiten zur Seite Sportevent nach Abschluss
        return $this->redirect()->toRoute('sportevent');
    }

    /**
     * Sucht eine Zusage des Nutzers in der Tabelle v_mitglied_sportevent und loescht diese ggf.
     * Das Mitglied ist mit dem Sportevent in der Tabelle praktisch verknuepft, die die Zusage repraesentiert.
     * Existiert eine solche Verknuepfung nicht, hat der Nutzer auch nicht zugesagt.
     * Die Verknuepfung wird geloescht, wenn sie den Nutzer betrifft (er also Rechte zum Loeschen hat)
     * @return redirect Weiterleitung bei Fehler zur entsprechenden Seite bzw. zum Profil
     */
    public function absagenAction()
    {
    	Authorisation::berechtigt(true, true, $this->getMitgliedTable(), $this);
    	
    	// Die Verknuepfung Mitglied-Sportevent aus der Tabelle entfernen, die die Zusage repraesentiert (Absagen)
	    $this->getZusageTable()->deleteZusage(Authorisation::getSporteventId(), Authorisation::getMitgliedId());
            
        // Weiterleiten zu den Kommentaren
        return $this->redirect()->toRoute('sportevent');
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