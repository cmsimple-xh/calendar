<?php
//=====================================================
//
// function for configuration of Calendar marquee
// Feb 2012 by svasti
//
//=====================================================
// Security check
if ((!function_exists('sv')))die('Access denied');


global  $pth,$plugin,$plugin_tx,$calendar_cf,$cf,$tx,$sl,$hjs;
$o      = '';
$notice = '';
$error  = '';

//reading the config2 file again is necessary in case preset changed something
include($pth['folder']['plugins'] . $plugin .'/config/config2.php');

$marquee_config          = isset($_POST['marquee_config'])          ? $_POST['marquee_config']          : '';
$marquee_number          = isset($_POST['marquee_number'])          ? $_POST['marquee_number']          : '';
$marquee_speed           = isset($_POST['marquee_speed'])           ? $_POST['marquee_speed']           : '';
$marquee_height          = isset($_POST['marquee_height'])          ? $_POST['marquee_height']          : '';
$marquee_headline_height = isset($_POST['marquee_headline_height']) ? $_POST['marquee_headline_height'] : '';
$marquee_height_unit     = isset($_POST['marquee_height_unit'])     ? $_POST['marquee_height_unit']     : '';
$marquee_1stline         = isset($_POST['marquee_1stline'])         ? $_POST['marquee_1stline']         : '';
$marquee_2ndline         = isset($_POST['marquee_2ndline'])         ? $_POST['marquee_2ndline']         : '';
$marquee_age             = isset($_POST['marquee_age'])             ? $_POST['marquee_age']             : '';

//======================================================
// writing the changed values into the repective files
//======================================================

if ($marquee_config == "marquee_config"){

    // needs to be processed here, as no value means no $_POST, although '' is needed
    $marquee_jquery  = isset($_POST['marquee_jquery']) ? $_POST['marquee_jquery'] : '';

    // configfile
    //=============
    $configfile = file_get_contents($pth['folder']['plugins'] . $plugin . '/config/config2.php');
    // change the values
    $configfile = preg_replace(
        array(
            "!marquee_in_jquery'.*\"(.*)\"!"  ,
            "!marquee_number_of_events_shown'.*\"(.*)\"!"  ,
            "!marquee_speed'.*\"(.*)\"!"  ,
            "!marquee_height'.*\"(.*)\"!"  ,
            "!marquee_headline_height'.*\"(.*)\"!"  ,
            "!marquee_height_unit'.*\"(.*)\"!"  ,
            "!marquee_1stline'.*\"(.*)\"!"  ,
            "!marquee_2ndline'.*\"(.*)\"!"  ,
            "!marquee_age'.*\"(.*)\"!"  ,
            ),
        array(
            "marquee_in_jquery']=\"".$marquee_jquery."\"",
            "marquee_number_of_events_shown']=\"".$marquee_number."\"",
            "marquee_speed']=\"".$marquee_speed."\"",
            "marquee_height']=\"".$marquee_height."\"",
            "marquee_headline_height']=\"".$marquee_headline_height."\"",
            "marquee_height_unit']=\"".$marquee_height_unit."\"",
            "marquee_1stline']=\"".$marquee_1stline."\"",
            "marquee_2ndline']=\"".$marquee_2ndline."\"",
            "marquee_age']=\"".$marquee_age."\"",
           ),
        $configfile);
   //save the values
    $config_ok = file_put_contents($pth['folder']['plugins'] . $plugin . '/config/config2.php',$configfile);
    $error .= $config_ok? '': $plugin_tx['calendar']['error_could_not_change_config_file'].', ';
    //and get the changed config values again
    include ($pth['folder']['plugins'] . $plugin .'/config/config2.php');

    if (!$config_ok) {
        $notice .= '<p class="error" style="clear:both">' . $plugin_tx['calendar']['error_occured'] . ': ' . $error . "</p>\n";
    }
}


//=============================
// start producing html data
//=============================

$o .= $notice;
$marquee_jquerychecked  = $calendar_cf['marquee_in_jquery']  ?  'checked="checked"' : '';

$o .= "<form class='calendar_config' style='margin:0' method='POST' action=''>\n";
$o .= tag('input type="hidden" value="marquee_config" name="marquee_config"') . "\n";


$o .= '<table><tr>';

// marquee with jQuery?
$o .= '<td>'
   .  $plugin_tx['calendar']['marquee_use_jquery'] . ': '
   .  '</td><td>'
   .  tag('input type="checkbox" '.$marquee_jquerychecked.' value="1" name="marquee_jquery"')
   .  '&nbsp; <a class="info_pop-up" href="#">'
   .  tag('input type="image" src="'
   .  $pth['folder']['plugins'] . $plugin . '/images'
   .  '/help_icon.png" style="width:16;height:16;"') . '<span>'
   .  $plugin_tx['calendar']['help_config_jquery_marquee']
   .  '</span></a>'
   .  "</td>\n";


$event_1stline  = $calendar_cf['marquee_1stline']=='event'?        ' selected':'';
$entry1_1stline = $calendar_cf['marquee_1stline']=='event_entry1'? ' selected':'';
$entry3_1stline = $calendar_cf['marquee_1stline']=='event_entry3'? ' selected':'';
$event_2ndline  = $calendar_cf['marquee_2ndline']=='event'?        ' selected':'';
$entry1_2ndline = $calendar_cf['marquee_2ndline']=='event_entry1'? ' selected':'';
$entry3_2ndline = $calendar_cf['marquee_2ndline']=='event_entry3'? ' selected':'';
$empty_2ndline  = $calendar_cf['marquee_2ndline']=='empty'?        ' selected':'';
$age_date       = $calendar_cf['marquee_age']    =='date'?         ' selected':'';
$age_1          = $calendar_cf['marquee_age']    =='1'?            ' selected':'';
$age_2          = $calendar_cf['marquee_age']    =='2'?            ' selected':'';
$age_not        = $calendar_cf['marquee_age']    =='not'?          ' selected':'';

