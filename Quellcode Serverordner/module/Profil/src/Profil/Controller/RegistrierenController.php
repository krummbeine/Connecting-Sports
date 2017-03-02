<?php
namespace Profil\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Profil\Model\Mitglied;
use Profil\Form\RegistrierenForm;
use Hilfsfunktionen\Authorisation;

/**
 * Beschreibt den RegistrierenController.
 * Bereitet das Formular und View f&uuml;r den Registrierung.
 * L&auml;dt die Validatoren und pr&uuml;ft Eingaben des Formulars.
 * Sucht ein Mitglied, dessen Pseudonym mit der Eingabe vom Formular &uuml;bereinstimmt.
 * Es darf nicht bereits ein Mitglied mit dem Pseudonym existieren.
 * Sind alle Formulareingaben korrekt und validiert, wird das Mitglied registriert.
 * Dazu werden die Formular-Eingaben in die Datenbank gespeichert via MitgliedTable.php.
 * Es wird gepr&uuml;ft, ob die Registrierung erfolgreich war.
 * In diesem Fall wird der Nutzer angemeldet (in Session Anmelde-Daten speichern)
 * Bei erfolgreicher Registrierung bzw. Anmeldung, wird der Nutzer zum Profil weitergeleitet.
 * @author Helbig Christian www.krummbeine.de
 */
class RegistrierenController extends AbstractActionController
{
	// Die Registrierung greift auf die Tabelle mitglied in der Datenbank ConnectingSports zu.
    protected $mitgliedTable;

    public function indexAction()
    {    	
    	// Das Formular festlegen
    	$form = new RegistrierenForm();
    	// Button-Text festlegen
    	$form->get('submit')->setValue('Registrieren');
    	
    	// Den Request auslesen, um Formulardaten/.. zu erhalten
    	$request = $this->getRequest();
    	
    	// Pr&uuml;fen, ob der Submit-Button des Formulars bet&auml;tigt wurde (Post abgesendet)
    	if ($request->isPost()) {
    		 
    		// Dummy-Mitglied-Objekt, um Formular-Validierung durchzuf&uuml;hren
    		$mitglied = new Mitglied();
    		// Validatoren in das Formular laden
    		$form->setInputFilter($mitglied->getInputFilter(true));
    		// Formular mit Eingaben des letzten Registrierungs-Versuchs f&uuml;llen (Pseudonym-Feld)
    		$form->setData($request->getPost());
    		 
    		// Formular auf G&uuml;ltigkeit pr&uuml;fen
    		if ($form->isValid()) {
    			// &uuml;bergebenes Daten-Array des Posts auslesen vom Formular
    			$post_data = $request->getPost();
    			// Das Pseudonym der Formular-Eingabe auslesen
    			$pseudonym = $post_data['pseudonym'];
    			
    			// Pr&uuml;fen, ob Pseudonym bereits in der Datenbank existiert
    			// Ein Mitglied holen, bei dem das Pseudonym &uuml;bereinstimmt
    			$treffer = $this->getMitgliedTable()->getMitgliedByPseudonym($pseudonym);
    			// Pr&uuml;fen, ob es ein Mitglied ($treffer) gab, bei dem das Pseudonym &uuml;bereinstimmt
    			if($treffer){
    				// An die View Zur&uuml;ckgeben, dass bereits ein Mitglied mit diesem Pseudonym existiert.
    				// Das Pseudonym muss ein Mitglied eindeutig identifizieren und darf nur einmal vorkommen.
    				return array(
    						'form' => $form,
    						'mitglied_name' => Authorisation::getMitgliedName(),
    						'mitglied_id' => Authorisation::getMitgliedId(),
    						'pseudonym_existiert_bereits' => true,
    				);
    			}
    			else{
    				// Pseudonym darf verwendet werden, da kein anderes Mitglied es verwendet hat.
    				// Registrierung ausf&uuml;hren:
    				// Mitglied-Daten aus dem Formular dem Mitglied zuweisen
    				$mitglied->exchangeArray($form->getData());
    				// Das Mitglied in die Datenbank speichern (Insert wird ausgef&uuml;hrt, da noch nicht vorhanden)
    				$this->getMitgliedTable()->saveMitglied($mitglied, false);
    				
    				// Erstelltes Mitglied wieder auslesen
    				$treffer = $this->getMitgliedTable()->getMitgliedByPseudonym($pseudonym);
    				// Pr&uuml;fen, ob Registrierung erfolgreich war
    				if($treffer)
    				{
    					// Erstelltes Mitglied existiert
    					
	    				// Anmelde-Daten des Logins in der Session speichern
    					Authorisation::setMitgliedId($treffer->mitglied_id);
    					Authorisation::setMitgliedName($treffer->pseudonym);
    					Authorisation::setPasswort($treffer->passwort);
	    				
	    				// Redirect: Weiterleiten zum Profil, da Registrierung und Anmeldung erfolgreich
	    			 	return $this->redirect()->toRoute('profil');
    				}
    				
    				// Fehler aufgetreten und an View melden
    				return array(
    						'form' => $form,
    						'mitglied_name' => Authorisation::getMitgliedName(),
    						'mitglied_id' => Authorisation::getMitgliedId(),
    						'fehler' => true,
    				);
    			}
    		}
    	}

    	// Das Formular wurde noch nicht abgesendet (POST) via Submit-Button
    	return array(
    			'form' => $form,
    			'mitglied_name' => Authorisation::getMitgliedName(),
    			'mitglied_id' => Authorisation::getMitgliedId(),
    	);
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
