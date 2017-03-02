<?php
namespace Profil\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Profil\Model\Mitglied;
use Profil\Form\LoginForm;
use Hilfsfunktionen\Authorisation;

/**
 * Beschreibt den LoginController
 * Bereitet das Formular und View f&uuml;r den Login.
 * L&auml;dt die Validatoren und pr&uuml;ft Eingaben des Formulars.
 * Sucht ein Mitglied, dessen Pseudonym mit der Eingabe vom Formular &uuml;bereinstimmt.
 * Und vergleicht in diesem Fall die Passw&ouml;rter.
 * Konnte der Nutzer erfolgreich verifiziert werden, wird er angemeldet (in Session Anmelde-Daten speichern)
 * Bei erfolgreicher Anmeldung, wird der Nutzer zum Profil weitergeleitet.
 * @author Helbig Christian www.krummbeine.de
 */
class LoginController extends AbstractActionController
{
	// Der Login greift auf die Tabelle mitglied in der Datenbank ConnectingSports zu.
    protected $mitgliedTable;

    /**
     * Stellt der View ein Formular f&uuml;r den Login bereit und Anmeldedaten.
     * Validiert und pr&uuml;ft das Login-Formular.
     * Verifiziert den Nutzer mittels &uuml;bereinstimmendem Pseudonym und Passwort in der Datenbank.
     * Leitet den Nutzer bei erfolgreicher Anmeldung zum Profil weiter.
     * @return redirect Weiterleitung zum Profil|Array mit \Profil\Form\LoginForm[] und Anmeldedaten f&uuml;r die Login-View
     */
    public function indexAction()
    {        
    	// Das Formular festlegen
        $form = new LoginForm();
        // Button-Text festlegen
        $form->get('submit')->setValue('Anmelden');
        
        // Den Request auslesen, um Formulardaten/.. zu erhalten
        $request = $this->getRequest();
        
        // Pr&uuml;fen, ob der Submit-Button des Formulars bet&auml;tigt wurde (Post abgesendet)
        if ($request->isPost()) {
        	
        	// Dummy-Mitglied-Objekt, um Formular-Validierung durchzuf&uuml;hren
        	$mitglied = new Mitglied();
        	// Validatoren in das Formular laden
        	$form->setInputFilter($mitglied->getInputFilter(false));
        	// Formular mit Eingaben des letzten Login-Versuchs f&uuml;llen (Pseudonym-Feld)
        	$form->setData($request->getPost());
        	
        	// Formular auf G&uuml;ltigkeit pr&uuml;fen
        	if ($form->isValid()) {
        		// &uuml;bergebenes Daten-Array des Posts auslesen vom Formular
        		$post_data = $request->getPost();
        		// Das Pseudonym und Passwort der Formular-Eingabe auslesen
        		$pseudonym = $post_data['pseudonym'];
        		// Passwort mit md5 verschl&uuml;sseln zur Gegenpr&uuml;fung
        		$passwort = md5($post_data['passwort']);
        		
        		// Ein Mitglied holen, bei dem das Pseudonym &uuml;bereinstimmt
        		$treffer = $this->getMitgliedTable()->getMitgliedByPseudonym($pseudonym);
        		// Pr&uuml;fen, ob es ein Mitglied ($treffer) gab, bei dem das Pseudonym &uuml;bereinstimmt
        		if($treffer){
        			// Pr&uuml;fen, ob das eingegebene Passwort mit dem des aus dem Treffers &uuml;bereinstimmt
        			// Vergleich zweier verschl&uuml;sselter Passwort
        			// Sind diese gleich, muss via md5 das selbe Passwort bei der Eingabe entstanden sein, wie
        			// es bei der Registrierung des Mitglieds der Fall war.
	        		if($passwort == $treffer->passwort){
	        			// Pseudonym konnte einem Mitglied zugeordnet werden UND
	        			// Die Passw&ouml;rter stimmen &uuml;berein
	        			
	        			// Anmelde-Daten des Logins in der Session speichern
	        			Authorisation::setMitgliedId($treffer->mitglied_id);
	        			Authorisation::setMitgliedName($treffer->pseudonym);
	        			Authorisation::setPasswort($treffer->passwort);
	        			
						// Weiterleiten zum Profil, da Mitglied nun verfifiziert und angemeldet.
	       				 return $this->redirect()->toRoute('profil');
	        		}
        		}
        		
        		// Fehler Zur&uuml;ckgegeben an die View,
        		// da obige return-Anweisungen nicht aufgerufen wurden
        		// Keine Bedingung f&uuml;r erfolgreichen Login erf&uuml;llt
        		return array(
        				'form' => $form,
        				'mitglied_name' => Authorisation::getMitgliedName(),
        				'mitglied_id' => Authorisation::getMitgliedId(),
        				'falscher_login' => true,
        		);
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
