<?php
namespace ArcAnswer\Controller;

use ArcAnswer\Entity\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Result;

class UserController extends AbstractActionController
{
	public function indexAction()
	{
		return array(
			'id' => (int) $this->params()->fromRoute('id', 0),
		);
	}

	public function createAction()
	{

	}

	public function updateAction()
	{
		return array(
			'id' => (int) $this->params()->fromRoute('id', 0),
		);
	}

	public function loginAction()
	{
		$result = 0;
		if ($this->request->isPost())
		{
			$login = $this->params()->fromPost('login');
			$password = $this->params()->fromPost('password');
			$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
			$auth->getAdapter()->setIdentityValue($login);
			$auth->getAdapter()->setCredentialValue($password);
			$result = $auth->authenticate();
		}
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
		$this->redirect()->toRoute('thread/index');
	}

	public function logoutAction()
	{
		$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
		$auth->clearIdentity();
		$this->flashMessenger()->addMessage('See you soon, my friend. I still have many things to tell you.');
		$this->redirect()->toRoute('thread/index');
	}
}