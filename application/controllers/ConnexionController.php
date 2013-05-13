<?php
class ConnexionController extends Zend_Controller_Action
{
	private $_auth;

      public function preDispatch() 
	  {
	    	    $doctypeHelper = new Zend_View_Helper_Doctype();
	            $doctypeHelper->doctype('HTML5');
	    		$this->_helper->layout->setLayout('loginlayout');
	    		$this->_auth = Zend_Auth::getInstance();

	  }

	public function indexAction()
	{
	 //cr�ation du fomulaire
		$form = new Default_Form_TConnexion();
		
        //assigne le formulaire � la vue
		$this->view->form = $form;

		// remplir le select par les profils
	     $form->setDbOptions('Profil',new Default_Model_Profil(),'getLogin','getLogin');
		//v�rification si la page a bien �t� appel�e � partir d'un formulaire
     
	     if($this->_request->isPost())
		{   
			
			//enregistrement des donn�es envoy�es � partir du formulaire dans un tableau
			$data = $this->_request->getPost();
			
			//validation du formulaire
			if($form->isValid($data))
			{
				//Zend_Debug::dump($data, $label = "Formulaire de connexion valide", $echo = true);
				$profil = $data['Profil'];
				$mot_passe =  $data['Password'];
				
				// MTA 
                $remember_me = $data['Remember']; // recupere la valeur de checkbox remember me 
                
                
            
              
                
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
					//Zend_Debug::dump($data, $label = "R�sultat de la connexion valide", $echo = true);
					//stockage de l'identit� sous forme d'objet
					//le permier argument permet d'indiquer les valeurs que l'on veut enregistrer (null indique que l'on enreegistre l'enti�ret� de l'objet)
					//le second argument permet d'indiquer les valeurs que l'on ne souhaite pas enregistrer
					$this->_auth->getStorage()->write($res = $authAdapter->getResultRowObject(null, 'mot_passe'));

					// MTA
					if ($remember_me === '1')
			        {   
			           	Zend_Session::rememberMe(24*3600);   // remember me pendant = 24h
	                }
	
					//permet de reg�n�rer l'identifiant de session
                    //  Zend_Session::rememberMe();  appel  Zend_Session::regenerateId();
                    //  Zend_Session::regenerateId();
                   
                    
			            if($data['Profil'] === 'x')     // si on a pas selectionn� une ressource  id = 'x'
						{
						   $this->view->error = "Veuillez selectionner un profil !";
						}
				        
						elseif ($data['Password'] === '')
						{	
							$this->view->error = "Veuillez renseignez votre Mot de pass !";	//cr�ation et initialisation d'un objet Default_Model_Users
						}
						else 
						{   
	
						    if($profil === 'Admin')
								$this->view->success = " Bienvenue a vous Mr l'".$profil;
							elseif($profil === 'Csm')
								$this->view->success = " Bienvenue a vous responsable de l'equipe ".$profil;
							elseif($profil === 'Equipe')
								$this->view->success = " Bienvenue a vous membre de l'".$profil;
							else 
							$this->view->success = " Bienvenue a vous membre guest ";

			                
							//redirection
					        $this->_helper->_redirector('calendriermensuel', 'calendrier');
					        
						}

				}
				else
				{
				        if($data['Profil'] === 'x' && $data['Password'] <> '')     // si on a pas selectionn� une ressource  id = 'x'
						{
						   $this->view->error = "Veuillez selectionner d'abord un profil !";
						}
					    else 
					    {
						$this->view->error = "Mot de passe incorrecte !";
						$form->getElement('Password')->setValue('');
						$form->getElement('Password')->setErrorMessages(array("Mot de passe invalide !"));
					    }
				}
			}
			else  // formulaire invalide 
			{

			           if($data['Profil'] === 'x')     // si on a pas selectionn� une ressource  id = 'x'
						{
						   $this->view->error = "Veuillez selectionner un profil !";
						}
						
						elseif ($data['Password'] === '')
						{	
							$this->view->error = "Veuillez renseignez votre Mot de passe !";	//cr�ation et initialisation d'un objet Default_Model_Users
						}
	
			}
		}
	
	} 
	  
	  
	public function connexionAction()
	{   
	   
	}

	public function deconnexionAction()
	{
		//r�initialisation de l'instance d'authentification et destruction de la session
		Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::destroy();
		$this->_helper->redirector('connexion','index');
	}
}