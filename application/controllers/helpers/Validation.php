<?php
class Default_Controller_Helpers_Validation extends Zend_Controller_Action_Helper_Abstract
{
     public function verifierSolde ($id_personne,$debut_annee_reference, $fin_annee_reference,$annee_reference,$nombre_jours)
    {
       	$conge = new Default_Model_Conge();
       	$solde = new Default_Model_Solde();
       	$solde = $solde->find($id_personne,$annee_reference);
       	$solde = $solde->getTotal_cp()+$solde->getTotal_q1()+$solde->getTotal_q2();
       	$conge = $conge ->somme_solde_annuel_confe($id_personne, $debut_annee_reference, $fin_annee_reference);
       	if ($solde <=($conge[0]['sum(nombre_jours)']+$nombre_jours))
        {        		
       		return true;
       	}
       	else return false;
    }

        
	public function calendrier($tableau_id,$debut_mois,$fin_mois)
	{
		$month =$_SESSION['salut']['mois'];
		$fin_mois1=date('Y-m-d',mktime(0,0,0,$month+1,0,$_SESSION['salut']['annee']));
		$debut_mois1=date('Y-m-d',mktime(0,0,0,$month,1,$_SESSION['salut']['annee']));
		
		$conge = new Default_Model_Conge();  
		$propostion = new Default_Model_Proposition();
		$nb_jr_ouv_mois_fr = $conge->joursOuvresDuMois($debut_mois1,$fin_mois1);
		$nb_jr_ouv_mois_ma = $propostion->joursOuvresDuMois($debut_mois1,$fin_mois1);
		
		$typeconge = new Default_Model_TypeConge();
		$result_set_types = $typeconge->fetchAll($str=array());
		$tableau_types = array();
		foreach($result_set_types as $p)
		{
			$tableau_types[$p->getId()] = $p->getCode();
		}
		
		$personne = new Default_Model_Personne();
		$conge = new Default_Model_Conge();
		$tableau_doublon = array();
		$tableau_non_doublon = array();
		
		if (count($tableau_id))
		{
			 
			$doublont = $conge->DoublontAuNiveauPole( $tableau_id, $debut_mois,  $fin_mois);
			$nondoublont = $conge->CongesNondoublontPole( $tableau_id,$debut_mois,$fin_mois) ;
			for($i=0;$i<count($nondoublont );$i++)
			{
				$tableau_non_doublon[$i]= $nondoublont[$i]['id_personne'];
			}
			for($i=0;$i<count($doublont);$i++)
			{
				$tableau_doublon[$i]= $doublont[$i]['id_personne'];
				
			}
			
		}
		
		else 
		{
			$doublont = $conge->doublont($debut_mois,$fin_mois);
			$nondoublont = $conge->CongesNondoublont( $debut_mois,$fin_mois) ;
			for($i=0;$i<count($nondoublont );$i++)
			{
				$tableau_non_doublon[$i]= $nondoublont[$i]['id_personne'];
			}
			for($i=0;$i<count($doublont);$i++)
			{
				$tableau_doublon[$i]= $doublont[$i]['id_personne'];
				
			}
		
		}
		$calendrier = array();
		
		if (count($tableau_non_doublon))
		{
			
			for($j=0;$j<count($tableau_non_doublon);$j++)
			{
				
				if (!(in_array($tableau_non_doublon[$j],$tableau_doublon)))
				{
					$reponse2 = $personne->obtenirresources($tableau_non_doublon[$j],$debut_mois,$fin_mois);
				
						$t=0;
						$calendrier[$j][$t]['nom']=$reponse2[0]['nom'];
						$calendrier[$j][$t]['id']=$reponse2[0]['id'];
						$calendrier[$j][$t]['prenom']=$reponse2[0]['prenom'];
						$calendrier[$j][$t]['centre_service']=$personne->setEntite($reponse2[0]['id_entite'])->getEntite()->getCs();
						$calendrier[$j][$t]['date_debut']=new Zend_Date($reponse2[0]['date_debut']);
						$calendrier[$j][$t]['date_fin']=new Zend_Date($reponse2[0]['date_fin']);
						$calendrier[$j][$t]['nombre_jours']=$this->calculNombreJour(1,$calendrier[$j][$t]['date_debut'],$calendrier[$j][$t]['date_fin'],$personne->setEntite($reponse2[0]['id_entite'])->getEntite()->getCs(),$reponse2[0]['nombre_jours']);
						$calendrier[$j][$t]['id_type_conge']=$tableau_types[$reponse2[0]['id_type_conge']];
						$calendrier[$j][$t]['mi_debut_journee']=$reponse2[0]['mi_debut_journee'];
						$calendrier[$j][$t]['mi_fin_journee']=$reponse2[0]['mi_fin_journee'];
						
				}
				else 
				{
					$reponse2 = $personne->obtenirresources($tableau_non_doublon[$j],$debut_mois,$fin_mois);
					for ($i=0;$i<count($reponse2);$i++)
					{
						$sommejours =array();
						$totaljours =0;
						for ($l=0;$l<count($reponse2);$l++)
						{
							$date_debut = new Zend_Date($reponse2[$l]['date_debut']);
							$date_fin = new Zend_Date($reponse2[$l]['date_fin']);
							$somme[$l] = $this->calculNombreJour(0,$date_debut,$date_fin,$personne->setEntite($reponse2[$l]['id_entite'])->getEntite()->getCs(),$reponse2[$l]['nombre_jours']);
							$totaljours = $totaljours + $somme[$l];
								
						}
						
					 	
						if ($personne->setEntite($reponse2[$i]['id_entite'])->getEntite()->getCs() ==1) 
						{
							$totaljours = $totaljours -  $nb_jr_ouv_mois_ma;
						}
						elseif($personne->setEntite($reponse2[$i]['id_entite'])->getEntite()->getCs() ==0) 
						{
							$totaljours = $totaljours - $nb_jr_ouv_mois_fr; 
						}
						$calendrier[$j][$i]['nom']=$reponse2[$i]['nom'];
						$calendrier[$j][$i]['id']=$reponse2[$i]['id'];
						$calendrier[$j][$i]['prenom']=$reponse2[$i]['prenom'];
						$calendrier[$j][$i]['centre_service']=$personne->setEntite($reponse2[$i]['id_entite'])->getEntite()->getCs();
						$calendrier[$j][$i]['date_debut']=new Zend_Date($reponse2[$i]['date_debut']);
						$calendrier[$j][$i]['date_fin']=new Zend_Date($reponse2[$i]['date_fin']);
						$resu =$conge->RecupererLeNombreConge( $tableau_non_doublon[$j],$reponse2[$i]['date_debut']);
						
						
						$calendrier[$j][$i]['nombre_jours']=$totaljours;
						if (($reponse2[$i]['date_debut']==$reponse2[$i]['date_fin']) && (count($resu)==2) )
						{
							
								$type =array();
								$calendrier[$j][$i]['mi_fin_journee']=1;
								$calendrier[$j][$i]['mi_debut_journee']=1;
							
								for ($l=0;$l<count($resu);$l++)
								{
									$type[$l]=$resu[$l]['id_type_conge'];
								
									
										if ($reponse2[$i]['mi_debut_journee'] ==1)
										{
											$calendrier[$j][$i]['id_type_conge'] =$tableau_types[$reponse2[$i]['id_type_conge']];
											$calendrier[$j][$i]['id_type_conge2'] =$tableau_types[$type[$l]];
										}
										elseif($reponse2[$i]['mi_fin_journee'] ==1)
										{
											$calendrier[$j][$i]['id_type_conge'] =$tableau_types[$type[$l]];
											$calendrier[$j][$i]['id_type_conge2'] = $tableau_types[$reponse2[$i]['id_type_conge']] ;
										}
									
								}	
							
							
						}
						else
						{
						$calendrier[$j][$i]['id_type_conge']=$tableau_types[$reponse2[$i]['id_type_conge']];
						$calendrier[$j][$i]['mi_debut_journee']=$reponse2[$i]['mi_debut_journee'];
						$calendrier[$j][$i]['mi_fin_journee']=$reponse2[$i]['mi_fin_journee'];
						$calendrier[$j][$i]['id']=$reponse2[$i]['id'];
					
						}
					}
				}
			}
			return($calendrier) ;
		}
		
	}

