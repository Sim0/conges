<?php
  class CalendrierController extends Zend_Controller_Action
  {
      public function preDispatch()
      {
          $doctypeHelper = new Zend_View_Helper_Doctype();
          $doctypeHelper->doctype('HTML5');
         
      }
      
      public function indexAction()
      {
          $conges          = array(); // tableau conteneur des congés qui seront renvoyés à la vue
          $keysIndiceConge = array(); // tableau des indice congé (dans le cas ou plusieurs congé posé dans un seul mois)
          $indiceConge ; //mois-annee , les congés posés sur un mois sont associés a cette clé
          $ressourcesArray = array();
          //requête Ajax reçue 
          if ($this->getRequest()->isXmlHttpRequest()) {
              //récupération des données envoyées en ajax
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
                    

                     
                      // composition de la date début a partir du mois et année saisie dans le form
                      $dateDebut = '01-' . $data['mois'] . '-' . $data['annee'];
                      
                      if ($data['mois'] == '1') // : février ( Janvier = 0) 
                          {
                          if (((($data['annee'] % 4) == 0) && ((($data['annee'] % 100) != 0) || (($data['annee'] % 400) == 0)))) // année bissextile
                              {
                              $dateFin = '29-' . $data['mois'] . '-' . $data['annee'] . ' 23:59:59';
                              
                          } else // année non bissextile
                              {
                              $dateFin = '28-' . $data['mois'] . '-' . $data['annee'];
                          }
                      } else if ($data['mois'] == '3' || $data['mois'] == '5' || $data['mois'] == '8' || $data['mois'] == 10) // avril / juin / Septembre / Novembre
                          {
                          $dateFin = '30-' . $data['mois'] . '-' . $data['annee'] . ' 23:59:59';
                      } else // Janvier/ Mars /Mai / Juillet /Aout / Octobre /Décembre
                          {
                          $dateFin = '31-' . $data['mois'] . '-' . $data['annee'] . ' 23:59:59';
                      }
                      
                      // récupération des congés 
                      $congeArray = $congeObj->conges_existant($personne->getId(), $dateDebut, $dateFin, '0');
                     
	                  $resultCount = count($congeArray);
	                    
	                   if($resultCount == 0) // si aucun congé
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
                           * récupéation du détail de la période de congé
                           * 
                           */
                          $conge = $outils->getPeriodeDetails($congeArray[$k]['date_debut'], $congeArray[$k]['date_fin'],$codeTypeConge,$cs, false);
                          $conge['nombreJours'] = $congeArray[$k]['nombre_jours']; 
                           
                          // indice congé sous format : annee-mois
                          $indiceConge = explode("-", $congeArray[$k]['date_debut']);
                          $indiceConge = $indiceConge['0'] . '-' . $indiceConge['1']; // indice conge sous format Annee-mois
                          
                          // compter le nombre de congé posés séparemment sur un moi
                          if (isset($keysIndiceConge[$indiceConge])) {
                              $keysIndiceConge[$indiceConge] = $keysIndiceConge[$indiceConge] + 1;
                          } else {
                              $keysIndiceConge[$indiceConge] = 0;
                              
                          }
                          
                          // stocker les congés dans la table sous l'indice [annee-mois][numConge]
                          $conges[$indiceConge][$keysIndiceConge[$indiceConge]] = (array) $conge;
                          
                          
                      }
                      
                      /*
                       * Tableau qui sera envoyé à la vue en format ensuite parsé en javascript
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
                    
                       // renvoie de la structure à la vue en format json .
                      $this->_helper->json($ressourcesArray);
                      
                      
                      
                      
                  } 
                  else if (isset($data['id_personne']) && $data['id_personne'] === 'x') // id personne non sélectionné toutes les ressources.
                  {	
                    $congeObj   = new Default_Model_Conge();
                    $congeArray = array();
                    
                    $congeArray = $congeObj->fetchAll($str = array());
                  
                    $i = 0;
                   
                   $resultCount = count($congeArray);
                    
                   if($resultCount == 0) // si aucun congé
                   {
                   	 $this->_helper->json(null);
                   	 return;
                   }
                    	  
                    foreach($congeArray as $v)
                    {
                 
                        $conge = array(); //réinitialisation de $conge (table temp)
                     
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
                           * récupéation du détail de la période de congé
                           * 
                           */
		                  $idTypeConge = $v->getId_type_conge();
		                  $typeConge = new Default_Model_TypeConge();
		                  $tc = $typeConge->find($idTypeConge);
		                  $codeTypeConge = $tc->getCode();
                          $conge = $outils->getPeriodeDetails($dd , $df,$codeTypeConge, $cs, false);
                          $conge['nombreJours'] = $v->getNombre_jours(); 
                           
                          // indice congé sous format : annee-mois
                          $indiceConge = explode("-", $dd);
                          $indiceConge = $indiceConge['0'] . '-' . $indiceConge['1']; // indice conge sous format Annee-mois
                         
                          // compter le nombre de congé posés séparemment sur un moi
                          if (isset($keysIndiceConge[$idPersonne]) && isset($keysIndiceConge[$idPersonne][$indiceConge])) {
                              $keysIndiceConge[$idPersonne][$indiceConge] = $keysIndiceConge[$idPersonne][$indiceConge] + 1;
                          } else {
                              $keysIndiceConge[$idPersonne][$indiceConge] = 0;
                              
                          }
                          
                          // stocker les congés dans la table sous l'indice [annee-mois][numConge]
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
                          
                         // structure envoyé au navigateur pour alimenter le calendrier
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
		             
		             $jferiesFR = $outils->setJoursFerie($data['annee'], true, false);
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
      
      public function calculNombreJoursCongeAction()
      {
          
          //création du fomulaire
          $form = new Default_Form_OutilsForm();
          //indique l'action qui va traiter le formulaire
          $form->setAction($this->view->url(array(
              'controller' => 'outils',
              'action' => 'calculNombreJoursConge'
          ), 'default', true));
          
          //assigne le formulaire à la vue
          $this->view->form  = $form;
          $this->view->title = "Créer un conge";
          /*************************************/
          $conge             = new Default_Model_Conge();
          
          // requete POST 
          if ($this->_request->isPost()) {
              
              
              // récupération des donnéees envoyées par le formulaire
              $data = $this->_request->getPost();
              
              
              // si date(s) non renseignée(s)
              if ($data['dateDebut'] == '' || $data['dateFin'] == '') {
                  if ($data['dateDebut'] == '') {
                      $this->view->error = 'saisissez la date de début !!';
                      $form->populate($data);
                  } else {
                      $this->view->error = 'saisissez la date de fin !!';
                      $form->populate($data);
                  }
              } else if ($data['dateDebut'] > $data['dateFin']) {
                  $this->view->error = 'date fin doit être supèrieure ou égale à date debut';
                  $form->populate($data);
              } else {
                  
                  $dateDebut      = $data['dateDebut'];
                  $dateFin        = $data['dateFin'];
                  $debutMidi      = $data['DebutMidi'];
                  $finMidi        = $data['FinMidi'];
                  $csm            = $data['csm'];
                  $am             = $data['AlsaceMoselle'];
                  
                  
                  $outilsb = new Default_Controller_Helpers_outilsb();
                  $dateTime = $outilsb->makeDatetime($dateDebut,$dateFin,$debutMidi,$finMidi);

                  $conge->setDate_debut($dateTime[0]);
                  $conge->setDate_fin($dateTime[1]);
                
                  
                  
                 
                  if ($csm == '0' && $am == '0') { //si CSM et Alsace Moselle non checkés
                      $conge->CalculNombreJoursConge();
                  } else {
                       //CSM checké
                      if ($csm == '1') {
                          $csm = true;
                      }
                       //Alsace Moselle checké
                      if ($am == '1') {
                          $am = true;
                      }
                      
                      $conge->CalculNombreJoursConge($csm, $am);
                  }
                  
                  
                  $form->populate($data);
              }
             
          }
         
      }
      
      
      
  }