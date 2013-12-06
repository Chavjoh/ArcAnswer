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
	const SESSION_FORM = 'formcreateuser';
	/**
 	 * @var \Doctrine\ORM\EntityManager
 	 */
	protected $em;

	protected function getEntityManager()
	{
		if (null === $this->em)
		{
			$this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		}
		return $this->em;
	}

	public function indexAction()
	{
		return array(
			'id' => (int) $this->params()->fromRoute('id', 0),
		);
	}

	public function createAction()
	{
		$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
		if ($auth->getIdentity() == null)
		{
			if ($this->request->isPost())
			{
				$nickname = $this->params()->fromPost('nick');
				$login = $this->params()->fromPost('login');
				$password = $this->params()->fromPost('pass1');
				$control = $this->params()->fromPost('pass2');
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
				$user = new User();
				$filter = $user->getInputFilter();
				if ($filter->setData(array(
					'login' => $login,
					'password' => $password,
					'nickname' => $nickname,
				))->setValidationGroup(InputFilterInterface::VALIDATE_ALL)->isValid())
				{
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
				else
				{
					foreach ($filter->getMessages() as $message)
					{
						foreach ($message as $key=>$val)
						{
							$this->flashMessenger()->addMessage($val);
						}
					}
					$session = new Container(self::SESSION_FORM);
					$session->offsetSet('nickname', $nickname);
					$session->offsetSet('login', $login);
					$this->redirect()->toRoute('user', array(
						'controller' => 'user',
						'action' => 'create',
					));
					return;
				}
			}
			else
			{
				$messages = $this->flashMessenger()->getMessages();
				$session = new Container(self::SESSION_FORM);
				$nickname = '';
				$login = '';
				if ($session->offsetExists('nickname'))
				{
					$nickname = $session->offsetGet('nickname');
					$login = $session->offsetGet('login');
					$session->getManager()->getStorage()->clear();
				}
				return array(
					'nickname' => $nickname,
					'login' => $login,
					'messages' => $messages,
				);
			}
		}
		else
		{
			$this->redirect()->toRoute('thread/index', array());
		}
	}

	public function updateAction()
	{
		return array(
			'id' => (int) $this->params()->fromRoute('id', 0),
		);
	}

	public function loginAction()
	{
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
		$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
		$auth->getAdapter()->setIdentityValue($login);
		$auth->getAdapter()->setCredentialValue($password);
		$result = $auth->authenticate();
		if ($this->request->isPost())
		{
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
		$this->redirect()->toRoute('thread/index', array());
	}

	public function logoutAction()
	{
		$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
		$auth->clearIdentity();
		$this->flashMessenger()->addMessage('See you soon, my friend. I still have many things to tell you.');
		$this->redirect()->toRoute('thread/index', array());
	}
}