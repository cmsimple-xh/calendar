<?php
//======================================================//
//                                                      //
// function for configuration of Calendar options       //
// Jan 2012 by svasti                                   //
//                                                      //
//======================================================//
// Security check
if ((!function_exists('sv')) || preg_match('!calendar'.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'calendarconfig.php!i', sv('PHP_SELF')))die('Access denied');


global  $pth,$plugin,$plugin_tx,$calendar_cf,$plugin_cf,$cf,$tx,$sl,$hjs;
$o = $error = $notice = '';

include($pth['folder']['plugins'].'calendar/includes/readcss.php');
//reading the config2 file again is necessary in case preset changed something
include($pth['folder']['plugins'] . $plugin .'/config/config2.php');

$calendarconfig       = isset($_POST['calendarconfig'])       ? $_POST['calendarconfig']       : '';
$calendartemplate     = isset($_POST['calendartemplate'])     ? $_POST['calendartemplate']     : '';
$backgroundcolor      = isset($_POST['backgroundcolor'])      ? $_POST['backgroundcolor']      : $backgroundcolor;
$backgroundimage      = isset($_POST['backgroundimage'])      ? $_POST['backgroundimage']      : $backgroundimage;
$bordercolor          = isset($_POST['bordercolor'])          ? $_POST['bordercolor']          : $bordercolor;
$borderwidth          = isset($_POST['borderwidth'])          ? $_POST['borderwidth']          : $borderwidth;
$birthdayimage        = isset($_POST['birthdayimage'])        ? $_POST['birthdayimage']        : $birthdayimage;
$hintmouseover        = isset($_POST['hintmouseover'])        ? $_POST['hintmouseover']        : '';
$dayspacing           = isset($_POST['dayspacing'])           ? $_POST['dayspacing']           : $dayspacing;
$popupbordercolor     = isset($_POST['popupbordercolor'])     ? $_POST['popupbordercolor']     : $popupbordercolor;
$popupborderwidth     = isset($_POST['popupborderwidth'])     ? $_POST['popupborderwidth']     : $popupborderwidth;
$popupbackground      = isset($_POST['popupbackground'])      ? $_POST['popupbackground']      : $popupbackground;
$birthdayfield        = isset($_POST['birthdayfield'])        ? $_POST['birthdayfield']        : $birthdayfield;
$headline             = isset($_POST['headline'])             ? $_POST['headline']             : $calendar_cf['bigcalendar_month_year_headline_style'];
$daynames             = isset($_POST['daynames'])             ? $_POST['daynames']             : $daynames;
$topmargin            = isset($_POST['topmargin'])            ? $_POST['topmargin']            : $topmargin;
$headlinecolor        = isset($_POST['headlinecolor'])        ? $_POST['headlinecolor']        : $headlinecolor;
$shadow               = isset($_POST['shadow'])               ? $_POST['shadow']               : $shadow;
$roundcorners         = isset($_POST['roundcorners'])         ? $_POST['roundcorners']         : $roundcorners;
$opacitynoevent       = isset($_POST['opacitynoevent'])       ? $_POST['opacitynoevent']       : $opacitynoevent;
$headlineconfig       = isset($_POST['headlineconfig'])       ? $_POST['headlineconfig']       : $headlineconfig;
$daynameconfig        = isset($_POST['daynameconfig'])        ? $_POST['daynameconfig']        : $daynameconfig;
$daynamespadtop       = isset($_POST['daynamespadtop'])       ? $_POST['daynamespadtop']       : $daynamespadtop;
$daynamespadbot       = isset($_POST['daynamespadbot'])       ? $_POST['daynamespadbot']       : $daynamespadbot;
$todaynameconfig      = isset($_POST['todaynameconfig'])      ? $_POST['todaynameconfig']      : $todaynameconfig;
$smallcalfont         = isset($_POST['smallcalfont'])         ? $_POST['smallcalfont']         : $smallcalfont;
$smallcalfontsize     = isset($_POST['smallcalfontsize'])     ? $_POST['smallcalfontsize']     : $smallcalfontsize;
$smallcallineheight   = isset($_POST['smallcallineheight'])   ? $_POST['smallcallineheight']   : $smallcallineheight;
$bigcalfont           = isset($_POST['bigcalfont'])           ? $_POST['bigcalfont']           : $bigcalfont;
$bigcalfontsize       = isset($_POST['bigcalfontsize'])       ? $_POST['bigcalfontsize']       : $bigcalfontsize;
$popupfont            = isset($_POST['popupfont'])            ? $_POST['popupfont']            : $popupfont;
$popupfontsize        = isset($_POST['popupfontsize'])        ? $_POST['popupfontsize']        : $popupfontsize;
$normaldaycolor       = isset($_POST['normaldaycolor'])       ? $_POST['normaldaycolor']       : $normaldaycolor;
$weekendcolor         = isset($_POST['weekendcolor'])         ? $_POST['weekendcolor']         : $weekendcolor;
$holidaycolor         = isset($_POST['holidaycolor'])         ? $_POST['holidaycolor']         : $holidaycolor;
$eventdaycolor        = isset($_POST['eventdaycolor'])        ? $_POST['eventdaycolor']        : $eventdaycolor;
$eventdaybgcolor      = isset($_POST['eventdaybgcolor'])      ? $_POST['eventdaybgcolor']      : $eventdaybgcolor;
$todaycolor           = isset($_POST['todaycolor'])           ? $_POST['todaycolor']           : $todaycolor;
$todaybgcolor         = isset($_POST['todaybgcolor'])         ? $_POST['todaybgcolor']         : $todaybgcolor;
$underlongevent       = isset($_POST['underlongevent'])       ? $_POST['underlongevent']       : '';
$underlongcolor       = isset($_POST['underlongcolor'])       ? $_POST['underlongcolor']       : $underlongcolor;
$underlongwidth       = isset($_POST['underlongwidth'])       ? $_POST['underlongwidth']       : $underlongwidth;
$mark2color           = isset($_POST['mark2color'])           ? $_POST['mark2color']           : $mark2color;
$mark2width           = isset($_POST['mark2width'])           ? $_POST['mark2width']           : $mark2width;
$dfbwidth             = isset($_POST['dfbwidth'])             ? $_POST['dfbwidth']             : '';
$dfbstyle             = isset($_POST['dfbstyle'])             ? $_POST['dfbstyle']             : '';
$dfbcolor             = isset($_POST['dfbcolor'])             ? $_POST['dfbcolor']             : '';
$dayfieldpadding      = isset($_POST['dayfieldpadding'])      ? $_POST['dayfieldpadding']      : $dayfieldpadding;
$notimesymbol         = isset($_POST['notimesymbol'])         ? $_POST['notimesymbol']         : '';


