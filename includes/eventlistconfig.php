<?php
//======================================================================//
//                                                                      //
// Basic configuration of Calendar event list options                   //
// (c) Jan 2012 by svasti                                               //
//                                                                      //
//======================================================================//

global  $cf,$cl,$l,$u,$pth,$plugin,$plugin_tx,$plugin_cf,$calendar_cf,$tx,$sl,$h,$hjs,$datapath;
$o = '';

// Security check
if ((!function_exists('sv')))die('Access denied');

//get the button-images
$imageFolder = $pth['folder']['plugins'] . $plugin . "/images";

include($pth['folder']['plugins'].'calendar/includes/readcss.php');
include ($pth['folder']['plugins'] . $plugin .'/config/config2.php');


$config          = isset($_POST['config'])          ? $_POST['config']          : '';
$iconset         = isset($_POST['iconset'])         ? $_POST['iconset']         : $calendar_cf['icon-set'];
$time            = isset($_POST['time'])            ? $_POST['time']            : '';
$entry3          = isset($_POST['entry3'])          ? $_POST['entry3']          : '';
$entry1          = isset($_POST['entry1'])          ? $_POST['entry1']          : '';
$link            = isset($_POST['link'])            ? $_POST['link']            : '';
$description     = isset($_POST['description'])     ? $_POST['description']     : '';
$indented        = isset($_POST['indented'])        ? $_POST['indented']        : $calendar_cf['show_description_nr_of_cells_indented'];
$datewidth       = isset($_POST['datewidth'])       ? $_POST['datewidth']       : $datewidth;
$timewidth       = isset($_POST['timewidth'])       ? $_POST['timewidth']       : $timewidth;
$eventwidth      = isset($_POST['eventwidth'])      ? $_POST['eventwidth']      : $eventwidth;
$entry3width     = isset($_POST['entry3width'])     ? $_POST['entry3width']     : $entry3width;
$entry1width     = isset($_POST['entry1width'])     ? $_POST['entry1width']     : $entry1width;
$linkwidth       = isset($_POST['linkwidth'])       ? $_POST['linkwidth']       : $linkwidth;
$datename        = isset($_POST['datename'])        ? $_POST['datename']        : '';
$timename        = isset($_POST['timename'])        ? $_POST['timename']        : '';
$eventname       = isset($_POST['eventname'])       ? $_POST['eventname']       : '';
$entry3name      = isset($_POST['entry3name'])      ? $_POST['entry3name']      : '';
$entry1name      = isset($_POST['entry1name'])      ? $_POST['entry1name']      : '';
$linkname        = isset($_POST['linkname'])        ? $_POST['linkname']        : '';
$nomarquee       = isset($_POST['nomarquee'])       ? $_POST['nomarquee']       : '';
$showbookedout   = isset($_POST['showbookedout'])   ? $_POST['showbookedout']   : '';
$nopastevent     = isset($_POST['nopastevent'])     ? $_POST['nopastevent']     : '';
$greypastevents  = isset($_POST['greypastevents'])  ? $_POST['greypastevents']  : '';
$eventlistpage   = isset($_POST['eventlistpage'])   ? $_POST['eventlistpage']   : '';
$futuremonths    = isset($_POST['futuremonths'])    ? $_POST['futuremonths']    : '';
$pastmonths      = isset($_POST['pastmonths'])      ? $_POST['pastmonths']      : '';
$showperiod      = isset($_POST['showperiod'])      ? $_POST['showperiod']      : '';
$showdailytimes  = isset($_POST['showdailytimes'])  ? $_POST['showdailytimes']  : '';
$showweekly      = isset($_POST['showweekly'])      ? $_POST['showweekly']      : '';
$showyearly      = isset($_POST['showyearly'])      ? $_POST['showyearly']      : '';
$showexceptions  = isset($_POST['showexceptions'])  ? $_POST['showexceptions']  : '';
$showmultievent  = isset($_POST['showmultievent'])  ? $_POST['showmultievent']  : '';
$showmark2       = isset($_POST['showmark2'])       ? $_POST['showmark2']       : '';
$listtemplate    = isset($_POST['listtemplate'])    ? $_POST['listtemplate']    : '';
$eventcolor      = isset($_POST['eventcolor'])      ? $_POST['eventcolor']      : $eventcolor;
$datecolor       = isset($_POST['datecolor'])       ? $_POST['datecolor']       : $datecolor;
$timecolor       = isset($_POST['timecolor'])       ? $_POST['timecolor']       : $timecolor;
$entry3color     = isset($_POST['entry3color'])     ? $_POST['entry3color']     : $entry3color;
$entry1color     = isset($_POST['entry1color'])     ? $_POST['entry1color']     : $entry1color;
$linkcolor       = isset($_POST['linkcolor'])       ? $_POST['linkcolor']       : $linkcolor;
$mastercolor     = isset($_POST['mastercolor'])     ? $_POST['mastercolor']     : $calendar_cf['mastercolor'];
$birthdaycolor   = isset($_POST['birthdaycolor'])   ? $_POST['birthdaycolor']   : $birthdaycolor;
$listfont        = isset($_POST['listfont'])        ? $_POST['listfont']        : $listfont;
$listfontsize    = isset($_POST['listfontsize'])    ? $_POST['listfontsize']    : $listfontsize;
$subheadfontsize = isset($_POST['subheadfontsize']) ? $_POST['subheadfontsize'] : $subheadfontsize;
$monthfontsize   = isset($_POST['monthfontsize'])   ? $_POST['monthfontsize']   : $monthfontsize;
$eventfontweight = isset($_POST['eventfontweight']) ? $_POST['eventfontweight'] : $eventfontweight;

