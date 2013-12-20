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
	/**
	 * Normalized input filter
	 * @var InputFilter
	 */
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

	/**
	 * Default constructor
	 */
	public function __construct()
	{
		// Nothing here
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
		if ($property === 'password')
		{
			$value = self::hashPassword($value);
		}
		$this->$property = $value;
	}

	/**
	 * Set a clear password
	 * @param String $clearPassword Clear password
	 * @return $this
	 */
	public function setClearPassword($clearPassword)
	{
		$this->password = self::hashPassword($clearPassword);
		return $this;
	}

	/**
	 * Hash the password for database storage
	 * @param String $clearPassword Clear password
	 * @return String Hashed password
	 */
	public static function hashPassword($clearPassword)
	{
		return sha1($clearPassword);
	}

	/**
	 * Test if 2 passwords are equals
	 * @param User $user User of which test the password
	 * @param String $clearPassword Clear password to test
	 * @return bool
	 */
	public static function testPassword($user, $clearPassword)
	{
		return self::hashPassword($clearPassword) === $user->password;
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
				'name' => 'login',
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'options' => array(
							'messages' => array(
								\Zend\Validator\NotEmpty::IS_EMPTY => 'Don\'t you want to fill in a login ?',
							),
						),
					),
				),
			)));
			$inputFilter->add($factory->createInput(array(
				'name' => 'password',
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'options' => array(
							'messages' => array(
								\Zend\Validator\NotEmpty::IS_EMPTY => 'Don\'t you want to fill in a password ?',
							),
						),
					),
				),
			)));
			$inputFilter->add($factory->createInput(array(
				'name' => 'nickname',
				'required' => true,
				'validators' => array(
					array(
						'name' => 'NotEmpty',
						'options' => array(
							'messages' => array(
								\Zend\Validator\NotEmpty::IS_EMPTY => 'Don\'t you want to fill in a nickname ?',
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
}