	// PTRI - � supprimer
	/*
	 * calculNombreJour
	 * d�finition : calcul le nombre de jours de cong�s pour un ressource
	 * param�tres :
	 * $flag			: ???
	 * $date_debut		: date de d�but renseign�e dans le formulaire (jour inclus)
	 * $date_fin		: date de fin renseign�e dans le formulaire (jour inclus)
	 * $centre_cervice	: distinction cong� FRANCE ou MAROC
	 * $nombre_jours	: ???
	 */
	public function calculNombreJour($flag,$date_debut,$date_fin,$centre_cervice,$nombre_jours)
	{
		$month =$_SESSION['salut']['mois'];
		$fin_mois=date('Y-m-d',mktime(0,0,0,$month+1,0,$_SESSION['salut']['annee']));
		$debut_mois=date('Y-m-d',mktime(0,0,0,$month,1,$_SESSION['salut']['annee']));
		$conge = new Default_Model_Conge();
		$propostion = new Default_Model_Proposition();
		$nb_jr_ouv_mois_fr = $conge->joursOuvresDuMois($debut_mois,$fin_mois);
		$nb_jr_ouv_mois_ma = $propostion->joursOuvresDuMois($debut_mois,$fin_mois);
	
		// (date_fin->mois  > session.mois)  et (date_debut->mois  < session.mois)  
		if (($date_fin->get(Zend_Date::MONTH)>$_SESSION['salut']['mois']) &&($date_debut->get(Zend_Date::MONTH)<$_SESSION['salut']['mois']))
		{
			return 0;		
		}
		// Mohamed khalil TAKAFI 
		// (date_fin->mois  == session.mois)  et (date_debut->mois  == session.mois)  
		elseif((($date_fin->get(Zend_Date::MONTH)== $_SESSION['salut']['mois']) &&($date_debut->get(Zend_Date::MONTH)== $_SESSION['salut']['mois'])))	
		{
			if (($centre_cervice==1)) 
			{
				return 	$propostion->joursOuvresDuMois($debut_mois,$date_fin) - $nombre_jours  ; 
				
			}
			elseif (($centre_cervice==0)) 
			{
				return 	$conge->joursOuvresDuMois($debut_mois,$fin_mois) - $nombre_jours ; 
			}
		
		}
		// (date_fin->mois  == session.mois)  et (date_debut->mois < session.mois)  		
		elseif((($date_fin->get(Zend_Date::MONTH)== $_SESSION['salut']['mois']) &&($date_debut->get(Zend_Date::MONTH)< $_SESSION['salut']['mois'])))	
		{
			
			if (($centre_cervice==1)) 
			{
				return 	$nb_jr_ouv_mois_ma - $propostion->joursOuvresDuMois($debut_mois,$date_fin);	
			}
			elseif (($centre_cervice==0)) 
			{	
				return 	 $nb_jr_ouv_mois_fr - $conge->joursOuvresDuMois($debut_mois,$date_fin); 
				
			}
		
		}
		// (date_fin->mois  > session.mois)  et (date_debut->mois < session.mois) 
		elseif((($date_fin->get(Zend_Date::MONTH)> $_SESSION['salut']['mois']) &&($date_debut->get(Zend_Date::MONTH)== $_SESSION['salut']['mois'])))	
		{
			
			if (($centre_cervice==1)) 
			{
				
				return 	$nb_jr_ouv_mois_ma - $propostion->joursOuvresDuMois($date_debut,$fin_mois);
				
			}
			elseif (($centre_cervice==0)) 
			{
				
				return 	 $nb_jr_ouv_mois_fr - $conge->joursOuvresDuMois($date_debut,$fin_mois);
				
			}
		
		}
		
							
							
	}


