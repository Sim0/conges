<?php
class Zend_View_Helper_LoggedProfil
{
	protected $_view;

	function setView($view)
	{
		$this->_view = $view;
	}

	function loggedProfil()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			//cr�ation du lien logout � partir de l'aide de vue url
			$logoutUrl = $this->_view->url(array('controller' =>'connexion', 'action' => 'deconnexion'), 'default', true);
			//r�cup�re l'identit� de l'utilisateur
			$user = $auth->getIdentity();
			//$username = $this->_view->escape($user->login);
			$profil = $this->_view->escape($user->login);
			//chaine qui sera affich�e si l'utilisateur est connect�
			$link = 'Bonjour, ' . '  Vous avez le profil ' . $profil . '  <a href="' . $logoutUrl . '">Deconnexion</a>';
		}
		else
		{
			//cr�ation du lien loin � partir de l'aide de vue url
			$loginUrl = $this->_view->url(array('controller' => 'connexion'), null, true);
			//chaine qui sera affich�e si l'utilisateur n'est pas connect�
			$link = '<a href="' . $loginUrl . '">Connexion</a>';
		}

		return $link;
	}
}