if(get_magic_quotes_gpc()) $eventlistpage = stripslashes($eventlistpage);

if ($config == "config"){

    // checkbox values, which haven't been sent, have to be set as FALSE
    $eventfontweight = isset($_POST['eventfontweight']) ? $eventfontweight : 0;

    //get the configfile
    $configfile = file_get_contents($pth['folder']['plugins'] . $plugin . '/config/config2.php');
    //and change the values
    $configfile = changevalue(
        array(
            'icon-set'                 => $iconset,
            'show_event_time'          => $time,
            'show_event_entry3'        => $entry3,
            'show_event_entry1'        => $entry1,
            'show_event_link'          => $link,
            'show_event_description'   => $description,
            'nr_of_cells_indented'     => $indented,
            'show_field_no_marquee'    => $nomarquee,
            'show_field_booked_out'    => $showbookedout,
            'show_future_months'       => $futuremonths,
            'show_past_months'         => $pastmonths,
            'show_period_of_events'    => $showperiod,
            'show_field_daily_times'   => $showdailytimes,
            'show_field_weekly'        => $showweekly,
            'show_field_yearly'        => $showyearly,
            'test_event_list_template' => $listtemplate,
            'mastercolor'              => $mastercolor,
            'show_field_exceptions'    => $showexceptions,
            'show_field_multievent'    => $showmultievent,
            'show_no_past_event'       => $nopastevent,
            'show_grey_past_events'    => $greypastevents,
            'show_field_mark2'         => $showmark2,
           ),
        $configfile,1);
   //save the values
    file_put_contents($pth['folder']['plugins'] . $plugin . '/config/config2.php',$configfile);
    //and get the changed config values again
    include ($pth['folder']['plugins'] . $plugin .'/config/config2.php');

    //same with language file
    $languagefile = file_get_contents($pth['folder']['plugins'] . $plugin . "/languages/$sl.php");
    $languagefile = changevalue(
        array(
            '_event_page'      => $eventlistpage,
            'event_date'       => $datename,
            'event_time'       => $timename,
            'event_main_entry' => $eventname,
            'event_entry3'     => $entry3name,
            'event_entry1'     => $entry1name,
            'event_link_etc'   => $linkname,
            ),
        $languagefile,2);
    file_put_contents($pth['folder']['plugins'] . $plugin . "/languages/$sl.php",$languagefile);
    include ($pth['folder']['plugins'] . $plugin ."/languages/$sl.php");

    //prepare some css values
    $csseventfontweight = $eventfontweight? 'bold':'normal';

    //change the css-values
    $cssfile =  changevalue(array(
                                'b1'   =>  $datewidth,
                                'b2'   =>  $timewidth,
                                'b3'   =>  $eventwidth,
                                'b4'   =>  $eventcolor,
                                'b5'   =>  $entry3width,
                                'b6'   =>  $entry3color,
                                'b7'   =>  $linkwidth,
                                'b8'   =>  $linkcolor,
                                'b9'   =>  $datecolor,
                                'b10'  =>  $timecolor,
                                'b11'  =>  $birthdaycolor,
                                'b12'  =>  $csseventfontweight,
                                'b13'  =>  $entry1width,
                                'b14'  =>  $entry1color,
                                'b15'  =>  $listfont,
                                'b16'  =>  $listfontsize,
                                'b17'  =>  $subheadfontsize,
                                'b18'  =>  $monthfontsize,
                                ),$cssfile);

    if(strlen($cssfile) > ($cssfilelength - 150)) //to prevent accidental erasure of css-file
    {
        file_put_contents($pth['folder']['plugins'] . '/calendar/css/stylesheet.css',$cssfile);
        $hjs .= '<link rel="stylesheet" href="'.$pth['folder']['plugins'].'/calendar/css/stylesheet.css" type="text/css">'."\n";
    }
}

