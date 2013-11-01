<?php
namespace ArcAnswer\Controller;

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
}