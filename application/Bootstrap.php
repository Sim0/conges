<?php

//les m�thodes pr�fix�es de '_init' sont automatiquement appel�es lors de
//l'appel de la m�thode bootstrap() de l'interface Zend_Application_Bootstrap_Bootstrapper
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	//bootstrap autoloader pour les ressources de l'application, �a permet de
	//charger les classes automatiquement en fonction des besoins
	protected function _initAutoload()
	{
		$autoloader = new Zend_Application_Module_Autoloader(array(
			'namespace' => 'Default',
			'basePath'  => dirname(__FILE__),
		));
		return $autoloader;
	}

	//bootstrap le doctype des vues
	protected function _initDoctype()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->doctype('XHTML1_STRICT');
	}

	protected function _initAutoloadRessource()
	{
		//configuration de l'Autoload
		$ressourceLoader = new Zend_Loader_Autoloader_Resource(array(
		'namespace' => 'Default',
		'basePath'  => dirname(__FILE__),
		));

		//permet d'indiquer les r�pertoires dans lesquels se trouveront nos classes:
		//notamment, l'ACL et le plugin
		$ressourceLoader->addResourceType('form', 'forms/', 'Form')
		->addResourceType('acl', 'acls/', 'Acl')
		->addResourceType('model', 'models/', 'Model')
		->addResourceType('plugin', 'plugins/', 'Controller_Plugin');

		return $ressourceLoader;
	}

	protected function _initJQuery()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
	}

	//	protected function _initAcl()
	//	{
	//		//Cr�ation d'une instance de notre ACL
	//		$acl = new Default_Acl_MyAcl();
	//
	//		//enregistrement du plugin de mani�re � ce qu'il soit ex�cut�
	//		Zend_Controller_Front::getInstance()->registerPlugin(new Default_Controller_Plugin_Acl());
	//
	//		//permet de d�finir l'acl par d�faut � l'aide de vue, de cette mani�re
	//		//l'ACL est accessible dans les vues
	//		Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($acl);
	//
	//		//v�rifie si une identit� existe et applique le r�le
	//		$auth = Zend_Auth::getInstance();
	//		$role = (!$auth->hasIdentity()) ? 'guest' : $auth->getIdentity()->login;
	//	}

}

