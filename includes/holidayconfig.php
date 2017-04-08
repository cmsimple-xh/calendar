<?php
//=====================================================
//
// holidaywizard for Calendar plugin for CMSimple
// Aug 2012 by svasti
//
//=====================================================

// Security check
if ((!function_exists('sv')))die('Access denied');


global  $pth,$plugin,$plugin_tx,$tx,$sl;
$o      = '';

// getting the button-images
$imageFolder = $pth['folder']['plugins'] . $plugin . "/images";


$holidaylist         = isset($_POST['holidaylist'])       ? $_POST['holidaylist']     : '';
$holiday_add         = isset($_POST['holiday_add'])       ? $_POST['holiday_add']     : array();
$holiday_up          = isset($_POST['holiday_up'])        ? $_POST['holiday_up']      : array();
$holiday_name        = isset($_POST['holiday_name'])      ? $_POST['holiday_name']    : array();
$holiday_date        = isset($_POST['holiday_date'])      ? $_POST['holiday_date']    : array();
$holiday_delete      = isset($_POST['holiday_delete'])    ? $_POST['holiday_delete']  : array();

if ($holidaylist){
    foreach ($holiday_name as $key=>$value) {
    	if(!isset($holiday_delete[$key])) {
            $newkey = $key;
            if(isset($holiday_up[$key+1])) $newkey = $key + 1;
            if(isset($holiday_up[$key])) $newkey = $key - 1;
            $holidayarray[$newkey] = $holiday_date[$key] . ',' .$holiday_name[$key];
        }
    }
    if(isset($holiday_add[0])) $holidayarray[] = ',';
    ksort($holidayarray);
    $holidays = implode(';',$holidayarray);

    $languagefile = file_get_contents($pth['folder']['plugins'] . $plugin . "/languages/$sl.php");

    // create language file entries if not there
    if(!preg_match('!holydays\'\]="(.*)";!' ,$languagefile)) {
        $languagefile = str_replace('?>',"\t".'$plugin_tx[\'calendar\'][\'holydays\']="";'."\n?>" ,$languagefile);
    }  

    $pattern = '!holydays\'\]="(.*)";!';
    $replacement = "holydays']=\"$holidays\";";
    $languagefile = preg_replace($pattern,$replacement,$languagefile);

    file_put_contents($pth['folder']['plugins'] . $plugin . "/languages/$sl.php",$languagefile);
    include ($pth['folder']['plugins'] . $plugin ."/languages/$sl.php");

}

$o .= "<form method='POST' action=''>";
$o .= '<table style="width:100%;" cellpadding="1" cellspacing="3">';
// headline
$o .= '<tr><td style="width:16px;"></td><th>'
   .  $plugin_tx['calendar']['holiday_name']
   .  '</th><th>'
   .  $plugin_tx['calendar']['holiday_date_calculation']
   .  '</th>';

$o .= '<td style="width:16px;">'
   .  tag("input type='image' src='".$imageFolder
   .  "/add.png' style='width:16;height:16' name='holiday_add[0]' value=TRUE alt='+'")
   .  "</td></tr>\n";

$holidayarray = explode(';',$plugin_tx['calendar']['holydays']);

foreach ($holidayarray as $key=>$value) {
    list($holiday_date,$holiday_name) = explode(",",$value);

    $o .= "<tr>\n"

       // up-button
       .  '<td>' . tag("input type='image' src='" . $imageFolder
       .  "/up.gif' style='width:16;height:16' name='holiday_up[$key]' value=TRUE alt='^'")
       .  '</td>'

       // Holiday name
       .  '<td>' . tag("input type='text' style='width: 96%;' value='".$holiday_name."' name='holiday_name[$key]'") . '</td>'
       // Holiday date calculation
       .  '<td>' . tag("input type='text' style='width: 96%;' value='".$holiday_date."' name='holiday_date[$key]'") . '</td>'

       // delete button
       .  '<td>' . tag("input type='image' src='" . $imageFolder
       .  "/delete.png' style='width:16;height:16' name='holiday_delete[$key]' value='delete' alt='-'")
       .  '</td>'

       // end of line
       .  "</tr>\n";

}

$o .= "</table>\n"
   .  tag("input type='hidden' value=TRUE name='holidaylist'")
   .  tag('input type="submit" class="submit" value="'.ucfirst($tx['action']['save']).'"')
   .  "</form>\n";

$o .= '<h4>'.$plugin_tx['calendar']['holiday_date_calculation'].'</h4>'
   .  $plugin_tx['calendar']['help_holiday_date_calculation'];


?>
