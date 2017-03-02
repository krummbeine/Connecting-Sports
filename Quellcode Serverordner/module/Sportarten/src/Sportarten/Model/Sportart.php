<?php
// module/Sportarten/src/Sportarten/Model/Sportarten.php:
namespace Sportarten\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;



/**
 * 
 * @author Helbig Christian www.krummbeine.de
 *
 */
class Sportart implements InputFilterAwareInterface
{
    public $sportart_id;
    public $titel;
    public $bild_url;
    protected $inputFilter;  //Eingabefilter

    /**
     * 
     * @param unknown $data
     */


    /*Die Funktion exchangeArray wird ben&ouml;tigt, damit wir mit der Klasse TableGateway von Zend arbeiten k&ouml;nnen.*/
    /*Durch diese Funktion werden die Spalten automatisch ausgef&uuml;llt */
    public function exchangeArray($data)
    {
        //isset — Pr&uuml;ft, ob eine Variable existiert und ob sie nicht NULL ist
        $this->sportart_id     = (isset($data['sportart_id']))     ? $data['sportart_id']     : null;
        $this->titel = (isset($data['titel'])) ? $data['titel'] : null;
        $this->bild_url  = (isset($data['bild_url']))  ? $data['bild_url']  : null;
    }

    /**
     * 
     */
    //Erstellt eine Kopie des Array-Objects
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * 
     * @param InputFilterInterface $inputFilter
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * 
     * @return \Zend\InputFilter\InputFilter
     */
    //F&uuml;r jede Spalte wird ein Input hinzugef&uuml;gt,um Eingaben zu filtern und zu validieren.
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'sportart_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'titel',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),       //unerw&uuml;nschte HTML-Tags entfernen
                    array('name' => 'StringTrim'),      //unn&ouml;tige Leerzeichen entfernen
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));

            /*$inputFilter->add($factory->createInput(
                array(
                    'name' => 'bild_url',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => '\Application\Validators\File\Image',
                            'options' => array(
                                'minSize' => '64',
                                'maxSize' => '1024',
                                'newFileName' => 'newFileName2',
                                'uploadPath' => './upload/sportart/'
                            )
                        )
                    )
                )
            ));*/

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}