// change the template
if ($calendar_cf['test_event_list_template']) {
	$cf['site']['template']          = $calendar_cf['test_event_list_template'];
	$pth['folder']['template']       = $pth['folder']['templates'].$cf['site']['template'].'/';
	$pth['file']['template']         = $pth['folder']['template'].'template.htm';
	$pth['file']['stylesheet']       = $pth['folder']['template'].'stylesheet.css';
	$pth['folder']['menubuttons']    = $pth['folder']['template'].'menu/';
	$pth['folder']['templateimages'] = $pth['folder']['template'].'images/';
}


$timechecked            = $calendar_cf['show_event_time']             ? 'checked="checked"' : '';
$entry3checked          = $calendar_cf['show_event_entry3']           ? 'checked="checked"' : '';
$entry1checked          = $calendar_cf['show_event_entry1']           ? 'checked="checked"' : '';
$linkchecked            = $calendar_cf['show_event_link']             ? 'checked="checked"' : '';
$descriptionchecked     = $calendar_cf['show_event_description']      ? 'checked="checked"' : '';
$nomarqueechecked       = $calendar_cf['show_field_no_marquee']       ? 'checked="checked"' : '';
$showmark2checked       = $calendar_cf['show_field_mark2']            ? 'checked="checked"' : '';
$showbookedoutchecked   = $calendar_cf['show_field_booked_out']       ? 'checked="checked"' : '';
$nopasteventchecked     = $calendar_cf['show_no_past_event']          ? 'checked="checked"' : '';
$greypasteventschecked  = $calendar_cf['show_grey_past_events']       ? 'checked="checked"' : '';
$showperiodchecked      = $calendar_cf['show_period_of_events']       ? 'checked="checked"' : '';
$showdailytimeschecked  = $calendar_cf['show_field_daily_times']      ? 'checked="checked"' : '';
$showweeklychecked      = $calendar_cf['show_field_weekly']           ? 'checked="checked"' : '';
$showyearlychecked      = $calendar_cf['show_field_yearly']           ? 'checked="checked"' : '';
$showexceptionschecked  = $calendar_cf['show_field_exceptions']       ? 'checked="checked"' : '';
$showmultieventchecked  = $calendar_cf['show_field_multievent']       ? 'checked="checked"' : '';
$eventfontweightchecked = $eventfontweight                            ? 'checked="checked"' : '';

