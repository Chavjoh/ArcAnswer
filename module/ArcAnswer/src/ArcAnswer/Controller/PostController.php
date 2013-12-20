<?php
namespace ArcAnswer\Controller;

use ArcAnswer\Entity\Vote;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\InputFilter\InputFilterInterface;
use Zend\Controller\Action\Exception;
use Zend\Session\Container;
use ArcAnswer\Entity\Post;

/**
 * Parser for BBCode
 * @package ArcAnswer\Controller
 */
class Parser
{
	/**
	 * Parse code
	 * @param String $text Text to parse
	 * @return String
	 */
	public static function compute($text)
	{
		$find = array(
			'~\[b\](.*?)\[/b\]~s',
			'~\[i\](.*?)\[/i\]~s',
			'~\[u\](.*?)\[/u\]~s',
			'~\[quote\](.*?)\[/quote\]~s',
			'~\[size=(.*?)\](.*?)\[/size\]~s',
			'~\[color=(.*?)\](.*?)\[/color\]~s',
			'~\[url\]((?:ftp|https?)://.*?)\[/url\]~s',
			'~\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~s',
			'~\[center\](.*?)\[/center\]~s',
			'~```(.*?)```~s',
		);

		$replace = array(
			'<b>$1</b>',
			'<i>$1</i>',
			'<span style="text-decoration:underline;">$1</span>',
			'<blockquote>$1</blockquote>',
			'<span style="font-size:$1px;">$2</span>',
			'<span style="color:$1;">$2</span>',
			'<a href="$1">$1</a>',
			'<img src="$1" alt="" />',
			'<div class="center">$1</div>',
			'<pre class="prettyprint">$1</pre>',
		);

		return preg_replace($find, $replace, $text);
	}
}

/**
 * Posts controller
 * @package ArcAnswer\Controller
 */
class PostController extends AbstractActionController
{
	/**
	 * Doctrine entity manager
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * Value of a single up vote
	 * @var int
	 */
	const UP_VOTE_VALUE = 1;

	/**
	 * Value of a single down vote
	 * @var int
	 */
	const DOWN_VOTE_VALUE = -1;

	/**
	 * Value of the gray border
	 * @var int
	 */
	const POST_GRAY = 182;

	/**
	 * Get the Doctrine Entity Manager
	 * @return \Doctrine\ORM\EntityManager
	 */
	protected function getEntityManager()
	{
		if (null === $this->em)
		{
			$this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		}
		return $this->em;
	}

	/**
	 * Action index
	 * Page displaying a thread content
	 * @return ViewModel|\Zend\Http\Response
	 */
	public function indexAction()
	{
		// Gets thread
		$threadid = (int)$this->params()->fromRoute('threadid', 0);
		$thread = $this->getEntityManager()->getRepository('ArcAnswer\Entity\Thread')->find($threadid);
		if ($thread === null)
		{
			return $this->getResponse()->setStatusCode(404);
		}

		// Gets logged user
		$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
		$user = $auth->getIdentity();

		// Gets posts
		$posts = $this->getEntityManager()->getRepository('ArcAnswer\Entity\PostVoteView')->findBy(array('thread' => $threadid));
		usort($posts, array('ArcAnswer\Entity\PostVoteView', 'sortByVote'));

		// Gets votes
		$votedPostId = array();
		if ($user != null)
		{
			$votes = $this->getEntityManager()->getRepository('ArcAnswer\Entity\Vote')->findBy(array('id_user' => $user->id));

			foreach ($votes as $vote)
			{
				$votedPostId[] = $vote->id_post->id;
			}
		}

		// Sorts posts
		$specialPostMap = array();
		$standardPostMapTemp = array();
		$maxVote = 0.1;
		foreach ($posts as $post)
		{
			if ($post->solution == true)
			{
				$specialPostMap['solution'] = array($post, !in_array($post->id, $votedPostId));
			}
			elseif ($post->id == $thread->mainPost->id)
			{
				$specialPostMap['question'] = array($post, !in_array($post->id, $votedPostId));
			}
			elseif ($maxVote < $post->total_votes)
			{
				$maxVote = $post->total_votes;
				$specialPostMap['popular'] = array($post, !in_array($post->id, $votedPostId));
			}
			else
			{
				$standardPostMapTemp[$post->id] = $post;
			}
		}

		// final ordering
		$order = $this->params()->fromPost('order_by', 'vote');
		$orderClause = 'sortBy' . ($order === 'vote' ? 'Vote' : 'Date');
		usort($standardPostMapTemp, array('ArcAnswer\Entity\PostVoteView', $orderClause));
		$standardPostMap = array();
		foreach ($standardPostMapTemp as $id => $post)
		{
			$standardPostMap[$id] = array($post, !in_array($id, $votedPostId));
		}

		// Gather flash messages
		$messages = $this->flashMessenger()->getMessages();

		// register sorter in layout
		$this->layout()->sortAction = '/post/index/' . $thread->id;
		if ($order == 'vote')
		{
			$this->layout()->sortList = array(
				'Order by vote' => 'vote',
				'Order by date' => 'date',
			);
		}
		else
		{
			$this->layout()->sortList = array(
				'Order by date' => 'date',
				'Order by vote' => 'vote',
			);
		}

		// instanciate BBcode parser
		$parser = new Parser();

		// send data to view
		return new ViewModel(array(
			'user' => $user,
			'thread' => $thread,
			'up_val' => self::UP_VOTE_VALUE,
			'down_val' => self::DOWN_VOTE_VALUE,
			'spePost' => $specialPostMap,
			'stdPost' => $standardPostMap,
			'gray' => self::POST_GRAY,
			'max_vote' => $maxVote,
			'messages' => $messages,
			'parser' => $parser,
		));
	}

