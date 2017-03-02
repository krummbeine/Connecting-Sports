<?php
namespace Profil\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Profil\Model\Mitglied;
use Profil\Form\ProfilForm;
use Hilfsfunktionen\Authorisation;


/**
 * Beschreibt den Controller Profil
 * Verwaltet Methoden, um das Profil eines Mitglieds anzuzeigen, zu bearbeiten und zu loeschen.
 * @author Helbig Christian www.krummbeine.de
 */
class ProfilController extends AbstractActionController
{
	// benoetigte Tables
    protected $mitgliedTable;
    protected $kommentarTable;
    protected $zusageTable;
    protected $bewertungTable;
    protected $lieblingssportartTable;
    
    /**
     * Meldet den Nutzer ab und loescht alle gespeicherten Login-Daten in der Sitzung
     * @return redirect Weiterleitung zu Login nach Abmeldung
     */
    public function abmeldenAction()
    { 	    	
    	// In der Sitzung gespeicherte Anmeldedaten loeschen
    	Authorisation::setMitgliedId(null);
    	Authorisation::setMitgliedName(null);
    	Authorisation::setPasswort(null);
    	
    	// Weiterleiten zum Login fuer erneute Anmeldung
    	return $this->redirect()->toRoute('login');
    }
    
    /**
     * 
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {    
    	// Das Profil soll nur fuer angemeldete Nutzer angezeigt werden.
    	// Pruefen, ob ein Mitglied angemeldet ist mit gueltigen gespeicherten Anmelde-Daten.
    	if(Authorisation::istAngemeldet($this->getMitgliedTable(), $this))
    	{
    		$fremdes_profil = false;
    		$zu_ladende_id = Authorisation::getMitgliedId();
    		
    		// Anmeldedaten wurden geprueft und duerfen geladen werden.
	    	// Pruefen, ob mitglied_id uebergeben wurde:
	    	if(null !== $this->params()->fromRoute('id')){
	    		// Es soll ein Mitglied geladen werden mit der mitglied_id: $zu_ladende_id
	    		$zu_ladende_id = $this->params()->fromRoute('id');
	    	}
	    	
	    	// Gehoert dem Nutzer das Profil? Oder laedt das Mitglied ein fremdes Profil?
	    	if($zu_ladende_id == Authorisation::getMitgliedId())
	    	{
	    		$fremdes_profil = null;
	    	}
	    	else
	    	{
	    		$fremdes_profil = true;
	    	}
	    	
	    	// Der aktuell angemeldete Nutzer selbst moechte sein Profil oeffnen
	    	return new ViewModel(array(
	    			'mitglied' => $this->getMitgliedTable()->getMitgliedById($zu_ladende_id),
	    			'meine_zusagen' => $this->getZusageTable()->meineZusagen($zu_ladende_id),
	    			'mitglieder_die_ich_mag' => $this->getBewertungTable()->mitgliederDieIchMag($zu_ladende_id),
	    			'mitglieder_die_mich_moegen' => $this->getBewertungTable()->mitgliederDieMichMoegen($zu_ladende_id),
	    			'lieblingssportarten' => $this->getLieblingssportartTable()->fetchAll($zu_ladende_id),
	    			'anzahl_likes' => $this->getBewertungTable()->meineBewertungSumme($zu_ladende_id, "war_sympathisch", "1"),
	    			'anzahl_anwesend' => $this->getBewertungTable()->meineBewertungSumme($zu_ladende_id, "war_anwesend", "1"),
	    			'anzahl_abwesend' => $this->getBewertungTable()->meineBewertungSumme($zu_ladende_id, "war_anwesend", "0"),
	    			'fremdes_profil' => $fremdes_profil,
	    	));
    	}
    }

    public function editAction()
    {    	 
    	if(Authorisation::istAngemeldet($this->getMitgliedTable(), $this))
    	{
    		$id = (int) $this->params()->fromRoute('id', 0);
    		if ($id != Authorisation::getMitgliedId()) {
    			// Nutzer will anderes Profil bearbeiten, wuerde jedoch sein eigenes bearbeiten
    			// Wird unterbunden -> zum Login weiterleiten.
    			return $this->redirect()->toRoute('login');
    		}
    		
    		$mitglied_id = Authorisation::getMitgliedId();
	        $mitglied = $this->getMitgliedTable()->getMitgliedById($mitglied_id);
	
	        $form  = new ProfilForm();
	
	        $form->bind($mitglied);
	        $form->get('submit')->setAttribute('value', 'Edit');
	
	        $request = $this->getRequest();
	        if ($request->isPost()) {
	            $form->setInputFilter($mitglied->getInputFilter(true));
	            $form->setData($request->getPost());
	
	            if ($form->isValid()) {
	                $this->getMitgliedTable()->saveMitglied($form->getData(), Authorisation::getMitgliedId() == 0);
	
	                // Weiterleiten zum Profil
	                return $this->redirect()->toRoute('profil');
	            }
	        }
	
	        return array(
	            'id' => $mitglied_id,
	            'form' => $form,
	        );
    	}
    }

    public function deleteAction()
    {    	
    	if(Authorisation::istAngemeldet($this->getMitgliedTable(), $this))
    	{    		
			$mitglied_id = Authorisation::getMitgliedId();
			
	        $request = $this->getRequest();
	        if ($request->isPost()) {
	            $del = $request->getPost('del', 'Nein');
	
	            // Dialog-Abfrage auswerten, ob der Nutzer wirklich geloescht werden soll:
	            if ($del == 'Ja') {
	            	// Der Nutzer soll geloescht werden:
	            	// Referenzierte vom Nutzer erstellte Dinge loeschen (au�er Sportarten und Sportevents)
	            	// Sportarten duerfen nur vom Admin geloescht werden
	            	// Sportevents muessen manuell geloescht werden! -> loescht dann auch alle dem Event zugeordnete Dinge
	            	$this->getKommentarTable()->deleteKommentarByMitgliedId($mitglied_id);
	            	$this->getZusageTable()->deleteZusageByMitgliedId($mitglied_id);
	            	$this->getBewertungTable()->deleteBewertungByMitgliedId($mitglied_id);
	            	
	            	// Den Nutzer selbst loeschen
	                $this->getMitgliedTable()->deleteMitglied($mitglied_id);
	                
	                // Abmelden
	                $this->abmeldenAction();
	            }
				else {
	            // Weiterleiten zum Profil
	            return $this->redirect()->toRoute('profil');
				}
	        }
	        else {
	        	$id = (int) $this->params()->fromRoute('id', 0);
                //id der Route != der der Angemeldeten Person ($mitglied_id)
	        	if ($id != Authorisation::getMitgliedId()) {
	        		// Nutzer will anderes Profil loeschen, wuerde jedoch sein eigenes loeschen
	        		// Wird unterbunden -> zum Login weiterleiten.
	        		return $this->redirect()->toRoute('login');
	        	}
	        }
	        
	        return new ViewModel(array(
	        		'mitglied_name' => Authorisation::getMitgliedName(),
	        		'mitglied_id' => Authorisation::getMitgliedId(),
	        ));
    	}
    }


    /**
     * Holt das Model fuer die Mitglieder
     * @return MitgliedTable Das Model zum Ansteuern der Datenbank mit der Entitaet "mitglied"
     */
    public function getMitgliedTable()
    {
        if (!$this->mitgliedTable) {
            $sm = $this->getServiceLocator();
            $this->mitgliedTable = $sm->get('Profil\Model\MitgliedTable');
        }
        return $this->mitgliedTable;
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
    public function getBewertungTable()
    {
    	if (!$this->bewertungTable) {
    		$sm = $this->getServiceLocator();
    		$this->bewertungTable = $sm->get('Sportevent\Model\BewertungTable');
    	}
    	return $this->bewertungTable;
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
}
