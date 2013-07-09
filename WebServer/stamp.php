<?php
require_once "assets/inc/mysql_session.php";
// Set the enviroment variable for GD
putenv('GDFONTPATH=' . realpath('.'));
// Name the font to be used (note the lack of the .ttf extension)
$font = 'assets/inc/FreeSans.ttf';
/* Name of tabs 
 *   - passed with URL "tab=#" 
 *   - SQL default value is "News" 
 *   - SQL limit of 12 char
 */
$tab_list = array ( 's'=>'SpecNotice',
                    '0'=>'News',
                    '1'=>'MyPAVCS',
                    '2'=>'Courses',
                    '3'=>'Community',
                    '4'=>'Gallery',
                    '5'=>'Elluminate',
                    '100'=>'Footer');

$ipadd=cleanData($_SERVER['REMOTE_ADDR']);
$username=cleanData($_GET['user']);
$tab=$tab_list[$_GET['tab']];

$tstamp=date("Y/m/d G:i:s T");
$date=date("Y-m-d");

if (! $username) {
	$username="UNKNOWN";
}
if (! $ipadd) {
	$ipadd="UNKNOWN";
}

if (! $tab) {
	$tab="UNKNOWN";
}

if (! $error ) { // If there is no error from the DB connect, then continue
	$insert_qry = "INSERT INTO cur_month (`username`,`dstamp`,`ipaddress`,`tab`) VALUES ('%s','%s','%s','%s')";
	$qry = sprintf($insert_qry,$username,$date,$ipadd,$tab);
	if (! mysql_query( $qry )) {
		$error = mysql_error();
	}
}

if (! $error) { // So no errors anywhere, the attendance record must have been recorded
	$mes_line1 = $username.",";
	$mes_line2 = "Thank you for logging into Blackboard on ".date("F j, Y");
} elseif (preg_match("/^Duplicate entry/",$error)) { // if error starts with "Duplicate entry", we have no reason to show any text
	$mes_line1 = $username.",";
	$mes_line2 = "Thank you for logging into Blackboard on ".date("F j, Y");
	$file = fopen("logs/dups_$date.log", "a");
	$output = $tstamp." - ".$username ." (". $ipadd .") - had a duplicate hit on script.\n";
	fwrite($file,$output);
	fclose($file);
} else { // must have been some error
	$mes_line1 = "ERROR: Please contact Kevin";
	$mes_line2 = 'Please provide the date and the time of error: '.date("g:i a").')';
	$file = fopen("logs/error_$date.log", "a");
	$output = $tstamp." - ".$username." - experienced error: ".$error."\n";
	fwrite($file,$output);
	fclose($file);
}

$db->disconnect();
// Set up an expires header to make sure the images is not cached day to day
// Get date for "tomorrow" in the correct format.
// Then set the Expires to "tomorrow" at 5AM GMT
// This means it will expire at Minight our time, (or 1am during Daylight Savings)
$expires=date ("D, j M Y",strtotime("+1 day"));

// Now setup and serve the PNG file
header( "Content-type: image/png" );
header("Expires: ".$expires." 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Consitnuously modified
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // NO CACHE




if ($tab=="News") { // We are on the news tab, so show the "full size" graphic
	$my_img = imagecreatetruecolor(450, 50);
	$white = imagecolorallocate($my_img, 255, 255, 255);
	$black = imagecolorallocate($my_img, 0, 0, 0);
	$trans = imagecolorallocatealpha($my_img, 0, 0, 0, 127); //Transparent
	imagesavealpha($my_img, true); //This will make it transparent
	imagefill($my_img, 0, 0, $trans);

	imagettftext($my_img, 14, 0, 11, 21, $black, $font, $mes_line1);
	imagettftext($my_img, 12, 0, 11, 41, $black, $font, $mes_line2);
} else { // We are on one of the other tabs, so only show the "small size" graphic
	$my_img = imagecreatetruecolor(2, 2);
	$white = imagecolorallocate($my_img, 255, 255, 255);
	$black = imagecolorallocate($my_img, 0, 0, 0);
	$trans = imagecolorallocatealpha($my_img, 0, 0, 0, 127); //Transparent
	imagesavealpha($my_img, true); //This will make it transparent
	imagefill($my_img, 0, 0, $trans);

}

imagepng($my_img);
imagedestroy($my_img);
?>
