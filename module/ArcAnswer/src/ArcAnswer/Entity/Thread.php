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
	/**
	 * Normalized input filter
	 * @var InputFilter
	 */
	protected $inputFilter;

	/**
	 * Cache indicator => do the thread has a solution ?
	 * @var boolean
	 */
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

	/**
	 * Default constructor
	 */
	public function __construct()
	{
		$this->posts = new ArrayCollection();
		$this->tags = new ArrayCollection();
	}

	/**
	 * Magic getter for protected attributes
	 * @param String $property Name of property to get
	 * @return mixed
	 */
	public function __get($property)
	{
		if ($property == "hasSolution")
		{
			if ($this->hasSolution == null)
			{
				$this->hasSolution = $this->hasSolution();
			}
		}

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
	 * Does the thread have a solution ?
	 * @return boolean
	 */
	public function hasSolution()
	{
		foreach ($this->posts AS $entry)
		{
			if ($entry->solution)
			{
				return true;
			}
		}

		return false;
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
				'name' => 'title',
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'options' => array(
							'messages' => array(
								\Zend\Validator\NotEmpty::IS_EMPTY => 'Don\'t you want to fill in a title for your question ?',
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

	/**
	 * Comparator for sorting threads by votes
	 * @param Thread $a
	 * @param Thread $b
	 * @return int
	 */
	public static function sortByVote(Thread $a, Thread $b)
	{
		$sumA = $a->mainPost->voteSum;
		$sumB = $b->mainPost->voteSum;

		return ($sumA == $sumB) ? 0 : (($sumA < $sumB) ? 1 : -1);
	}

	/**
	 * Comparator for sorting threads by dates
	 * @param Thread $a
	 * @param Thread $b
	 * @return int
	 */
	public static function sortByDate(Thread $a, Thread $b)
	{
		$dateA = $a->mainPost->date;
		$dateB = $b->mainPost->date;

		return ($dateA == $dateB) ? 0 : (($dateA < $dateB) ? 1 : -1);
	}
}