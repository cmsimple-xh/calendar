<?php
// CMSimple Calendar Plugin, v. 1.4. See version_history.txt for details


// Security check
if ((!function_exists('sv')) || preg_match('!calendar/index.php!i', sv('PHP_SELF')))die('Access denied');

// checking if alternative filepath is wanted
if (!$plugin_cf['calendar']['filepath_data']){$datapath = $pth['folder']['plugins'].$plugin."/content/";}
else {$datapath = $plugin_cf['calendar']['filepath_data'];}

// checking if language specific files are wanted or not
if ($plugin_cf['calendar']['same-event-calendar_for_all_subsites']) {$lang="";} else $lang = "_".$sl;

//load config2 values
include ($pth['folder']['plugins'].'calendar/config/config2.php');

if (version_compare(CMSIMPLE_XH_VERSION,'CMSimple_XH 1.5',"<")) {
    //loading default language files (not necessary for CMSimple 1.5 onwards)
    include ($pth['folder']['plugins'].'calendar/languages/default.php');
    include ($pth['folder']['plugins']."calendar/languages/$sl.php");
    //choosing fckeditor as calendar editor
    $calendareditor = $calendar_cf['show_event_description']? 'fckeditor' : '';
} else {
    //from CMSimple 1.5 on taking the standard editor as calendar editor
    $calendareditor = $calendar_cf['show_event_description']? $cf['editor']['external']:'';
}
    //overwriting the calendar editor if wanted
    $calendareditor = ($plugin_cf['calendar']['editor'] && $calendar_cf['show_event_description'])? $plugin_cf['calendar']['editor']:$calendareditor;


//if php 4 is used, this function has to be supplied (by cmb)
if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $data) {
        $f = @fopen($filename, 'w');
        if (!$f) {
            return false;
        } else {
        if (is_array($data)) {$data = implode('', $data);} 
            $bytes = fwrite($f, $data);
            fclose($f);
            return $bytes;
        }
    }
}



// *******************************************************************************************************
// *                                                                                                     *
// *                                         Building the Calendar                                       *
// *                                                                                                     *
// *******************************************************************************************************

//===================================================================
// HELPER FUNCTION to parse the date-separator, set in plugin-config,
// to build the config-string for the Date-Picker and to
// check for allowed seperators
// Allowed separators:
// full-stop ".", forward slash "/" and minus/dash "-"
//===================================================================
function dpSeperator($mode='') {

    global $plugin_cf;

    $sep = $plugin_cf['calendar']['date_delimiter'];
    $dp_sep = ''; //the string to configure the DatePicker

    if ($sep != '/' && $sep != '-') {
        $sep = '.'; //set default
    }

    switch ($sep) {
    case '.':
        $dp_sep = 'dt';
        break;
    case '/':
        $dp_sep = 'sl';
        break;
    case '-':
        $dp_sep = 'ds';
        break;
    }

    if ($mode == 'dp') {
        return $dp_sep;
    }
    else {
        return $sep;
    }
}

//==================================================================
//      displaying a calendar on the template or on a page
//==================================================================
function calendar($year='',$month='',$specialeventpage='',$size='',$number='',$columns='',$file='',$addfile='',$useeventpage2='')
{
    global $pth,$plugin;
    $plugin=basename(dirname(__FILE__),"/");
    include ($pth['folder']['plugins'].'calendar/includes/calendar.php');
    return $o;
}
function bigcalendar()
{
    return calendar('','','','big');
}
function multicalendar($number=2,$columns='',$file='',$addfile='')
{
    return calendar('','','','',$number,$columns,$file,$addfile);
}
function year($year='',$columns=3,$file='',$addfile='',$specialeventpage='')
{
    return calendar($year,1,$specialeventpage,'',12,$columns,$file,$addfile);
}

