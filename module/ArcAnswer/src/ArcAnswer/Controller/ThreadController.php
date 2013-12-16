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

	public static function dispatchThreadList($threadList)
	{
		$arraySolved = array();
		$arrayUnsolved = array();

		foreach ($threadList AS $thread)
		{
			if ($thread->hasSolution)
			{
				$arraySolved[] = $thread;
			}
			else
			{
				$arrayUnsolved[] = $thread;
			}
		}

		return array($arraySolved, $arrayUnsolved);
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

		$threadList = $this->getEntityManager()->getRepository('ArcAnswer\Entity\Thread')->findAll();

		/*
		 * ORDER BY SPECIFIC FUNCTION
		 */
		usort($threadList, array('ArcAnswer\Controller\ThreadController', 'sortByVote'));

		/*
		 * KEEP ONLY SOLVED OR UNSOLVED THREAD
		 */
		list($arraySolved, $arrayUnsolved) = $this->dispatchThreadList($threadList);

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
			'infoBoxVisibility' => $this->infoBoxVisibility(),
			'user' => $user,
			'newTitle' => $newTitle,
			'newQuestion' => $newQuestion,
			'newTags' => $newTags,
			'threadListSolved' => $arraySolved,
			'threadListUnsolved' => $arrayUnsolved,
            'infoBoxVisibility' => $this->infoBoxVisibility(),
			'username' => ($user == null ? '&lt;aucun&gt;' : $user->nickname),
			'messages' => $messages,
		));
	}

	public function createAction()
	{
		// gathering connected user
		$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
		$user = $auth->getIdentity();

		// if user is not currently connected, back to thread/index with flash message
		if ($user == null)
		{
			$this->flashMessenger()->addMessage('You must be logged in to ask other members about your poor personnal problems.');
			return $this->redirect()->toRoute('thread/index', array());
		}

		// if page has not been called by form post, back to thread/index with flash message
		if (!$this->request->isPost())
		{
			$this->flashMessenger()->addMessage('Acces to this page is restricted.');
			return $this->redirect()->toRoute('thread/index', array());
		}

		// gathering POST params
		$textTitle = $this->params()->fromPost('title');
		$textQuestion = $this->params()->fromPost('question');
		$textTags = $this->params()->fromPost('tags');

		// success variable
		$success = false;

		// create main post
		$mainPost = new Post();

		// if main post values are valid
		$filter = $mainPost->getInputFilter();
		if ($filter->setData(array(
			'content' => $textQuestion,
			'solution' => 0,
		))->setValidationGroup(InputFilterInterface::VALIDATE_ALL)->isValid())
		{
			// prepare main post
			$mainPost->content = $filter->getValue('content');
			$mainPost->solution = $filter->getValue('solution');
			$mainPost->user = $user;
			$mainPost->date = new \DateTime('now');

			// create thread
			$thread = new Thread();

			// if thread values are valid
			$filter = $thread->getInputFilter();
			if ($filter->setData(array(
				'title' => $textTitle,
			))->setValidationGroup(InputFilterInterface::VALIDATE_ALL)->isValid())
			{
				// prepare thread and crosslink with main post
				$thread->title = $filter->getValue('title');
				$thread->mainPost = $mainPost;
				$mainPost->thread = $thread;

				// prepare request for gathering tags
				$qb = $this->getEntityManager()->createQueryBuilder();
				$qb
					->select('t')
					->from('ArcAnswer\Entity\Tag', 't')
					->where($qb->expr()->like($qb->expr()->lower('t.name'), ':tag'));

				// loop on all given tags
				$tags = explode(',', $textTags);
				foreach ($tags as $tag)
				{
					$tag = trim($tag);
					$result = $qb->setParameter('tag', strtolower($tag))->getQuery()->getOneOrNullResult();

					// create tag if it doesn't currently exist
					if ($result == null)
					{
						$result = new Tag();
						$filter = $result->getInputFilter();
						if ($filter->setData(array(
							'name' => $tag,
						))->setValidationGroup(InputFilterInterface::VALIDATE_ALL)->isValid())
						{
							$result->name = $filter->getValue('name');
						}
						else
						{
							$this->flashMessenger()->addMessage('Tag "' . $tag . '" is not a valid tag and has not been added.');
							$result = null;
						}
					}

					// if tag has been accepted, add him to thread
					if ($result != null)
					{
						$thread->tags[] = $result;
						$this->getEntityManager()->persist($result);
					}
				}

				// flush entities into database
				$this->getEntityManager()->persist($mainPost);
				$this->getEntityManager()->persist($thread);
				$this->getEntityManager()->flush();

				// success variable
				$success = true;
			}

			// if thread values are not valid
			else
			{
				// register filter messages
				foreach ($filter->getMessages() as $message)
				{
					foreach ($message as $key=>$val)
					{
						$this->flashMessenger()->addMessage($val);
					}
				}
			}

		}

		// if main post values are not valid
		else
		{
			// register filter messages
			foreach ($filter->getMessages() as $message)
			{
				foreach ($message as $key=>$val)
				{
					$this->flashMessenger()->addMessage($val);
				}
			}
		}

		// if thread posting was successful
		if ($success)
		{
			// flash success message and redirect to newly created thread
			$this->flashMessenger()->addMessage('added with success');
			return $this->redirect()->toRoute('post/index', array('threadid' => $thread->id));
		}

		// if thread posting failed
		else
		{
			// register form values and redirect to same page
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

    public function addtagAction()
    {
        // gathering connected user
        $auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
        $user = $auth->getIdentity();

        // if user is not currently connected, back to thread/index with flash message
        if ($user == null)
        {
            $this->flashMessenger()->addMessage('You must be logged in to ask other members about your poor personal problems.');
            return $this->redirect()->toRoute('thread/index', array());
        }

        // if page has not been called by form post, back to thread/index with flash message
        if (!$this->request->isPost())
        {
            $this->flashMessenger()->addMessage('Acces to this page is restricted.');
            return $this->redirect()->toRoute('thread/index', array());
        }

        // gathering POST params
        $threadId = $this->params()->fromPost('threadid');
        $tag = $this->params()->fromPost('newtag');

        // prepare request for gathering thread
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('t')
            ->from('ArcAnswer\Entity\Thread', 't')
            ->where($qb->expr()->like($qb->expr()->lower('t.id'), ':thread'));

        // gathering thread
        $thread = $qb->setParameter('thread', strtolower($threadId))->getQuery()->getOneOrNullResult();

        // checking if thread exist
        if ($thread == null)
        {
            $this->flashMessenger()->addMessage('Stop playing with the URL please.');
            return $this->redirect()->toRoute('thread/index', array());
        }

        // checking if thread belong to us
        if ($thread->mainPost->user != $user)
        {
            $this->flashMessenger()->addMessage('This thread does not belong to you. I won\'t kill you for this time, but...');
            return $this->redirect()->toRoute('thread/index', array());
        }

        // prepare request for gathering tags
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
            ->select('t')
            ->from('ArcAnswer\Entity\Tag', 't')
            ->where($qb->expr()->like($qb->expr()->lower('t.name'), ':tag'));

        // loop on all given tags
        $tags = explode(',', $tag);
        foreach ($tags as $tag)
        {
            $tag = trim($tag);
            $result = $qb->setParameter('tag', strtolower($tag))->getQuery()->getOneOrNullResult();

            // create tag if it doesn't currently exist
            if ($result == null)
            {
                $result = new Tag();
                $filter = $result->getInputFilter();
                if ($filter->setData(array(
                    'name' => $tag,
                ))->setValidationGroup(InputFilterInterface::VALIDATE_ALL)->isValid())
                {
                    $result->name = $filter->getValue('name');
                }
                else
                {
                    $this->flashMessenger()->addMessage('Tag "' . $tag . '" is not a valid tag and has not been added.');
                    $result = null;
                }
            }

            // if tag has been accepted, add him to thread
            if ($result != null)
            {
                $thread->tags[] = $result;
                $this->getEntityManager()->persist($result);
            }
        }

        // persist database
        $this->getEntityManager()->flush();

        // redirect to thread
        return $this->redirect()->toRoute('post/index', array('threadid' => $threadId));
    }
}