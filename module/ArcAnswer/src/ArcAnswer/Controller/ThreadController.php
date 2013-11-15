<?php
namespace ArcAnswer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ArcAnswer\Entity\Thread;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;

class ThreadController extends AbstractActionController
{
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
		$resultSet = $this->getEntityManager()->getRepository('ArcAnswer\Entity\Thread')->findAll();
		return new ViewModel(array(
			'search' => $this->params()->fromRoute('search', ''),
			'threads' => $resultSet
		));
	}

	public function createAction()
	{

	}
}