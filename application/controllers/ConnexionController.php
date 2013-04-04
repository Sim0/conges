<?php
class ConnexionController extends Zend_Controller_Action
{
	private $_auth;

	public function init()
	{
		//r�cup�ration de l'instance d'authentification
		$this->_auth = Zend_Auth::getInstance();
		// Appel de l'aide comme m�thode sur le gestionnaire d'aides:
		$layout = $this->_helper->layout();
		$layout->disableLayout();
		
   
	}

	public function indexAction()
	{
		//cr�ation et affichage dans la vue du formulaire
		$connexionForm = new Default_Form_Connexion();
		$connexionForm->setAction($this->view->url(array('controller' => 'connexion', 'action' => 'connexion'), 'default', true));
		$this->view->form = $connexionForm;
	
		
	}

	public function connexionAction()
	{
		//cr�ation du fomulaire
		$form = new Default_Form_Connexion();
		//indique l'action qui va traiter le formulaire
		$form->setAction($this->view->url(array('controller' => 'connexion', 'action' => 'connexion'), 'default', true));
		

		//assigne le formulaire � la vue
		$this->view->form = $form;

		//v�rification si la page a bien �t� appel�e � partir d'un formulaire
		if($this->_request->isPost())
		{
			//enregistrement des donn�es envoy�es � partir du formulaire dans un tableau
			$data = $this->_request->getPost();

			//validation du formulaire
			if($form->isValid($data))
			{
				//Zend_Debug::dump($data, $label = "Formulaire de connexion valide", $echo = true);
				$profil = $data['profil'];
				$mot_passe =  $data['mot_passe'];

				//cr�ation d'un adpatateur d'authentification utilisant une base de donn�es
				//le premier argument correspond � l'adptateur par d�faut
				//le second correspond � la table qui est utilis�e pour l'authentification
				//le troisi�me indique la colonne utilis�e pour repr�senter l'identit� (le profil)
				//le quatri�me argument indique la colonne utilis�e pour repr�senter le cr�dit (le password)
				$authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table_Abstract::getDefaultAdapter(),
															'profil',		//table 'profil' 
															'login',		//champ identit� 
															'mot_passe');	//champ cr�dit

				//pr�paration de la requ�te d'authentification en indiquant l'identit� et le cr�dit
				$authAdapter->setIdentity($profil);
				$authAdapter->setCredential($mot_passe);

				//ex�cution de la requ�te d'authentification et enregistrement du r�sultat
				$result = $this->_auth->authenticate($authAdapter);

				//si l'authentification a r�ussi
				if($result->isValid())
				{
					Zend_Debug::dump($data, $label = "R�sultat de la connexion valide", $echo = true);
					//stockage de l'identit� sous forme d'objet
					//le permier argument permet d'indiquer les valeurs que l'on veut enregistrer (null indique que l'on enreegistre l'enti�ret� de l'objet)
					//le second argument permet d'indiquer les valeurs que l'on ne souhaite pas enregistrer
					$this->_auth->getStorage()->write($res = $authAdapter->getResultRowObject(null, 'mot_passe'));

					//permet de reg�n�rer l'identifiant de session
					Zend_Session::regenerateId();

					//redirection
					$this->_helper->_redirector('calendriermensuel', 'calendrier');
				}
				else
				{
					//si erreur rencontr�e, le formulaire est recharg�
					
					echo "mot de passe incorrecte";
					///$this->_helper->_redirector('index', 'connexion');
				
				}
			}
			else
			{
				//si erreur rencontr�e, le formulaire est recharg�
				
				echo "mot de passe incorrecte";
				//$this->_helper->_redirector('index', 'connexion');
				
			}
		}
		else
		{
			//redirection si la page n'a pas �t� appel�e � partir d'un formulaire
			//$this->_redirect($this->view->url(array('controller' => 'profil'), 'default', true));
			
			echo "mot de passe incorrecte";	
			//$this->_helper->redirector('index', 'connexion');
			
		}
	}

	public function deconnexionAction()
	{
		//r�initialisation de l'instance d'authentification et destruction de la session
		Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::destroy();
		$this->_helper->redirector('index', 'connexion');
	}
}