<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
// BDD CONNEXION

$bdd = new PDO("mysql:host=localhost;dbname=boutique;", 'root', 'root');
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$bdd->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");

// SESSION
session_start();

//CONST path
define("ROOT", $_SERVER['DOCUMENT_ROOT'] . '/09-BOUTIQUE/');

// $_SERVER['DOCUMENT_ROOT'] --> c://wamp64/www
// echo ROOT . "<hr>";  // C:/wamp64/www/PHP/09-BOUTIQUE

// Cette constante retourne le chemin physique du dossier 09-BOUTIQUE sur le serveur local wamp64.
// Lors de l'enregistrement d'une image/photo, nous aurons du chemin physique complet vers le dossier photo sur le serveur pour enregistrer la photo dans le bon dossier
// On appel$_SERVER['DOCUMENT_ROOT'] parce que chaque serveur possède des chemins différents

define('URL', 'http://localhost/09-BOUTIQUE/');
// Cette url servira à enregistrer l'URL d'une image/photo dans la BDD

// INCLUSION
// En appellant init.inc dans chaque fichier, nous incluons en même temps les fonctions déclarées

require_once 'functions.inc.php';
