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
 * @ORM\Table(name="post_vote_view")
 */
class PostVoteView implements InputFilterAwareInterface
{
	/**
	 * Normalized input filter
	 * @var InputFilter
	 */
	protected $inputFilter;

	/**
	 * @ORM\Column(type="integer", name="total_votes")
	 */
	protected $total_votes;

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
	 * Comparator for PostVoteView objects by votes
	 * @param PostVoteView $a
	 * @param PostVoteView $b
	 * @return int
	 */
	public static function sortByVote(PostVoteView $a, PostVoteView $b)
	{
		$sumA = $a->total_votes;
		$sumB = $b->total_votes;
		$sumA == null ? $sumA = 0 : $sumA;
		$sumB == null ? $sumB = 0 : $sumB;

		return ($sumA == $sumB) ? 0 : (($sumA < $sumB) ? 1 : -1);
	}

	/**
	 * Comparator for PostVoteView objects by dates
	 * @param PostVoteView $a
	 * @param PostVoteView $b
	 * @return int
	 */
	public static function sortByDate(PostVoteView $a, PostVoteView $b)
	{
		$dateA = $a->date;
		$dateB = $b->date;

		return ($dateA == $dateB) ? 0 : (($dateA < $dateB) ? 1 : -1);
	}
}