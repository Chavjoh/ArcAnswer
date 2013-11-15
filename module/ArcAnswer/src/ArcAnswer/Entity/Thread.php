<?php
namespace ArcAnswer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * Thread Entity.
 *
 * @ORM\Entity
 * @ORM\Table(name="thread")
 */
class Thread implements InputFilterAwareInterface
{
	protected $inputFilter;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", name="title", length=100)
	 */
	protected $title;

	/**
	 * @ORM\ManyToOne(targetEntity="Post")
	 * @ORM\JoinColumn(name="main_post_id", referencedColumnName="id")
	 */
	protected $mainPost;

	/**
	 * @ORM\OneToMany(targetEntity="Post", mappedBy="thread")
	 */
	protected $posts;

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
			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}

	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Les filtres sont déjà définis directement dans le modèle");
	}
}