<?php
namespace Profil\Form;

use Zend\Form\Form;

class RegistrierenForm extends Form
{
	public function __construct($name = null)
	{
		parent::__construct('mitglied');

		$this->setAttribute('method', 'post');

		// mitglied_id
		$this->add(array(
				'name' => 'mitglied_id',
				'attributes' => array(
						'type'  => 'hidden',
				),
		));

		// pseudonym
		$this->add(array(
				'name' => 'pseudonym',
				'attributes' => array(
						'type'  => 'text',
				),
				'options' => array(
						'label' => _('Pseudonym '),
				),
		));

		// facebook_id
		$this->add(array(
				'name' => 'facebook_id',
				'attributes' => array(
						'type'  => 'hidden',
				),
		));

		// passwort
		$this->add(array(
				'name' => 'passwort',
				'attributes' => array(
						'type'  => 'password',
				),
				'options' => array(
						'label' => _('Passwort '),
				),
		));

		// pr&uuml;ft passwort
		$this->add(array(
			'name' => 'passwortbestaetigen',
			'attributes' => array(
				'type'  => 'password',
			),
			'options' => array(
				'label' => _('Passwort pruefen '),
			),
		));

		// geburtstag
		$this->add(array(
				'name' => 'geburtstag',
				'attributes' => array(
						'type'  => 'date',
				),
				'options' => array(
						'label' => _('Geburtstag '),
						'format' => 'Y-m-d'
				),
		));

		// meine_stadt
		$this->add(array(
				'name' => 'meine_stadt',
				'attributes' => array(
						'type'  => 'text',
				),
				'options' => array(
						'label' => _('Meine Stadt (PLZ Ortsname) '),
				),
		));
		
		// beschreibung
		$this->add(array(
				'name' => 'beschreibung',
				'attributes' => array(
						'type'  => 'textarea',
				),
				'options' => array(
						'label' => _('Beschreibe dich kurz selbst '),
				),
		));

		// submit-button
		$this->add(array(
				'name' => 'submit',
				'attributes' => array(
						'type'  => 'submit',
						'value' => _('Go'),
						'id' => 'submitbutton',
				),
		));
	}
}