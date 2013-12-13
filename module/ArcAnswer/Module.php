<?php
namespace ArcAnswer;

class Module
{
	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\ClassMapAutoLoader' => array(
				__DIR__ . '/autoload_classmap.php',
			),
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}

	public function onBootstrap($e)
	{
		$auth = $e->getApplication()->getServiceManager()->get('doctrine.authenticationservice.orm_default');
		$viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
		$viewModel->GLOBAL_AUTH = $auth;

	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}
}