<?php
namespace Sportarten\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Sportarten\Model\Sportart;
use Sportarten\Form\SportartForm;
use Hilfsfunktionen\Authorisation;
use Zend\Session\Container;

/**
 * Beschreibung vom Controller Sportarten
 * Enthaehlt Methoden zum Hinzufuegen, Loeschen, Bearbeiten einer Sportart und Hochladen eines Bildes.
 * Außerdem enthaehlt er die Funktion zum Aendern der globalen Sprache der Applikation.
 * @author Helbig Christian www.krummbeine.de
 */
class SportartenController extends AbstractActionController
{
	// Benoetigte Tables
    protected $sportartTable;
    protected $mitgliedTable;
    
    /**
     * Wechselt die Sprache bei Aufruf
     * Via URL einzustellende Sprache einmalig &uuml;bergeben z.B: /sportarten/sprache?sprache=en_US
     * @return Weiterleitung zur Startseite (Refresh)
     */
    public function spracheAction()
    {
    	// Sprache auslesen von z.B: /sportarten/sprache?sprache=en_US
    	$sprache = $this->params()->fromQuery('sprache');


    	if ($sprache) {
    		// Eine Sprache wurde &uuml;bergeben.
    		// global.php holen
    		$config = $this->serviceLocator->get('config');
    		if(isset($config['locale']['available'][$sprache]))
    		{
    			// Pr&uuml;fen, ob z.B. en_US oder de_DE im Array available im Array locale existiert --> verf&uuml;gbar ist
    			// Die Sprache kann gesetzt werden, da sie existiert und verf&uuml;gbar ist f&uuml;r die Applikation
    			Authorisation::setSprache($sprache);
    		}
    	}
    	// Vorherige Seiten_url auslesen
    	$url = $this->getRequest()->getHeader('Referer')->getUri();
    	// Zur vorherigen Seiten weiterleiten
    	$this->redirect()->toUrl($url);
    }
    
    /**
     * Laedt eine Datei hoch und gibt den Namen.Typ der Datei zurueck (Bild-Upload)
     * @param File $datei Die Datei, die hochgeladen werden soll von $request->getFiles()->toArray()
     * @return String Dateiname.Dateityp der hochgeladenen Datei
     */
    private function hochladen($datei)
    {
    	$returnWert = null;
    	if(isset($datei['bild_url']['name']))
    	{
	    	$httpadapter = new \Zend\File\Transfer\Adapter\Http();
	    	// Dateigroesse festlegen (1KB):
	    	$filesize  = new \Zend\Validator\File\Size(array('min' => 1000 ));
	    	// Erlaubte Dateiendungen festlegen:
	    	$extension = new \Zend\Validator\File\Extension(array('extension' => array('jpg', 'jpeg', 'png', 'gif')));
	    	// Validatoren setzen:
	    	$httpadapter->setValidators(array($filesize, $extension), $datei['bild_url']['name']);
	    	// Auf Gueltigkeit pruefen:
	    	if($httpadapter->isValid()) {
	    		// Darf hochgeladen werden
	    		// Pfad fuer das Speichern festlegen:
	    		$httpadapter->setDestination(PUBLIC_PATH . '/upload/sportart/');
	    		if($httpadapter->receive()) {
	    			// Upload abgeschlossen
	    			// Datei-Name der hochgeladenen Datei (Bild) auslesen:
	    			$hochgeladeneDatei = $httpadapter->getFileName();
	    			// Dateiname in ein Array atomisieren
	    			$nameHochgeladeneDatei = explode('sportart', $hochgeladeneDatei);
	    		}
	    	}
	    	// Atomisiertes Array an Index [1] auslesen: return "Dateiname.Dateityp" fuer bild_url in Tabelle "sportart"
	    	if(isset($nameHochgeladeneDatei[1]))
	    		$returnWert = $nameHochgeladeneDatei[1];
    	}
    	return $returnWert;
    }
    
