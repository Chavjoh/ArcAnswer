<?php
namespace ArcAnswer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Header;
use ArcAnswer\Entity\Thread;
use Doctrine\ORM\EntityManager;
use Zend\Http\Header\SetCookie;
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

    public function hideInformationBoxAction()
    {
        $this->getResponse()->getHeaders()->addHeader( new SetCookie( 'informationBox', 'hide' ) );
        $this->redirect()->toRoute('thread');
    }

    public function showInformationBoxAction()
    {
        $this->getResponse()->getHeaders()->addHeader( new SetCookie( 'informationBox', 'show' ) );
        $this->redirect()->toRoute('thread');
    }

    private function getInformationBox()
    {

        if( true )
        {
            // add info box to the view
        }

    }

}