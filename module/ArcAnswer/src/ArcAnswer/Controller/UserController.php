<?php
namespace ArcAnswer\Controller;

use ArcAnswer\Entity\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Result;
use Zend\Session\Container;
use Zend\InputFilter\InputFilterInterface;

class UserController extends AbstractActionController
{
	/**
	 * Doctrine entity manager
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * Session identifier for registering form data during user creation error
	 */
	const SESSION_FORM = 'formcreateuser';

	/**
	 * Get the Doctrine Entity Manager
	 * @return \Doctrine\ORM\EntityManager
	 */
	protected function getEntityManager()
	{
		if (null === $this->em)
		{
			$this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		}
		return $this->em;
	}

	/**
	 * Action index
	 * Home page of user, showing the account details
	 * @return \Zend\Http\Response
	 */
	public function indexAction()
	{
		// gathering connected user
		$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
		$user = $auth->getIdentity();

		// if no user is connected, redirect to home page
		if ($user == null)
		{
			return $this->redirect()->toRoute('thread/index', array());
		}

		// prepare flash messages
		$messages = $this->flashMessenger()->getMessages();

		// send infos to view
		return array(
			'login' => $user->login,
			'nickname' => $user->nickname,
			'messages' => $messages,
		);
	}

	/**
	 * Action create
	 * Form for user creation
	 * @return \Zend\Http\Response|ViewModel
	 */
	public function createAction()
	{
		// gathering connected user
		$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');

		// if no user is currently connected
		if ($auth->getIdentity() == null)
		{
			// manage form data
			if ($this->request->isPost())
			{
				// gathering values from post
				$nickname = $this->params()->fromPost('nick');
				$login = $this->params()->fromPost('login');
				$password = $this->params()->fromPost('pass1');
				$control = $this->params()->fromPost('pass2');

				// if both passwords are not equal, refresh the page with flash message
				if (!($password === $control))
				{
					$session = new Container(self::SESSION_FORM);
					$session->offsetSet('nickname', $nickname);
					$session->offsetSet('login', $login);
					$this->flashMessenger()->addMessage('Both password do not match');
					$this->redirect()->toRoute('user', array(
						'controller' => 'user',
						'action' => 'create',
					));
					return;
				}

				// creation of user
				$user = new User();

				// filtering data
				$filter = $user->getInputFilter();
				if ($filter->setData(array(
					'login' => $login,
					'password' => $password,
					'nickname' => $nickname,
				))->setValidationGroup(InputFilterInterface::VALIDATE_ALL)->isValid()
				)
				{
					// register user and log it in automatically
					$user->login = $filter->getValue('login');
					$user->password = $filter->getValue('password');
					$user->nickname = $filter->getValue('nickname');
					$this->getEntityManager()->persist($user);
					$this->getEntityManager()->flush();
					$this->flashMessenger()->addMessage('Welcome in our clan !');
					$this->request->setMethod('GET');
					return $this->forward()->dispatch('ArcAnswer\Controller\User', array(
						'action' => 'login',
						'login' => $filter->getValue('login'),
						'password' => $filter->getValue('password'),
					));
				}

				// if data filtering failed
				else
				{
					// add error messages as flash
					foreach ($filter->getMessages() as $message)
					{
						foreach ($message as $key => $val)
						{
							$this->flashMessenger()->addMessage($val);
						}
					}

					// register form data for further display
					$session = new Container(self::SESSION_FORM);
					$session->offsetSet('nickname', $nickname);
					$session->offsetSet('login', $login);

					// refresh page
					return $this->redirect()->toRoute('user', array(
						'controller' => 'user',
						'action' => 'create',
					));
				}
			}

			// display form
			else
			{
				// get flash messages
				$messages = $this->flashMessenger()->getMessages();

				// get form registered data
				$session = new Container(self::SESSION_FORM);
				$nickname = '';
				$login = '';
				if ($session->offsetExists('nickname'))
				{
					$nickname = $session->offsetGet('nickname');
					$login = $session->offsetGet('login');
					$session->getManager()->getStorage()->clear();
				}

				// send data to view
				return new ViewModel(array(
					'nickname' => $nickname,
					'login' => $login,
					'messages' => $messages,
				));
			}
		}

		// if a user is already connected, redirect to home page
		else
		{
			return $this->redirect()->toRoute('thread/index', array());
		}
	}

