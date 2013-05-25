<?php
class OutilsController extends Zend_Controller_Action
{
	  public function preDispatch() 
	  {
	    	    $doctypeHelper = new Zend_View_Helper_Doctype();
	            $doctypeHelper->doctype('HTML5');
	    		$this->_helper->layout->setLayout('mylayout');      
	  }
	  
	  
	  
	  public function calculNombreJoursCongeAction()
	{
		
		//cr�ation du fomulaire
		$form = new Default_Form_OutilsForm();
		//indique l'action qui va traiter le formulaire
		$form->setAction($this->view->url(array('controller' => 'outils', 'action' => 'calculNombreJoursConge'), 'default', true));

        //assigne le formulaire � la vue
		$this->view->form = $form;
		$this->view->title = "Cr�er un conge";
			/*************************************/
	    $conge = new Default_Model_Conge();
	  
	// requete POST 
		if($this->_request->isPost())   
		{
			
			
			// r�cup�ration des donn�es envoy�s par le formulaire
			$data = $this->_request->getPost();
			
		
			// si date(s) non renseign�e(s)
            if($data['dateDebut'] == '' || $data['dateFin'] == '')
            {
            	if($data['dateDebut'] =='')
            	{
            	$this->view->error = 'saisissez la date de d�but !!';
            	$form->populate($data);
            	}
            	else
            	{
            	$this->view->error = 'saisissez la date de fin !!';
            	$form->populate($data);
            	}
            }
            else if($data['dateDebut'] >$data['dateFin'] )
            {
            	$this->view->error = 'date fin doit �tre supperieur ou �gale � date debut';
            	$form->populate($data);
            }
            else 
            {
            	
            $dateDebut = $data['dateDebut'];
			$dateFin = $data['dateFin'];
			$debutMidi = $data['DebutMidi'];
			$finMidi = $data['FinMidi'];
			$csm = $data['csm'];
			$am = $data['AlsaceMoselle'];
			$anneeReference = '2013';
			
			$conge->setDate_debut($dateDebut);
			$conge->setDate_fin($dateFin);
			$conge->setAnnee_reference($anneeReference);
			
			
			
			
			if($debutMidi == '0')
			{
				$conge->setMi_debut_journee(false);
			}
			else
			{
				$conge->setMi_debut_journee(true);
			}
			if($finMidi == '0')
			{
				$conge->setMi_fin_journee(false);
			}
			else 
			{
				$conge->setMi_fin_journee(true);
			}
			if($csm == '0' && $am == '0' )
			{//si CSM et Alsace Moselle non check�s
				$conge->CalculNombreJoursConge();
			}
			else
			{
				//CSM check�
				if($csm == '1')
				{
					$csm = true;
				}
				//Alsace Moselle check�
				if($am == '1')
				{
					$am = true;
				}	
							
				$conge->CalculNombreJoursConge($csm,$am);
			}
			

			$form->populate($data);
          }
            	
            }
			
	}

		
		
	
	  
	  
	  
}