//======================================================
// writing the changed values into the repective files
//======================================================

if ($calendarconfig == "calendarconfig"){

    // needs to be processed here, as no value means no $_POST, although '' is needed
    $nowrap          = isset($_POST['nowrap'])          ? $_POST['nowrap']           : '';
    $writetime       = isset($_POST['writetime'])       ? $_POST['writetime']        : '';
    $writeevent      = isset($_POST['writeevent'])      ? $_POST['writeevent']       : '';
    $writeentry3     = isset($_POST['writeentry3'])     ? $_POST['writeentry3']      : '';
    $writeentry1     = isset($_POST['writeentry1'])     ? $_POST['writeentry1']      : '';
    $birthdayentry3  = isset($_POST['birthdayentry3'])  ? $_POST['birthdayentry3']   : '';
    $birthdayage     = isset($_POST['birthdayage'])     ? $_POST['birthdayage']      : '';
    $dayheight       = isset($_POST['dayheight'])       ? $_POST['dayheight']        : '';
    $smallcalpopup   = isset($_POST['smallcalpopup'])   ? $_POST['smallcalpopup']    : '';
    $entry3popup     = isset($_POST['entry3popup'])     ? $_POST['entry3popup']      : '';
    $eventdaybold    = isset($_POST['eventdaybold'])    ? $_POST['eventdaybold']     : '';
    $todaybold       = isset($_POST['todaybold'])       ? $_POST['todaybold']        : '';
    $linebetween     = isset($_POST['linebetween'])     ? $_POST['linebetween']      : '';

    // configfile
    //=============
    $configfile = file_get_contents($pth['folder']['plugins'] . $plugin . '/config/config2.php');
    // change the values
    $configfile = changevalue(
        array(
            'test_calendar_template'                => $calendartemplate,
            'bigcalendar_write_time'                => $writetime,
            'bigcalendar_write_event'               => $writeevent,
            'bigcalendar_write_entry3'              => $writeentry3,
            'bigcalendar_write_entry1'              => $writeentry1,
            'bigcalendar_anniversary_write_entry3'  => $birthdayentry3,
            'bigcalendar_anniversary_write_age'     => $birthdayage,
            'bigcalendar_line_between_entries'      => $linebetween,
            'bigcalendar_symbol_if_no_time_given'   => $notimesymbol,
            'calendar-popup_big'                    => $smallcalpopup,
            'titleattributepopup_entry3'            => $entry3popup,
            'bigcalendar_month_year_headline_style' => $headline,
            'dont_underline_longevents'             => $underlongevent,
           ),
        $configfile,1);
   //save the values
    $config_ok = file_put_contents($pth['folder']['plugins'] . $plugin . '/config/config2.php',$configfile);
    $error .= $config_ok? '': $plugin_tx['calendar']['error_could_not_change_config_file'].', ';
    //and get the changed config values again
    include ($pth['folder']['plugins'] . $plugin .'/config/config2.php');


    // language file
    //================
    $languagefile = file_get_contents($pth['folder']['plugins'] . $plugin . "/languages/$sl.php");
    $languagefile = changevalue(
        array(
            'hint_mouseover_in_calendar' => $hintmouseover,
            ),
        $languagefile,2);
    $lang_ok = file_put_contents($pth['folder']['plugins'] . $plugin . "/languages/$sl.php",$languagefile);
    $error .= $lang_ok? '' : $plugin_tx['calendar']['error_could_not_change_language_file'].', ';

    include ($pth['folder']['plugins'] . $plugin ."/languages/$sl.php");

    // css-values
    //=============
    $nowrapdata         = $nowrap? 'nowrap' : 'normal';
    if(function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) {
        $headlineconfig     = "\n" . trim(stripslashes($headlineconfig))  . "\n";
        $daynameconfig      = "\n" . trim(stripslashes($daynameconfig))   . "\n";
        $todaynameconfig    = "\n" . trim(stripslashes($todaynameconfig)) . "\n";
    }
    $csseventdaybold    = $eventdaybold?     'bold' :'';
    $csstodaybold       = $todaybold?        'bold' :'';
    $bordercollapse = $dayspacing=='0px'? 'collapse':'separate';
    $dayfieldborder = $dfbwidth .' '.$dfbstyle.' '.$dfbcolor;

    $cssfile = changevalue(array(
                                'a1'       => $normaldaycolor,
                                'a2'       => $weekendcolor,
                                'a3'       => $holidaycolor,
                                'a4'       => $eventdaycolor,
                                'a5,!im'   => $eventdaybgcolor,
                                'a6'       => $underlongcolor,
                                'a7'       => $underlongwidth,
                                'a8'       => $mark2color,
                                'a9'       => $mark2width,
                                'a10'      => $csseventdaybold,

                                'c1'       => $headlinecolor,
                                'c2'       => $backgroundcolor,
                                'c3,url'   => $backgroundimage,
                                'c4'       => $bordercolor,
                                'c5'       => $borderwidth,
                                'c6'       => $dayspacing,
                                'c7'       => $nowrapdata,
                                'c10'      => $daynamespadtop,
                                'c11'      => $daynamespadbot,
                                'c13'      => $dayheight,
                                'c14'      => $topmargin,
                                'c18'      => $daynames,
                                'c19,!im'  => $birthdayfield,
                                'c20'      => $roundcorners,
                                'c21'      => $shadow,
                                'c22'      => $opacitynoevent,
                                'c31,sU'   => $headlineconfig,
                                'c32,sU'   => $daynameconfig,
                                'c33,sU'   => $todaynameconfig,
                                'c34'      => $bigcalfont,
                                'c35'      => $bigcalfontsize,
                                'c36'      => $dayfieldborder,
                                'c37'      => $bordercollapse,
                                'c38'      => $dayfieldpadding,

                                'sc1'      => $smallcallineheight,
                                'sc2'      => $smallcalfont,
                                'sc3'      => $smallcalfontsize,
                                'sc4,url!' => $birthdayimage,
                                'sc5'      => $todaycolor,
                                'sc6,!im'  => $todaybgcolor,
                                'sc7'      => $csstodaybold,

                                'p1'       => $popupbordercolor,
                                'p2'       => $popupborderwidth,
                                'p3'       => $popupbackground,
                                'p4'       => $popupfont,
                                'p5'       => $popupfontsize,
                                ),$cssfile);

    if(strlen($cssfile) > ($cssfilelength - 500)) //to prevent accidental erasure of css-file
    {
        $css_ok = file_put_contents($pth['folder']['plugins'] . '/calendar/css/stylesheet.css',$cssfile);

        //trying to make the browser read the file without caching
        $x = rand (1,100);
        $hjs .= '<link rel="stylesheet" href="'.$pth['folder']['plugins'].'/calendar/css/overwrite.css?reload='.$x.'" type="text/css">'."\n"
              . '<link rel="stylesheet" href="'.$pth['folder']['plugins'].'/calendar/css/stylesheet.css?reload='.$x.'" type="text/css">'."\n";
    } else $css_ok = FALSE;

    $error .= $css_ok===FALSE? $plugin_tx['calendar']['error_could_not_change_css_file'].', ' : '';
    $error = trim($error, ', ');
    if (!$css_ok || !$lang_ok || !$config_ok) {
        $notice .= '<p class="error" style="clear:both">' . $plugin_tx['calendar']['error_occured'] . ': ' . $error . "</p>\n";
    }

}

