<?php
namespace Profil\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Profil\Model\Lieblingssportart;
use Profil\Form\LieblingssportartForm;
use Hilfsfunktionen\Authorisation;

/**
 * Beschreibung vom Controller Lieblingssportart
 * Enthaelt Actions, die per Route aufgerufen werden koennen (definierte Route in config/module.config.php)
 * Mittels dieser Actions koennen Lieblingssportarten hinzugefuegt, geloescht und bearbeitet werden
 * Jeweils in Abhaengigkeit vom Nutzer, Login-Status.
 * @author Helbig Christian www.krummbeine.de
 */
class LieblingssportartController extends AbstractActionController
{
	// Benoetigte Tables fuer die View
    protected $lieblingssportartTable;
    protected $mitgliedTable;
    
    /**
     * Erstellt eine neue Lieblingssportart-Verknuepfung. Also ein Gefaellt mir fuer die Sportart.
     * Danach wird zur Liste der Sportevents weitergeleitet.
     * @return redirect Weiterleitung zu Modul Sportart Index-Seite
     */
    public function addAction()
    {    	
    	// Nur gueltig angemeldete Mitglieder duerfen eine Lieblingssportart liken
    	// Man muss eine Sportart ausgewaehlt haben, aber kein Sportevent
    	Authorisation::berechtigt(true, false, $this->getMitgliedTable(), $this);
    	
    	// Neue Lieblingssportart-Instanz erstellen -> Neu in Datenbank einfuegen
        $lieblingssportart = new Lieblingssportart();
                
        // Erstellt eine Mitglied-Sportart-Verknuepfung.. jedoch OHNE BESCHREIBUNG !
        // Fuer den LIKE-Button der Sportart unter Sportart-Modul
        $this->getLieblingssportartTable()->saveLieblingssportart($lieblingssportart, Authorisation::getSportartId(), Authorisation::getMitgliedId());
        
        return $this->redirect()->toRoute('sportart', array(
        		'action' => 'index', 'id' => Authorisation::getSportartId(),
        ));
    }

    /**
     * Stellt ein Formular fuer das Bearbeiten einer neuen Lieblingssportart-Verknuepfung bereit, wenn noch kein Post abgesendet wurde.
     * Dieses Formular sendet einen Post an diese Methode mit Daten fuer die neue Lieblingssportart (Beschreibung).
     * Die Eingaben werden geprueft und bei Gueltigkeit wird die Lieblingssportart-Verknuepfung in der Datenbank aktualisiert.
     * Danach wird zum Profil weitergeleitet.
     * @return redirect Weiterleitung, wenn Lieblingssportart-Vknpf bearbeitet | \Profil\Form\LieblingssportartForm[] Formular fuer die Bearbeitung der Lieblingssportart
     */
    public function editAction()
    {    	
    	// Nur gueltig angemeldete Mitglieder duerfen eine Lieblingssportart bearbeiten
    	// Man muss keine Sportart ausgewaehlt haben und ebenso kein Sportevent
    	Authorisation::berechtigt(false, false, $this->getMitgliedTable(), $this);
    	
    	// Die der Route uebergebenen sportart_id auslesen, die die Lieblingssportart-Verknuepfung eindeutig identifiziert
    	// zusammen mit der mitglied_id
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
        	// Bei Fehler zurueck zum Bearbeitungs-Formular leiten
            return $this->redirect()->toRoute('lieblingssportart', array(
                'action' => 'edit'
            ));
        }
        
        $lieblingssportart = $this->getLieblingssportartTable()->getLieblingssportart($id, Authorisation::getMitgliedId());

        $form  = new LieblingssportartForm();
        $form->bind($lieblingssportart);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        // Nur der Admin oder der Verfasser des Lieblingssportartes duerfen das Lieblingssportart aendern
        if(Authorisation::getMitgliedId() == 0 || Authorisation::getMitgliedId() == $lieblingssportart->mitglied_id)
        {
	        if ($request->isPost()) {
	            $form->setInputFilter($lieblingssportart->getInputFilter());
	            $form->setData($request->getPost());
	
	            if ($form->isValid()) {
	            	// Formular gueltig ausgefuellt
	            	// Lieblingssportart-Verknuepfung speichern
	                $this->getLieblingssportartTable()->saveLieblingssportart($form->getData(), $id, Authorisation::getMitgliedId());
	
	                // Weiterleiten zum Profil
	                return $this->redirect()->toRoute('profil');
	            }
	        }
        }
        
        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    /**
     * Loescht eine Lieblingssportart aus dem Profil des Mitglieds bzw. in der Verknuepfung v_sportart_mitglied
     * @return redirect Weiterleitung zum Profil
     */
    public function deleteAction()
    {
    	// Nur gueltig angemeldete Mitglieder duerfen eine Lieblingssportart loeschen
    	// Man muss keine Sportart ausgewaehlt haben und ebenso kein Sportevent
    	Authorisation::berechtigt(false, false, $this->getMitgliedTable(), $this);
    	
    	// Die der Route uebergebenen sportart_id auslesen, die die Lieblingssportart-Verknuepfung eindeutig identifiziert
    	// zusammen mit der mitglied_id
        $sportart_id = (int) $this->params()->fromRoute('id', 0);
        
        if ($sportart_id) {
        	// Die Lieblingssportart-Verknuepfung loeschen
        	$this->getLieblingssportartTable()->deleteLieblingssportart($sportart_id, Authorisation::getMitgliedId());
        }
        
        // Weiterleiten zum Profil
        return $this->redirect()->toRoute('profil');
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