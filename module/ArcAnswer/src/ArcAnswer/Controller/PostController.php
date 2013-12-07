<?php
namespace ArcAnswer\Controller;

use ArcAnswer\Entity\Vote;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\InputFilter\InputFilterInterface;

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
        $mainPost = $this->getEntityManager()->getRepository('ArcAnswer\Entity\PostVoteView')->find($thread->mainPost->id);

        $posts = $this->getEntityManager()->getRepository('ArcAnswer\Entity\PostVoteView')->findBy(array('thread' => $threadid));
        $solutionPost = null;
        $popularPost = null;

        $keySolution = null;
        $keyPopular = null;
        $maxVote = $posts[0]->total_votes;
        foreach($posts as $key=>$post)
        {
            if( $post->solution == true)
            {
                $keySolution = $key;
            }
            elseif( $post->id == $mainPost->id )
            {
                unset( $posts[$key] );
            }
            elseif( $maxVote <= $post->total_votes )
            {
                $keyPopular = $key;
                $maxVote = $posts[$key]->total_votes;
            }
        }


        if ( $keyPopular !== null )
        {
            $popularPost = $posts[$keyPopular];
            unset( $posts[$keyPopular] );
        }
        if ( $keySolution !== null )
        {
            $solutionPost = $this->getEntityManager()->getRepository('ArcAnswer\Entity\PostVoteView')->find( $posts[$keySolution]->id );
            unset( $posts[$keySolution] );
        }


        $auth = $this->getServiceLocator()->get('doctrine.authenticationservice.orm_default');
        $user = $auth->getIdentity();

        return array(
            'thread' => $thread,
            'posts' => $posts,
            'mainPost' => $mainPost,
            'solutionPost' => $solutionPost,
            'popularPost' => $popularPost,
            'user' => $user,
            'up_val' => self::UP_VOTE_VALUE,
            'down_val' => self::DOWN_VOTE_VALUE,
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
                $vote->id_user = $filter->getValue('id_user');
                $vote->id_post = $filter->getValue('id_post');
                $vote->value = $filter->getValue('value');
                $this->getEntityManager()->persist($vote);
                $this->getEntityManager()->flush();

                $response->setVariable("success", true);
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