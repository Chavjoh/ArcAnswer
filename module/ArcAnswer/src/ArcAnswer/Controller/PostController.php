<?php
namespace ArcAnswer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PostController extends AbstractActionController
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

        return array(
            'thread' => $thread,
            'posts' => $posts,
            'mainPost' => $mainPost,
            'solutionPost' => $solutionPost,
            'popularPost' => $popularPost,
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
		return array(
			'postid' => (int) $this->params()->fromRoute('postid', 0),
			'val' => (int) $this->params()->fromRoute('val', -1),
		);
	}

	public function electAction()
	{
		return array(
			'postid' => (int) $this->params()->fromRoute('postid', 0),
		);
	}
}