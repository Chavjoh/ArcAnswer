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

	public function __construct()
	{
		$this->posts = new ArrayCollection();
	}

	public function __get($property)
	{
		return $this->$property;
	}

	public function __set($property, $value)
	{
		$this->$property = $value;
	}

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

	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Les filtres sont déjà définis directement dans le modèle");
	}
}