	/*
	 * PTRI - Ajout des fonctions de calcul de jour de cong�s
	 */

	function dimanche_paques($annee)
	{
		return date("Y-m-d", easter_date($annee));
	}
	function vendredi_saint($annee)
	{
		$dimanche_paques = $this->dimanche_paques($annee);
		return date("Y-m-d", strtotime("$dimanche_paques -2 day"));
	}
	function lundi_paques($annee)
	{
		$dimanche_paques = $this->dimanche_paques($annee);
		return date("Y-m-d", strtotime("$dimanche_paques +1 day"));
	}
	function jeudi_ascension($annee)
	{
		$dimanche_paques = $this->dimanche_paques($annee);
		return date("Y-m-d", strtotime("$dimanche_paques +39 day"));
	}
	function lundi_pentecote($annee)
	{
		$dimanche_paques = $this->dimanche_paques($annee);
		return date("Y-m-d", strtotime("$dimanche_paques +50 day"));
	}

	function jours_feries_maroc($annee) 
	{
		//global $logger;
		////$logger->debug("appel en base");
        $jours_feries_csm_dates = null;
		$logger = new Zend_Log();
		$writer = new Zend_Log_Writer_Stream('php://output');
		$logger->addWriter($writer);

		$ferie = new Default_Model_Ferie();
		$jours_feries_csm = $ferie->fetchAll("annee_reference = '".$annee."'");
		foreach ($jours_feries_csm as $j) 
		{
			$jours_feries_csm_dates[] = $j->getDate_debut();
		}

		return $jours_feries_csm_dates;
	}