// js file for color picker
$hjs .=  '<script type="text/javascript" src="'.$pth['folder']['plugins'].'calendar/jscolor/jscolor.js"></script>';

$o .= "\n\n<!-- Start Calendar Event List Config -->\n\n";
$o .= "<form method='POST' action=''>\n";
$o .= tag('input type="hidden" value="config" name="config"') . "\n";



// icon set
$handle = opendir($pth['folder']['plugins'].'calendar/images/');
$iconfolders = array();
while(false !== ($folder = readdir($handle))) {
	if(strpos($folder,'past')===FALSE && strpos($folder,'.')===FALSE){
		$iconfolders[] = $folder;
 	}
}
natcasesort($iconfolders);
$iconlist = '';
$iconfolder_select = '';
foreach($iconfolders as $folder){
	$selected = '';
	if($folder == $calendar_cf['icon-set']) {$selected = ' selected';}
	$iconfolder_select .= "\n\t".'<option value="'.$folder.'"'. $selected.'> '.$folder.'&nbsp; </option>';
}
$o .= '<div class="eventlistconfig" style="line-height:1.8">';

$o .= '<a  class="info_pop-up" href="#">'
   .  tag('input type="image" src="'
   .  $imageFolder
   .  '/help_icon.png" style="width:16;height:16;" alt="Help"')
   .  '<span>'.showIcons().'</span></a> Icon set n&ordm;: ';

$o .=  "<select name='iconset'>"
   .   "\n" . $iconfolder_select
   .   "\n</select>";

$o .= " \n" .  icon("int") . " \n" . icon("info") . " \n" . icon("ext") ."\n\n";


/*
// select event-list style template
$handle=opendir($datapath);
$templates = array();
while (false !== ($file = readdir($handle))) {
	if(strpos($file, '.tpl')) {
		$templates[$file] = $file;
	}
}
closedir($handle);
natcasesort($templates);
$styles_select = '';
foreach($templates as $file=>$eventliststyle){
	$selected = '';
	if($eventliststyle == $plugin_cf['calendar']['eventlist_style']) {$selected = ' selected';}
	$styles_select .= "<option value='$file'$selected>$eventliststyle</option>\n";
}
$o .= ' &nbsp; <span class="nowrap">' . $plugin_tx['calendar']['config_eventlist_style'] . ': '
   .  "<select name='eventliststyle'>"
   .  "\n" . '<option value="">' . $plugin_tx['calendar']['config_eventlist_table_selected'] . '</option>'
   .  "\n" . $styles_select
   .  "</select></span>\n\n";
*/


// font
$o .= ' &nbsp; <span class="nowrap">' . $plugin_tx['calendar']['config_fontfamily'] . ': ';
$o .= selectFont('listfont',$listfont,'listfontsize',$listfontsize)."</span>\n\n"; 

// font-size subhead
$o .= '<span class="nowrap">' . $plugin_tx['calendar']['config_subhead_fontsize'] . ': ';
$o .= selectFont('','','subheadfontsize',$subheadfontsize)."</span>\n\n";

// font-size month etc
$o .= '<span class="nowrap">' . $plugin_tx['calendar']['config_month_fontsize'] . ': ';
$o .= selectFont('','','monthfontsize',$monthfontsize)."</span>\n\n";

$o .= "</div>\n";




$o .= '<div class="cal_separator" ></div>';

// date
$o .= '<div class="eventlistconfig"><div>'
   .  $plugin_tx['calendar']['config_date']
   .  '</div>'
   .  $plugin_tx['calendar']['config_name'] . ': '
   .  tag('input type="text"  value="'
   .  $plugin_tx['calendar']['event_date'] . '" name="datename" style="width:9em"')
   .  ' '
   .  $plugin_tx['calendar']['config_width'] . ': '
   .  tag('input type="text"  value="'
   .  $datewidth . '" name="datewidth" style="width:4em"')
   .  ' '
   .  $plugin_tx['calendar']['config_color'] . ': '
   .  tag('input type="text" class="color" id="date" value="'
   .  $datecolor . '" name="datecolor" id="datecolor"')
   .  "</div>\n";

