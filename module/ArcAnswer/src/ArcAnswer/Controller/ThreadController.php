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

	public static function sortByVote(Thread $a, Thread $b)
	{
		$sumA = $a->mainPost->voteSum;
		$sumB = $b->mainPost->voteSum;

		return ($sumA == $sumB) ? 0 : (($sumA < $sumB) ? 1 : -1);
	}

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
		/*
		 * TRY WITH QUERY
		 *
		$query = $this->getEntityManager()->createQuery("SELECT t FROM ArcAnswer\Entity\Thread t JOIN t.id_post_thread p ORDER BY p.getVoteSum() ASC");
		$resultSet = $query->getResult();
		*/

		/*
		 * TRY WITH QUERY BUILDER
		 *
		$qb = $this->getEntityManager()->createQueryBuilder();
		$qb->select('t');
		$qb->from('ArcAnswer\Entity\Thread', 't');
		$qb->innerJoin('t.mainPost', 'p');
		$qb->orderBy('p.voteSum', 'DESC');
		$resultSet = $qb->getQuery()->getResult();
		*/

		$threadResolved = $this->getEntityManager()->getRepository('ArcAnswer\Entity\Thread')->findAll();

		/*
		 * ORDER BY SPECIFIC FUNCTION
		 */
		usort($threadResolved, array('ArcAnswer\Controller\ThreadController', 'sortByVote'));

		$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
		$user = $auth->getIdentity();
		$flash = $this->flashMessenger();
		$messages = array();
		if ($flash->hasMessages())
		{
			$messages = $flash->getMessages();
		}
		return new ViewModel(array(
			'search' => $this->params()->fromRoute('search', ''),
			'threads' => $threadResolved,
            'infoBoxVisibility' => $this->infoBoxVisibility(),
			'username' => ($user == null ? '&lt;aucun&gt;' : $user->nickname),
			'messages' => $messages,
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