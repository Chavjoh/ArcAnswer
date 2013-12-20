<?php
namespace ArcAnswer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tag Entity.
 *
 * @ORM\Entity
 * @ORM\Table(name="tag")
 */
class Tag implements InputFilterAwareInterface
{
	/**
	 * Normalized input filter
	 * @var InputFilter
	 */
	protected $inputFilter;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id_tag")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", name="name_tag", length=250)
	 */
	protected $name;

	/**
	 * Default constructor
	 */
	public function __construct()
	{
		$this->posts = new ArrayCollection();
	}

	/**
	 * Magic getter for protected attributes
	 * @param String $property Name of property to get
	 * @return mixed
	 */
	public function __get($property)
	{
		return $this->$property;
	}

	/**
	 * Magic setter for protected attributes
	 * @param String $property Name of property to set
	 * @param String $value Value to set
	 */
	public function __set($property, $value)
	{
		$this->$property = $value;
	}

	/**
	 * Prepare and return input filter
	 * @return InputFilter
	 */
	public function getInputFilter()
	{
		if (!$this->inputFilter)
		{
			$inputFilter = new InputFilter();
			$factory = new InputFactory();

			$inputFilter->add($factory->createInput(array(
				'name' => 'name',
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'options' => array(
							'messages' => array(
								\Zend\Validator\NotEmpty::IS_EMPTY => 'A tag seems to be empty... STOP IT NOW !',
							),
						),
					),
				),
			)));

			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}

	/**
	 * Define input filter
	 * @param InputFilterInterface $inputFilter
	 * @return void|InputFilterAwareInterface
	 * @throws \Exception
	 */
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Les filtres sont déjà définis directement dans le modèle");
	}
}