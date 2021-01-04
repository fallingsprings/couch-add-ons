<?php	
	if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

// Preserve the spaces at the beginnings and ends of strings.
// Variables will be inserted where indicated.

$and = ' и ';
$color_error = 'Некорректное значение цвета.';

$out_of_range_min_error = 'За границей допустимого. Минимальное значение ';//min
$out_of_range_max_error = 'За границей допустимого. Максимальное значение ';//max

$number_error = 'Не число';
$number_step_error = 'Неподходящее число. Два ближайших подходящих числа: ';//lower and higher
$number_step_edge_error = 'Неподходящее число. Ближайшее подходящее число: ';//lower

$date_error = 'Неверная дата.';
$date_step_error = 'Неверная дата. Две ближайшие подходящие даты: ';//lower and higher
$date_step_edge_error = 'Неверная дата. Ближайшая подходящая дата: ';//lower or higher

$time_error = 'Неверное время.';
$time_step_error = 'Неверное время. Два ближайших подходящих времени: ';//lower and higher
$time_step_edge_error = 'Неверное время. Ближайшее подходящее время: ';//lower or higher