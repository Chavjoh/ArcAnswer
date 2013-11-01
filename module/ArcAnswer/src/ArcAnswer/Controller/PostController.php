<?php
namespace ArcAnswer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PostController extends AbstractActionController
{
	public function indexAction()
	{
		return array(
			'threadid' => (int) $this->params()->fromRoute('threadid', 0),
		);
	}

	public function createAction()
	{
		return array(
			'threadid' => (int) $this->params()->fromRoute('threadid', 0),
		);
	}

	public function voteAction()
	{
		return array(
			'postid' => (int) $this->params()->fromRoute('postid', 0),
			'val' => (int) $this->params()->fromRoute('val', -1),
		);
	}

	public function electAction()
	{
		return array(
			'postid' => (int) $this->params()->fromRoute('postid', 0),
		);
	}
}