<?php
namespace ArcAnswer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Http\Header;

use ArcAnswer\Entity\Post;
use ArcAnswer\Entity\Thread;
use ArcAnswer\Entity\Tag;
use ArcAnswer\Entity\User;

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
			'threads' => $resultSet,
            'infoBoxVisibility' => $this->infoBoxVisibility(),
		));
	}

	public function createAction()
	{

	}

    public function hideInformationBoxAction()
    {
        $this->getResponse()->getHeaders()->addHeader( new SetCookie( 'informationBox', 'hide', time() + 365 * 60 * 60 * 24 ) );
        $result = new JsonModel(array(
            'success'=>true,
        ));
        return $result;
    }

    public function showInformationBoxAction()
    {
        $this->getResponse()->getHeaders()->addHeader( new SetCookie( 'informationBox', 'show' ) );
        $result = new JsonModel(array(
            'success'=>true,
        ));
        return $result;
    }

    private function infoBoxVisibility()
    {
        $cookie = $this->getRequest()->getCookie();

        if (isset($cookie->informationBox)) {
            return $cookie->informationBox;
        }
        return 'show';
    }
}