$o .= '<td rowspan="6" style="padding-left:1em;vertical-align:top">'

   .  $plugin_tx['calendar']['marquee_1stline'] . tag('br')
   .  "\n" . '<select name="marquee_1stline">'
   .  "\n<option value='event'$event_1stline>".$plugin_tx['calendar']['event_main_entry'].'</option>'
   .  "\n<option value='event_entry1'$entry1_1stline>".$plugin_tx['calendar']['event_entry1'].'</option>'
   .  "\n<option value='event_entry3'$entry3_1stline>".$plugin_tx['calendar']['event_entry3'].'</option>'
   .  "\n</select>". tag('br')

   .  $plugin_tx['calendar']['marquee_2ndline'] . tag('br')
   .  "\n" . '<select name="marquee_2ndline">'
   .  "\n<option value='event'$event_2ndline>".$plugin_tx['calendar']['event_main_entry'].'</option>'
   .  "\n<option value='event_entry1'$entry1_2ndline>".$plugin_tx['calendar']['event_entry1'].'</option>'
   .  "\n<option value='event_entry3'$entry3_2ndline>".$plugin_tx['calendar']['event_entry3'].'</option>'
   .  "\n<option value='empty'$empty_2ndline>".$plugin_tx['calendar']['marquee_emptyline'].'</option>'
   .  "\n</select>". tag('br'). tag('br')

   .  $plugin_tx['calendar']['marquee_age'] . tag('br')
   .  "\n" . '<select name="marquee_age">'
   .  "\n<option value='date'$age_date>".$plugin_tx['calendar']['marquee_dateline'].'</option>'
   .  "\n<option value='1'$age_1>".$plugin_tx['calendar']['marquee_1stline'].'</option>'
   .  "\n<option value='2'$age_2>".$plugin_tx['calendar']['marquee_2ndline'].'</option>'
   .  "\n<option value='not'$age_not>".$plugin_tx['calendar']['marquee_not_in_marquee'].'</option>'
   .  "\n</select>"

   .  "</td></tr>\n";


// number of events in marquee
$j=0;
$number_select = '';
for ($i = 1;$i <=10 ;$i++ ) {
	$selected = '';
	if($i == $calendar_cf['marquee_number_of_events_shown']) {$selected = ' selected'; $j = 1;}
	$number_select .= "\n<option value='$i'$selected>$i</option>";
}
$o .= "\n" . '<tr><td>'
   .  $plugin_tx['calendar']['marquee_number_of_events_shown'] . ': &nbsp; '
   .  '</td><td>'
   .  "\n" . '<select name="marquee_number">'
   .  $number_select
   .  "\n</select>"
   .  "</td></tr>\n";


// speed of marquee
$j=0;
$number_select = '';
for ($i = 1;$i <=4 ;$i += .2 ) {
	$selected = '';
	if("$i" == $calendar_cf['marquee_speed']) {$selected = ' selected'; $j = 1;}
	$number_select .= "\n<option value='$i'$selected>" . str_pad($i,3,' ') . '</option>';
}
$o .= "\n" . '<tr><td>'
   .  $plugin_tx['calendar']['marquee_speed'] . ': &nbsp; '
   .  '</td><td>'
   .  "\n" . '<select name="marquee_speed">'
   .  $number_select
   .  "\n</select>"
   .  "</td></tr>\n";


// height of marquee
$o .= "\n" . '<tr><td>'
   .  $plugin_tx['calendar']['marquee_height'] . ': &nbsp; '
   .  '</td><td>'
   .  tag('input type="text"  value="'
   .  $calendar_cf['marquee_height'] . '" name="marquee_height" style="width:3em"')
   .  "</td></tr>\n";


// height of headline with date
$o .= "\n" . '<tr><td>'
   .  $plugin_tx['calendar']['marquee_dateline_height'] . ': '
   .  '</td><td>'
   .  tag('input type="text"  value="'
   .  $calendar_cf['marquee_headline_height'] . '" name="marquee_headline_height" style="width:3em"')
   .  "</td></tr>\n";


// units of the heights
$values = array('px','pt','em','%');
$values_select = '';
$j = 0;
foreach ($values as $option) {
	$selected = '';
	if($option == $calendar_cf['marquee_height_unit']) {$selected = ' selected'; $j = 1;}
	$values_select .= "\n<option value='$option'$selected>$option&nbsp;&nbsp;</option>";
}
$o .= "\n" . '<tr><td>'
   .  $plugin_tx['calendar']['marquee_height_unit'] . ': '
   .  '</td><td>'
   .  "\n<select name='marquee_height_unit'>"
   .  $values_select
   .  "\n</select>"
   .  "</td></tr>\n";

$o .= '</table>';


//==============
// save
//==============
$o .= '<div style="text-align:right;">'
   .  tag('input type="submit"   value="'.$plugin_tx['calendar']['menu_save_config'].'"')
   .  "</div>\n";

$o .=  "</form>";
