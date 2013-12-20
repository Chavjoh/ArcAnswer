<?php
namespace ArcAnswer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

use ArcAnswer\Entity\Thread;
use ArcAnswer\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * Post Entity.
 *
 * @ORM\Entity
 * @ORM\Table(name="post")
 */
class Post implements InputFilterAwareInterface
{
	/**
	 * Normalized input filter
	 * @var InputFilter
	 */
	protected $inputFilter;

	/**
	 * Sum of all votes for this post
	 * @var int
	 */
	protected $voteSum = null;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id_post")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", name="content_post")
	 */
	protected $content;

	/**
	 * @ORM\Column(type="datetime", name="date_post")
	 */
	protected $date;

	/**
	 * @ORM\Column(type="boolean", name="solution_post")
	 */
	protected $solution;

	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="id_user_post", referencedColumnName="id_user")
	 */
	protected $user;

	/**
	 * @ORM\ManyToOne(targetEntity="Thread")
	 * @ORM\JoinColumn(name="id_thread_post", referencedColumnName="id_thread")
	 */
	protected $thread;

	/**
	 * @ORM\OneToMany(targetEntity="Vote", mappedBy="id_post")
	 */
	private $vote;

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
		if ($property == "voteSum")
		{
			if ($this->voteSum == null)
			{
				$this->voteSum = $this->getVoteSum();
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
	 * Compute and return vote sum for this post
	 * @return int
	 */
	public function getVoteSum()
	{
		$sum = 0;

		foreach ($this->vote AS $entry)
		{
			$sum += $entry->value;
		}

		return $sum;
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
				'name' => 'content',
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'options' => array(
							'messages' => array(
								\Zend\Validator\NotEmpty::IS_EMPTY => 'Don\'t you want to fill in a text for your post ?',
							),
						),
					),
				),
			)));

			$inputFilter->add($factory->createInput(array(
				'name' => 'solution',
				'required' => true,
			)));

			$inputFilter->add($factory->createInput(array(
				'name' => 'threadid',
				'required' => false,
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