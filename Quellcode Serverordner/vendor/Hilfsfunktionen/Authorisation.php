<?php
namespace Hilfsfunktionen;
use Zend\Session\Container;

/**
 * Hilfsfunktionen, die von der gesamten Application genutzt werden k&ouml;nnen.
 * Enth&auml;lt f&uuml;r den Nutzerfluss Methoden, die den Nutzer Zur&uuml;ckleiten, falls dieser einen Schritt &uuml;bersprungen hat
 * wie z.B.: Auswahl der Sportart oder Auswahl des Sportevents bevor das Sportevent aufgerufen wird.
 * Enth&auml;lt Getter und Setter f&uuml;r Sitzungsvariablen
 * @author Helbig Christian www.krummbeine.de
 */
class Authorisation {
	
	// Statische Variable $sitzung, die Sitzungs-Daten enth&auml;lt wie passwort, mitglied_id, mitglied_name,..
	static $sitzung;
	
	/**
	 * Initialisiert die Sitzung
	 * Wird aufgerufen am Ende der Datei Authorisation.php (dieser Datei)
	 */
	static function initialisieren(){
		// Die statische Variable $sitzung initialisieren
		self::$sitzung = new container('sitzung');
	}
	
	/**
	 * Eine Methode, die zum Test von z.B. einer Controller-Action aufgerufen werden kann.
	 * Damit wird gepr&uuml;ft, dass die Klasse Authorisation erfolgreich eingebunden wurde.
	 */
	public static function test(){
		print("Aufruf der Test-Funktion in Klasse Authorisation war erfolgreich! ID: ".Authorisation::$sitzung->mitglied_id);
	}
	
	// Nutzerfluss-Pr&uuml;fungen // // // // // // // // // // // // // // // // // // //
	
	/**
	 * Pr&uuml;ft, ob der Nutzer ein Sportevent ausgew&auml;hlt hat.
	 * Wenn nicht, wird dieser auf entsprechende Seite umgeleitet, um dies nachzuholen.
	 * @param Sportevent\Controller Der Controller, von dem die Funktion aufgerufen wurde.
	 * @return redirect Eventuelle Weiterleitung zur entsprechenden Seite
	 */
	public static function istSporteventAusgewaehlt($controller)
	{		 
		// Wenn kein Sportevent ausgew&auml;hlt wurde, wird zu Sportevent-Auswahl (Sportart-Modul) weitergeleitet
		if(!self::getSporteventId())
			return $controller->redirect()->toRoute('sportart');
	}
	
	/**
	 * Pr&uuml;ft, ob der Nutzer eine Sportart ausgew&auml;hlt hat.
	 * Wenn nicht, wird dieser auf entsprechende Seite umgeleitet, um dies nachzuholen.
	 * @param Sportevent\Controller Der Controller, von dem die Funktion aufgerufen wurde.
	 * @return redirect Eventuelle Weiterleitung zur entsprechenden Seite
	 */
	public static function istSportartAusgewaehlt($controller)
	{
		// Wenn keine Sportart ausgew&auml;hlt wurde, wird zur Startseite (Sportarten-Modul) weitergeleitet
		if(!self::getSportartId())
			return $controller->redirect()->toRoute('sportarten');
	}
	
	// Authorisations-Pr&uuml;fungen // // // // // // // // // // // // // // // // // // //
	
	/**
	 * Pr&uuml;ft, ob ein Nutzer g&uuml;ltig angemeldet ist, indem die Funktion die gespeicherten Anmelde-Daten
	 * aus der Sitzung mit denen in der Datenbank abgleicht. Es wird sozusagen immer wieder ein neuer
	 * Login gemacht.
	 * @param Profil\Model $mitgliedTable Das Model zur Ansteuerung der Datenbank-Tabelle mitglied
	 * @param Controller $controller Der Controller, von dem der Aufruf kommt f&uuml;r die Weiterleitung
	 * @return boolean true: Nutzer ist g&uuml;ltig angemeldet - sonst Weiterleitung zum Login
	 */
	public static function istAngemeldet($mitgliedTable, $controller){		 
		if(self::getMitgliedId() !== null){
			// Das Mitglied aus der Datenbank holen:
			$treffer = $mitgliedTable->getMitgliedById(self::getMitgliedId());
			// Pr&uuml;fen, ob das in der Session gespeicherte Passwort und ID g&uuml;ltig sind
			if($treffer->passwort == self::getPasswort())
			{
				// Die gespeicherten Anmelde-Daten sind g&uuml;ltig.
				// Die Pr&uuml;fung war erfolgreich
				// Zur&uuml;ckgeben, dass der Nutzer (g&uuml;ltig) angemeldet ist.
				return true;
			}
		}
		// Die gespeicherten Anmeldedaten sind ung&uuml;ltig.
		// L&ouml;sche die Anmeldung.
		self::setMitgliedId(null);
		self::setMitgliedName(null);
		self::setPasswort(null);
		
		// Zur&uuml;ckgeben, dass Nutzer nicht (g&uuml;ltig) angemeldet ist.
		// Die Pr&uuml;fung ist fehlgeschlagen
		// Nutzer ist ung&uuml;ltig / nicht angemeldet --> Weiterleitung zum Login
		return $controller->redirect()->toRoute('login');
	}
	
