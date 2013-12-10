<?php
namespace ArcAnswer\Controller;

use ArcAnswer\Entity\Vote;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\InputFilter\InputFilterInterface;
use Zend\Controller\Action\Exception;

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
        $threadid = (int) $this->params()->fromRoute('threadid', 0);
        $thread = $this->getEntityManager()->getRepository('ArcAnswer\Entity\Thread')->find($threadid);
        if ( $thread === null )
        {
            $this->getResponse()->setStatusCode(404);
            return;
        }



        $auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
        $user = $auth->getIdentity();

        $posts = $this->getEntityManager()->getRepository('ArcAnswer\Entity\PostVoteView')->findBy(array('thread' => $threadid));
        $votes = $this->getEntityManager()->getRepository('ArcAnswer\Entity\Vote')->findBy(array('id_user' => $user->id));

        $specialPostMap = array();
        $standardPostMap = array();
        $votedPostId = array();

        foreach( $votes as $vote )
        {
            $votedPostId[] = $vote->id_post->id;
        }

        $maxVote = $posts[0]->total_votes;
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
            elseif( $maxVote <= $post->total_votes )
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
        return array(
            'postid' => (int) $this->params()->fromRoute('postid', 0),
        );
    }
}