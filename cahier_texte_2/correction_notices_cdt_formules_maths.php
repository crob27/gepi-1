<?php
/*
*
* Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* GEPI is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with GEPI; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// Initialisations files
require_once("../lib/initialisations.inc.php");
//require_once("../lib/transform_functions.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_2/correction_notices_cdt_formules_maths.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_texte_2/correction_notices_cdt_formules_maths.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Correction des notices CDT',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (!acces_cdt()) {
	die("Le module n'est pas activé.");
}

$step=isset($_POST['step']) ? $_POST['step'] : (isset($_GET['step']) ? $_GET['step'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

$telecharger_et_corriger=isset($_POST['telecharger_et_corriger']) ? $_POST['telecharger_et_corriger'] : (isset($_GET['telecharger_et_corriger']) ? $_GET['telecharger_et_corriger'] : NULL);

//**************** EN-TETE *****************
$titre_page = "Cahier de textes - Notices avec images math";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();

$eff_parcours=5;

echo "<p class='bold'><a href='";
echo "../cahier_texte_admin/index.php";
echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

echo "<p>Pendant un temps, la génération d'images de formule mathématiques dans le CDT2 fonctionnait en dynamique (<em>sans téléchargement des images dans l'arborescence des documents Gepi</em>).<br />Cela ne permet pas une consultation hors ligne d'un Export CDT et cela surcharge inutilement le serveur générant les images.</p>\n";
echo "<br />\n";

$sql="SELECT * FROM ct_entry WHERE contenu LIKE '%src=\"http://latex.codecogs.com/%' OR contenu LIKE '%src=\"https://latex.codecogs.com/%';";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_ct_entry=mysqli_num_rows($res);

$sql="SELECT * FROM ct_devoirs_entry WHERE contenu LIKE '%src=\"http://latex.codecogs.com/%' OR contenu LIKE '%src=\"https://latex.codecogs.com/%';";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_ct_devoirs_entry=mysqli_num_rows($res);

if(!isset($telecharger_et_corriger)) {
	if(($nb_ct_entry>0)||($nb_ct_devoirs_entry>0)) {
		echo "<p><strong>$nb_ct_entry</strong> compte-rendus et <strong>$nb_ct_devoirs_entry</strong> notices de devoirs comportent des images pointant vers <a href='http://latex.codecogs.com/'>http://latex.codecogs.com/</a> ou <a href='https://latex.codecogs.com/'>https://latex.codecogs.com/</a></p>\n";
		echo "<p><a href='".$_SERVER['PHP_SELF']."?telecharger_et_corriger=y".add_token_in_url()."'>Procéder à la correction</a></p>\n";
		echo "<p>Les notices vont être parcourues par tranches de $eff_parcours.</p>\n";
	}
	else {
		echo "<p>Aucune image ne pointe vers http://latex.codecogs.com/ ni https://latex.codecogs.com/ <br />Aucune correction n'est nécessaire.</p>\n";
	}
}
else {
	check_token(false);

	correction_notices_cdt_formules_maths($eff_parcours);

	$sql="SELECT * FROM ct_entry WHERE contenu LIKE '%src=\"http://latex.codecogs.com/%' OR contenu LIKE '%src=\"https://latex.codecogs.com/%';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_ct_entry=mysqli_num_rows($res);

	$sql="SELECT * FROM ct_devoirs_entry WHERE contenu LIKE '%src=\"http://latex.codecogs.com/%' OR contenu LIKE '%src=\"https://latex.codecogs.com/%';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_ct_devoirs_entry=mysqli_num_rows($res);

	if(($nb_ct_entry>0)||($nb_ct_devoirs_entry>0)) {
		echo "<form action='".$_SERVER['PHP_SELF']."' id='form1' method='post'>\n";
		echo "<p>Il reste $nb_ct_entry compte-rendus et $nb_ct_devoirs_entry notices de devoirs à traiter.</p>\n";
		echo add_token_field();
		echo "<input type='hidden' name='telecharger_et_corriger' value='y' />\n";
		echo "<input type='submit' value='Suite' />\n";
		echo "</form>\n";

		echo "<script type='text/javascript'>
	setTimeout(document.forms['form1'].submit(), 7000);
</script>\n";
	}
	else {
		echo "<br />\n";
		echo "<p>Téléchargements et corrections terminés.</p>\n";
	}
}

require("../lib/footer.inc.php");
die();

?>
