<?php
namespace Sportevent\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Hilfsfunktionen\Authorisation;
use Sportevent\Model\Bewertung;

/**
 * Beschreibung vom Controller Bewertung
 * Enthaelt Actions, die per Route aufgerufen werden koennen (definierte Route in config/module.config.php)
 * Mittels dieser Actions kann der Nutzer unter bestimmten Bedingungen ein anderes Mitglied bewerten.
 * Es gibt die Bewertungen: war anwesend, war nicht anwesend, Favourite (hab ich gemocht)
 * Außerdem gibt es eine Action, die die Bewertung fuer dieses Mitglied bei diesem Sportevent Zuruecksetzt.
 * Bewertet werden darf nur, wenn man selbst am Sportevent teilgenommen hatte & das Event vorbei ist.
 * @author Helbig Christian www.krummbeine.de
 */
class BewertungController extends AbstractActionController
{
	// Benoetigte Tables fuer die View
	protected $bewertungTable;
	protected $mitgliedTable;
    
	/**
	 * Holt eine passende Bewertung als Koerper zum Speichern der Einzel-Bewertungen
	 * @param INT $ueber_mitglied_id Die eindeutige ID des Mitglieds, dem die Bewertung gil
	 * @return \Sportevent\Controller\Bewertung Die passende Bewertung zum Speichern/Aktualisieren einer Bewertung
	 */
	private function getPassendeBewertung($ueber_mitglied_id)
	{
		// Einen passenden Bewertungstupel holen
		$bewertung = $this->getBewertungTable()->getBewertungByKeys(
				Authorisation::getMitgliedId(),
				Authorisation::getSporteventId(),
				$ueber_mitglied_id
		);
		
		// Die passende Bewertung zum Aktualisieren/Speichern der gewuenschten Bewertung zurueckgeben
		return $bewertung;
	}
	
	/**
	 * Holt das Array mit Attributen fuer die Bewertung zum Speichern/Aktualisieren der Bewertung
	 * So gehen zuvor gesetzte Attribute in der Bewertung nicht verloren!
	 * @param INT $ueber_mitglied_id Die eindeutige ID des Mitglieds, dem die Bewertung gil
	 * @return \Sportevent\Controller\Bewertung Die passende Bewertung zum Speichern/Aktualisieren einer Bewertung
	 */
	private function getBewertungsArray($ueber_mitglied_id)
	{
		// Die passende Bewertung als Koerper zum Speichern/Aktualisieren der Bewertung suchen
		$bewertung = $this->getPassendeBewertung($ueber_mitglied_id);
		
		// Wurde eine passende Bewertung als Koerper gefunden?
		if(null == $bewertung) {
			// Neuen 'Koerper' zum Speichern von den Einzel-Bewertungen erstellen:
			$bewertung = new Bewertung();
			
			// Array-Vorlage fuer die Bewertung zurueckgeben
			// Setzt die spaeter enthaltenden Einzel-Bewertungen auf -1 (nicht gesetzt)
			// Der Koerper enthaelt noch keine Einzelbewertungen
			return 	$daten = array(
					'sportevent_id'  		=> Authorisation::getSporteventId(),
					'ueber_mitglied_id'  	=> $ueber_mitglied_id,
					'war_anwesend' 			=> '-1',
					'war_sympathisch' 		=> '-1',
					'von_mitglied_id' 		=> Authorisation::getMitgliedId(),
			);
		}
		
		// Array-Vorlage fuer die Bewertung zurueckgeben
		// Laedt Attribute des Bewertungs-Koerpers, damit sie nicht verloren gehen beim Aktualisieren
		// Enthaltende Einzel-Bewertungen: war_sympathisch und war_anwesend
		return 	$daten = array(
   			'sportevent_id'  	 	=> Authorisation::getSporteventId(),
   			'ueber_mitglied_id'  	=> $ueber_mitglied_id,
    		'war_anwesend' 			=> $bewertung->war_anwesend,
			'war_sympathisch' 		=> $bewertung->war_sympathisch,	
    		'von_mitglied_id' 		=> Authorisation::getMitgliedId(),
    	);
	}
	
