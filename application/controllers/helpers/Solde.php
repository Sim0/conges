<?php
class Default_Controller_Helpers_Solde extends Zend_Controller_Action_Helper_Abstract
{
	/*
	 * R�gle de gestion des soldes et droits � cong�s :
	 * 
	 * Droits : g�r�s uniquement pr les ressources FR
	 * - non g�r� : les 24 jours de cong�s annuels CSM (reliquat max = 10)
	 * - non g�r� : ressource FR changeant de modalit� en cours d'ann�e
	 * 
	 * Soldes pour l'ann�e de r�f�rence n :
	 * - acquis = droits ann�e r�f�rence
	 * - reliquat = droits ann�e n-1, disponible uniquement en janvier
	 * - consomm� = somme nombre de jours pos�s jusqu'� la date du jour hors cong�s type pr�visionnel
	 * => pas de gestion du bool�en ferme
	 * - consomm� pr�visionnel = somme nombre de jours pos�s 
	 * - solde = acquis - consomm�
	 * - solde pr�visionnel = acquis - consomm� pr�visionnel
	 */

	/*
	 * PTRI - Calculer le solde � partir de droits et de consomm�
	 * 
	 * @param $droits 
	 * @param $consomme
	 * @return tableau solde CP Q1 Q2
	 */
	function calculer_solde($ressource,$annee_reference,$droits=false,$consomme=false,$ferme=true) 
	{
		
		$logger = new Zend_Log();
		$writer = new Zend_Log_Writer_Stream('php://output');
		$logger->addWriter($writer);
	
		if (!$droits) {
			if (!$ressource) {
				throw new Exception("Droits et ressources non renseign�s");
			}
			else {
				// Calculer les droits de la personne
				$solde = new Default_Model_Solde();
				$solde = $solde->find($annee_reference-1,$ressource->getId());
				
				// Si le solde de la personne recherch�e n'existe pas...
				if ($solde->getPersonne()->id != null) {
					$droits['CP'] = $solde->getTotal_cp();
					$droits['Q1'] = $solde->getTotal_q1();
					$droits['Q2'] = $solde->getTotal_q2();
				}
				// Alors on le calcule ici
				else {
					$solde = $this->calculer_droits_a_conges($ressource,$annee_reference-1);
					$droits['CP'] = $solde['CP']+$solde['CPA'];
					$droits['Q1'] = $solde['Q1'];
					$droits['Q2'] = 0;
				}
			}
		}
	
		if (!$consomme) {
			if (!$ressource) {
				throw new Exception("Consomm� et ressources non renseign�s");
			}
			else {
				$consomme = $this->calculer_consomme($ressource,$annee_reference,$ferme);
			}
		}
		
		return array("CP" => $droits['CP']-$consomme['CP'],"Q1" => $droits['Q1']-$consomme['Q1'],"Q2" => $droits['Q2']-$consomme['Q2']);
	}

	/*
	 * PTRI - Calculer le consomm� d'une ressource sur une ann�e r�f�rence 
	 * 
	 * @param $ressource id personne
	 * @param $annee_reference en 2013, calcul du consomm� entre le 01/01/13 et 31/01/14
	 * @return tableau consomm� CP Q1 Q2
	 */
	function calculer_consomme($ressource,$annee_reference,$ferme=true) 
	{
	
		$logger = new Zend_Log();
		$writer = new Zend_Log_Writer_Stream('php://output');
		$logger->addWriter($writer);

		// Compter les CP Q1 pos�s sur l'ann�e n-1 : du 1/1/n-1 au 31/1/n
		$conge = new Default_Model_Conge();
		$date_debut = ($annee_reference - 1).'-01-01';
		$date_fin = $annee_reference.'-12-31';
		
//		var_dump($date_debut,$date_fin);
		
		$resultat = $conge->somme_conges($ressource->getId(),$annee_reference,$date_debut,$date_fin,$ferme);
		
		$conso_cp = isset($resultat[0]['somme'])?$resultat[0]['somme']:0;
		$conso_q1 = isset($resultat[1]['somme'])?$resultat[1]['somme']:0;
		$conso_q2 = isset($resultat[2]['somme'])?$resultat[2]['somme']:0;
		
		return array("CP" => $conso_cp,"Q1" => $conso_q1,"Q2" => $conso_q2);
		
	}
	
