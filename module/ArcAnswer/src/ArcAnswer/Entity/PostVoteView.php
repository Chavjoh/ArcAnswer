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