	/**
	 * Pr&uuml;ft, ob der Nutzer berechtigt ist, die Actions in einem Controller $controller auszuf&uuml;hren.
	 * Der Nutzer muss zuvor ggf. Sportart, Sportevent ausgew&auml;hlt haben und immer g&uuml;ltig angemeldet sein.
	 * @param bool $sportart_pruefen Wenn true: Es soll gepr&uuml;ft werden, ob die Sportart ausgew&auml;hlt ist
	 * @param bool $sportevent_pruefen Wenn true: Es soll gepr&uuml;ft werden, ob ein Sportevent ausgew&auml;hlt ist
	 * @param MitgliedTable $mitgliedTable F&uuml;r die Pr&uuml;fung einer g&uuml;ltigen Anmeldung, wird der MitgliedTable ben&ouml;tigt
	 * @param Controller $controller Der Controller, von dem der Aufruf kommt f&uuml;r die Weiterleitung
	 */
	public static function berechtigt($sportart_pruefen, $sportevent_pruefen, $mitgliedTable, $controller){
		// Pr&uuml;fen, ob der Nutzer eine Sportart und ein Sportevent ausgew&auml;hlt hat (falls gew&uuml;nscht)
		// Wenn nicht, wird dieser auf entsprechende Seite umgeleitet, um dies nachzuholen
		if($sportart_pruefen)
			self::istSportartAusgewaehlt($controller);
		if($sportevent_pruefen)
			self::istSporteventAusgewaehlt($controller);
		// Wird diese Funktion aufgerufen, soll immer gepr&uuml;ft werden, ob der Nutzer
		// g&uuml;ltig angemeldet ist. Die Anmeldung ist Voraussetzung f&uuml;r &auml;nderungen an der Datenbank
		// und den Aufruf der Profile
		self::istAngemeldet($mitgliedTable, $controller);
	}

	// Getter-Methoden // // // // // // // // // // // // // // // // // // //
	
	/**
	 * Getter f&uuml;r Sitzungs-Variablen
	 * @return INT mitglied_id
	 */
	public static function getMitgliedId(){
		return self::$sitzung->mitglied_id;
	}
	
	/**
	 * Getter f&uuml;r Sitzungs-Variablen
	 * @return String mitglied_name
	 */
	public static function getMitgliedName(){
		return self::$sitzung->mitglied_name;
	}
	
	/**
	 * Getter f&uuml;r Sitzungs-Variablen
	 * @return STRING Eingestellte Sprache f&uuml;r die Anwendung
	 */
	public static function getSprache(){
		// Wenn keine Sprache eingestellt ist, standardm&auml;ßig Deutsch einstellen
		if(!isset(self::$sitzung->sprache))
			self::$sitzung->sprache = "de_DE";
		return self::$sitzung->sprache;
	}
	
	/**
	 * Getter f&uuml;r Sitzungs-Variablen
	 * @return INT sportevent_id
	 */
	public static function getSporteventId(){
		return self::$sitzung->sportevent_id;
	}
	
	/**
	 * Getter f&uuml;r Sitzungs-Variablen
	 * @return INT sportart_id
	 */
	public static function getSportartId(){
		return self::$sitzung->sportart_id;
	}
	
	/**
	 * Getter f&uuml;r Sitzungs-Variablen
	 * @return STRING passwort (ist md5-verschl&uuml;sselt!)
	 */
	public static function getPasswort(){
		return self::$sitzung->passwort;
	}
	
	// Setter-Methoden // // // // // // // // // // // // // // // // // // //
	
	/**
	 * Setter f&uuml;r Sitzungs-Variable mitglied_id
	 * @param INT $mitglied_id
	 */
	public static function setMitgliedId($mitglied_id){
		self::$sitzung->mitglied_id = $mitglied_id;
	}
	
	/**
	 * Setter f&uuml;r Sitzungs-Variable sprache
	 * @param STRING $sprache Einzustellende Sprache f&uuml;r die Applikation
	 */
	public static function setSprache($sprache){
		self::$sitzung->sprache = $sprache;
	}
	
	/**
	 * Setter f&uuml;r Sitzungs-Variable mitglied_name
	 * @param String $mitglied_name
	 */
	public static function setMitgliedName($mitglied_name){
		self::$sitzung->mitglied_name = $mitglied_name;
	}
	
	/**
	 * Setter f&uuml;r Sitzungs-Variable sportevent_id
	 * @param INT $sportevent_id
	 */
	public static function setSporteventId($sportevent_id){
		self::$sitzung->sportevent_id = $sportevent_id;
	}
	
	/**
	 * Setter f&uuml;r Sitzungs-Variable sportart_id
	 * @param INT $sportart_id
	 */
	public static function setSportartId($sportart_id){
		self::$sitzung->sportart_id = $sportart_id;
	}
	
	/**
	 * Setter f&uuml;r Sitzungs-Variable passwort
	 * @param String $passwort
	 */
	public static function setPasswort($passwort){
		self::$sitzung->passwort = $passwort;
	}
}

// Die Klasse Authorisation initialisieren (Session laden)
Authorisation::initialisieren();