    /**
     * Auflistung der Sportarten:
     * Sportarten aus dem Model abrufen (aus Datenbank auslesen) und an die View uebergeben zum Anzeigen
     * @return \Zend\View\Model\ViewModel Daten fuer die View bereitstellen
     */
    public function indexAction()
    {
        return new ViewModel(array(
            'sportarten' => $this->getSportartTable()->fetchAll(),
        	'mitglied_name' => Authorisation::getMitgliedName(),
        	'mitglied_id' => Authorisation::getMitgliedId(),
        	'sprache' => Authorisation::getSprache(),
        ));
    }

    /**
     * Fuegt einen neuen Sportart-Tupel in der Tabelle "sportart" aus der Datenbank ein
     * und stellt dazu ein Formular zur Verfuegung
     * @return redirect Weiterleitung zur Startseite bzw. Liste der Sportarten | \Sportarten\Form\SportartForm[] Die Form zum Bearbeiten der Sportart
     */
    public function addAction()
    {		
    	// Nur angemeldete User duerfen eine Sportart hinzufuegen
    	Authorisation::istAngemeldet($this->getMitgliedTable(), $this);
    	
    	// Das Formular fuer das Hinzufuegen holen / erstellen:
	    $form = new SportartForm();
	    //Durch Klick auf den Submit-Button (Add) wird das Formular abgesendet.
	    $form->get('submit')->setValue('Neue Sportart erstellen'); 
	
	    $request = $this->getRequest();

        if ($request->isPost()) {
        	// Formular wurde abgesendet
        	// Neue Instanz von Sportart erstellen zum neu Einfuegen in die Datenbank
            $sportart = new Sportart();
            
            // Inputfilter setzen fuer das Validieren
            $form->setInputFilter($sportart->getInputFilter());
            // Vom vorherigen Post die Daten in das Formular uebertragen (falls Validation fehlgeschlagen)
            $form->setData($request->getPost());

 			if($form->isValid()){
            	// Eingaben des Formulars sind gueltig
            	// Daten vom Formular in die Instanz von Sportart uebertragen zum Hochladen
                $sportart->exchangeArray($form->getData());
                // Ausgewaehltes Bild eventuell hochladen, falls ausgewaehlt und Dateiname.Dateityp der bild_url zuweisen
                $bild_url = $this->hochladen($request->getFiles()->toArray());
				// Die neue Sportart in die Datenbank speichern
                $this->getSportartTable()->saveSportart($sportart, $bild_url);

                // Weiterleiten zur Startseite nach Abschluss
                return $this->redirect()->toRoute('sportarten');

            }
        }
        // Formular an die View zurueckgeben, wenn noch kein Post ausgefuehrt wurde
        return array('form' => $form);
    }
    
    /**
     * Aktualisiert die Attribute eines Sportart-Tupels in der Tabelle "sportart" aus der Datenbank
     * und stellt dazu ein Formular zur Verfuegung
     * @return redirect Weiterleitung zur Startseite bzw. Liste der Sportarten | \Sportarten\Form\SportartForm[] Die Form zum Bearbeiten der Sportart
     */
    public function editAction()
    {
    	// Nur angemeldete User duerfen eine Sportart bearbeiten
    	Authorisation::istAngemeldet($this->getMitgliedTable(), $this);
    	
    	// Nur Admin darf Aenderungen vornehmen
    	if(Authorisation::getMitgliedId() == 0)
    	{
    		// Uebergebene sportart_id von der Route auslesen
	        $id = (int) $this->params()->fromRoute('id', 0);
	        
	        if (!$id) {        
	        	// Wenn sportart_id nicht uebergeben, Weiterleiten zur Add-Action von Sportarten
	            return $this->redirect()->toRoute('sportarten', array(
	                'action' => 'add'
	            ));
	        }
	        // Zu bearbeitende Sportart via eindeutiger sportart_id ($id) aus der Datenbank holen zum Bearbeiten
	        $sportart = $this->getSportartTable()->getSportart($id);
			// Form erstellen / holen zum Bearbeiten der Sportart
	        $form  = new SportartForm();
	        // Attributwerte der geholten Sportart in das Formular uebertragen (Verbinden)
	        $form->bind($sportart);
	        // Den Button-Text zum Absenden des Formulares festlegen
	        $form->get('submit')->setAttribute('value', 'Sportart bearbeiten');
	
	        $request = $this->getRequest();
	        if ($request->isPost()) {
	        	// Formular wurde abgesendet
	        	// InputFilter setzen fuer die Validation
	            $form->setInputFilter($sportart->getInputFilter());
	            // Formulareingaben des letzten Posts in das Formular uebertragen (Fehlgeschlagenes Speichern)
	            $form->setData($request->getPost());
	            if ($form->isValid()) {
	            	// Formulareingaben sind gueltig
	            	// Ausgewaehltes Bild eventuell hochladen, falls ausgewaehlt und Dateiname.Dateityp der bild_url zuweisen
	            	$bild_url = $this->hochladen($request->getFiles()->toArray());
	            	// Sportart aktualisieren - Sportart-Tupel in Datenbank aktualisieren:
	                $this->getSportartTable()->saveSportart($form->getData(), $bild_url);
					// Weiterleitung zur Startseite
	                return $this->redirect()->toRoute('sportarten');
	            }
	        }
        	// Daten an die View uebertragen
	        return array(
	            'id' => $id,
	            'form' => $form,
	        );
    	}
    	else {
    		// Wenn nicht Admin, kein Zugriff: Weiterleitung zur Startseite
    		return $this->redirect()->toRoute('sportarten');
    	}
    }