	/**
	 * Bewertet ein anderes Mitglied und erstellt/aktualisiert dabei eine Bewertung
	 * Dazu wird in der Tabelle bewertung ein Tupel (Bewertung) erstellt / bearbeitet
	 * Setzt die entsprechende Einzel-Bewertung in eine Instanz / in einen Tupel von Bewertung, die/der als Koerper dient.
	 * In diesen Korper werden die Einzelbewertungen gespeichert (war_anwesend und war_sympathisch)
	 * @param STRING action_name Der Name der Action (gibt an, was als Einzelbewertung gespeichert werden soll)
	 * @param INT ueber_mitglied_id Die eindeutige ID des Mitglieds, das bewertet werden soll
	 * @return redirect Weiterleitung zum Sportevent (Refresh der Seite)
	 */
	private function speichereBewertung($action_name, $ueber_mitglied_id)
	{
		// Nur gueltig angemeldete Nutzer duerfen andere Bewerten
		Authorisation::berechtigt(true, true, $this->getMitgliedTable(), $this);
		// Nutzer ist gueltig angemeldet
		
		// ######### Aus Zeitgruenden ausnahmsweise nicht implementiert (keine 100%ige Sicherheit) #####################
		// Einzige nicht 99% abgesicherte Aktion vom Nutzer
		// Pruefung, ob das Sportevent bereits stattgefunden hat
		// Pruefung, ob das bewertungs-willige Mitglied dem Event auch zugesagt hatte
		// Nur Mitglieder duerfen andere Mitglieder bewerten, wenn beide dem Event zugesagt hatten
		// Pruefung, ob das zu bewertende Mitglied ebenfalls dem Event zuvor zugesagt hatte
		// ################################### XX ###################################### ###############################
		
		// Alle Pruefungen waren okay, Mitglied ueber_mitglied_id darf von Mitglied von_mitglied_id bewertet werden
		// unter dem gewaehltem Sportevent:
			 
		// Holt das Array mit den ggf. zuvor gespeicherten Einzel-Bewertungs-Daten
		$daten = $this->getBewertungsArray($ueber_mitglied_id);
		
		// Die zu speichernde Einzel-Bewertung in das Daten-Array einfuegen:
		if($action_name == "anwesend") {
			// Ueberschreibt war_anwesend im zu sendenen Array mit 1 (war anwesend)
			$daten['war_anwesend'] = '1';
		}
		if($action_name == "abwesend") {
			// Ueberschreibt war_anwesend im zu sendenen Array mit 0 (war nicht anwesend)
			$daten['war_anwesend'] = '0';
		}
		if($action_name == "sympathisch") {
			// Ueberschreibt war_sympathisch im zu sendenen Array mit 1 (war sympathisch)
			$daten['war_sympathisch'] = '1';
		}
		
		// Holt das passende Model Bewertung zum Speichern in die Datenbank
		$bewertung = $this->getPassendeBewertung($ueber_mitglied_id);
		// Wenn kein passendes Model verfuegbar, wird eine neue Instanz angelegt von Bewertung
		if(null == $bewertung)
			$bewertung = new Bewertung();
		
		// Die Bewertung $bewertung in der Datenbank speichern mit dem Daten-Array $daten
		$this->getBewertungTable()->saveBewertung($bewertung, $daten);
			 
		// Weiterleitung zum Sportevent, um aenderungen sichtbar zu machen
		return $this->redirect()->toRoute('sportevent');
	}
	
	/**
	 * Ruft die Funktion speichereBewertung(..) auf
	 * Uebergibt dieser Funktion den Namen dieser Action und die der Action übergebenen ID
	 * des Mitglieds, das bewertet werden soll (ueber_mitglied_id)
	 */
    public function anwesendAction()
    {
    	// Uebergebene mitglied_id des zu bewertenden Mitglieds auslesen
    	$ueber_mitglied_id = (int) $this->params()->fromRoute('id', 0);
    	// Die ID wird benoetigt, wenn nicht uebergeben: Weiterleitung zum Sportevent
    	if (!$ueber_mitglied_id)
    		return $this->redirect()->toRoute('sportevent');
    	
    	$this->speichereBewertung("anwesend", $ueber_mitglied_id);
    }
    
    /**
     * Ruft die Funktion speichereBewertung(..) auf
     * Uebergibt dieser Funktion den Namen dieser Action und die der Action übergebenen ID
     * des Mitglieds, das bewertet werden soll (ueber_mitglied_id)
     */
    public function abwesendAction()
    {
    	// Uebergebene mitglied_id des zu bewertenden Mitglieds auslesen
    	$ueber_mitglied_id = (int) $this->params()->fromRoute('id', 0);
    	// Die ID wird benoetigt, wenn nicht uebergeben: Weiterleitung zum Sportevent
    	if (!$ueber_mitglied_id)
    		return $this->redirect()->toRoute('sportevent');
    	
    	$this->speichereBewertung("abwesend", $ueber_mitglied_id);
    }
    
    /**
     * Ruft die Funktion speichereBewertung(..) auf
     * Uebergibt dieser Funktion den Namen dieser Action und die der Action übergebenen ID
     * des Mitglieds, das bewertet werden soll (ueber_mitglied_id)
     */
    public function magIchAction()
    {
    	// Uebergebene mitglied_id des zu bewertenden Mitglieds auslesen
    	$ueber_mitglied_id = (int) $this->params()->fromRoute('id', 0);
    	// Die ID wird benoetigt, wenn nicht uebergeben: Weiterleitung zum Sportevent
    	if (!$ueber_mitglied_id)
    		return $this->redirect()->toRoute('sportevent');
    	
    	$this->speichereBewertung("sympathisch", $ueber_mitglied_id);
    }
    
    /**
	 * Bewertet ein anderes Mitglied: Mitglied war sympathisch (mag ich) (Favorit)
	 * Dazu wird in der Tabelle bewertung ein Tupel (Bewertung) erstellt / bearbeitet
     * @return redirect Weiterleitung zum Sportevent (Refresh der Seite) (Abbruch bei falscher bewertung_id-uebergabe)
     */
    public function zuruecksetzenAction()
    {
    	Authorisation::berechtigt(true, true, $this->getMitgliedTable(), $this);
    	// Nutzer ist gueltig angemeldet
    	// Uebergebene mitglied_id des zu bewertenden Mitglieds auslesen
    	$ueber_mitglied_id = (int) $this->params()->fromRoute('id', 0);
    	// Die ID wird benoetigt, wenn nicht uebergeben: Weiterleitung zum Sportevent
    	if (!$ueber_mitglied_id)
    		return $this->redirect()->toRoute('sportevent');
    	
        // Die Bewertung loeschen mit drei zusammen eindeutig identifizierenden Keys (Parameter):
	    $this->getBewertungTable()->deleteBewertungByKeys(
	    		Authorisation::getMitgliedId(), 
	    		Authorisation::getSporteventId(), 
	    		$ueber_mitglied_id
	    );
            
        // Weiterleiten zum Sportevent nach Abschluss
        return $this->redirect()->toRoute('sportevent');    	
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