$nowrapchecked           = $nowrap                                 ?              'checked="checked"' : '';
$writetimechecked        = $calendar_cf['bigcalendar_write_time']  ?              'checked="checked"' : '';
$writeeventchecked       = $calendar_cf['bigcalendar_write_event'] ?              'checked="checked"' : '';
$writeentry3checked      = $calendar_cf['bigcalendar_write_entry3'] ?             'checked="checked"' : '';
$writeentry1checked      = $calendar_cf['bigcalendar_write_entry1'] ?             'checked="checked"' : '';
$birthdayentry3checked   = $calendar_cf['bigcalendar_anniversary_write_entry3'] ? 'checked="checked"' : '';
$birthdayagechecked      = $calendar_cf['bigcalendar_anniversary_write_age']?     'checked="checked"' : '';
$linebetweenchecked      = $calendar_cf['bigcalendar_line_between_entries']?      'checked="checked"' : '';
$smallcalpopupchecked    = $calendar_cf['calendar-popup_big']?                    'checked="checked"' : '';
$entry3popupchecked      = $calendar_cf['titleattributepopup_entry3']?            'checked="checked"' : '';
$underlongeventchecked   = $calendar_cf['dont_underline_longevents']?             'checked="checked"' : '';
$eventdayboldchecked     = $eventdaybold?                                         'checked="checked"' : '';
$todayboldchecked        = $todaybold?                                            'checked="checked"' : '';