// time
$o .= '<div class="eventlistconfig"><div>'
   .  $plugin_tx['calendar']['config_time_on'] . ' '
   .  tag('input type="checkbox" '.$timechecked.' value="1" name="time"')
   .  '</div>'
   .  $plugin_tx['calendar']['config_name'] . ': '
   .  tag('input type="text"  value="'
   .  $plugin_tx['calendar']['event_time'] . '" name="timename" style="width:9em"')
   .  ' '
   .  $plugin_tx['calendar']['config_width'] . ': '
   .  tag('input type="text"  value="'
   .  $timewidth . '" name="timewidth" style="width:4em"')
   .  ' '
   .  $plugin_tx['calendar']['config_color'] . ': '
   .  tag('input type="text" class="color" id="time" value="'
   .  $timecolor . '" name="timecolor" id="timecolor"')
   .  "</div>\n";

// entry 1
$o .= '<div class="eventlistconfig"><div>'
   .  $plugin_tx['calendar']['config_on'] . ' '
   .  tag('input type="checkbox" '.$entry1checked.' value="1" name="entry1"')
   .  '</div>'
   .  $plugin_tx['calendar']['config_name'] . ': '
   .  tag('input type="text"  value="'
   .  $plugin_tx['calendar']['event_entry1'] . '" name="entry1name" style="width:9em"')
   .  ' '
   .  $plugin_tx['calendar']['config_width'] . ': '
   .  tag('input type="text"  value="'
   .  $entry1width . '" name="entry1width" style="width:4em"')
   .  ' '
   .  $plugin_tx['calendar']['config_color'] . ': '
   .  tag('input type="text" class="color" id="entry1" value="'
   .  $entry1color . '" name="entry1color" id="entry1color"')
   .  "</div>\n";

// main event entry
$o .= '<div class="eventlistconfig"><div>'
   .  $plugin_tx['calendar']['config_event']
   .  '</div>'
   .  $plugin_tx['calendar']['config_name'] . ': '
   .  tag('input type="text"  value="'
   .  $plugin_tx['calendar']['event_main_entry'] . '" name="eventname" style="width:9em"')
   .  ' '
   .  $plugin_tx['calendar']['config_width'] . ': '
   .  tag('input type="text"  value="'
   .  $eventwidth . '" name="eventwidth" style="width:4em"')
   .  ' '
   .  $plugin_tx['calendar']['config_color'] . ': '
   .  tag('input type="text" class="color" id="mainentry" value="'
   .  $eventcolor . '" name="eventcolor" id="eventcolor"')
   .  ' '
   .  tag('input type="checkbox" '.$eventfontweightchecked.' value="1" name="eventfontweight"')
   .  $plugin_tx['calendar']['config_bold'] . ' '

   .  "</div>\n";

// entry 3
$o .= '<div class="eventlistconfig"><div>'
   .  $plugin_tx['calendar']['config_on'] . ' '
   .  tag('input type="checkbox" '.$entry3checked.' value="1" name="entry3"')
   .  '</div>'
   .  $plugin_tx['calendar']['config_name'] . ': '
   .  tag('input type="text"  value="'
   .  $plugin_tx['calendar']['event_entry3'] . '" name="entry3name" style="width:9em"')
   .  ' '
   .  $plugin_tx['calendar']['config_width'] . ': '
   .  tag('input type="text"  value="'
   .  $entry3width . '" name="entry3width" style="width:4em"')
   .  ' '
   .  $plugin_tx['calendar']['config_color'] . ': '
   .  tag('input type="text" class="color" id="entry3" value="'
   .  $entry3color . '" name="entry3color" id="entry3color"')
   .  "</div>\n";