	/**
	 * Action update
	 * Update user data like nickname or password
	 * @return ViewModel|\Zend\Http\Response
	 */
	public function updateAction()
	{
		// gathering connected user
		$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
		$user = $auth->getIdentity();

		// if no user is connected, redirect to home page
		if ($user == null)
		{
			return $this->redirect()->toRoute('thread/index', array());
		}

		// manage form data
		if ($this->request->isPost())
		{
			// gathering data from post
			$nickname = $this->params()->fromPost('nick');
			$login = $this->params()->fromPost('login');
			$password = $this->params()->fromPost('pass1');
			$control = $this->params()->fromPost('pass2');

			// if both passwords are not equals, flash a message
			if (!($password === $control))
			{
				$this->flashMessenger()->addMessage('Both password do not match');
			}

			// if both passwords are equals (no matter about they are empty)
			else
			{
				// filtering data
				$filter = $user->getInputFilter();
				$data = array(
					'login' => $login,
					'password' => $password,
					'nickname' => $nickname,
				);

				// if password is empty, unregister it (no update)
				if ($password === '')
				{
					$filter->remove('password');
					unset($data['password']);
				}

				// if data filtering succeed
				if ($filter->setData($data)->setValidationGroup(InputFilterInterface::VALIDATE_ALL)->isValid())
				{
					// update user
					$user->login = $filter->getValue('login');
					$user->nickname = $filter->getValue('nickname');
					if (!($password === ''))
					{
						$user->password = $filter->getValue('password');
					}
					$this->getEntityManager()->persist($user);
					$this->getEntityManager()->flush();
					$this->flashMessenger()->addMessage('Your account has been updated');
					if (!($password === ''))
					{
						$this->flashMessenger()->addMessage('Your password has been changed');
					}
				}

				// if data filtering failed
				else
				{
					// register flash messages
					foreach ($filter->getMessages() as $message)
					{
						foreach ($message as $key => $val)
						{
							$this->flashMessenger()->addMessage($val);
						}
					}
				}
			}

			// redirect to user profile page
			return $this->redirect()->toRoute('user', array(
				'controller' => 'user',
				'action' => 'index',
			));
		}

		// display form
		else
		{
			return new ViewModel(array(
				'login' => $user->login,
				'nickname' => $user->nickname,
			));
		}
	}

	/**
	 * Action login
	 * Login with an existint account
	 * @return \Zend\Http\Response
	 */
	public function loginAction()
	{
		// gathering data from post or route, depending on the call method
		// route method is used by internal login procedure after account creation
		$login = '';
		$password = '';
		if ($this->request->isPost())
		{
			$login = $this->params()->fromPost('login');
			$password = $this->params()->fromPost('password');
		}
		else
		{
			$login = $this->params()->fromRoute('login');
			$password = $this->params()->fromRoute('password');
		}

		// proceed with authentication
		$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
		$auth->getAdapter()->setIdentityValue($login);
		$auth->getAdapter()->setCredentialValue($password);
		$result = $auth->authenticate();

		// if the login has been requested by the user (no automated login after account creation)
		if ($this->request->isPost())
		{
			// register flash message based on login success/failure
			switch ($result->getCode())
			{
				case Result::FAILURE_IDENTITY_NOT_FOUND:
					$this->flashMessenger()->addMessage('Get out of my way, little weak thing...');
					break;
				case Result::FAILURE_CREDENTIAL_INVALID:
					$this->flashMessenger()->addMessage('Ahem... Put your thumb on the scanner again pleaZZARGHBLL');
					break;
				case Result::SUCCESS:
					$this->flashMessenger()->addMessage('Welcome back, questioner !');
					break;
				default:
					$this->flashMessenger()->addMessage('Wa-Wa-Wa-What is the FUCK ?');
					break;
			}
		}

		// redirect to home page
		$this->redirect()->toRoute('thread/index', array());
	}

	/**
	 * Action logout
	 * Logout current user
	 * @return \Zend\Http\Response
	 */
	public function logoutAction()
	{
		// logout identity in authentication module
		$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
		$auth->clearIdentity();

		// register success flash message
		$this->flashMessenger()->addMessage('See you soon, my friend. I still have many things to tell you.');

		// redirect to home page
		$this->redirect()->toRoute('thread/index', array());
	}
}