<?php
namespace ArcAnswer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

use ArcAnswer\Entity\Post;
use ArcAnswer\Entity\Tag;

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

	// Cache indication if thread has solution
	protected $hasSolution = null;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id_thread")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", name="title_thread", length=250)
	 */
	protected $title;

	/**
	 * @ORM\ManyToOne(targetEntity="Post")
	 * @ORM\JoinColumn(name="id_post_thread", referencedColumnName="id_post")
	 */
	protected $mainPost;

	/**
	 * @ORM\OneToMany(targetEntity="Post", mappedBy="thread")
	 */
	protected $posts;

	/**
	 * @ORM\ManyToMany(targetEntity="Tag")
	 * @ORM\JoinTable(name="tag_thread",
	 *      joinColumns={@ORM\JoinColumn(name="id_thread", referencedColumnName="id_thread")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="id_tag", referencedColumnName="id_tag", unique=true)}
	 *      )
	 */
	protected $tags;

	public function __construct()
	{
		$this->posts = new ArrayCollection();
		$this->tags = new ArrayCollection();
	}

	public function __get($property)
	{
		if ($property == "hasSolution")
		{
			if ($this->hasSolution == null)
				$this->hasSolution = $this->hasSolution();
		}

		return $this->$property;
	}

	public function hasSolution()
	{
		foreach ($this->posts AS $entry)
		{
			if ($entry->solution)
				return true;
		}

		return false;
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