// set choosen template for bigcalendar view
if ($calendar_cf['test_calendar_template']) {
	$cf['site']['template']          = $calendar_cf['test_calendar_template'];
	$pth['folder']['template']       = $pth['folder']['templates'].$cf['site']['template'].'/';
	$pth['file']['template']         = $pth['folder']['template'].'template.htm';
	$pth['file']['stylesheet']       = $pth['folder']['template'].'stylesheet.css';
	$pth['folder']['menubuttons']    = $pth['folder']['template'].'menu/';
	$pth['folder']['templateimages'] = $pth['folder']['template'].'images/';
} else {
    include ($pth['file']['config']);
}

//=============================
// start producing html data
//=============================

// js file for color picker
$hjs .= '<script type="text/javascript" src="'.$pth['folder']['plugins'].'calendar/jscolor/jscolor.js"></script>';

//calling toggle function
$hjs .= '<script type="text/javascript">
function openedit(button,hidearea,clicktext,reclicktext,displaytype,antihidearea)
{
    if(document.getElementById(button).className == "calendar_edit_off") {
        document.getElementById(button).value = clicktext;
        document.getElementById(button).className = "calendar_edit_on";
        document.getElementById(hidearea).style.display = displaytype;
        document.getElementById(antihidearea).style.display = "none";
    } else {
        document.getElementById(button).value = reclicktext;
        document.getElementById(button).className = "calendar_edit_off";
        document.getElementById(hidearea).style.display = "none";
        document.getElementById(antihidearea).style.display = displaytype;
    }
}
</script>'."\n";

$o .= $notice;

$o .= "<form class='calendar_config' style='margin:0' method='POST' action=''>\n";
$o .= tag('input type="hidden" value="calendarconfig" name="calendarconfig"') . "\n";



//====================================
//************************************
//
//     big calendar settings
//
//************************************
//====================================

$hide = $calendar_cf['show_bigcalendar_config']? '':' style="display:none;"';
$o .= '<div id="bigcalendar"'.$hide.'>';
$o .= '<div class="cal_separator"></div>';

$o .= '<div style="height:auto;color:black;font-size:100%;"><h4 style="margin:0;padding:0 0 3px;">'
   .  $plugin_tx['calendar']['config_bigcalendar']
   .  '</h4></div>'."\n";


//select a template
$o .= '<div>'
   .  templateSelect($calendar_cf['test_calendar_template'],'calendartemplate')
   .  $plugin_tx['calendar']['config_template']
   .  "</div>\n";


//background
//==================
//find the path (code from php.net/manual/en/function.realpath.php User Contributions)
$relativePath = $pth['folder']['plugins'].'calendar/css/'.$plugin_cf['calendar']['filepath_calendar_background_images'];
$pattern = '/\w+\/\.\.\//';
while(preg_match($pattern,$relativePath)){
    $relativePath = preg_replace($pattern, '', $relativePath);
}

$handle=opendir($relativePath);
$images = array();
while (false !== ($file = readdir($handle))) {
	if($file != "." && $file != ".." && substr($file,-3)!='gif') {
		$images[] = $file;
		}
	}
closedir($handle);
natcasesort($images);
$images_select = '';
foreach($images as $file){
	$selected = '';
	if($backgroundimage == $file) {$selected = ' selected';}
	$images_select .= "\n<option value=$file$selected>$file</option>";
}
$o .= '<div>'
   .  "<select name='backgroundimage'>"
   .  "\n" . '<option value="">' . $plugin_tx['calendar']['config_no_image'] . '</option>'
   .  "\n" . $images_select
   .  '</select>'
   .  tag('input type="text" class="color {pickerPosition:\'left\'}" value="'
   .  $backgroundcolor . '" name="backgroundcolor"')
   .  $plugin_tx['calendar']['config_background_or_color']
   .  "</div>\n";



//border
$j=0;
$values_select = '';
for ($i = 0;$i <=15 ;$i++ ) {
	$selected = '';
	if($i.'px' == $borderwidth) {$selected = ' selected'; $j = 1;}
	$values_select .= "\n".'<option value="'.$i.'px"'. $selected.'>'.$i.'px</option>';
}
// in case the css file contains a value outside the given range, this value will be shown
$preselect = $j? '' : '<option value="'.$borderwidth.'" selected>' . $borderwidth . '</option>';

$o .= '<div>'
   .  tag('input type="text" class="color {pickerPosition:\'left\'}" value="'
   .  $bordercolor . '" name="bordercolor"')
   .  '<select name="borderwidth">'
   .  $preselect . $values_select
   .  '</select>'
   .  $plugin_tx['calendar']['config_border_color_width']
   .  "</div>\n";