function calendar2($year='',$month='',$specialeventpage='',$size='',$number='',$columns='')
{
    global $plugin_cf;
    return calendar($year,$month,$specialeventpage,$size,$number,$columns,$plugin_cf['calendar']['second-calendar_filename'],$addfile='',$useeventpage2=1);
}
function calendar12($year='',$month='',$specialeventpage='',$size='',$number='',$columns='')
{
    global $plugin_cf;
    return calendar($year,$month,$specialeventpage,$size,$number,$columns,'',$addfile=$plugin_cf['calendar']['second-calendar_filename']);
}
function bigcalendar2()
{
    global $plugin_cf;
    return calendar('','','','big','','',$plugin_cf['calendar']['second-calendar_filename'],'',$useeventpage2=1);
}
function bigcalendar12()
{
    global $plugin_cf;
    return calendar('','','','big','','','',$plugin_cf['calendar']['second-calendar_filename']);
}
function multicalendar2($number=2,$columns='')
{
    global $plugin_cf;
    return calendar('','','','',$number,$columns,$plugin_cf['calendar']['second-calendar_filename'],'',$useeventpage2=1);
}
function multicalendar12($number=2,$columns='')
{
    global $plugin_cf;
    return calendar('','','','',$number,$columns,'',$plugin_cf['calendar']['second-calendar_filename']);
}
function year2($year='',$columns=3)
{
    global $plugin_cf;
    return calendar($year,1,'','',12,$columns,$plugin_cf['calendar']['second-calendar_filename'],'',$useeventpage2=1);
}
function year12($year='',$columns=3)
{
    global $plugin_cf;
    return calendar($year,1,'','',12,'',$columns,$plugin_cf['calendar']['second-calendar_filename']);
}

// ********************************************************************************************************
// *                                                                                                      *
// *                                         Showing the Event List                                       *
// *                                                                                                      *
// ********************************************************************************************************

//======================================
// HELPER FUNCTION
// select the chosen icons (normal color or pale color for past events) and detect their width & height
//======================================
function icon($which_icon,$pale='')
{
    global  $plugin_cf,$calendar_cf,$plugin_tx,$pth,$plugin;
    $iconset = $calendar_cf['icon-set'];
    if ($pale) $iconset = $iconset."past";
    $iconFolder = $pth['folder']['plugins'] . $plugin . "/images/" . $iconset;
    $selected_icon = "";
    switch (substr($which_icon,0,3)) {
        case 'inf':
            $fileextension = file_exists ("$iconFolder/info.gif") ? "gif" : "png";
            list($width, $height, $type, $attr) = getimagesize("$iconFolder/info.$fileextension");
            $selected_icon = tag("img src='$iconFolder/info.$fileextension' $attr alt='Info'");
            break;
        case 'ext':
            $fileextension = file_exists ("$iconFolder/external_link.gif") ? "gif" : "png";
            list($width, $height, $type, $attr) = getimagesize("$iconFolder/external_link.$fileextension");
            $selected_icon = tag("img src='$iconFolder/external_link.$fileextension' $attr alt='external Link'");
            break;
        case 'int':
        case 'in?':
            $fileextension = file_exists ("$iconFolder/internal_link.gif") ? "gif" : "png";
            list($width, $height, $type, $attr) = getimagesize("$iconFolder/internal_link.$fileextension");
            $selected_icon = tag("img src='$iconFolder/internal_link.$fileextension' $attr alt='internal Link'");
            break;
        case 'pdf':
        case 'pfx':
            if ($pale) {
            $selected_icon = tag("img src='".$pth['folder']['plugins'] . $plugin . "/images/pdf_past.gif'  width='18' height='16' alt='PDF Document'");
            } else {
            $selected_icon = tag("img src='".$pth['folder']['plugins'] . $plugin . "/images/pdf.gif'  width='18' height='16' alt='PDF Document'");
            }
            break;
        case 'doc':
            if ($pale) {
            $selected_icon = tag("img src='".$pth['folder']['plugins'] . $plugin . "/images/word_past.gif'  width='18' height='16' alt='Word Document'");
            } else {
            $selected_icon = tag("img src='".$pth['folder']['plugins'] . $plugin . "/images/word.gif'  width='18' height='16' alt='Word Document'");
            }

            break;
    }
    return $selected_icon;
}

