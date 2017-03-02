<?php
namespace Sportevent\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Sportevent\Model\Kommentar;
use Sportevent\Form\KommentarForm;
use Hilfsfunktionen\Authorisation;

/**
 * Beschreibung vom Controller Kommentar
 * Enthaelt Actions, die per Route aufgerufen werden koennen (definierte Route in config/module.config.php)
 * Mittels dieser Actions koennen Kommentare hinzugefuegt, geloescht und bearbeitet werden
 * Jeweils in Abhaengigkeit vom Nutzer, Login-Status, welche Sportart und Sportevent gewaehlt wurde.
 * @author Helbig Christian www.krummbeine.de
 */
class KommentarController extends AbstractActionController
{
	// Benoetigte Tables fuer die View
	// Um den Namen der Sportart auslesen zu koennen
	protected $sportartTable;
	// Um den Namen des Sportevents auslesen zu koennen
	protected $sporteventTable;
	// Um die Kommentar-Tupel aus der Datenbank auslesen zu koennen
    protected $kommentarTable;
    // Um die Mitglied-Tupel aus der Datenbank auslesen zu koennen
    protected $mitgliedTable;
    
    /**
     * Stellt ein Formular fuer das Erstellen eines neuen Kommentares bereit, wenn noch kein Post abgesendet wurde.
     * Dieses Formular sendet einen Post an diese Methode mit Daten fuer das neue Kommentar.
     * Die Eingaben werden geprueft und bei Gueltigkeit wird das neue Kommentar in der Datenbank erstellt.
     * Danach wird zum Sportevent weitergeleitet.
     * @return redirect Weiterleitung, wenn Kommentar erfolgreich erstellt | \Sportevent\Form\KommentarForm[] Formular fuer das neue Kommentar
     */
    public function addAction()
    {    	
    	// Nur gueltig angemeldete Nutzer haben Zugriff auf die Datenbank zum Aendern
    	Authorisation::berechtigt(true, true, $this->getMitgliedTable(), $this);
    	
        $form = new KommentarForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        
	        if ($request->isPost()) {
	        	// Formular wurde abgeschickt:
	        	// Neues Kommentar erstellen (Instanz):
	            $kommentar = new Kommentar();
	            // Inputfilter fuer die Validierung setzen
	            $form->setInputFilter($kommentar->getInputFilter());
	            // Attribute vom vorherig abgesendeten Post in das Formular uebertragen (falls vorher fehlgeschlagenes Erstellen - Validatoren)
	            $form->setData($request->getPost());
	
	            if ($form->isValid()) {
	            	// Formular-Eingaben sind gueltig:
	            	// Daten vom Formular in die neue Instanz von Kommentar uebertragen
	                $kommentar->exchangeArray($form->getData());
	                // Das Kommentar in die Datenbank speichern
	                $this->getKommentarTable()->saveKommentar($kommentar, Authorisation::getSporteventId(), Authorisation::getMitgliedId());
	
	                // Weiterleiten zur Seite Sportevent
	                return $this->redirect()->toRoute('sportevent');
	            }
	        }
        
	    // Das Formular fuer das Erstellen eines Kommentares zurueckgeben fuer die View
        return array('form' => $form);
    }

    /**
     * Bearbeitet ein Kommentar in der Datenbank
     * @return \Sportevent\Form\KommentarForm[] Formular zum Erstellen eines Formulares fuer die View, Weiterleitung zum Sportevent oder sich selbst
     */
    public function editAction()
    {    	
    	// Nur gueltig angemeldete Nutzer haben Zugriff auf die Datenbank zum Aendern
    	Authorisation::berechtigt(true, true, $this->getMitgliedTable(), $this);
    	
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
        	// Wenn keine kommentar_id per Route uebergeben wurde, zum Hinzufuegen von Kommentaren
            return $this->redirect()->toRoute('kommentar', array(
                'action' => 'add'
            ));
        }
        // Das Kommentar aus der Datenbank holen via eindeutiger kommentar_id
        $kommentar = $this->getKommentarTable()->getKommentar($id);

        // Form erstellen zum Bearbeiten des Kommentares
        $form  = new KommentarForm();
        // Attribute vom bestehenden Kommentar in das Formular uebertragen
        $form->bind($kommentar);
        // Bestaetigungsbutton festlegen
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        // Nur der Admin oder der Verfasser des Kommentares duerfen das Kommentar aendern
        if(Authorisation::getMitgliedId() == 0 || Authorisation::getMitgliedId() == $kommentar->mitglied_id)
        {
	        if ($request->isPost()) {
	            $form->setInputFilter($kommentar->getInputFilter());
	            $form->setData($request->getPost());
	
	            if ($form->isValid()) {
	            	// Furmulareingaben sind gueltig:
	            	// Formulareingaben speichern / aktualisieren
	                $this->getKommentarTable()->saveKommentar($form->getData(), Authorisation::getSporteventId(), Authorisation::getMitgliedId());
	
	                // Redirect to list of sportevents
	                return $this->redirect()->toRoute('sportevent');
	            }
	        }
        }
        
        // Das Formular zum Bearbeiten zurueckgeben fuer die View
        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    /**
     * Loescht ein Kommentar aus der Datenbank
     * @return redirect Weiterleitung zum (ausgewaehltem) Sportevent
     */
    public function deleteAction()
    {
    	// Nur gueltig angemeldete Nutzer haben Zugriff auf die Datenbank zum Aendern
    	Authorisation::berechtigt(true, true, $this->getMitgliedTable(), $this);
    	
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
        	// Wenn keine kommentar_id per Route uebergeben wurde, zum Sportevent weiterleiten
            return $this->redirect()->toRoute('sportevent');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'Nein');

            if ($del == 'Ja' || $del == 'Yes') {
            	// Der Nutzer hat das Loeschen im Dialog bestaetigt
            	// Das Kommentar aus der Datenbank holen mittels eindeutige kommentar_id
            	// zur Pruefung, ob der Nutzer der Eigentuemer des Kommentares ist.
            	$kommentar = $this->getKommentarTable()->getKommentar($id);
            	// Nur der Admin oder der Verfasser des Kommentares duerfen das Kommentar loeschen
            	if(Authorisation::getMitgliedId() == 0 || Authorisation::getMitgliedId() == $kommentar->mitglied_id)
            	{
	                $id = (int) $request->getPost('id');
	                // Kommentar loeschen
	                $this->getKommentarTable()->deleteKommentar($id);
            	}
            }

            // Weiterleiten zu den Kommentaren
            return $this->redirect()->toRoute('sportevent');
        }

        return array(
            'kommentar_id'    => $id,
            'kommentar' => $this->getKommentarTable()->getKommentar($id)
        );
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