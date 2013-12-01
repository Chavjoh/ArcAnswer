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
        $posts = $this->getEntityManager()->getRepository('ArcAnswer\Entity\PostVoteView')->findBy(array('thread' => $threadid));

        // TODO remplace the foreach by something like : $keyMain = array_search(array('id' => $posts[0]->thread->mainPost->id), $posts);

        $keyMain = -1;
        $needle = $posts[0]->thread->mainPost->id;
        foreach($posts as $key=>$post)
        {
            $currentKey = $key;
            if( $needle == $post->id)
            {
                $keyMain = $currentKey;
            }
        }

        $mainPost = $posts[$keyMain];
        unset($posts[$keyMain]);

        return array(
            'posts' => $posts,
            'mainPost' => $mainPost,
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