$o .= '<div class="cal_separator"></div>';


// month-year headline styling + color
$j=0;
$values_select = '';
for ($i = 1;$i <=6 ;$i++ ) {
	$selected = '';
	if('h'.$i == $headline) {$selected = ' selected'; $j = 1;}
	$values_select .= "\n<option value='h$i'$selected>h$i</option>";
}
$preselect = '';
if(!$j) $preselect .= '<option value="'.$headline.'" selected>' . $headline . '</option>';
if($headline) $preselect .= '<option value=""></option>';
$o .= '<div>'
   .  tag('input type="text" class="color {pickerPosition:\'left\'}" value="'
   .  $headlinecolor . '" name="headlinecolor"')
   .  '<select name="headline">'
   .  "\n" . $preselect . $values_select
   .  '</select>'
   .  $plugin_tx['calendar']['config_headline_style']
   .  "</div>\n";

// margin top
$o .= '<div>'
   .  tag('input type="text"  value="'
   .  $topmargin . '" name="topmargin" style="width:4em"')
   .  '<span class="title" title="'.$plugin_tx['calendar']['help_config_topmargin'].'">'
   .  $plugin_tx['calendar']['config_margin-top']
   .  '</span>'
   .  "</div>\n";

// more headline css
$o .= '<div>'
   .  $plugin_tx['calendar']['config_headline_more_css'];
$o .=  tag('input type="button" value="+" class="calendar_edit_off" id="headlineconfig_button" style="margin-left:0;"
                   onclick=\'openedit("headlineconfig_button","headlineconfig","&ndash;","+","block");\'');
$o .= "</div>\n";

$o .= '<div id="headlineconfig" >';
$o .= '<textarea style="width:100%;height:100%;" name="headlineconfig">'
   .  $headlineconfig . '</textarea>';
$o .= "</div>\n";



$o .= '<div class="cal_separator"></div>';



//color of weekday names
$o .= '<div>'
   .  tag('input type="text" class="color {pickerPosition:\'left\'}" value="'
   .  $daynames . '" name="daynames"')
   .  $plugin_tx['calendar']['config_day_name_color']
   .  "</div>\n";

//padding around weekend names
$o .= '<div>'
   .  tag('input type="text"  value="'
   .  $daynamespadtop . '" name="daynamespadtop" style="width:3em"')
   .  tag('input type="text"  value="'
   .  $daynamespadbot . '" name="daynamespadbot" style="width:3em"')
   .  '<span class="title" title="'.$plugin_tx['calendar']['help_config_padweekdaynames'].'">'
   .  $plugin_tx['calendar']['config_weekdaynames_padding']
   .  '</span>'
   .  "</div>\n";


// more weekday names css
$o .= '<div>'
   .  $plugin_tx['calendar']['config_day_names_more_css'];
$o .=  tag('input type="button" value="+" class="calendar_edit_off" id="daynameconfig_button" style="margin-left:0;"
                   onclick=\'openedit("daynameconfig_button","daynameconfig","&ndash;","+","block");\'');
$o .=   "</div>\n";

$o .= '<div id="daynameconfig" >';
$o .= '<textarea style="width:100%;height:100%;" name="daynameconfig">'
   .  $daynameconfig . '</textarea>'
//   .  '<div>' . $plugin_tx['calendar']['config_more'] . '</div>'
   . '<textarea style="width:100%;height:5em;" name="todaynameconfig">'
               .  $todaynameconfig . '</textarea>';
$o .= "</div>\n";



$o .= '<div class="cal_separator"></div>';



// day box height
$o .= '<div>'
   .  tag('input type="text"  value="'
   .  $dayheight . '" name="dayheight" style="width:4em"')
   .  $plugin_tx['calendar']['config_day_field_height']
   .  "</div>\n" ;



// day field border
list($dfbwidth,$dfbstyle,$dfbcolor)=explode(' ',$dayfieldborder);

$j=0;
$values_select = '';
for ($i = 0;$i <=4 ;$i++ ) {
	$selected = '';
	if($i.'px' == $dfbwidth) {$selected = ' selected'; $j = 1;}
	$values_select .= "\n".'<option value="'.$i.'px"'. $selected.'>'.$i.'px</option>';
}
$preselect = $j? '' : '<option value="'.$dfbwidth.'" selected>' . $dfbwidth . '</option>';

$o .= '<div>'
   .  '<select name="dfbwidth">'
   .  $preselect . $values_select
   .  '</select>';

$dottedselect = $dfbstyle=='dotted'? ' selected':'';
$solidselect  = $dfbstyle=='solid'?  ' selected':'';
$dashedselect = $dfbstyle=='dashed'? ' selected':'';

$o .=  '<select name="dfbstyle">'
   .   "<option value='dotted'$dottedselect>dotted</option>\n"
   .   "<option value='dashed'$dashedselect>dashed</option>\n"
   .   "<option value='solid'$solidselect>solid</option>\n";

