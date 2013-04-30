<?php
class SoldeController extends Zend_Controller_Action
{
	//action par d�faut
	protected $_annee;
	public function indexAction()
	{
		//cr�ation d'un d'une instance Default_Model_Solde
		$solde= new Default_Model_Solde();

		//$this->view permet d'acc�der � la vue qui sera utilis�e par l'action
		//on initialise la valeur usersArray de la vue
		//(cf. application/views/scripts/users/index.phtml)
		//la valeur correspond � un tableau d'objets de type Default_Model_Solde r�cup�r�s par la m�thode fetchAll()
		//$this->view->usersArray = $personne->fetchAll();
		$var = (int)$this->_annee;
		//cr�ation de notre objet Paginator avec comme param�tre la m�thode
		//r�cup�rant toutes les entr�es dans notre base de donn�es
		
		//r�cup�rant toutes les entr�es dans notre base de donn�es
		$paginator = Zend_Paginator::factory($solde->fetchAll('annee_reference='.$var));
		//indique le nombre d�l�ments � afficher par page
		$paginator->setItemCountPerPage(20);
		//r�cup�re le num�ro de la page � afficher
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));

		//$this->view permet d'acc�der � la vue qui sera utilis�e par l'action
		//on initialise la valeur usersArray de la vue
		//(cf. application/views/scripts/users/index.phtml)
		$this->view->soldeArray = $paginator;
	}

	public function createAction()
	{

		
		//cr�ation du fomulaire
		$form = new Default_Form_Solde();
		//indique l'action qui va traiter le formulaire
		$form->setAction($this->view->url(array('controller' => 'solde', 'action' => 'create'), 'default', true));
		$form->submit_sl->setLabel('Initiliser');
		$this->view->form = $form;
		$this->view->title = "Initialiser Les Soldes";
		$data =array();
		//si la page est POST�e = formulaire envoy�
		if($this->_request->isPost())
		{
			//r�cup�ration des donn�es envoy�es par le formulaire
			$data = $this->_request->getPost();

			//v�rifie que les donn�es r�pondent aux conditions des validateurs
			if($form->isValid($data))
			{
				
				$solde = new Default_Model_Solde();
				$var = (int)$form->getValue('annee_reference_sl');
				$solde1 = $solde->fetchall2('annee_reference ='.$var);
				
				if (!count($solde1))
				{
			
					$personne = new Default_Model_Personne();
					$personne = $personne->fetchall($str =array());
					foreach ($personne as $p)
					{
						
						$date_entree = $p->getDate_entree();
						$modalite = $p->getModalite()->getId();
						
						$solde->setPersonne($p);
						$solde->setTotal_cp($date_entree);
						$solde->setTotal_q1($modalite);
						$solde->setTotal_q2(0);
						$solde->setAnnee_reference($form->getValue('annee_reference_sl'));
						$solde->save();
					}
					
					$this->_annee =$form->getValue('annee_reference_sl');
					$this->_helper->redirector('index');
				}
				
				else
				{
					echo "solde est deja declarer pour cet annee";
					$form->populate($data);
				}
			}
			else
				{
					
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
			$personne = new Default_Model_Solde();
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