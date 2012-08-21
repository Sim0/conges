<?php
class LoginController extends Zend_Controller_Action
{
	private $_auth;

	public function init()
	{
		//r�cup�ration de l'instance d'authentification
		$this->_auth = Zend_Auth::getInstance();
	}

	public function indexAction()
	{
		//cr�ation et affichage dans la vue du formulaire
		$loginForm = new Default_Form_Login();
		$loginForm->setAction($this->view->url(array('controller' => 'login', 'action' => 'login'), 'default', true));
		$this->view->loginForm = $loginForm;
	}

	public function loginAction()
	{
		//r�cup�ration de la requ�te
		$request = $this->_request;

		//v�rification si la page a bien �t� appel�e � partir d'un formulaire
		if($request->isPost())
		{
			$loginForm = new Default_Form_Login();
			//enregistrement des donn�es envoy�es � partir du formulaire dans un tableau
			$data = $request->getPost();

			//validation du formulaire
			if($loginForm->isValid($data))
			{
				$login = $data['login'];
				$password =  $data['password'];

				//cr�ation d'un adpatateur d'authentification utilisant une base de donn�es
				//le premier argument correspond � l'adptateur par d�faut
				//le second correspond � la table qui est utilis�e pour l'authentification
				//le troisi�me indique la colonne utilis�e pour repr�senter l'identit� (le login)
				//le quatri�me argument indique la colonne utilis�e pour repr�senter le cr�dit (le password)
				$authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table_Abstract::getDefaultAdapter(), 'users', 'login', 'password');

				//cr�ation de validateurs permettant de v�rifier si certaines exigences sont respect�es
				//par le login et le password
				//Zend_Validate_NotEmpty(): permet de v�rifier que la valeur n'est pas vide
				//Zend_Validate_StringLength(4) v�rifie la taille minimum d'une chaine
				$validatorLogin = new Zend_Validate();
				$validatorLogin->addValidator(new Zend_Validate_NotEmpty());
				$validatorLogin->addValidator(new Zend_Validate_StringLength(4));
				$validatorPassword = new Zend_Validate();
				$validatorPassword->addValidator(new Zend_Validate_NotEmpty());
				$validatorPassword->addValidator(new Zend_Validate_StringLength(4));

				//V�rification que le login et le password respectent les validateurs
				if($validatorLogin->isValid($login) && $validatorPassword->isValid($password))
				{
					//pr�paration de la requ�te d'authentification en indiquant l'identit� et le cr�dit
					$authAdapter->setIdentity($login);
					$authAdapter->setCredential($password);

					//ex�cution de la requ�te d'authentification et enregistrement du r�sultat
					$result = $this->_auth->authenticate($authAdapter);

					//si l'authentification a r�ussi
					if($result->isValid())
					{
						//stockage de l'identit� sous forme d'objet
						//le permier argument permet d'indiquer les valeurs que l'on veut enregistrer (null indique que l'on enreegistre l'enti�ret� de l'objet)
						//le second argument permet d'indiquer les valeurs que l'on ne souhaite pas enregistrer
						$this->_auth->getStorage()->write($res = $authAdapter->getResultRowObject(null, 'password'));

						//permet de reg�n�rer l'identifiant de session
						Zend_Session::regenerateId();

						//redirection
						$this->_helper->_redirector('index', 'users');
					}
				}
			}
		}
		else
		{
			//redirection si la page n'a pas �t� appel�e � partir d'un formulaire
			$this->_redirect($this->view->url(array('controller' => 'login'), 'default', true));
		}
	}

	public function logoutAction()
	{
		//r�initialisation de l'instance d'authentification et destruction de la session
		Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::destroy();
		$this->_helper->redirector('index', 'index');
	}
}