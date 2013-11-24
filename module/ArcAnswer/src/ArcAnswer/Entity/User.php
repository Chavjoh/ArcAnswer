<?php
namespace ArcAnswer\Entity;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * User Entity.
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User implements InputFilterAwareInterface
{
	protected $inputFilter;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="id_user")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", name="login_user", length=250)
	 */
	protected $login;

	/**
	 * @ORM\Column(type="string", name="password_user", length=40)
	 */
	protected $password;

	/**
	 * @ORM\Column(type="string", name="nickname_user", length=250)
	 */
	protected $nickname;

	public function __construct()
	{
		// Nothing here
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