// link etc
$o .= '<div class="eventlistconfig"><div>'
   .  $plugin_tx['calendar']['config_on'] . ' '
   .  tag('input type="checkbox" '.$linkchecked.' value="1" name="link"')
   .  '</div>'
   .  $plugin_tx['calendar']['config_name'] . ': '
   .  tag('input type="text"  value="'
   .  $plugin_tx['calendar']['event_link_etc'] . '" name="linkname" style="width:9em"')
   .  ' '
   .  $plugin_tx['calendar']['config_width'] . ': '
   .  tag('input type="text"  value="'
   .  $linkwidth . '" name="linkwidth" style="width:4em"')
   .  ' '
   .  $plugin_tx['calendar']['config_color'] . ': '
   .  tag('input type="text" class="color" id="link" value="'
   .  $linkcolor . '" name="linkcolor" id="linkcolor"')
   .  "</div>\n";

// additional description
$j=0;
$values_select = '';
for ($i = 0;$i <=3 ;$i++ ) {
	$selected = '';
	if($i == $indented) {$selected = ' selected'; $j = 1;}
	$values_select .= "\n\t<option value='$i' $selected>$i</option>";
}

$o .= '<div class="eventlistconfig"><div>'
   .  $plugin_tx['calendar']['config_on'] . ' '
   .  tag('input type="checkbox" '.$descriptionchecked.' value="1" name="description"')
   .  '</div>'
   .  '"' .  $plugin_tx['calendar']['event_description']  . '"'
   . '&nbsp; &nbsp; '
   .  sprintf($plugin_tx['calendar']['config_indented'],


      "<select name='indented'> $values_select &nbsp; </select>\n")
   .  "</div>\n";

$o .= '<div style="clear:both;"></div>';



// birthday special color
$o .= '<div class="eventlistconfig">'
   .  '<div>'
   .  '&nbsp;</div>'
   .  $plugin_tx['calendar']['config_birthday_color']
   .  ': '
   .  tag('input type="text" class="color" value="'
   .  $birthdaycolor . '" name="birthdaycolor"');
$o .= "</div>\n";


// give all fields the same color through a master color picker
// this code was specially made by Jan Odvarko jscolor.com on request, many thanx, Jan
$o .= '<div class="eventlistconfig">'
   .  '<div>'
   .  '&nbsp;</div>'
   .  $plugin_tx['calendar']['config_all_same_color']
   .  ': '
   .  tag('input id="master-picker" value="'.$mastercolor.'" name="mastercolor" ')
   .  '<script type="text/javascript">
       var masterPicker = new jscolor.color(
          document.getElementById("master-picker"),
          {pickerPosition: "right"}
       );

       var ids = [
          "date",
          "time",
          "entry1",
          "mainentry",
          "entry3",
          "link"
       ];

       var syncPickers = function () {
          for (var i = 0; i < ids.length; i += 1) {
             var controlledPicker = document.getElementById(ids[i]);
             if (!controlledPicker.color) {
                controlledPicker.color = new jscolor.color(
                   document.getElementById(ids[i]),
                   {pickerPosition: "right"}
                );
             }
             controlledPicker.color.fromString(masterPicker.toString());
          }
       };

       masterPicker.onImmediateChange = syncPickers;
       //syncPickers();
       </script>';


$o .= "</div>\n";


$o .= '<div class="cal_separator"></div>';


// number of future and past months in event list and treatment of past events
$o .= '<div class="eventlistconfig">'
   .  sprintf($plugin_tx['calendar']['config_event_list_period']
   ,  tag('input type="checkbox" '.$showperiodchecked.' value="1" name="showperiod"')
   ,  tag('input type="text"  value="'
   .  $calendar_cf['show_future_months'] . '" name="futuremonths" style="width:1.5em"')
   ,  tag('input type="text"  value="'
   .  $calendar_cf['show_past_months'] . '" name="pastmonths" style="width:1.5em"')
   ,  tag('input type="checkbox" '.$greypasteventschecked.' value="1" name="greypastevents"')
   ,  tag('input type="checkbox" '.$nopasteventchecked.' value="1" name="nopastevent"'))
   .  "</div>\n";


$o .= '<div class="cal_separator"></div>';


