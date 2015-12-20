<?php	
	if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

// Preserve the spaces at the beginnings and ends of strings.
// Variables will be inserted where indicated.

$and = ' and ';
$color_error = 'Not a valid hexadecimal color value.';

$out_of_range_min_error = 'Out of range. Minimum value is ';//min
$out_of_range_max_error = 'Out of range. Maximum value is ';//max

$number_error = 'Not a number';
$number_step_error = 'Not a valid number. The two nearest valid numbers are ';//lower and higher
$number_step_edge_error = 'Not a valid number. The nearest valid number is ';//lower

$date_error = 'Not a valid date.';
$date_step_error = 'Not a valid date. The two nearest valid dates are ';//lower and higher
$date_step_edge_error = 'Not a valid date. The nearest valid date is ';//lower or higher

$time_error = 'Not a valid time.';
$time_step_error = 'Not a valid time. The two nearest valid times are ';//lower and higher
$time_step_edge_error = 'Not a valid time. The nearest valid time is ';//lower or higher