//=====================================================
//   MAIN FUNCTION
//   displaying the   E V E N T L I S T    on a page
//=====================================================

function events($month='',$year='',$end_month='',$past_month='',$file='',$addfile='',$style='',$pastevents='')
{
    global $pth,$plugin;
    $plugin=basename(dirname(__FILE__),"/");$o='';
    include ($pth['folder']['plugins'].'calendar/includes/eventlist.php');
    return $o;
}
function events2($month='',$year='',$end_month='',$past_month='')
{
    global $plugin_cf;
    return events($month,$year,$end_month,$past_month,$plugin_cf['calendar']['second-calendar_filename']);
}
function events12($month='',$year='',$end_month='',$past_month='')
{
    global $plugin_cf;
    return events($month,$year,$end_month,$past_month,'',$plugin_cf['calendar']['second-calendar_filename']);
}


//=====================================================
//   Marquee display of the  N E X T   E V E N T (S)
//=====================================================
// BEGIN CMB
// modificaton of the header must not happen inside nextevent(),
// because if that is called from the template only,
// $hjs is already sent to the browser, so it is too late. :(

//checking if JQuery plugin is installed
if(file_exists($pth['folder']['plugins'].'jquery/jquery.inc.php') && $calendar_cf['marquee_in_jquery']) {
    include_once($pth['folder']['plugins'].'jquery/jquery.inc.php');
    include_jQuery();
    //include_jQueryUI();
    include_jQueryPlugin('marquee', $pth['folder']['plugins'].'calendar/includes/jquery.marquee.js');
//    $hjs .= "<script type='text/javascript'>
//    /* <![CDATA[ */
//        jQuery(function() {jQuery('div.calendar_marquee marquee').marquee()})
//    /* ]]> */
//    </script>\n";
}
// CMB END //erased calls which caused problems for the addition "stop on mouse over", svasti for calendar 1.4

function nextevent($number='',$file='',$addfile='',$checkcontents=''){
    global $pth,$plugin;
    $plugin=basename(dirname(__FILE__),"/");
    include ($pth['folder']['plugins'].'calendar/includes/nextevent.php');
    return $o;
}
function nextevent12() {
    global $plugin_cf;
    return nextevent('','',$plugin_cf['calendar']['second-calendar_filename']);
}

//================================================================================================//
// ********************************************************************************************** //
// *                                                                                            * //
// *                          B A C K E N D, editing the event file                             * //
// *                                                                                            * //
// ********************************************************************************************** //
//================================================================================================//


function eventForm($events,$saving_notice='',$file='',$standardmode,$calendar2='')
{
    global $pth; include_once ($pth['folder']['plugins'].'calendar/includes/eventform.php');
    return $o;
}
function editevents($file='',$standardmode=1,$calendar2='')
{
    global $pth; include_once ($pth['folder']['plugins'].'calendar/includes/editevents.php');
    return $o;
}
function editevents2()
{
    global $plugin_cf;
    return editevents($plugin_cf['calendar']['second-calendar_filename'],1,1);
}
function loadEventFile($file='')
{
    global $pth,$plugin;
    $plugin=basename(dirname(__FILE__),"/");
    include_once ($pth['folder']['plugins'].'calendar/includes/loadeventfile.php');
    return $event_array;
}
function saveEventFile($array,$file,$standardmode)
{
    global $pth,$plugin;
    $plugin=basename(dirname(__FILE__),"/");
    include_once ($pth['folder']['plugins'].'calendar/includes/saveeventfile.php');
    return $notice;
}

// preloading files for tinymce
if ($calendareditor =='tinymce'){
    include_once($pth['folder']['plugins'].'tinymce/links.php');
    include_once($pth['folder']['plugins'].'tinymce/init.php');
}

// preloading simple markup
include_once ($pth['folder']['plugins'].'calendar/includes/simplemarkup.php');


