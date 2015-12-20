<?php	
	if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

// Preserve the spaces at the beginnings and ends of strings.
// Variables will be inserted where indicated.

$and = ' y ';
$color_error = 'No es un valor de color hexadecimal válido';

$out_of_range_min_error = 'Fuera de rango. El minimo es ';//min
$out_of_range_max_error = 'Fuera de rango. El maximo es ';//max

$number_error = 'No un número';
$number_step_error = 'No un número válido. Los dos números válidos más cercanas son ';//lower and higher
$number_step_edge_error = 'No un número válido. El número válido más cercano es ';//lower

$date_error = 'No es una fecha válida.';
$date_step_error = 'No es una fecha válida. Las dos fechas válidas más cercanas son ';//lower and higher
$date_step_edge_error = 'No es una fecha válida. La fecha válida más cercana es ';//lower or higher

$time_error = 'No es un tiempo válido.';
$time_step_error = 'No es un tiempo válido. Los dos tiempos válidos cercanos son ';//lower and higher
$time_step_edge_error = 'No es un tiempo válido. El tiempo válido más cercano es ';//lower or higher