    /**
     * Loescht eine Sportart aus der Tabelle "sportart" in der Datenbank
     * @return redirect Weiterleitung zur Startseite bzw. Liste der Sportarten
     */
    public function deleteAction()
    {
    	// Nur angemeldete User d&uuml;rfen eine Sportart l&ouml;schen
    	Authorisation::istAngemeldet($this->getMitgliedTable(), $this);
    	
    	if(Authorisation::getMitgliedId() == 0)
    	{
    		// Nur der Admin darf die Sportart loeschen
    		// Alle Sportevents muessen zuvor geloescht werden! Manuell
    		// Der Admin muss dies machen - um Missbrauch zu vermeiden
    		// In der Regel sollen keine Sportarten gel&ouml;scht werden, da zu viele Daten verloren gehen
    		// Nur im absoluten Notfall loeschen
    		
    		// Eindeutige sportart_id aus der Route auslesen
	        $id = (int) $this->params()->fromRoute('id', 0);

	        if (!$id) {
	        	// Wenn keine sportart_id uebergeben wurde, Weiterleiten zur Startseite
	            return $this->redirect()->toRoute('sportarten');
	        }
			
	        $request = $this->getRequest();
	        if ($request->isPost()) {
	        	// Formular wurde abgesendet (Ja-Nein-Dialog, ob man die Sportart wirklich loeschen mag)
	        	// Aus dem Post die Antwort auslesen
	            $del = $request->getPost('del', 'Nein');
	            if ($del == 'Ja' || $del == 'Yes') {
	            	// Es wurde im Formular Ja angeklickt, die Sportart soll geloescht werden
	            	// Zu loeschende eindeutige sportart_id auslesen
		            $sportart_id = (int) $request->getPost('id');
		            // Sportart-Tupel aus der Datenbank loeschen via eindeutiger sportart_id
		            $this->getSportartTable()->deleteSportart($sportart_id);
	            }
	
	            // Weiterleitung zur Startseite
	            return $this->redirect()->toRoute('sportarten');
	        }
	
	        // Daten an die View uebertragen
	        return array(
	            'id'    => $id,
	            'sportart' => $this->getSportartTable()->getSportart($id)
	        );
    	}
        else {
        	// Wenn nicht Admin, kein Zugriff: Weiterleitung zur Startseite
        	return $this->redirect()->toRoute('sportarten');
        }
    }

    /**
     * Holt die PHP-Datei sportartTable, die Funktionen enth&auml;lt, um mit der Tabelle sportart
     * in der Datenbank ConnectingSports zu interagieren.
     * @return Profil\Model\SportartTable
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
     * Holt die PHP-Datei mitgliedTable, die Funktionen enth&auml;lt, um mit der Tabelle mitglied
     * in der Datenbank ConnectingSports zu interagieren. Wird z.B.: f&uuml;r Login, Registrieren und Profil ben&ouml;tigt.
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