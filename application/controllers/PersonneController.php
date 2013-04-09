<?php
class PersonneController extends Zend_Controller_Action
{
	//action par d�faut
	public function indexAction()
	{
		//cr�ation d'un d'une instance Default_Model_Personne
		$personne = new Default_Model_Personne();

		//$this->view permet d'acc�der � la vue qui sera utilis�e par l'action
		//on initialise la valeur usersArray de la vue
		//(cf. application/views/scripts/users/index.phtml)
		//la valeur correspond � un tableau d'objets de type Default_Model_Personne r�cup�r�s par la m�thode fetchAll()
		//$this->view->usersArray = $personne->fetchAll();

		//cr�ation de notre objet Paginator avec comme param�tre la m�thode
		//r�cup�rant toutes les entr�es dans notre base de donn�es
		$paginator = Zend_Paginator::factory($personne->fetchAll($str =array()));
		//indique le nombre d�l�ments � afficher par page
		$paginator->setItemCountPerPage(20);
		//r�cup�re le num�ro de la page � afficher
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));

		//$this->view permet d'acc�der � la vue qui sera utilis�e par l'action
		//on initialise la valeur usersArray de la vue
		//(cf. application/views/scripts/users/index.phtml)
		$this->view->personneArray = $paginator;
	}

	public function createAction()
	{
		//cr�ation du fomulaire
		$form = new Default_Form_Personne();
		//indique l'action qui va traiter le formulaire
		$form->setAction($this->view->url(array('controller' => 'personne', 'action' => 'create'), 'default', true));
		$form->submit_pr->setLabel('Ajouter');

		//assigne le formulaire � la vue
		$this->view->form = $form;
		$this->view->title = "Ajouter Une Ressource";
		//si la page est POST�e = formulaire envoy�
		if($this->_request->isPost())
		{
			//r�cup�ration des donn�es envoy�es par le formulaire
			$data = $this->_request->getPost();

			//v�rifie que les donn�es r�pondent aux conditions des validateurs
			if($form->isValid($data))
			{
				//cr�ation et initialisation d'un objet Default_Model_Personne
				//qui sera enregistr� dans la base de donn�es
				$personne = new Default_Model_Personne();
				$personne->setNom($form->getValue('nom_pr'));
				$personne->setPrenom($form->getValue('prenom_pr'));
				$personne->setDate_entree($form->getValue('date_entree_pr'));
				$personne->setId_pole($form->getValue('id_pole_pr'));
				$personne->setDate_debut('0000-00-00');
				$personne->setDate_fin('0000-00-00');
				$personne->setId_entite($form->getValue('id_entite_pr'));
				$personne->setId_modalite($form->getValue('id_modalite_pr'));
				$personne->setId_fonction($form->getValue('id_fonction_pr'));
				$personne->setCentre_service($form->getValue('centre_service_pr'));
				$personne->setPourcent($form->getValue('pourcent_pr'));
				$personne->setStage($form->getValue('stage_pr'));
				if($form->getValue('id_modalite_pr')!= 7 && $form->getValue('centre_service_pr') ==1) 
				{
					echo "la modalit� est no compatible avec le centre cservice";
					
					$form->populate($data);
				}
				else
				{
					$personne->save();
					/* initialisation du solde conge de la personne cree*/		
					$resul = $personne ->find ($personne->maxid());
					$solde= new Default_Model_Solde();
					$solde ->setId_personne($resul->getId());
					$solde ->setTotal_cp($form->getValue('date_entree_pr'));
					if ($form->getValue('centre_service_pr') == 1)
					{
						//$solde ->setTotal_q1(0);
						$solde ->setTotal_q2(0);// a revoir
					}
					else 
					{
						//$solde ->setTotal_q1($form->getValue('id_modalite_pr'));
						$solde ->setTotal_q2(1);
					}
					$solde ->setTotal_q1($form->getValue('id_modalite_pr'));
					$solde ->setAnnee_reference();
					$solde->save();
	
					//redirection
					$this->_helper->redirector('index');
				}
			}
			else
			{
		                
				        
						$errorsMessages =$form->getMessages();
						// Pour afficher le tableau des erreurs => print_r($errorsMessages);
						 
						echo"<em><span style='background-color:rgb(255,0,0)'>";
						// affiche messagge d'erreur si valeur n'est pas entre 0 et 100
						if(isset($errorsMessages['pourcent_pr']['notBetween']))
						echo $errorsMessages['pourcent_pr']['notBetween']."<br/>";
						if(isset($errorsMessages['pourcent_pr']['notDigits']))
						// affiche messagge d'erreur si valeur n'est pas num�rique
						echo $errorsMessages['pourcent_pr']['notDigits']."<br/>";
						
						echo"</span></em>";
						echo"</span></em></strong>";
						
						
						



				
				$form->populate($data);
			}
		}
	}

	public function editAction()
	{
		//cr�ation du fomulaire
		$form = new Default_Form_Personne();
		//indique l'action qui va traiter le formulaire
		$form->setAction($this->view->url(array('controller' => 'personne', 'action' => 'edit'), 'default', true));
		$form->submit->setLabel('Modifier');

		//assigne le formulaire � la vue
		$this->view->form = $form;
		$this->view->title = "Modifier les donnees d'Une Ressource";
		//si la page est POST�e = formulaire envoy�
		if($this->getRequest()->isPost())
		{
			//r�cup�ration des donn�es envoy�es par le formulaire
			$data = $this->getRequest()->getPost();

			//v�rifie que les donn�es r�pondent aux conditions des validateurs
			if($form->isValid($data))
			{
				//cr�ation et initialisation d'un objet Default_Model_Personne
				//qui sera enregistr� dans la base de donn�es
				$personne = new Default_Model_Personne();
				$personne->setId($form->getValue('id'));
				$personne->setNom($form->getValue('nom_pr'));
				$personne->setPrenom($form->getValue('prenom_pr'));
				$personne->setDate_debut('0000-00-00');
				$personne->setDate_fin('0000-00-00');
				$personne->setId_pole($form->getValue('id_pole_pr'));
				$personne->setId_entite($form->getValue('id_entite_pr'));
				$personne->setId_fonction($form->getValue('id_fonction_pr'));
				$personne->setCentre_service($form->getValue('centre_service_pr'));
				$personne->setPourcent($form->getValue('pourcent_pr'));
				$personne->setStage($form->getValue('stage_pr'));
				$personne_vant_modif = new default_model_Personne();
				$personne_vant_modif  = $personne_vant_modif ->find($form->getValue('id'));
				$date_entree1 = $personne_vant_modif ->getDate_entree();
				$modalite_base =  $personne_vant_modif ->getId_modalite();
				$date_entree_base=$form->getValue('date_entree_pr');
				$modalite = $form->getValue('id_modalite_pr');
				if ($date_entree1 !=$date_entree_base || $modalite_base != $modalite )
				{
					$solde= new Default_Model_Solde();
					$solde->delete($form->getValue('id'));
					$solde->setId_personne($form->getValue('id'));
					$solde ->setTotal_cp($date_entree_base);
					$solde ->setTotal_q1($modalite);
					$solde ->setTotal_q2(2);
					$solde ->setAnnee_reference();
					$solde->save();
				}
				$personne->setDate_entree($date_entree_base);
				$personne->setId_modalite($modalite);
				$personne->save();

			
				//redirection
				$this->_helper->redirector('index');
			}
			else
			{
				//si erreur rencontr�e, le formulaire est rempli avec les donn�es
				//envoy�es pr�c�demment
				$form->populate($data);
			}
		}
		else
		{
			//r�cup�ration de l'id pass� en param�tre
			$id = $this->_getParam('id', 0);

			if($id > 0)
			{
				//r�cup�ration de l'entr�e
				$personne = new Default_Model_Personne();
				$personne = $personne->find($id);

				//assignation des valeurs de l'entr�e dans un tableau
				//tableau utilis� pour la m�thode populate() qui va remplir le champs du formulaire
				//avec les valeurs du tableau
				$data[] = array();
				$data['id'] = $personne->getId();
				$data['nom'] = $personne->getNom();
				$data['prenom'] = $personne->getPrenom();
				$data['date_entree'] = $personne->getDate_entree();
				$data['pole'] = $personne->getId_pole();
				$data['entite'] = $personne->getId_entite();
				$data['modalite'] = $personne->getId_modalite();
				$data['fonction'] = $personne->getId_fonction();
				$data['centre_service'] = $personne->getCentre_service();
				$data['pourcent'] = $personne->getPourcent();
				$data['stage'] = $personne->getStage();
				$form->populate($data);
			}
		}
	}

	public function deleteAction()
	{
		//r�cup�re les param�tres de la requ�te
		$params = $this->getRequest()->getParams();

		//v�rifie que le param�tre id existe
		if(isset($params['id']))
		{
			$id = $params['id'];

			//cr�ation du mod�le pour la suppression
			$personne = new Default_Model_Personne();
			//appel de la fcontion de suppression avec en argument,
			//la clause where qui sera appliqu�e
			$result = $personne->delete("id=$id");

			//redirection
			$this->_helper->redirector('index');
		}
		else
		{
			$this->view->form = 'Impossible delete: id missing !';
		}
	}

}