	function jours_feries($annee, $alsacemoselle=false, $maroc=false)
	{
		if (!$maroc) 
		{
			$jours_feries = array
			(    $this->dimanche_paques($annee)
			,    $this->lundi_paques($annee)
			,    $this->jeudi_ascension($annee)
			,    $this->lundi_pentecote($annee)

			,    "$annee-01-01"        //    Nouvel an
			,    "$annee-05-01"        //    F�te du travail
			,    "$annee-05-08"        //    Armistice 1945
			,    "$annee-08-15"        //    Assomption
			,    "$annee-07-14"        //    F�te nationale
			,    "$annee-11-11"        //    Armistice 1918
			,    "$annee-11-01"        //    Toussaint
			,    "$annee-12-25"        //    No�l
			);
			
			if($alsacemoselle)
			{
				$jours_feries[] = "$annee-12-26";
				$jours_feries[] = $this->vendredi_saint($annee);
			}
			sort($jours_feries);
			return $jours_feries;
		}
		else {
			return $this->jours_feries_maroc($annee);
		}
	}
	
	
	

	function est_ferie($jour, $alsacemoselle=false, $maroc=false)
	{
		$jour = date("Y-m-d", strtotime($jour));
		$annee = substr($jour, 0, 4);
		
		return in_array($jour, $this->jours_feries($annee, $alsacemoselle, $maroc));
	}

	
	
	
	
	// Indique si une date doit �tre normalis�e ou non
	function a_normaliser($date,$maroc=false) 
	{
		if (in_array(date_format($date, 'l'),array('Saturday','Sunday')) || $this->est_ferie(date_format($date, 'Y-m-d'),false,$maroc)) 
		{
			return true;
		}

		return false;
	}

	
	
	
	// On normalise un flag midi par rapport � une date non normalis�e
	function normaliser_flag_midi($date,$midi,$maroc=false) 
	{
	
		// Si la date de d�but ou fin cong� tombe un WE ou JF, le flag midi ne peut pas �tre actif
		if ($this->a_normaliser($date,$maroc)) 
		{
			$midi = false;
		}

		return $midi;
	}
	
	
	
	

	// Si la date de d�but de cong� tombe un WE ou JF, on l'avance au 1er JO
	function normaliser_date_debut_conge($date,$maroc=false) 
	{
		while (in_array(date_format($date, 'l'),array('Saturday','Sunday')) || $this->est_ferie(date_format($date,'Y-m-d'),false,$maroc)) 
		{
			$date->add(new DateInterval("P1D"));
		}

		return $date;
	}
	
	
	
	
	

