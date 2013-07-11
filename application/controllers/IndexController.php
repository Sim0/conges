<?php

//un controller doit h�riter de la classe Zend_Controller_Action,
//il doit respecter la nomenclature [ControllerName]Controller
class IndexController extends Zend_Controller_Action
{
	
	function preDispatch()
	{
	
          $doctypeHelper = new Zend_View_Helper_Doctype();
          $doctypeHelper->doctype('HTML5');
        
     
		
		$auth = Zend_Auth::getInstance();

		if (!$auth->hasIdentity()) 
		{
			$this->_redirect('connexion/index');
		}
	}

	function init()
	{
		$this->initView();
		//Zend_Loader::loadClass('Album');
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->role = Zend_Auth::getInstance()->getIdentity();
	}
	
	//une action doit respecter la nomenclature [actionName]Action
	public function indexAction()
{
          $conges          = array(); // tableau conteneur des cong�s qui seront renvoy�s � la vue
          $keysIndiceConge = array(); // tableau des indice cong� (dans le cas ou plusieurs cong� pos� dans un seul mois)
          $indiceConge ; //mois-annee , les cong�s pos�s sur un mois sont associ�s a cette cl�
          $ressourcesArray = array();
          //requ�te Ajax re�ue 
          if ($this->getRequest()->isXmlHttpRequest()) {
              //r�cup�ration des donn�es envoy�es en ajax
              $data = $this->getRequest()->getPost();
              
              if (isset($data['annee']) && isset($data['mois'])) {
                  
                  if (isset($data['id_personne']) && $data['id_personne'] !== 'x') {
                      
                      $personne = new Default_Model_Personne();
                      $personne->find($data['id_personne']);
                      $cs = $personne->getEntite()->getCs();
                      
                      if ($cs == '1')
                          $cs = true;
                      else
                          $cs = false;
                      
                      $congeObj   = new Default_Model_Conge();
                      $congeArray = array();
                      
                      $outils  = new Default_Controller_Helpers_outils();
                      $jferies = $outils->setJoursFerie($data['annee'], $cs, false);
                      $jferies = (array) $jferies;
                    

                     
                      // composition de la date d�but a partir du mois et ann�e saisie dans le form
                      $dateDebut = '01-' . $data['mois'] . '-' . $data['annee'];
                      
                      if ($data['mois'] == '1') // : f�vrier ( Janvier = 0) 
                          {
                          if (((($data['annee'] % 4) == 0) && ((($data['annee'] % 100) != 0) || (($data['annee'] % 400) == 0)))) // ann�e bissextile
                              {
                              $dateFin = '29-' . $data['mois'] . '-' . $data['annee'] . ' 23:59:59';
                              
                          } else // ann�e non bissextile
                              {
                              $dateFin = '28-' . $data['mois'] . '-' . $data['annee'];
                          }
                      } else if ($data['mois'] == '3' || $data['mois'] == '5' || $data['mois'] == '8' || $data['mois'] == 10) // avril / juin / Septembre / Novembre
                          {
                          $dateFin = '30-' . $data['mois'] . '-' . $data['annee'] . ' 23:59:59';
                      } else // Janvier/ Mars /Mai / Juillet /Aout / Octobre /D�cembre
                          {
                          $dateFin = '31-' . $data['mois'] . '-' . $data['annee'] . ' 23:59:59';
                      }
                      
                      // r�cup�ration des cong�s 
                      $congeArray = $congeObj->conges_existant($personne->getId(), $dateDebut, $dateFin, '0');
                     
                     
	                    $resultCount = count($congeArray);
	                    
		                   if($resultCount == 0)  // si aucun cong� n'est retourn�.
		                   {
		                   	 $this->_helper->json(null);
		                   	 return;
		                   }
                      
                      foreach ($congeArray as $k => $v) {
                           
                          $idTypeConge = $congeArray[$k]['id_type_conge'];
		                  $typeConge = new Default_Model_TypeConge();
		                  $tc = $typeConge->find($idTypeConge);
		                  $codeTypeConge = $tc->getCode();   
                              
                          /*
                           * r�cup�ation du d�tail de la p�riode de cong�
                           * 
                           */
                          $conge = $outils->getPeriodeDetails($congeArray[$k]['date_debut'], $congeArray[$k]['date_fin'],$codeTypeConge,$cs, false);
                          $conge['nombreJours'] = $congeArray[$k]['nombre_jours']; 
                           
                          // indice cong� sous format : annee-mois
                          $indiceConge = explode("-", $congeArray[$k]['date_debut']);
                          $indiceConge = $indiceConge['0'] . '-' . $indiceConge['1']; // indice conge sous format Annee-mois
                          
                          // compter le nombre de cong� pos�s s�paremment sur un moi
                          if (isset($keysIndiceConge[$indiceConge])) {
                              $keysIndiceConge[$indiceConge] = $keysIndiceConge[$indiceConge] + 1;
                          } else {
                              $keysIndiceConge[$indiceConge] = 0;
                              
                          }
                          
                          // stocker les cong�s dans la table sous l'indice [annee-mois][numConge]
                          $conges[$indiceConge][$keysIndiceConge[$indiceConge]] = (array) $conge;
                          
                          
                      }
                      
                      /*
                       * Tableau qui sera envoy� � la vue en format ensuite pars� en javascript
                       * pour dessiner le calendrier
                       */
                      $ressourcesArray = array('ressources'=>
                                                     array('0'=>
                                                          array('id_personne' => $personne->getId(),
                                                                'Nom' => $personne->getNomPrenom(),
                                                                'Pole' => array('libelle' =>$personne->getPole()->getLibelle(),'value'=> $personne->getPole()->getId()),
                                                                'Entite' => array('libelle'=>$personne->getEntite()->getLibelle(),'value' => $personne->getEntite()->getId()),
                                                                'Fonction'=> array('libelle' => $personne->getFonction()->getLibelle(),'value'=> $personne->getFonction()->getId()),
                                                                'cs' => $personne->getEntite()->getCs(),
                                                                'conge'=>  $conges )),
                                                        
                                                     
                                                       'Ferie'=> $jferies['joursFerie']);
                    
                       // renvoie de la structure � la vue en format json .
                      $this->_helper->json($ressourcesArray);
                      
                      
                      
                      
                  } 
                  else if (isset($data['id_personne']) && $data['id_personne'] === 'x') // id personne non s�lectionn� toutes les ressources.
                  {	
                    $congeObj   = new Default_Model_Conge();
                    $congeArray = array();
                    
                    $congeArray = $congeObj->fetchAll($str = array());
                    
                    
                    $resultCount = count($congeArray);
                    
                   if($resultCount == 0)// si aucun cong�
                   {
                   	 $this->_helper->json(null);
                   	 return;
                   }
                    
                    
                    $i = 0;
                    
                    
                    foreach($congeArray as $v)
                    {
                        $conge = array(); //r�initialisation de $conge (table temp)
                     
                     		$idPersonne = $v->getId_personne();
                     		$personne = new Default_Model_Personne();
                     	
	                     	$personne->find($idPersonne);
	                      	$cs = $personne->getEntite()->getCs() ;
                             if ($cs == '1')
	                          $cs = true;
	                    	  else
	                          $cs = false;
                        
                              $congeObj   = new Default_Model_Conge();
		                      $congeArray = array();
		                      
		                      $outils  = new Default_Controller_Helpers_outils();
		                    
		                      
		                     $dd =  $v->getDate_debut();
		                     $df = $v->getDate_fin();
		                   
		                      
				            
		                              
		                /*
                           * r�cup�ation du d�tail de la p�riode de cong�
                           * 
                           */
		                  $idTypeConge = $v->getId_type_conge();
		                  $typeConge = new Default_Model_TypeConge();
		                  $tc = $typeConge->find($idTypeConge);
		                  $codeTypeConge = $tc->getCode();
                          $conge = $outils->getPeriodeDetails($dd , $df,$codeTypeConge, $cs, false);
                          $conge['nombreJours'] = $v->getNombre_jours(); 
                           
                          // indice cong� sous format : annee-mois
                          $indiceConge = explode("-", $dd);
                          $indiceConge = $indiceConge['0'] . '-' . $indiceConge['1']; // indice conge sous format Annee-mois
                         
                          // compter le nombre de cong� pos�s s�paremment sur un moi
                          if (isset($keysIndiceConge[$idPersonne]) && isset($keysIndiceConge[$idPersonne][$indiceConge])) {
                              $keysIndiceConge[$idPersonne][$indiceConge] = $keysIndiceConge[$idPersonne][$indiceConge] + 1;
                          } else {
                              $keysIndiceConge[$idPersonne][$indiceConge] = 0;
                              
                          }
                          
                          // stocker les cong�s dans la table sous l'indice [annee-mois][numConge]
                          $conges[$idPersonne][$indiceConge][$keysIndiceConge[$idPersonne][$indiceConge]] = (array) $conge;
                         
                              
                          
                    }
               
                   foreach ($conges as $k => $v)
                    {
                    	
                      	$personne = new Default_Model_Personne();
                     	
                     	$personne->find($k);
                      	$cs = $personne->getEntite()->getCs() ;
                         $nomPrenom = $personne->getNomPrenom();
                         $fonction = $personne->getFonction()->getLibelle();
                         $entite = $personne->getEntite()->getLibelle();
                         $pole = $personne->getPole()->getLibelle(); 	

                         if ($cs == '1')
                          $cs = true;
                    	  else
                          $cs = false;
                          
                         // structure envoy� au navigateur pour alimenter le calendrier
                     $ressources[$i] =   array('id_personne' => $k,
                                                                'Nom' => $nomPrenom,
                                                                'Pole' => array('libelle' =>$personne->getPole()->getLibelle(),'value'=> $personne->getPole()->getId()),
                                                                'Entite' => array('libelle'=>$personne->getEntite()->getLibelle(),'value' => $personne->getEntite()->getId()),
                                                                'Fonction'=> array('libelle' => $personne->getFonction()->getLibelle(),'value'=> $personne->getFonction()->getId()),
                                                                'cs' => $cs,
                                                                'conge'=>  $conges[$k] );
                          
                          
		                         $i++;
                    }

                     $jferiesCSM = $outils->setJoursFerie($data['annee'], true, false);
		             $jferiesCSM = (array) $jferiesCSM;
		             
		             $jferiesFR = $outils->setJoursFerie($data['annee'], false, false);
		             $jferiesFR = (array) $jferiesFR;
                     
		             $ressourcesArray = array('ressources'=> $ressources,
                                              'Ferie'=> array_merge($jferiesCSM['joursFerie'],$jferiesFR['joursFerie']));
		                 
                     $this->_helper->json($ressourcesArray);
                    
                     
                  }
                  else
                  {
                  	   $this->view->error = 'Choisissez une personne !';
                  }
                  
              }
                  
               } else { // affichage du formulaire .
          	
              $form = new Default_Form_CalendrierForm();
              $form->setDbOptions('personne', new Default_Model_Personne(), 'getId', 'getNomPrenom');
              
              $this->view->form = $form;
            
          }
          
      }

}

