<?php
namespace ArcAnswer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

use ArcAnswer\Entity\Post;
use ArcAnswer\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vote Entity.
 *
 * @ORM\Entity
 * @ORM\Table(name="vote")
 */
class Vote implements InputFilterAwareInterface
{
	/**
	 * Normalized input filter
	 * @var InputFilter
	 */
	protected $inputFilter;

	/**
	 * @ORM\Id
	 * @ORM\OneToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
	 */
	protected $id_user;

	/**
	 * @ORM\Id
	 * @ORM\OneToOne(targetEntity="Post")
	 * @ORM\JoinColumn(name="id_post", referencedColumnName="id_post")
	 */
	protected $id_post;

	/**
	 * @ORM\Column(type="integer", name="value_vote")
	 */
	protected $value;

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
				'name' => 'id_user',
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
					),
				),
			)));
			$inputFilter->add($factory->createInput(array(
				'name' => 'id_post',
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
					),
				),
			)));
			$inputFilter->add($factory->createInput(array(
				'name' => 'value',
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
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