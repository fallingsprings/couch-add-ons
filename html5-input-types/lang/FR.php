<?php	
	if ( !defined("K_COUCH_DIR") ) die(); // cannot be loaded directly

// Preserve the spaces at the beginnings and ends of strings.
// Variables will be inserted where indicated.

$and = " et ";
$color_error = "Pas une code couleur hexadécimale valide.";

$out_of_range_min_error = "Hors de la plage. Valeur minimale est ";//min
$out_of_range_max_error = "Hors de la plage. Valeur maximale est ";//max

$number_error = "Pas un nombre";
$number_step_error = "Pas un nombre valide. Les numéros valides les plus proches sont ";//lower and higher
$number_step_edge_error = "Pas un nombre valide. Le numéro valide le plus proche est ";//lower

$date_error = "Pas un date valide.";
$date_step_error = "Pas un date valide. Les dates valides les plus proches sont ";//lower and higher
$date_step_edge_error = 'Pas un date valide. Le date valide le plus proche est ';//lower or higher

$time_error = "Pas un temps valide.";
$time_step_error = "Pas un temps valide. Les temps valides les plus proches sont ";//lower and higher
$time_step_edge_error = "Pas un temps valide. Le temps valide la plus proche est ";//lower or higher