// input fields
$o .= '<div class="eventlistconfig" style="margin-top:.5em;">'
   .  '<u>'.$plugin_tx['calendar']['config_input_fields'] . '</u>: &nbsp;'
   .  tag('input type="checkbox" '.$showmark2checked.' value="1" name="showmark2"')
   .  $plugin_tx['calendar']['event_mark2']
   .  ' &nbsp; '
   .  tag('input type="checkbox" '.$nomarqueechecked.' value="1" name="nomarquee"')
   .  $plugin_tx['calendar']['event_not_on_marquee']
   .  ' &nbsp; <span class="nowrap">'
   .  tag('input type="checkbox" '.$showbookedoutchecked.' value="1" name="showbookedout"')
   .  $plugin_tx['calendar']['event_booked_out'] . '</span>'
   .  ' &nbsp; <span class="nowrap">'
   .  tag('input type="checkbox" '.$showdailytimeschecked.' value="1" name="showdailytimes"')
   .  $plugin_tx['calendar']['event_times_are_daily'] . '</span>'
   .  ' &nbsp; <span class="nowrap">'
   .  tag('input type="checkbox" '.$showweeklychecked.' value="1" name="showweekly"')
   .  $plugin_tx['calendar']['event_weekly'] . '</span>'
   .  ' &nbsp; <span class="nowrap">'
   .  tag('input type="checkbox" '.$showyearlychecked.' value="1" name="showyearly"')
   .  $plugin_tx['calendar']['event_yearly'] . '</span>'
   .  ' &nbsp; <span class="nowrap">'
   .  tag('input type="checkbox" '.$showexceptionschecked.' value="1" name="showexceptions"')
   .  $plugin_tx['calendar']['config_exceptions'] . '</span>'
   .  ' &nbsp; <span class="nowrap">'
   .  tag('input type="checkbox" '.$showmultieventchecked.' value="1" name="showmultievent"')
   .  $plugin_tx['calendar']['config_additional'] . '</span>'
//   .  ' &nbsp; '
//   .  '<span style="white-space:nowrap;">"' . $plugin_tx['calendar']['event_entry3'] . '"= '
//   .  tag('input type="checkbox" '.$entry3areachecked.' value="1" name="entry3area"')
//   .  $plugin_tx['calendar']['config_textarea'] . '</span>'
   .  "</div>\n";


// event list page
$pages_select = '';
$x = 0;
for ($i=0;$i<$cl;$i++) {

    $levelindicator = '';
    for ($j=1;$j<$l[$i];$j++) {$levelindicator .= '&ndash;&nbsp;';}
    $page = $levelindicator.$h[$i];
	$selected = '';
	if($plugin_tx['calendar']['_event_page'] == '?'.$u[$i]) {$selected = ' selected'; $x++;}
	$pages_select .= "\n".'<option value="' . '?'.$u[$i] . '"'. $selected.'>'.$page.'</option>';
}
// in case just the name of a page was entered in the language-file
$preselect = (!$x && $plugin_tx['calendar']['_event_page'])? '<option value="'
           . $plugin_tx['calendar']['_event_page'] . '" selected>' . $plugin_tx['calendar']['_event_page'] . '</option>' : '';

$o .= '<div class="eventlistconfig" style="margin-top:.5em;">'
   .  $plugin_tx['calendar']['config_list_page'] . ': '
   .  "\n" . '<select name="eventlistpage">'
   .  "\n" . '<option value="">' . $plugin_tx['calendar']['config_no_event_list_page'] . '</option>'
   .  "\n" . $preselect . $pages_select . "\n</select>";

$o .= templateSelect($calendar_cf['test_event_list_template'],'listtemplate')
   .  $plugin_tx['calendar']['config_template'] . "</div>\n";




// save basic config
$o .= '<div style="text-align:right; margin-bottom:.5em;">'
   .  tag('input type="submit"   value="'.$plugin_tx['calendar']['menu_save_config'].'"')
   .  "</div>\n";

$o .=  "</form>";

