<?php
namespace ArcAnswer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Http\Header;
use Zend\Session\Container;
use Zend\InputFilter\InputFilterInterface;

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

	const SESSION_FORM = 'formcreatethread';

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

		$messages = $this->flashMessenger()->getMessages();
		$session = new Container(self::SESSION_FORM);
		$newTitle = '';
		$newQuestion = '';
		$newTags = '';
		if ($session->offsetExists('title'))
		{
			$newTitle = $session->offsetGet('title');
			$newQuestion = $session->offsetGet('question');
			$newTags = $session->offsetGet('tags');
			$session->offsetUnset('title');
			$session->offsetUnset('question');
			$session->offsetUnset('tags');
		}

		return new ViewModel(array(
			'search' => $this->params()->fromRoute('search', ''),
			'threads' => $threadResolved,
			'infoBoxVisibility' => $this->infoBoxVisibility(),
			'user' => $user,
			'newTitle' => $newTitle,
			'newQuestion' => $newQuestion,
			'newTags' => $newTags,
			'messages' => $messages,
		));
	}

	public function createAction()
	{
		$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
		$user = $auth->getIdentity();
		if ($user == null)
		{
			$this->flashMessenger()->addMessage('You must be logged in to ask other members about your poor personnal problems.');
			return $this->redirect()->toRoute('thread/index', array());
		}
		if (!$this->request->isPost())
		{
			$this->flashMessenger()->addMessage('Acces to this page is restricted.');
			return $this->redirect()->toRoute('thread/index', array());
		}
		$textTitle = $this->params()->fromPost('title');
		$textQuestion = $this->params()->fromPost('question');
		$textTags = $this->params()->fromPost('tags');

		$success = false;

		$mainPost = new Post();
		$filter = $mainPost->getInputFilter();
		if ($filter->setData(array(
			'content' => $textQuestion,
			'solution' => 0,
		))->setValidationGroup(InputFilterInterface::VALIDATE_ALL)->isValid())
		{
			$mainPost->content = $filter->getValue('content');
			$mainPost->solution = $filter->getValue('solution');
			$mainPost->user = $user;
			$mainPost->date = new \DateTime('now');

			$thread = new Thread();
			$filter = $thread->getInputFilter();
			if ($filter->setData(array(
				'title' => $textTitle,
			))->setValidationGroup(InputFilterInterface::VALIDATE_ALL)->isValid())
			{
				$thread->title = $filter->getValue('title');
				$thread->mainPost = $mainPost;
				$mainPost->thread = $thread;
				$this->getEntityManager()->persist($mainPost);
				$this->getEntityManager()->persist($thread);
				$this->getEntityManager()->flush();
				$success = true;
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
			}

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
		}

		if ($success)
		{
			$this->flashMessenger()->addMessage('added with success');
			return $this->redirect()->toRoute('thread/index', array());
		}
		else
		{
			$session = new Container(self::SESSION_FORM);
			$session->offsetSet('title', $textTitle);
			$session->offsetSet('question', $textQuestion);
			$session->offsetSet('tags', $textTags);
			return $this->redirect()->toRoute('thread/index', array());
		}

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