	// Si la date de fin de cong� tombe un WE ou JF, on la retarde au dernier JO
	function normaliser_date_fin_conge($date,$maroc=false) 
	{
	
		while (in_array(date_format($date, 'l'),array('Saturday','Sunday')) || $this->est_ferie(date_format($date, 'Y-m-d'),false,$maroc)) 
		{
			$date->sub(new DateInterval("P1D"));
		}
		
		return $date;
	}


	function calculer_jours_ouvres($date_debut,$date_fin) 
	{
		
		$nombre_jours_ouvres = 0;

		// Parcourir l'intervalle
		$date_iterator = $date_debut;
		while ($date_iterator <= $date_fin) 
		{
			// Loguer les jours ouvr�s (tous les jours sauf les samedi, dimanche, f�ri�s)
			$weekday = date_format($date_iterator, 'l');
			if (!in_array($weekday,array('Saturday','Sunday'))  && !$this->est_ferie(date_format($date_iterator, 'Y-m-d'),false,false)) {
		 		$nombre_jours_ouvres++;
		 	}

			// Incr�menter l'iterator
			$date_iterator->add(new DateInterval("P1D"));
		}

		return $nombre_jours_ouvres;
	}

	/*
	 * PTRI - FIN des fonctions de calcul de nombre de jours de cong�s
	 */

	

	
	

	
	
	

/*	
	public function periode($date_debut,$date_fin,$maroc) 
	{  
		$dd = new DateTime($date_debut);  // date_debut xxxx-xx-xx xx:xx:xx
		$df = new DateTime($date_fin);    // date_fin   xxxx-xx-xx xx:xx:xx
		
		$dt = substr($dd->format("Y-m-d H:i:s"),11,18);  // time debut  xx:xx:xx
		$ft = substr($df->format("Y-m-d H:i:s"),11,18);  // time fin  xx:xx:xx

		$i  = 0;
	   if($dd < $df )
	   { 	   	
		    	$tab[$i] = $dd->format("Y-m-d");
		    	$sab[$i] = "O0:00:00";
		        $i = 1;

		        while($dd  < $df )
		      { 
		        $s = $dd->add(new DateInterval('P1D'));
		        $tab[$i] = $s->format("Y-m-d");
		        $sab[$i] = "O";
		        
		        $i++;		
		      }
	   }
	   else 
	   {
	      return null;
	   }

	
	 	$rr =   array_combine($tab,$sab);
	    $rr[$tab[0]] = $dt;
	    $rr[$tab[count($rr) - 1]] = $ft;
	   
   return $rr;
}
*/
	
	
	
	
	
	
	
	
	/*
	 * PTRI - Calculer les droits � cong�s d'une ressource
	 */
	function calculer_droits_a_conges($ressource,$annee_reference) 
	{
		/*
		 * si annee_entree = annee_reference
		 * 	si date_entree < 1er juin : cp = nb_mois depuis date_entr�e * 2.25
		 * 	sinon cp = 0
		 * sinon si annee_entree = annee_reference - 1
		 * 	si date_entree < 1er juin : cp = 27
		 * 	sinon cp = nb_mois depuis date_entr�e * 2.25
		 * sinon cp = 27 + anciennete
		 * 
		 * anciennet�($annee_reference)
		 * 	switch 01/06/annee_reference - date_entree
		 * 		case 2<=n<3 : anciennete = 1
		 * 		case 3<=n<5 : anciennete = 2
		 * 		case 5<=n<8 : anciennete = 3
		 * 		case n>=8 : anciennete = 4
		 * 	
		 * rtt d�pend de la modalite
		 * 
		 * temps partiel (pourcentage)
		 * 
		 * Q2 : initialis� � l'�cran de gestion des soldes
		 * 
		 */
		
		$logger = new Zend_Log();
		$writer = new Zend_Log_Writer_Stream('php://output');
		$logger->addWriter($writer);
		
		$cp = 0;
		$cpa = 0;
		$q1 = 0;
		
		$date_entree = new DateTime($ressource->getDate_entree());
		
		$annee_entree = date_format($date_entree, 'Y'); // annee au format 2013
		$mois_entree = date_format($date_entree, 'n'); // mois au format 1 � 12
		$jour_entree = date_format($date_entree, 'j'); // mois au format 1 � 31

		// Calcul des CP
		if ($annee_entree == $annee_reference) 
		{
			if ($mois_entree < 6) 
			{
				$cp = 2.25 * (6 - $mois_entree);
				
				if ($jour_entree >= 15) 
				{
					$cp -= 2.25;
				}
			}
			else 
			{
				$cp = 0;
			}
		}
		elseif ($annee_entree == $annee_reference - 1) 
		{
			if ($mois_entree < 6) 
			{
				$cp = 27;
			}
			else 
			{
				$cp = 2.25 * (5 + 12 - $mois_entree + 1);
				if ($jour_entree >= 15) 
				{
					$cp -= 2.25;
				}
			}
		}
		else 
		{
			$cp = 27;
		}
		
		// Calcul des CP Anciennet�
		$annee_reference = new DateTime($annee_reference.'-06-01');
		echo date_format($annee_reference, 'd-m-Y').'<BR>';
		$interval = $date_entree->diff($annee_reference);
		$i = $interval->format('%y');
		if ($i >= 2 && $i < 3) 
		{
			$cpa = 1;
		}
		elseif ($i >= 3 && $i < 5) 
		{
			$cpa = 2;
		}
		elseif ($i >= 5 && $i < 8) 
		{
			$cpa = 3;
		}
		elseif ($i >= 8) 
		{
			$cpa = 4;
		}
				
		// Calcul des RTT Q1
		$annee_reference = date_format($annee_reference, 'Y');
		$debut_annee = new DateTime($annee_reference.'-01-01');
		$fin_annee = new DateTime($annee_reference.'-12-31');
		$nb_jo = $this->calculer_jours_ouvres($debut_annee,$fin_annee);
		
		$nb_rtt_ms = 7.4 *($nb_jo-25-12) + 7 > 1607 ? 13 : 12;
		$nb_rtt_rm_ac = $nb_jo-25-218 < 10 ? 10 : $nb_jo-25-218;
		
		$modalite = new Default_Model_Modalite();
		$modalite = $modalite->find($ressource->getId_modalite());
		$modalite = $modalite->getCode();
		
		if ($modalite == "MS") 
		{
			$q1 = 7.4 * ($nb_jo-25-12) + 7  > 1607 ? 13 : 12;
		}
		elseif ($modalite == "RM" || $modalite == "AC") 
		{
			$q1 = $nb_jo-25-218 < 10 ? 10 : $nb_jo-25-218;
		}
		elseif ($modalite == "NO") 
		{
			$q1 = 0;
		}
		else 
		{
			$q1 = 10;
		}
	
		// Pour les nouveaux entrants, appliquer un prorata
		if ($annee_entree == $annee_reference) 
		{
			$nb_mois_complets = 12 - $mois_entree + 1;
			
			if ($jour_entree >= 15) 
			{
				$nb_mois_complets -= 1;
			}
			
			$q1 = round($q1 * $nb_mois_complets / 12, 0, PHP_ROUND_HALF_DOWN); 
		}
		
		// ratio temps partiels
		$ressource->getPourcent();
		$cp = round($cp * $ressource->getPourcent() / 100, 0, PHP_ROUND_HALF_DOWN);
		$cpa = round($cpa * $ressource->getPourcent() / 100, 0, PHP_ROUND_HALF_DOWN);
		$q1 = round($q1 * $ressource->getPourcent() / 100, 0, PHP_ROUND_HALF_DOWN);
		
		//	$logger->log($jours_feries_csm[0]->getDate_debut(), Zend_Log::INFO);
		return array("CP" => $cp,"CPA" => $cpa,"Q1" => $q1);
	}


}
