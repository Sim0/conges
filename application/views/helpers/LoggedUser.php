<?php
class Zend_View_Helper_LoggedUser
{
	protected $_view;

	function setView($view)
	{
		$this->_view = $view;
	}

	function loggedUser()
	{
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			//cr�ation du lien logout � partir de l'aide de vue url
			$logoutUrl = $this->_view->url(array('controller' =>'login', 'action' => 'logout'), 'default', true);
			//r�cup�re l'identit� de l'utilisateur
			$user = $auth->getIdentity();
			$username = $this->_view->escape($user->login);
			$role = $this->_view->escape($user->role);
			//chaine qui sera affich�e si l'utilisateur est connect�
			$link = 'Welcome, ' . $username .  ' | Vous avez le role ' . $role . ' | <a href="' . $logoutUrl . '">Log out</a>';
		}
		else
		{
			//cr�ation du lien loin � partir de l'aide de vue url
			$loginUrl = $this->_view->url(array('controller' => 'login'), null, true);
			//chaine qui sera affich�e si l'utilisateur n'est pas connect�
			$link = '<a href="' . $loginUrl . '">Log in</a>';
		}

		return $link;
	}
}