	/**
	 * Action create
	 * Creation of a new post
	 * @return \Zend\Http\Response
	 */
	public function createAction()
	{
		// get thread id
		$threadid = (int)$this->params()->fromRoute('threadid', 0);

		// gathering authenticated user
		$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
		$user = $auth->getIdentity();

		// if no user is currently logged in, redirect to home page
		if ($user == null)
		{
			return $this->redirect()->toRoute('thread/index', array());
		}

		// if request type is not post, redirect to home page
		if (!$this->request->isPost())
		{
			return $this->redirect()->toRoute('thread/index', array());
		}

		// get comment from post
		$comment = $this->params()->fromPost('comment');

		// Create new post
		$post = new Post();

		// Filtering input data
		$filter = $post->getInputFilter();
		if ($filter->setData(array(
			'content' => $comment,
			'threadid' => $threadid,
			'solution' => 0,
		))->setValidationGroup(InputFilterInterface::VALIDATE_ALL)->isValid()
		)
		{
			// Register post
			$post->content = $filter->getValue('content');
			$post->thread = $thread = $this->getEntityManager()->getRepository('ArcAnswer\Entity\Thread')->find($filter->getValue('threadid'));
			$post->solution = $filter->getValue('solution');
			$post->user = $user;
			$post->date = new \DateTime('now');
			$this->getEntityManager()->persist($post);
			$this->getEntityManager()->flush();
		}

		// Redirect to current page
		return $this->redirect()->toRoute('post/index', array('threadid' => (string)$threadid));
	}

	/**
	 * Action vote
	 * Vote for a designated post
	 * @return JsonModel
	 */
	public function voteAction()
	{
		// get values from route
		$postId = (int)$this->params()->fromRoute('postid', 0);
		$value = (int)$this->params()->fromRoute('val', self::DOWN_VOTE_VALUE);

		// gathering authenticated user
		$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
		$user = $auth->getIdentity();

		// prepare JSON model
		$response = new JsonModel();
		$response->setVariable("success", false);

		// if a user is currently logged in
		if ($user != null)
		{
			// create new vote
			$vote = new Vote();

			// filter input data
			$filter = $vote->getInputFilter();
			if ($filter->setData(array(
				'id_user' => $user->id,
				'id_post' => $postId,
				'value' => $value,
			))->setValidationGroup(InputFilterInterface::VALIDATE_ALL)->isValid()
			)
			{
				// if user has not already voted for that post
				if (is_null($this->getEntityManager()->getRepository('ArcAnswer\Entity\Vote')->find(array("id_user" => $user->id, "id_post" => $postId))))
				{
					$vote->id_user = $user;
					$vote->id_post = $this->getEntityManager()->getRepository('ArcAnswer\Entity\Post')->find($postId);
					$vote->value = $filter->getValue('value');
					$this->getEntityManager()->persist($vote);
					$this->getEntityManager()->flush();

					$response->setVariable("success", true);
				}
			}
		}

		// return JSON response
		return $response;
	}

	/**
	 * Action elect
	 * Elect a post as the solution for its thread
	 * @return \Zend\Http\Response
	 */
	public function electAction()
	{
		// gather post from route
		$postId = (int)$this->params()->fromRoute('postid', 0);
		$post = $this->getEntityManager()->getRepository('ArcAnswer\Entity\Post')->find($postId);

		// gather list of all posts from current thread
		$posts = $this->getEntityManager()->getRepository('ArcAnswer\Entity\PostVoteView')->findBy(array('thread' => $post->thread->id));

		// gathering authenticated user
		$auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
		$user = $auth->getIdentity();

		// marker for solution verification
		$hasSolution = false;

		// loop on all posts to find one already marked as solution
		foreach ($posts as $elem)
		{
			if ($elem->solution == true)
			{
				$hasSolution = true;
			}
		}

		// if user is logged in and current thread does not already have solution
		if ($user != null && $hasSolution == false)
		{
			// if current user is the owner of the thread, mark as solution
			if ($user->id == $post->thread->mainPost->user->id)
			{
				$post->solution = 1;
				$this->getEntityManager()->merge($post);
				$this->getEntityManager()->flush();
			}
		}

		// redirect to thread page
		return $this->redirect()->toRoute('post/index', array('threadid' => (string)($post->thread->id)));
	}
}