$o .=  tag('input type="text" class="color" value="'
   .  $dfbcolor . '" name="dfbcolor"')

   .  $plugin_tx['calendar']['config_dayfield_border']
   .  '</span>'
   .  "</div>\n";

$j=0;
$values_select = '';
for ($i = 0;$i <=4 ;$i++ ) {
	$selected = '';
	if($i.'px' == $dayfieldpadding) {$selected = ' selected'; $j = 1;}
	$values_select .= "\n".'<option value="'.$i.'px"'. $selected.'>'.$i.'px</option>';
}
$preselect = $j? '' : '<option value="'.$dayfieldpadding.'" selected>' . $dayfieldpadding . '</option>';

$o .= '<div>'
   .  '<select name="dayfieldpadding">'
   .  $preselect . $values_select
   .  '</select>'
   .  $plugin_tx['calendar']['config_dayfield_padding']
   .  "</div>\n";


//day border spacing + day border collapse Combination
$j=0;
$spacingvalues_select = '';
for ($i = 0;$i <=50 ;$i++ ) {
	$selected = '';
	if($i.'px' == $dayspacing) {$selected = ' selected'; $j = 1;}
	$spacingvalues_select .= "<option value='${i}px' $selected>${i}px</option>\n";
}
$preselect = $j? '' : '<option value="'.$dayspacing.'" selected>' . $dayspacing . '</option>';
if($bordercollapse=='collapse' || !$dayspacing) $preselect = "<option value='0px' selected>0px</option>\n";
$o .= '<div>'
   .  "\n" . '<select name="dayspacing">'
   .  "\n" . $preselect . $spacingvalues_select
   .  '</select>'
   .  $plugin_tx['calendar']['config_day_border_spacing']
   .  "</div>\n";


//round corners of day boxes
$o .= '<div>'
   .  tag('input type="text"  value="'
   .  $roundcorners . '" name="roundcorners" style="width:8em"')
   .  '<span class="title" title="'.$plugin_tx['calendar']['help_config_roundcorners'].'">'.$plugin_tx['calendar']['config_roundcorners'] .'</span>'
   .  "</div>\n";

//shadow of day boxes
$o .= '<div>'
   .  tag('input type="text"  value="'
   .  $shadow . '" name="shadow" style="width:10em"')
   .  '<span class="title" title="'.$plugin_tx['calendar']['help_config_shadow'].'">'.$plugin_tx['calendar']['config_shadow'] .'</span>'
   .  "</div>\n";

//opacity for day without event
$j=0;
$opacity_select = '';
for ($i = 0;$i < 1 ;$i = $i + 0.1 ) {
	$selected = '';
	if($i == $opacitynoevent) {$selected = ' selected'; $j = 1;}
	$opacity_select .= "\n\t".'<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
}
// in case the css file contains a value outside the given range, this value will be shown
$preselect = $j? '' : '<option value="'.$opacitynoevent.'" selected>' . $opacitynoevent . '</option>';

$o .= '<div>'
   .  "\n" . '<select name="opacitynoevent">'
   .  "\n" . $preselect . $opacity_select
   .  '</select>'
   .  $plugin_tx['calendar']['config_opacity_no_event'] 
   .  "</div>\n";


//birthday background
$o .= '<div>'
   .  tag('input type="text" class="color {pickerPosition:\'left\'}" value="'
   .  $birthdayfield . '" name="birthdayfield"')
   .  $plugin_tx['calendar']['config_birthday_in_dayfield']
   .  "</div>\n";




$o .= '<div class="cal_separator"></div>';


// nowrap of event entry text in date fields
$o .= '<div>'
   .  tag('input type="checkbox" '.$nowrapchecked.' value="1" name="nowrap"')
   .  $plugin_tx['calendar']['config_one_line_per_entry']
   .  "</div>\n";

// details to be displayed in the calendar
$o .= '<div>'
   .  tag('input type="checkbox" '.$linebetweenchecked.' value="1" name="linebetween"')
   .  $plugin_tx['calendar']['config_line_between_events_in_bigcalendar']
   .  "</div>\n";

$o .= '<div>'
   .  tag('input type="checkbox" '.$writetimechecked.' value="1" name="writetime"')
   .  $plugin_tx['calendar']['event_time']
   .  "</div>\n";

// symbol for entries without time when time is supposed to be displayed
$j=0;
$values_select = '';
foreach (array('','@','â€“','â€”','â€¢','Â·','â™ ','â™£','â™¥','â™¦') as $keys=>$value ) {
	$selected = '';
	if($value == $calendar_cf['bigcalendar_symbol_if_no_time_given']) {$selected = ' selected'; $j = 1;}
	$values_select .= "\n".'<option value="'.$value.'"'. $selected.'>&nbsp;'.$value.'</option>';
}
$o .= '<div>'
   .  '<select name="notimesymbol">'
   .  $values_select
   .  '</select>'
   .  $plugin_tx['calendar']['config_symbol_when_no_time_is_given']
   .  "</div>\n";


