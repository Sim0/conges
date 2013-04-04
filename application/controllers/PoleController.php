<?php
class PoleController extends Zend_Controller_Action
{
	//action par d�faut
	public function indexAction()
	{
		//cr�ation d'un d'une instance Default_Model_Users
		$pole = new Default_Model_Pole();

		//$this->view permet d'acc�der � la vue qui sera utilis�e par l'action
		//on initialise la valeur usersArray de la vue
		//(cf. application/views/scripts/users/index.phtml)
		//la valeur correspond � un tableau d'objets de type Default_Model_Users r�cup�r�s par la m�thode fetchAll()
		//$this->view->usersArray = $users->fetchAll();

		//cr�ation de notre objet Paginator avec comme param�tre la m�thode
		//r�cup�rant toutes les entr�es dans notre base de donn�es
		$paginator = Zend_Paginator::factory($pole->fetchAll($str =array()));
		//indique le nombre d�l�ments � afficher par page
		$paginator->setItemCountPerPage(10);
		//r�cup�re le num�ro de la page � afficher
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));

		//$this->view permet d'acc�der � la vue qui sera utilis�e par l'action
		//on initialise la valeur usersArray de la vue
		//(cf. application/views/scripts/users/index.phtml)
		$this->view->congeArray = $paginator;
	}

	public function createAction()
	{
		//cr�ation du fomulaire
		$form = new Default_Form_Pole();
		//indique l'action qui va traiter le formulaire
		$form->setAction($this->view->url(array('controller' => 'conge', 'action' => 'create'), 'default', true));
		$form->submit->setLabel('Creer');

		//assigne le formulaire � la vue
		$this->view->form = $form;

		//si la page est POST�e = formulaire envoy�
		if($this->_request->isPost())
		{
			//r�cup�ration des donn�es envoy�es par le formulaire
			$data = $this->_request->getPost();

			//v�rifie que les donn�es r�pondent aux conditions des validateurs
			if($form->isValid($data))
			{
				//cr�ation et initialisation d'un objet Default_Model_Users
				//qui sera enregistr� dans la base de donn�es
				$pole = new Default_Model_Pole();
				$pole->setLibelle($form->getValue('libelle'));

				$pole->save();

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
	}

	public function editAction()
	{
		//cr�ation du fomulaire
		$form = new Default_Form_Pole();
		//indique l'action qui va traiter le formulaire
		$form->setAction($this->view->url(array('controller' => 'conge', 'action' => 'edit'), 'default', true));
		$form->submit->setLabel('Modifier');

		//assigne le formulaire � la vue
		$this->view->form = $form;

		//si la page est POST�e = formulaire envoy�
		if($this->getRequest()->isPost())
		{
			//r�cup�ration des donn�es envoy�es par le formulaire
			$data = $this->getRequest()->getPost();

			//v�rifie que les donn�es r�pondent aux conditions des validateurs
			if($form->isValid($data))
			{
				
				//cr�ation et initialisation d'un objet Default_Model_Pole
				//qui sera enregistr� dans la base de donn�es
				$pole = new Default_Model_Pole();
				$pole->setId($form->getValue('id'));
				$pole->setLibelle($form->getValue('libelle'));
				$pole->save();

				
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
				$pole = new Default_Model_Pole();
				$pole = $pole->find($id);

				//assignation des valeurs de l'entr�e dans un tableau
				//tableau utilis� pour la m�thode populate() qui va remplir le champs du formulaire
				//avec les valeurs du tableau
				$data[] = array();
				$data['id'] = $pole->getId();
				$data['libelle'] = $pole->getLibelle();
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
			$pole = new Default_Model_Pole();
			//appel de la fcontion de suppression avec en argument,
			//la clause where qui sera appliqu�e
			$result = $pole->delete("id=$id");

			//redirection
			$this->_helper->redirector('index');
		}
		else
		{
			$this->view->form = 'Impossible delete: id missing !';
		}
	}
	/*public function rederigerversindexAction ()
	{
	   $this->_helper->redirector('index');
		
	}*/
	
	
}