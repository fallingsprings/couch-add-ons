<?php	
	if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

// Preserve the spaces at the beginnings and ends of strings.
// Variables will be inserted where indicated.

$and = ' तथा ';
$color_error = 'अमान्य हेक्साडेसिमल रंग मूल्य।';

$out_of_range_min_error = 'क्षेत्र के बहार। न्यूनतम मूल्य है ';//min
$out_of_range_max_error = 'क्षेत्र के बहार। अधिकतम मूल्य है ';//max

$number_error = 'यह संख्या नहीं है';
$number_step_error = 'अमान्य संख्या। एकाधिक निकटतम वैध संख्या ';//lower and higher
$number_step_edge_error = 'अमान्य संख्या। निकटतम वैध संख्या हैं ';//lower

$date_error = 'अमान्य दिनांक।';
$date_step_error = 'अमान्य दिनांक। एकाधिक निकटतम वैध दिनांक ';//lower and higher
$date_step_edge_error = 'अमान्य दिनांक। निकटतम वैध दिनांक हैं ';//lower or higher

$time_error = 'अमान्य समय।';
$time_step_error = 'अमान्य समय। एकाधिक निकटतम वैध समय ';//lower and higher
$time_step_edge_error = 'अमान्य समय। निकटतम वैध समय हैं ';//lower or higher