$o .= '<div>'
   .  tag('input type="checkbox" '.$writeentry1checked.' value="1" name="writeentry1"')
   .  $plugin_tx['calendar']['event_entry1']
   .  "</div>\n";

$o .= '<div>'
   .  tag('input type="checkbox" '.$writeeventchecked.' value="1" name="writeevent"')
   .  $plugin_tx['calendar']['event_main_entry']
   .  "</div>\n";

$o .= '<div>'
   .  tag('input type="checkbox" '.$writeentry3checked.' value="1" name="writeentry3"')
   .  $plugin_tx['calendar']['event_entry3']
   .  "</div>\n";

$o .= '<div>'
   .  tag('input type="checkbox" '.$birthdayentry3checked.' value="1" name="birthdayentry3"')
   .  $plugin_tx['calendar']['event_entry3'] . ' ('. $plugin_tx['calendar']['config_birthday'] . ')'
   .  "</div>\n";

$o .= '<div>'
   .  tag('input type="checkbox" '.$birthdayagechecked.' value="1" name="birthdayage"')
   .  $plugin_tx['calendar']['config_birthday_age']
   .  "</div>\n";

// font
$o .= '<div>'.selectFont('bigcalfont',$bigcalfont,'bigcalfontsize',$bigcalfontsize)."</div>\n";;

// mouse over hint
$o .= '<div>'
   .  $plugin_tx['calendar']['config_hint'] . ':&nbsp;'
   .  tag('input type="text"  value="'
   .  $plugin_tx['calendar']['hint_mouseover_in_calendar'] . '" name="hintmouseover" style="width:28em"')
   .  "</div>\n";

$o .= '</div>';


//====================================
//************************************
//
//     small calendar settings
//
//************************************
//====================================

$hide = $calendar_cf['show_smallcalendar_config']? '':' style="display:none;"';
$o .= "<div id='smallcalendar'$hide>\n";
$o .= '<div class="cal_separator"></div>';

// headline "small calendar"
$o .= '<div style="height:auto;color:black;font-size:100%;"><h4 style="margin:0;padding:0 0 3px;">'
   .  $plugin_tx['calendar']['config_small_calendar']
   .  '</h4></div>';

// font
$o .= '<div>'.selectFont('smallcalfont',$smallcalfont,'smallcalfontsize',$smallcalfontsize,'smallcallineheight',$smallcallineheight)
    . $plugin_tx['calendar']['config_lineheight'] . "</div>\n";;

// normal day
$o .= '<div style="clear:left;">1) '
   .  tag('input type="text" class="color  {pickerPosition:\'left\'}" value="'
   .  $normaldaycolor . '" name="normaldaycolor"')
   .  $plugin_tx['calendar']['config_normalday'] 
   .  "</div>\n";

// weekend
$o .= '<div>2) '
   .  tag('input type="text" class="color {pickerPosition:\'left\'}" value="'
   .  $weekendcolor . '" name="weekendcolor"')
   .  $plugin_tx['calendar']['config_weekend']
   .  "</div>\n";

// holiday
$o .= '<div>3) '
   .  tag('input type="text" class="color {pickerPosition:\'left\'}" value="'
   .  $holidaycolor . '" name="holidaycolor"')
   .  $plugin_tx['calendar']['config_holiday']
   .  "</div>\n";


// event day
$o .= '<div>4) '
   .  tag('input type="text" class="color {pickerPosition:\'left\'}" value="'
   .  $eventdaycolor . '" name="eventdaycolor"')
   .  tag('input type="text" class="color {pickerPosition:\'left\'}" value="'
   .  $eventdaybgcolor . '" name="eventdaybgcolor"')
   .  $plugin_tx['calendar']['config_eventday']
   .  ' '
   .  tag('input type="checkbox" '.$eventdayboldchecked.' value="1" name="eventdaybold"')
   .  $plugin_tx['calendar']['config_bold'] . ' '
   .  "</div>\n";


// longevent underline
$j=0;
$values_select = '';
for ($i = 0;$i <=4 ;$i++ ) {
	$selected = '';
	if($i.'px' == $underlongwidth) {$selected = ' selected'; $j = 1;}
	$values_select .= "\n\t".'<option value="'.$i.'px"'. $selected.'>'.$i.'px</option>';
}
// in case the css file contains a value outside the given range, this value will be shown
$preselect = $j? '' : '<option value="'.$underlongwidth.'" selected>' . $underlongwidth . '</option>';

$o .= '<div>6) '
   .  tag('input type="text" class="color {pickerPosition:\'left\'}" value="'
   .  $underlongcolor . '" name="underlongcolor"')
   .  '<select name="underlongwidth">'
   .  $preselect . $values_select
   .  '</select>'
   .  sprintf($plugin_tx['calendar']['config_underline_longevent']
   ,  tag('input type="checkbox" '.$underlongeventchecked.' value="1" name="underlongevent"'))
   .  "</div>\n";


