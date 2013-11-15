<?php
namespace ArcAnswer\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Header;
use Zend\Http\Header\SetCookie;

class ThreadController extends AbstractActionController
{
    private static  $HIDE_INFO_BOX_COOKIE_NAME = 'hideInformationBox';

	public function indexAction()
	{
		return array(
			'search' => $this->params()->fromRoute('search', ''),
		);
	}

	public function createAction()
	{

	}

    public function hideInformationBoxAction()
    {
        $this->getResponse()->getHeaders()->addHeader( new SetCookie( 'informationBox', 'hide' ) );
        $this->redirect()->toRoute('thread');
    }

    public function showInformationBoxAction()
    {
        $this->getResponse()->getHeaders()->addHeader( new SetCookie( 'informationBox', 'show' ) );
        $this->redirect()->toRoute('thread');
    }

    private function getInformationBox()
    {

        if( true )
        {
            // add info box to the view
        }

    }

}