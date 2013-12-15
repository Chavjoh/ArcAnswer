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


class PostController extends AbstractActionController
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Value of a sigle up vote
     */
    const UP_VOTE_VALUE = 1;

    /**
     * @var Value of a sigle down vote
     */
    const DOWN_VOTE_VALUE = -1;

    /**
     * @var Gray of the left border
     */
    const POST_GRAY = 182;

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
        // Gets thread
        $threadid = (int) $this->params()->fromRoute('threadid', 0);
        $thread = $this->getEntityManager()->getRepository('ArcAnswer\Entity\Thread')->find($threadid);
        if ( $thread === null )
        {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Gets logged user
        $auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
        $user = $auth->getIdentity();

        // Gets posts
        $posts = $this->getEntityManager()->getRepository('ArcAnswer\Entity\PostVoteView')->findBy(array('thread' => $threadid));
        usort($posts, array('ArcAnswer\Entity\PostVoteView', 'sortByVote'));

        // Gets votes
        $votedPostId = array();
        if( $user!= null )
        {
            $votes = $this->getEntityManager()->getRepository('ArcAnswer\Entity\Vote')->findBy(array('id_user' => $user->id));

            foreach( $votes as $vote )
            {
                $votedPostId[] = $vote->id_post->id;
            }
        }

        // Sorts posts
        $specialPostMap = array();
        $standardPostMap = array();
        $maxVote = 0.1;
        foreach($posts as $post)
        {
            if( $post->solution == true)
            {
                $specialPostMap['solution'] = array( $post, !in_array( $post->id, $votedPostId ) );
            }
            elseif( $post->id == $thread->mainPost->id )
            {
                $specialPostMap['question'] = array( $post, !in_array( $post->id, $votedPostId ) );
            }
            elseif( $maxVote < $post->total_votes )
            {
                $maxVote = $post->total_votes;
                $specialPostMap['popular'] = array( $post, !in_array( $post->id, $votedPostId ) );
            }
            else
            {
                $standardPostMap[$post->id] = array( $post, !in_array( $post->id, $votedPostId ) );
            }
        }

        return array(
            'user' => $user,
            'thread' => $thread,
            'up_val' => self::UP_VOTE_VALUE,
            'down_val' => self::DOWN_VOTE_VALUE,
            'spePost' => $specialPostMap,
            'stdPost' => $standardPostMap,
            'gray' => self::POST_GRAY,
            'max_vote' => $maxVote,
        );
    }

    public function createAction()
    {
        $threadid = (int) $this->params()->fromRoute('threadid', 0);
        $auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
        $user = $auth->getIdentity();
        if ($user == null)
        {
            return $this->redirect()->toRoute('thread/index', array());
        }
        if (!$this->request->isPost())
        {
            return $this->redirect()->toRoute('thread/index', array());
        }
        $comment = $this->params()->fromPost('comment');

        $post = new Post();
        $filter = $post->getInputFilter();
        if ($filter->setData(array(
            'content' => $comment,
            'threadid' => $threadid,
            'solution' => 0,
        ))->setValidationGroup(InputFilterInterface::VALIDATE_ALL)->isValid())
        {
            $post->content = $filter->getValue('content');
            $post->thread = $thread = $this->getEntityManager()->getRepository('ArcAnswer\Entity\Thread')->find($filter->getValue('threadid'));
            $post->solution = $filter->getValue('solution');
            $post->user = $user;
            $post->date = new \DateTime('now');

            $this->getEntityManager()->persist($post);
            $this->getEntityManager()->flush();
        }
        return $this->redirect()->toRoute('post/index', array('threadid'=>(string)$threadid));
    }

    public function voteAction()
    {
        $postId = (int) $this->params()->fromRoute('postid', 0);
        $value = (int) $this->params()->fromRoute('val', self::DOWN_VOTE_VALUE);
        $auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
        $user = $auth->getIdentity();
        $response = new JsonModel();
        $response->setVariable("success", false);

        if( $user != null )
        {
            $vote = new Vote();
            $filter = $vote->getInputFilter();
            if ($filter->setData(array(
                'id_user' => $user->id,
                'id_post' => $postId,
                'value' => $value,
            ))->setValidationGroup(InputFilterInterface::VALIDATE_ALL)->isValid())
            {
                if ( is_null( $this->getEntityManager()->getRepository('ArcAnswer\Entity\Vote')->find(array("id_user" => $user->id, "id_post" => $postId)) ) )
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

        return $response;
    }

    public function electAction()
    {
        $postId = (int) $this->params()->fromRoute('postid', 0);
        $post = $this->getEntityManager()->getRepository('ArcAnswer\Entity\Post')->find($postId);

        $posts = $this->getEntityManager()->getRepository('ArcAnswer\Entity\PostVoteView')->findBy(array('thread' => $post->thread->id));

        $auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
        $user = $auth->getIdentity();

        $hasSolution = false;

        foreach($posts as $elem)
        {
            if( $elem->solution == true)
            {
                $hasSolution = true;
            }
        }

        if( $user != null && $hasSolution == false )
        {
            if ( $user->id == $post->thread->mainPost->user->id )
            {
                    $post->solution = 1;
                    $this->getEntityManager()->merge($post);
                    $this->getEntityManager()->flush();
            }
        }
        return $this->redirect()->toRoute('post/index', array('threadid'=>(string)($post->thread->id)));
    }
}