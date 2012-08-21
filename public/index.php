<?php
//d�finition de la constante 'APPLICATION_PATH'
defined('APPLICATION_PATH')
	|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

//d�finition de la constante 'APPLICATION_ENV'
defined('APPLICATION_ENV')
	|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

	// mise en place des r�pertoires et chargement des classes
set_include_path('.'
. PATH_SEPARATOR . './../library'
. PATH_SEPARATOR . './../application/models/'
. PATH_SEPARATOR . get_include_path());

require_once 'Zend/Application.php';

//cr�ation d'un objet Zend_Application qui est la classe de base et le point
//d'entr�e de l'application
//le constructeur contient deux arguments:
//   - argument 1: repr�sente l'environnement actuel dans lequel tourne
//                 l'application
//   - argument 2: chemin vers un fichier Zend_Config � charger pour la
//                 configuration de l'application, l'argument 1 d�termine la
//                 section de configuration � charger depuis le fichier
$application = new Zend_Application(
		APPLICATION_ENV,
		APPLICATION_PATH . '/configs/application.ini');

//appel de la m�thode bootstrap() du bootstrap pour lancer l'application
//toutes les m�thodes du bootstrap pr�fix�es par '_init' sont appel�es
//(cf. application/Bootstrap.php)
$application->bootstrap();

Zend_Controller_Front::getInstance()->throwExceptions(true);

//appel de la m�thode run() du bootstrap pour lancer le dispatch de
//l'application
$application->run();