//=====================================
// finds internal URL of site pagenames
//=====================================
function pagenameToUrl($pagename)
{
   global $u, $cf;
    $urlarray = array();
    if(substr($pagename, 0,1)=='?' || substr($pagename, 0,1)=='/' || substr($pagename, 0,1)=='.') {
        if (!strpos($pagename,'%') && !preg_match('!&.+=!', $pagename)) {

            list($start,$end) = explode('?',$pagename,2);
            $end = str_replace('&', '&amp;',$end);
            $endarray = explode($cf['uri']['seperator'],$end);
            foreach ($endarray as $key=>$value) {
                $endarray[$key] = uenc($value);
            }
            $end = implode($cf['uri']['seperator'],$endarray);
            $pagename = $start.'?'.$end;
        }
        return $pagename;
    }

    $pagename = preg_replace('#&(?!amp;)#', '&amp;',$pagename);
    $pagename = uenc($pagename);
    for ($i = 0; $i < count ($u); $i++) {
        $name = '';
        $divider = strrpos($u[$i],$cf['uri']['seperator']) ? strrpos($u[$i],$cf['uri']['seperator'])+1 : 0;
        $name = substr($u[$i],$divider);
        // checking all pagenames in case a name occurs more than once
        if ($name == $pagename) array_push($urlarray, $u[$i]);
    }

    if (count($urlarray)==1) $url = $urlarray[0];
    // if the name occurs more than once, FALSE is returned
    if (count($urlarray)>1) $url = FALSE;
    // if the name doesn't occur, 0 is returned
    if (count($urlarray)==0) $url = 0;

    return $url;
}


//=========================================
// converts internal URL to simple pagename
//=========================================
function urlToPagename($url)
{
    global $cf;
    if(substr($url, 0,1)=='?' || substr($url, 0,1)=='/') {
        if (strpos($url,'%')!==false) {
            list($start,$end) = explode('?',$url,2);
            $end = urldecode($end);
            $url = $start.'?'.$end;
        }
    } else {
        $url = urldecode($url);
        $divider = strrpos($url,$cf['uri']['seperator']) ? strrpos($url,$cf['uri']['seperator'])+1 : 0;
        $url = substr($url,$divider);
    }
    $pagename = str_replace('_',' ',$url);
    return $pagename;
}


//===============================================
// Determines the link type: www, page, pdf, doc
//===============================================
function findLinkType($url)
{
    // checking does the name end in .pdf? -> external or internal downloads pdf link
    if(preg_match('!.+\..+\/.+\.pdf$!',$url)) return "pfx:"; elseif(preg_match('!\.pdf$!',$url)) return "pdf:";

    // checking does the name end in .doc or .docx?
    if(preg_match('!\.doc(x?)$!',$url)) return "doc:";

    // does the name start with "?"
    if(substr($url,0,1)=='?' || substr($url,0,1)=='/') return "in?:";

    // checking is the name a pagename?
    if(pagenameToUrl($url)) {
      $result = 'int:';
    // checking is the name a pagenam which occurs twice
    } elseif (pagenameToUrl($url)===FALSE) {
      $result = 'er2:' ;
    // checing if the name has characteristics of external URL
    } elseif (strstr($url,'.') && !strstr($url,' ')){
      $result = 'ext:' ;
    } else $result = 'err:';

    return $result;
}


//=======================================================================================
// Chronological sorting of events, function by manu, given via posting in CMSimple Forum
//=======================================================================================
function dateSort($a, $b){
    $pattern = '!(.*)\\'.dpSeperator().'(.*)\\'.dpSeperator().'(.*)!';
    $replace = '\3\2\1';
    $a_i = $a['weekday'] . preg_replace($pattern,$replace,$a['datestart']).$a['starttime'];
    $b_i = $b['weekday'] . preg_replace($pattern,$replace,$b['datestart']).$b['starttime'];
    if ($a_i == $b_i) return 0;
    return ($a_i < $b_i) ? -1 : 1;
}


?>