// alternative marking "mark2"
$j=0;
$values_select = '';
for ($i = 0;$i <=4 ;$i++ ) {
	$selected = '';
	if($i.'px' == $mark2width) {$selected = ' selected'; $j = 1;}
	$values_select .= "\n\t".'<option value="'.$i.'px"'. $selected.'>'.$i.'px</option>';
}
// in case the css file contains a value outside the given range, this value will be shown
$preselect = $j? '' : '<option value="'.$mark2width.'" selected>' . $mark2width . '</option>';

$o .= '<div>5) '
   .  tag('input type="text" class="color {pickerPosition:\'left\'}" value="'
   .  $mark2color . '" name="mark2color"')
   .  '<select name="mark2width">'
   .  $preselect . $values_select
   .  '</select>'
   .  $plugin_tx['calendar']['config_mark2']
   .  "</div>\n";


// birthday
$handle=opendir($relativePath);
$images = array();
while (false !== ($file = readdir($handle))) {
	if(substr($file,-3) == 'gif') {
		$images[] = $file;
		}
	}
closedir($handle);
natcasesort($images);
$images_select = '';
foreach($images as $file){
	$selected = '';
	if($birthdayimage == $file) {$selected = ' selected';}
	$images_select .= "\n<option value=$file$selected>$file</option>";
}
$o .= '<div>7) '
   .  "<select name='birthdayimage'>"
   .  "\n" . '<option value="">' . $plugin_tx['calendar']['config_no_image'] . '</option>'
   .  "\n" . $images_select
   .  '</select>'
   .  $plugin_tx['calendar']['config_birthday_image']
   .  "</div>\n";



// today
$o .= '<div>8) '
   .  tag('input type="text" class="color {pickerPosition:\'left\'}" value="'
   .  $todaycolor . '" name="todaycolor"')
   .  tag('input type="text" class="color {pickerPosition:\'left\'}" value="'
   .  $todaybgcolor . '" name="todaybgcolor"')
   .  $plugin_tx['calendar']['config_today_background']
   .  ' '
   .  tag('input type="checkbox" '.$todayboldchecked.' value="1" name="todaybold"')
   .  $plugin_tx['calendar']['config_bold'] . ' '
   .  "</div>\n";







$o .= '</div>';



//====================================
//************************************
//
//       pop-up settings
//
//************************************
//====================================

$hide = $calendar_cf['show_popup_config']? '':' style="display:none;"';
$o .= "<div id='popup'$hide>\n";
$o .= '<div class="cal_separator"></div>';

//headline
$o .= '<div style="height:auto;color:black;font-size:100%;"><h4 style="margin:0;padding:0 0 3px;">'
   .  $plugin_tx['calendar']['config_popup']
   .  '</h4></div>';

// font
$o .= '<div>'.selectFont('popupfont',$popupfont,'popupfontsize',$popupfontsize)."</div>\n";;

//bordercolor
$o .= '<div>'
   .  $plugin_tx['calendar']['config_border_color'] . ': '
   .  tag('input type="text" class="color" value="'
   .  $popupbordercolor . '" name="popupbordercolor"')
   .  "</div>\n";

//borderwidth
$j=0;
$popupborderwidth_select = '';
for ($i = 0;$i <=7 ;$i++ ) {
	$selected = '';
	if($i.'px' == $popupborderwidth) {$selected = ' selected'; $j = 1;}
	$popupborderwidth_select .= "\n\t".'<option value="'.$i.'px"'. $selected.'>'.$i.'px</option>';
}
// in case the css file contains a value outside the given range, this value will be shown
$preselect = $j? '' : '<option value="borderwidth" selected>' . $popupborderwidth . '</option>';

$o .= '<div>'
   .  $plugin_tx['calendar']['config_border_width'] . ': '
   .  "\n" . '<select name="popupborderwidth">'
   .  "\n" . $preselect . $popupborderwidth_select
   .  "\n</select></div>\n";


//backgroundcolor
$o .= '<div>'
   .  $plugin_tx['calendar']['config_background_color'] . ': '
   .  tag('input type="text" class="color" value="'
   .  $popupbackground . '" name="popupbackground"')
   .  "</div>\n";




//popup in small calendar
$o .= '<div>'
   .  tag('input type="checkbox" '.$smallcalpopupchecked.' value="1" name="smallcalpopup"')
   .  $plugin_tx['calendar']['config_popup_in_small_calendar']
   .  "</div>\n";

// entry3 in title attribute popup or not?
$o .= '<div>'
   .  $plugin_tx['calendar']['config_in_title_label']
   .  tag('input type="checkbox" '.$entry3popupchecked.' value="1" name="entry3popup"')
   .  $plugin_tx['calendar']['event_entry3']
   .  "</div>\n";



$o .= '</div>';




//==============
// save 
//==============
if($calendar_cf['show_popup_config'] || $calendar_cf['show_smallcalendar_config'] || $calendar_cf['show_bigcalendar_config']) {
    $o .= '<div style="float:right; margin-bottom:.5em;">'
       .  tag('input type="submit"   value="'.$plugin_tx['calendar']['menu_save_config'].'"')
       .  "</div>\n";
}


$o .=  "</form>";
