<?php
namespace ArcAnswer\Controller;

use ArcAnswer\Entity\User;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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
		if ($result)
		{
			$this->flashMessenger()->addMessage('Welcome back, questioner !');
		}
		else
		{
			$this->flashMessenger()->addMessage('Get out of my way, little weak thing...');
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