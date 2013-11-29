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

        $query = $this->getEntityManager()->createQuery('SELECT SUM(v.value) as total FROM ArcAnswer\Entity\Vote v WHERE v.id_post = :id_post');
        $query->setParameter('id_post', $thread->mainPost->id);
        $votes = $query->getResult();

		return array(
			'thread' => $thread,
            'votes' => $votes[0]['total'],
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