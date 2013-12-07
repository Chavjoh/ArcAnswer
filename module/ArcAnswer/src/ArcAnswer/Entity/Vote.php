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
 * User Entity.
 *
 * @ORM\Entity
 * @ORM\Table(name="vote")
 */
class Vote implements InputFilterAwareInterface
{
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
                'name' => 'vote',
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

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Les filtres sont déjà définis directement dans le modèle");
    }
} 