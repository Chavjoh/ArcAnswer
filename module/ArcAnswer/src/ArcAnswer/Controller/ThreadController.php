<?php
namespace ArcAnswer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ThreadController extends AbstractActionController
{
	public function indexAction()
	{
		return array(
			'search' => $this->params()->fromRoute('search', ''),
		);
	}

	public function createAction()
	{

	}
}