	/*
	 * PTRI - Calculer le reliquat
	 * Droits ann�e n-1 - consomm�e de l'ann�e de r�f�rence n-1
	 * Pas de reliquats pour les Q2
	 * 
	 * @param $ressource id personne
	 * @param $annee_reference en 2013, calcul du reliquat 2012 � poser entre le 01/01/13 et 31/01/13
	 */
	function calculer_reliquat($ressource,$annee_reference,$ferme=true) 
	{
		
		$logger = new Zend_Log();
		$writer = new Zend_Log_Writer_Stream('php://output');
		$logger->addWriter($writer);

		// D�terminer le solde de l'ann�e de r�f�rence n-1
		$solde = new Default_Model_Solde();
		
		$solde = $solde->find($annee_reference-1,$ressource->getId());
		
		// Si le solde de la personne recherch�e n'existe pas...
		if ($solde->getPersonne()->getId() != null) {
			$solde_cp = $solde->getTotal_cp();
			$solde_q1 = $solde->getTotal_q1();
		}
		// Alors on le calcule ici
		else {
			$solde = array();
			$solde = $this->calculer_droits_a_conges($ressource,$annee_reference-1);
			$solde_cp = $solde['CP']+$solde['CPA'];
			$solde_q1 = $solde['Q1'];
		}
		
		// Compter les CP Q1 pos�s sur l'ann�e n-1 : du 1/1/n-1 au 31/1/n
		$conge = new Default_Model_Conge();
		$date_debut = ($annee_reference-1).'-01-01';
		
		$date_fin = $annee_reference.'-12-31';
//		var_dump($date_debut,$date_fin);
		
		$resultat = $conge->somme_conges($ressource->getId(),$annee_reference-1,$date_debut,$date_fin,$ferme);
		
		$conso_cp = isset($resultat[0]['somme'])?$resultat[0]['somme']:0;
		$conso_q1 = isset($resultat[1]['somme'])?$resultat[1]['somme']:0;
		
		$reliquat_cp = $solde_cp - $conso_cp;
		$reliquat_q1 = $solde_q1 - $conso_q1;
		
		return array("CP" => $reliquat_cp,"Q1" => $reliquat_q1);
	}
	
	/*
	 * PTRI - Calculer les droits � cong�s d'une ressource
	 */
	public function calculer_droits_a_conges($ressource,$annee_reference) 
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
		if ($annee_entree == $annee_reference) {
			if ($mois_entree < 6) {
				$cp = 2.25 * (6 - $mois_entree);
				if ($jour_entree >= 15) {
					$cp -= 2.25;
				}
			}
			else {
				$cp = 0;
			}
		}
		elseif ($annee_entree == $annee_reference - 1) {
			if ($mois_entree < 6) {
				$cp = 27;
			}
			else {
				$cp = 2.25 * (5 + 12 - $mois_entree + 1);
				if ($jour_entree >= 15) {
					$cp -= 2.25;
				}
			}
		}
		else {
			$cp = 27;
		}
		
		// Calcul des CP Anciennet�
		$annee_reference = new DateTime($annee_reference.'-06-01');
		// echo date_format($annee_reference, 'd-m-Y').'<BR>';
		$interval = $date_entree->diff($annee_reference);
		$i = $interval->format('%y');
		if ($i >= 2 && $i < 3) {
			$cpa = 1;
		}
		elseif ($i >= 3 && $i < 5) {
			$cpa = 2;
		}
		elseif ($i >= 5 && $i < 8) {
			$cpa = 3;
		}
		elseif ($i >= 8) {
			$cpa = 4;
		}
				
		// Calcul des RTT Q1
		$annee_reference = date_format($annee_reference, 'Y');
		$debut_annee = new DateTime($annee_reference.'-01-01');
		$fin_annee = new DateTime($annee_reference.'-12-31');
		
		$validation = new Default_Controller_Helpers_Validation();
		$nb_jo = $validation->calculer_jours_ouvres($debut_annee,$fin_annee);
		
		$nb_rtt_ms = 7.4*($nb_jo-25-12)+7>1607 ? 13 : 12;
		$nb_rtt_rm_ac = $nb_jo-25-218<10 ? 10 : $nb_jo-25-218;
		
		$modalite = new Default_Model_Modalite();
		$modalite = $modalite->find($ressource->getModalite()->getId());
		$modalite = $modalite->getCode();
		
		if ($modalite == "MS") {
			$q1 = 7.4*($nb_jo-25-12)+7>1607 ? 13 : 12;
		}
		elseif ($modalite == "RM" || $modalite == "AC") {
			$q1 = $nb_jo-25-218<10 ? 10 : $nb_jo-25-218;
		}
		elseif ($modalite == "NO") {
			$q1 = 0;
		}
		else {
			$q1 = 10;
		}
	
		// Pour les nouveaux entrants, appliquer un prorata
		if ($annee_entree == $annee_reference) {
			$nb_mois_complets = 12 - $mois_entree + 1;
			if ($jour_entree >= 15) {
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
