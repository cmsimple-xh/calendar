<?php
/*
CMSimple - Calendar Plugin Admin
 - 1.4 rc some functions added to simplify configuration
 - 1.4 alpha by svasti (12/2011) new direct "eventlist configuration", "calendar configuration", "presets" etc.
 - 1.3 beta 9 by svasti (10/2011) new simplyfied menu, no standard plugin admin menu, no different input tables any more
 - 1.3 beta 5 expanded admin functions by svasti (8/2011)
   added additional menu with new functions:
   Test Event List, View the Input table in different widths, Backup menu, Credits with Version check
 - version 1.2 modified by Holger
 - version 1.1 by svasti 3/2011
 - version 0.9 Mod by Tory 15-01-2008
 - Modified by Bob (cmsimple.fr), 02/2008
 - originally by Michael Svarrer (versions up to 0.5).
*/

define('CALENDAR_VERSION', '1.4.6');

if ((!function_exists('sv')) || preg_match('!admin.php!i', sv('PHP_SELF')))die('Access denied');

ini_set('display_errors', 0);
error_reporting(0);
if(function_exists('xh_debugmode')){
	xh_debugmode();
}

initvar('calendar');
if($calendar){
    $plugin = basename(dirname(__FILE__),"/");

    //Helper-functions
    function calendar_getTemplates() {
        global $pth;
        $templates = glob($pth['folder']['plugins'] . 'calendar/templates/*.tpl');
        $options[] = '';
        foreach ($templates as $template) {
                $options[] = basename($template);
        }
        return $options;
    }


    //check permissions which are not checked by pluginloader
    if(!is_writable($pth['folder']['plugins'].$plugin.'/config/config2.php'))
    	e('cntwriteto', 'file',   $pth['folder']['plugins'].$plugin.'/config/config2.php');
    if(!is_writable($pth['folder']['plugins'].$plugin.'/languages/'.$sl.'.php'))
    	e('cntwriteto', 'file',   $pth['folder']['plugins'].$plugin.'/languages/'.$sl.'.php');
    if(!is_writable($pth['folder']['plugins'].$plugin.'/content/'))
    	e('cntwriteto', 'folder', $pth['folder']['plugins'].$plugin.'/content/');
    if(!is_writable($pth['folder']['plugins'].$plugin.'/templates/'))
    	e('cntwriteto', 'folder', $pth['folder']['plugins'].$plugin.'/templates/');


    $admxx = isset($_POST['admxx']) ? $_POST['admxx'] : $admxx = isset($_GET['admxx']) ? $_GET['admxx'] : '';
    $admin = isset($_POST['admin']) ? $_POST['admin'] : $admin = isset($_GET['admin']) ? $_GET['admin'] : '';
    $newfilename       = isset($_POST['newfilename'])   ? TRUE : FALSE ;
    $file              = isset($_POST['file'])          ? $_POST['file'] : '';


    // checking if language specific files are wanted or not
    if ($plugin_cf['calendar']['same-event-calendar_for_all_subsites']) {$lang="";} else $lang = "_".$sl;

    $eventfile = $datapath."eventcalendar$lang.txt";
    if(!is_file($eventfile)){$handle = fopen($eventfile, "w");
    fclose($handle);}


    //==================
    // standard menu
    //==================

    $plugin_main_on = $admxx=='plugin_main' || (!$admxx && !$admin) ?       ' class="selected"':'';
    $eventlist_on =   $admxx=='eventlist'?         ' class="selected"':'';
    $calendar_on =    $admxx=='calendar'?          ' class="selected"':'';
    $editevents2_on = $admxx=='editevents2'?       ' class="selected"':'';
    $eventlist2_on =  $admxx=='eventlist2'?        ' class="selected"':'';
    $calendar2_on =   $admxx=='calendar2'?         ' class="selected"':'';
    $marquee_on =     $admxx=='marquee'?           ' class="selected"':'';
    $holidays_on =    $admxx=='holidays'?          ' class="selected"':'';
    $backup_on =      $admxx=='backup'?            ' class="selected"':'';
    $config_on =      $admin=='plugin_config'?     ' class="selected"':'';
    $stylesheet_on =  $admin=='plugin_stylesheet'? ' class="selected"':'';
    $language_on =    $admin=='plugin_language'?   ' class="selected"':'';
    $credits_on =     $admxx=='credits'?           ' class="selected"':'';

    $o .= '<p class="calendar_admin_menu">'
       .  '<a'.$plugin_main_on.' href="?&amp;' . $plugin . '&amp;admxx=plugin_main" />' .$plugin_tx['calendar']['menu_main'].'</a>&nbsp; '
       .  '<a'.$eventlist_on  .' href="?&amp;' . $plugin . '&amp;admxx=eventlist" />' .$plugin_tx['calendar']['menu_test_event_list'].'</a>&nbsp; '
       .  '<a'.$calendar_on   .' href="?&amp;' . $plugin . '&amp;admxx=calendar" />' .$plugin_tx['calendar']['menu_show_calendar'].'</a>&nbsp; ';

    if ($plugin_cf['calendar']['second-calendar_filename']) {
    $o .= '<a'.$editevents2_on.' href="?&amp;' . $plugin . '&amp;admxx=editevents2" />2. ' .$plugin_tx['calendar']['menu_main'].'</a>&nbsp; '
       .  '<a'.$eventlist2_on .' href="?&amp;' . $plugin . '&amp;admxx=eventlist2" />2. ' .$plugin_tx['calendar']['menu_test_event_list'].'</a>&nbsp; '
       .  '<a'.$calendar2_on  .' href="?&amp;' . $plugin . '&amp;admxx=calendar2" />2. ' .$plugin_tx['calendar']['menu_show_calendar'].'</a>&nbsp; ';
    }

    $o .= '<a'.$marquee_on    .' href="?&amp;' . $plugin . '&amp;admxx=marquee" />' .$plugin_tx['calendar']['menu_marquee'].'</a>&nbsp; '
       .  '<a'.$holidays_on   .' href="?&amp;' . $plugin . '&amp;admxx=holidays" />' .$plugin_tx['calendar']['menu_holidays'].'</a>&nbsp; '
       .  '<a'.$backup_on     .' href="?&amp;' . $plugin . '&amp;admxx=backup" />' .$plugin_tx['calendar']['menu_backup'].'</a>&nbsp; '
       .  '<a'.$config_on     .' href="?&amp;' . $plugin . '&amp;admin=plugin_config&action=plugin_edit" />' .$plugin_tx['calendar']['menu_config'].'</a>&nbsp; '
       .  '<a'.$stylesheet_on .' href="?&amp;' . $plugin . '&amp;admin=plugin_stylesheet&action=plugin_text" />' .$plugin_tx['calendar']['menu_css'].'</a>&nbsp; '
       .  '<a'.$language_on   .' href="?&amp;' . $plugin . '&amp;admin=plugin_language&action=plugin_edit" />' .$plugin_tx['calendar']['menu_language'].'</a>&nbsp; '
       .  '<a                    href="'.        $pth['file']['plugin_help'] . '" target="_blank" />' .$plugin_tx['calendar']['menu_help'].'</a>&nbsp; '
       .  '<a'.$credits_on    .' href="?&amp;' . $plugin . '&amp;admxx=credits" />' .$plugin_tx['calendar']['menu_credits'].'</a></p>' . "\n";


    // enabling navigation to stylesheet, config, language
    if($admin){
        $o .= plugin_admin_common($action,$admin,$plugin);
        $hjs .= '<style type="text/css">
                .pluginedittable textarea.plugininput,
                .pluginedittable textarea.plugininputmax {
                    background:white;
                    color:black;
                    font-family: verdana;
                    padding-top:2px;
                    padding-bottom:0;
                    width:100%;
                }
                .pluginedittable textarea.plugininput {
                    height:1.8em;
                }
                .plugineditform textarea.plugintextarea {
                    height:50em;
                    background:white;
                    color:black;
                }
                td.plugincf {
                    width:40%;
                }
                td.plugincfcap {
                    width:100%;
                    font-size:120%;
                    color:#007;
                    padding-bottom:1.3em !important;
                }
                </style>'."\n";

    } elseif(!$admxx) $admxx='plugin_main';


    // ================
    // editing events
    // ================
    if($admxx=='plugin_main'){

        $o .= '<div style="float:right;font-size:80%;margin:-1em 0 0 0;">Calendar_XH '.constant('CALENDAR_VERSION').'</div>';
        $o .= '<h1>'. $plugin_tx['calendar']['menu_main']
           .  ' &nbsp; <span style="font-size:60%"> ';

        $o .= $plugin_tx['calendar']['config_presets'];
        if(isset($_POST['preset']) || isset($_POST['backup'])) {
            $o .=  tag('input type="button" value="&ndash;" class="calendar_edit_on" id="presets_button" style="margin-left:0;"
                   onclick=\'openedit("presets_button","presets","&ndash;","+","block");\'');
            $displaypresets = '';
        } else {
            $o .=  tag('input type="button" value="+" class="calendar_edit_off" id="presets_button" style="margin-left:0;"
                   onclick=\'openedit("presets_button","presets","&ndash;","+","block");\'');
            $displaypresets = 'style="display:none;"';
        }

        $o .=  ' &nbsp; '. $plugin_tx['calendar']['menu_eventlistconfig'];
        if(isset($_POST['config'])) {
            $o .=  tag('input type="button" value="&ndash;" class="calendar_edit_on" id="eventlistconfig_button" style="margin-left:0;"
                   onclick=\'openedit("eventlistconfig_button","eventlistconfig","&ndash;","+","block");\'');
            $displayeventlistconfig = '';
        } else {
            $o .=  tag('input type="button" value="+" class="calendar_edit_off" id="eventlistconfig_button" style="margin-left:0;"
                   onclick=\'openedit("eventlistconfig_button","eventlistconfig","&ndash;","+","block");\'');
            $displayeventlistconfig = 'style="display:none;"';
        }

        $o .= '</span></h1>' . "\n";
        $o .= "<div id='presets' $displaypresets>"
           .  presets('eventlist')
           .  "</div>\n";
        $o .= "<div id='eventlistconfig' $displayeventlistconfig>"
           .  eventlistConfig()
           .  "</div>\n";
        $o .= editevents();
    }
    if($admxx=='editevents2'){
        $o .= '<h1>'. $plugin_tx['calendar']['menu_main']
           .  ' 2 &nbsp;<small>'.$plugin_cf['calendar']['second-calendar_filename'] . '</small></h1>';
        $o .= editevents($plugin_cf['calendar']['second-calendar_filename'],1,1);
    }


    //***********************
    //  show the event list
    //***********************
    if ($admxx=='eventlist')  {
        $o .= eventlistconfigmenu();
        $o .= events('','','','','','',$calendar_cf['show_eventlist_style']);
    }
    if ($admxx=='eventlist2')  {
        $o .= eventlistconfigmenu();
        $o .= events('','','','',$plugin_cf['calendar']['second-calendar_filename'],'',$calendar_cf['show_eventlist_style']);
    }


    //*********************
    //  show the marquee
    //*********************
    if ($admxx=='marquee')  {
        $o .= '<h4>'.$plugin_tx['calendar']['menu_marquee'].'</h4>';
        $o .= marqueeConfig();
        $o .= nextevent();
        $o .= tag('br') . '<p>'.$plugin_tx['calendar']['marquee_contents'].': </p>';
        $o .= nextevent('','','',1);
    }


    //*********************
    //  show holiday wizard
    //*********************
    if ($admxx=='holidays')  {
        $o .= '<h4>'.$plugin_tx['calendar']['menu_holidays'].'</h4>';
        $o .= holidayConfig();
    }

    //**************************************************
    //  options and display of month and year calendar
    //**************************************************
    if ($admxx=='calendar')  {
        $o .= calendarconfigmenu();
        if($calendar_cf['show_year_in_backend']) {
            $o .= bigcalendar();
        } else {
            $o .= year();
        }
    }
    if ($admxx=='calendar2')  {
        $o .= calendarconfigmenu();
        if($calendar_cf['show_year_in_backend']) {
            $o .= bigcalendar2();
        } else {
            $o .= year2();
        }
    }


    //***************************************
    //    credits page with version check
    //***************************************
    if ($admxx=='credits') {
        $o .= '<h1>Calendar_XH '.constant('CALENDAR_VERSION')."</h1>\n";

        $o .= "<ul>\n"
           .  "<li>&copy; 2011-2013 by <a href='http://frankziesing.de/cmsimple/' target='_blank'>svasti</a>, based on"
           .  "<li>versions 1.2.1&ndash;1.2.9 and 1.3 beta 1&ndash;9 by svasti\n"
           .  "<li>version 1.2 &copy; 2011 by <a href='http://cmsimple.holgerirmler.de' target='_blank'>Holger</a></li>\n"
           .  "<li>version 1.1 (major update) by svasti</li>\n"
           .  "<li>version 1.0 by Tory</li>\n"
           .  "<li>mod 02/2008 by Bob (cmsimple.fr)</li>\n"
           .  "<li>versions 0.6&ndash;0.9 by Tory</li>\n"
           .  "<li>versions 0.1&ndash;0.5 by <a href='http://svarrer.dk' target='_blank'>Michael Svarrer</a>, who started the plugin in 2005.</li></ul>\n"
           .  '<p><small><b>Acknowledgements:</b>' . tag('br')
           .  'This version uses <a href="http://jscolor.com/" target="_blank">JSColor</a> by Jan Odvárko (CZ),'. tag('br')
           .  '<a href="http://frequency-decoder.com/" target="_blank">datePicker</a> by Brian McAllister (IR/FR),'. tag('br')
           .  'and was written using the <a href="http://www.pspad.com/en/" target="_blank">PSpad</a>-Editor of Jan Fiala (CZ).</small></p>'
           .  '<p><small>A big thank you to <a href="3-magi.net" target="_blank">cmb</a> (D) for valuable coding hints,' . tag('br')
           .  'and to oldnema (CZ) for repeated testing during development of the plugin.'
           .  '</small></p>'
           .  '<p>For bug reports and suggestions please use the <a href="http://www.cmsimpleforum.com/">CMSimple Forum</a></p>'
           .  "\n";
    }


    //******************************
    //   backup and file managing
    //******************************

    // here the user can look into all event files, merge and delete files, and save files under a new name
    //=====================================================================================================
    if (substr($admxx,0,6)=='backup') {

        // 3 choices; e = edit view, s = source code view, t = templates/presets
        // everything is there, but is rendered invisible by java script
        $view = substr($admxx,6,1);
        if($action=="delete")  $view='e';
        if($action=="delete1") $view='s';
        if($action=="delete2") $view='t';

        $returnview = isset($_POST['returnview'])? $_POST['returnview'] : '';
        $view = $returnview? $returnview : $view;

        $o .= '<h1>Calendar Backup &nbsp; <span style="font-size:60%">'
           .  $plugin_tx['calendar']['notice_backup_setting'] .': '
           .  $plugin_cf['calendar']['backups']
           .  "</span></h1>\n";

        // 3 buttons for selection: Open in edit view/ in source code view/ open template-files
        $o .= "<p><small>\n";
        $o .= "<input type='button' id='edit' class='";
        if(!$view || $view=='e') $o .= 'calendar_button_pressed'; else $o .= 'calendar_button';
        $o .= "' value='" . $plugin_tx['calendar']['backup-admin_open_in_edit_view']."' onclick=\"

                        document.getElementById('edit').className = 'calendar_button_pressed';
                        document.getElementById('source').className = 'calendar_button';
                        document.getElementById('preset').className = 'calendar_button';

                        document.getElementById('hidden_edit_view_links').style.display = 'block';
                        document.getElementById('hidden_source_view_links').style.display = 'none';
                        document.getElementById('hidden_preset_links').style.display = 'none';

                        document.getElementById('merge').className = 'calendar_button';
                        document.getElementById('hidden_merge_menu').style.display = 'none';
                        document.getElementById('merge').style.display = 'inline';
                        document.getElementById('merge').value = '".$plugin_tx['calendar']['backup-admin_merge_files']."';

                        document.getElementById('delete').style.display = 'inline';
                        document.getElementById('delete').className = 'calendar_button';
                        document.getElementById('hidden_delete_menu').style.display = 'none';

                        document.getElementById('delete1').style.display = 'none';
                        document.getElementById('hidden_delete1_menu').style.display = 'none';

                        document.getElementById('delete2').style.display = 'none';
                        document.getElementById('hidden_delete2_menu').style.display = 'none';

                        document.getElementById('notice').innerHTML = '';
                     \">";

        $o .= "<input type='button' id='source' class='";
        if($view=='s') $o .= 'calendar_button_pressed'; else $o .= 'calendar_button';
        $o .= "' value='" . $plugin_tx['calendar']['backup-admin_open_in_source_view']."' onclick=\"


                        document.getElementById('source').className = 'calendar_button_pressed';
                        document.getElementById('edit').className = 'calendar_button';
                        document.getElementById('preset').className = 'calendar_button';

                        document.getElementById('hidden_source_view_links').style.display = 'block';
                        document.getElementById('hidden_edit_view_links').style.display = 'none';
                        document.getElementById('hidden_preset_links').style.display = 'none';

                        document.getElementById('merge').className = 'calendar_button';
                        document.getElementById('hidden_merge_menu').style.display = 'none';
                        document.getElementById('merge').style.display = 'inline';
                        document.getElementById('merge').value = '".$plugin_tx['calendar']['backup-admin_merge_files']."';

                        document.getElementById('delete').style.display = 'none';
                        document.getElementById('hidden_delete_menu').style.display = 'none';

                        document.getElementById('delete1').style.display = 'inline';
                        document.getElementById('delete1').className = 'calendar_button';
                        document.getElementById('hidden_delete1_menu').style.display = 'none';

                        document.getElementById('delete2').style.display = 'none';
                        document.getElementById('hidden_delete2_menu').style.display = 'none';

                        document.getElementById('notice').innerHTML = '';
                        document.getElementById('returnfrommerge').value = 's';
                     \">";

        $o .= "<input type='button' id='preset' class='";
        if($view=='t') $o .= 'calendar_button_pressed'; else $o .= 'calendar_button';
        $o .= "' value='" . $plugin_tx['calendar']['backup-admin_presets']."' onclick=\"

                        document.getElementById('preset').className = 'calendar_button_pressed';
                        document.getElementById('edit').className = 'calendar_button';
                        document.getElementById('source').className = 'calendar_button';

                        document.getElementById('hidden_preset_links').style.display = 'block';
                        document.getElementById('hidden_edit_view_links').style.display = 'none';
                        document.getElementById('hidden_source_view_links').style.display = 'none';

                        document.getElementById('merge').className = 'calendar_button';
                        document.getElementById('hidden_merge_menu').style.display = 'none';

                        document.getElementById('merge').style.display = 'none';
                        document.getElementById('delete').style.display = 'none';
                        document.getElementById('delete1').style.display = 'none';
                        document.getElementById('hidden_delete_menu').style.display = 'none';
                        document.getElementById('hidden_delete1_menu').style.display = 'none';
                        document.getElementById('delete2').className = 'calendar_button';
                        document.getElementById('delete2').style.display = 'block';

                        document.getElementById('notice').innerHTML = '';
                   \">";

        $o .= tag('br') . $plugin_tx['calendar']['backup-admin_saving_hint'] . "\n</small></p>\n";

        // this listing disappears after actions. The actions call listFiles again to get an updated file list

        if(!isset($_POST['action']) && !$newfilename) $o .= listFiles();

        // forwarding the file for opening
        //================================
        $file_to_open = substr($admxx,7);
        if($file_to_open) {

            // 1st case: edit view chosen
            if ($view == "e") {
                if(!$newfilename)$o .= closeFileViewButton();
                $o .=  editevents($file_to_open,false);
            }

            // 2nd case: source view chosen
            if ($view == "s") {
                if(!isset($_POST['action'])) $o .= closeFileViewButton('s');
                $o .= sourceView($file_to_open);
            } 
            // 3nd case: templates chosen
            if ($view == "t") {
                if(!isset($_POST['action'])) $o .= closeFileViewButton('t');
                $o .= sourceView($file_to_open,1);
            } 

        } else {
        // nothing chosen: show 2 buttons for merging and deleting files
            $o .=  deleteAndCombine();
        }

        if($view=='s') $o .=  '<script type="text/javascript">
                            // <![CDATA[
               document.getElementById("hidden_edit_view_links").style.display = "none";
               document.getElementById("hidden_source_view_links").style.display = "block";
               document.getElementById("hidden_preset_links").style.display = "none";
               document.getElementById("delete").style.display = "none";
               document.getElementById("hidden_delete_menu").style.display = "none";
               document.getElementById("delete1").style.display = "inline";
                           // ]]>
                            </script>';
        if($view=='t') $o .=  '<script type="text/javascript">
                            // <![CDATA[
               document.getElementById("hidden_edit_view_links").style.display = "none";
               document.getElementById("hidden_source_view_links").style.display = "none";
               document.getElementById("hidden_preset_links").style.display = "block";

               document.getElementById("merge").style.display = "none";
               document.getElementById("delete").style.display = "none";
               document.getElementById("hidden_delete_menu").style.display = "none";
               document.getElementById("delete2").className = "calendar_button";
               document.getElementById("delete2").style.display = "block";

                           // ]]>
                            </script>';
    }
}
//==================================
//
//        F U N C T I O N S
//
//==================================


//*******************************************
//         configuration functions
//*******************************************
function presets($filetype)
{
    global $pth; include_once ($pth['folder']['plugins'].'calendar/includes/presets.php');
    return $o;
}
function eventlistConfig()
{
    global $pth; include_once ($pth['folder']['plugins'].'calendar/includes/eventlistconfig.php');
    return $o;
}
function calendarConfig()
{
    global $pth; include_once ($pth['folder']['plugins'].'calendar/includes/calendarconfig.php');
    return $o;
}
function marqueeConfig()
{
    global $pth; include_once ($pth['folder']['plugins'].'calendar/includes/marqueeconfig.php');
    return $o;
}
function holidayConfig()
{
    global $pth; include_once ($pth['folder']['plugins'].'calendar/includes/holidayconfig.php');
    return $o;
}


//*******************************************
//    function to change values in files
//*******************************************
function changevalue($valuearray,$file,$type=0)
{
    global $plugin_cf;

    foreach ($valuearray as $trigger=>$value) {
        if($type==1) {
            //config files, also language files
            $pattern = "!(".$trigger."']=\")(.*)\"!";
            $replacement ='${1}'.$value.'"';
        }

        if($type==2) {
            //language files with check if entry is missing and generation of missing entries
            $pattern = "!(".$trigger."']=\")(.*)\"!";
            $replacement ='${1}'.$value.'"';

            if (!preg_match($pattern,$file)) {
                $file = str_replace('?>',"\t".'$plugin_tx[\'calendar\'][\''.$trigger.'\']="";'."\n?>",$file);
            }
        }

        if(!$type) {
            //css files
            $special = ''; //for special cases
            if(strpos($trigger,',')) {
                list($trigger,$special) = explode(',',$trigger);
            }

            if($special=='sU') {
                $pattern = "!(\/\*".$trigger."\*\/)(.*)\}!sU";
                $replacement ='${1}'.$value.'}';

            } else $pattern = "!(\/\*".$trigger."\*\/)(.*);!";

            if($special=='!im') {
                $replacement ='${1}'.$value.' !important;';

            } elseif ($special=='url!' && $value) {
                $replacement ='${1} url(' . $plugin_cf['calendar']['filepath_calendar_background_images'] . $value .') !important;';

            } elseif ($special=='url' && $value) {
                $replacement ='${1} url(' . $plugin_cf['calendar']['filepath_calendar_background_images'] . $value .');';

            } elseif ($special!='sU') $replacement ='${1}'.$value.';';
        }

        $file = preg_replace($pattern,$replacement,$file);
    }

	return $file;
}


//==============================================
// little helper function of eventlist and
// calendar configuration,
// shows options menu of available templates
//==============================================
function templateSelect($cf_value,$name_postvar)
{
    global $pth,$plugin_tx;
    $o = '';

    // basic code taken from Martin Dampken's page_params
    // and modified by svasti
    $handle = opendir($pth['folder']['templates']);
    $templates = array();
    while(false !== ($file = readdir($handle))) {
    	if(is_dir($pth['folder']['templates'].$file) && strpos($file, '.') !== 0) {
    		$templates[] = $file;
    		}
    	}
    natcasesort($templates);
    $templates_select = '';
    foreach($templates as $file){
    	$selected = '';
    	if($cf_value && $file == $cf_value) {$selected = 'selected';}
    	$templates_select .= "\n\t".'<option value="'.$file.'"'. $selected.'>'.$file.'</option>';
    }
    $o .=  "<select name='$name_postvar'>"
       .   "\n" . '<option value=""' . $selected . '>' . $plugin_tx['calendar']['config_use_default_template'] . '</option>'
       .   "\n" . $templates_select
       .   "\n</select>";

    return $o;
}

//==============================================
// config menu on top of calendar
//==============================================
function calendarconfigmenu()
{
    global $pth,$plugin_tx,$plugin,$calendar_cf;
    $o = '';

    //settings for hiding or showing of configuration possibilities
    if(isset($_POST['hideconfig'])) {
        $configfile = file_get_contents($pth['folder']['plugins'] . $plugin . '/config/config2.php');
        if(isset($_POST['showbigcalendar']))   $configfile = preg_replace("!show_bigcalendar_config.*\"(.*)\"!","show_bigcalendar_config']=\"1\"",$configfile);
        if(isset($_POST['hidebigcalendar']))   $configfile = preg_replace("!show_bigcalendar_config.*\"(.*)\"!","show_bigcalendar_config']=\"\"",$configfile);
        if(isset($_POST['showsmallcalendar'])) $configfile = preg_replace("!show_smallcalendar_config.*\"(.*)\"!","show_smallcalendar_config']=\"1\"",$configfile);
        if(isset($_POST['hidesmallcalendar'])) $configfile = preg_replace("!show_smallcalendar_config.*\"(.*)\"!","show_smallcalendar_config']=\"\"",$configfile);
        if(isset($_POST['showpopupconfig']))   $configfile = preg_replace("!show_popup_config.*\"(.*)\"!","show_popup_config']=\"1\"",$configfile);
        if(isset($_POST['hidepopupconfig']))   $configfile = preg_replace("!show_popup_config.*\"(.*)\"!","show_popup_config']=\"\"",$configfile);
        if(isset($_POST['show_year_in_backend']))         $configfile = preg_replace("!show_year_in_backend.*\"(.*)\"!","show_year_in_backend']=\"1\"",$configfile);
        if(isset($_POST['show_month']))        $configfile = preg_replace("!show_year_in_backend.*\"(.*)\"!","show_year_in_backend']=\"\"",$configfile);
        unset(
            $_POST['showbigcalendar'],
            $_POST['hidebigcalendar'],
            $_POST['showsmallcalendar'],
            $_POST['hidesmallcalendar'],
            $_POST['showpopupconfig'],
            $_POST['hidepopupconfig'],
            $_POST['show_year_in_backend'],
            $_POST['show_month']
        );
        file_put_contents($pth['folder']['plugins'] . $plugin . '/config/config2.php',$configfile);
        include ($pth['folder']['plugins'] . $plugin .'/config/config2.php');
    }


    $o .=  "<form method='POST' style='margin:0;'  action=''>\n";
    $o .= tag('input type="hidden" value="1" name="hideconfig"') . "\n";

    $o .= '<p style="margin:0 0 6px;" >';

    $o .= $plugin_tx['calendar']['config_presets'];
    if(isset($_POST['preset']) || isset($_POST['backup'])) {
        $o .=  tag('input type="button" value="&ndash;" class="calendar_edit_on" id="presets_button" style="margin-left:0;"
               onclick=\'openedit("presets_button","presets","&ndash;","+","block");\'');
        $displaypresets = '';
    } else {
        $o .=  tag('input type="button" value="+" class="calendar_edit_off" id="presets_button" style="margin-left:0;"
               onclick=\'openedit("presets_button","presets","&ndash;","+","block");\'');
        $displaypresets = 'display:none;"';
    }

    $o .=  ' ';

    $o .=  $plugin_tx['calendar']['config_bigcalendar'];
    if($calendar_cf['show_bigcalendar_config']) {
        $o .=  tag('input type="submit" class="config_button_pressed" value="&ndash;" name="hidebigcalendar" ');
    } else $o .=  tag('input type="submit" class="config_button" value="+" name="showbigcalendar" ');


    $o .=  ' ';

    $o .=  $plugin_tx['calendar']['config_small_calendar'];
    if($calendar_cf['show_smallcalendar_config']) {
        $o .=  tag('input type="submit" class="config_button_pressed" value="&ndash;" name="hidesmallcalendar" ');
    } else $o .=  tag('input type="submit" class="config_button" value="+" name="showsmallcalendar" ');

    $o .=  ' ';

    $o .=  '<span class="nowrap">' . $plugin_tx['calendar']['config_popup'];
    if($calendar_cf['show_popup_config']) {
        $o .=  tag('input type="submit" class="config_button_pressed" value="&ndash;" name="hidepopupconfig" ');
    } else $o .=  tag('input type="submit" class="config_button" value="+" name="showpopupconfig" ');
    $o .= '</span>';

    $o .=  ' ';

    if($calendar_cf['show_year_in_backend']) {
        $o .=  tag('input type="submit"  style="width:auto;" value="'.$plugin_tx['calendar']['config_year'].'" name="show_month" ');
    } else $o .=  tag('input type="submit"  style="width:auto;" value="'.$plugin_tx['calendar']['config_month'].'" name="show_year_in_backend" ');


    $o .= "</p>\n";
    $o .= "</form>";

    $o .= "<div id='presets' style='padding-top:10px;$displaypresets'>"
       .  presets('calendar')
       .  "</div>\n";

    $o .= calendarConfig();

    return $o;
}

//==============================================
// config menu on top of eventlist
//==============================================
function eventlistconfigmenu()
{
    global $pth,$cf,$calendar_cf,$plugin_tx,$plugin,$datapath;
    $o = '';

    $style = isset($_POST['style']) ? $_POST['style'] : $calendar_cf['show_eventlist_style'];

    if(isset($_POST['style'])) {
        $configfile = file_get_contents($pth['folder']['plugins'] . $plugin . '/config/config2.php');
        $configfile = preg_replace("!show_eventlist_style.*\"(.*)\"!","show_eventlist_style']=\"$style\"",$configfile);
        file_put_contents($pth['folder']['plugins'] . '/calendar/config/config2.php',$configfile);
        include ($pth['folder']['plugins'] . '/calendar/config/config2.php');
    }

    // template change
    if ($calendar_cf['test_event_list_template']) {
    	$cf['site']['template']          = $calendar_cf['test_event_list_template'];
    	$pth['folder']['template']       = $pth['folder']['templates'].$cf['site']['template'].'/';
    	$pth['file']['template']         = $pth['folder']['template'].'template.htm';
    	$pth['file']['stylesheet']       = $pth['folder']['template'].'stylesheet.css';
    	$pth['folder']['menubuttons']    = $pth['folder']['template'].'menu/';
    	$pth['folder']['templateimages'] = $pth['folder']['template'].'images/';
    }
    $o .= "<form method='POST' style='margin:0;' action=''>\n";

    // list all availabel event-list templates
    $handle=opendir($pth['folder']['plugins'].'/calendar/templates/');
    $templates = array();
    while (false !== ($reading = readdir($handle))) {
    	if(strpos($reading, '.tpl')) {
    		$templates[] = $reading;
    	}
    }
    closedir($handle);
    natcasesort($templates);
    $styles_select = '';
    foreach($templates as $key=>$eventliststyle){
    	$selected = '';
    	if($eventliststyle == $calendar_cf['show_eventlist_style']) {$selected = ' selected';}
    	$styles_select .= "<option value='$eventliststyle'$selected>".substr($eventliststyle,0,-4)."</option>\n";
    }

    $standardselected = !$calendar_cf['show_eventlist_style']?  ' selected':'';
    $tableselected = $calendar_cf['show_eventlist_style'] == 1? ' selected':'';

//      $o .= tag('input type="submit"   value="'.$plugin_tx['calendar']['menu_test'].'"');
    $o .= "<select name='style' OnChange ='this.form.submit()'>"
       .  "\n" . "<option value=''$standardselected>" . $plugin_tx['calendar']['config_eventlist_standard'] . '</option>'
       .  "\n" . "<option value='1'$tableselected>" . $plugin_tx['calendar']['config_eventlist_table_selected'] . '</option>'
       .  "\n" . $styles_select
       .  "</select>\n";

    $o .= "</form>";

    return $o;
}

//==============================================
// little helper function of basic configuration
// shows the Icons used
//==============================================
function showIcons()
{
    global  $pth,$plugin,$plugin_tx;
    $path = $pth['folder']['plugins'] . '/calendar/images/';

    $icon_set_array = array();
    $icon_set       = '';
    $o              = '';

    $handle = opendir ($path);
    while (false !== ($icon_folder = readdir ($handle))) {
       if ($icon_folder != "." && $icon_folder != ".."  && is_dir($path . $icon_folder)) {

            $icon_set .= $icon_folder.": \n";
            $icon_array = array();
            $handle2 = opendir ($path.$icon_folder);
            while (false !== ($icon = readdir ($handle2))) {
                if ($icon != "." && $icon != "..") {
                    list($width, $height, $type, $attr) = getimagesize($path.$icon_folder."/".$icon);
                    array_push($icon_array, tag('img src="'.$path.$icon_folder.'/'.$icon.'" '.$attr.'') ." \n");
                }
            }
            closedir($handle2);
            rsort($icon_array);
            $icon_set .= implode (" ",$icon_array);
            array_push($icon_set_array,$icon_set);
            $icon_set = '';
        }
    }
    closedir($handle);
    //sorting necessary, otherwise on some servers the list will be disordered
    natsort($icon_set_array);
    foreach($icon_set_array as $key => $value){
        if (!strpos($value, "past")) {
            $o .= "Icon set $value\n" . tag('br') . tag ('br');
        } else {
            $o .= "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \n"
               .  preg_replace('!^.+(past:)!','',$value)
               . "\n" . tag('br') . tag ('br');
        }
    }
    //$o .= "(&rarr; ".$plugin_tx['calendar']['menu_config'].")";
    return $o;
}


//=============================================
// a little helper function of Backup function
// closees the shown file view
//=============================================
function closeFileViewButton($mode='')
{
    global $plugin_tx,$plugin;
    $o = '';
    $o .= '<input type="button" class="calendar_button" onclick="location.href=\'?&' . $plugin . '&admxx=backup'.$mode.'\'" value="'
    . $plugin_tx['calendar']['backup-admin_close_this_file_view'].'">';
    $o .= tag('br');
    return $o;
}


//*******************************************************
//
//   showing the list of files in calendar/content/ + calendar/templates/
//
//*******************************************************
function listFiles()
{
    global $pth,$plugin,$file,$datapath;
    $o = '';

    // 1st list for edit view
    //================================
    $o .= "<table id='hidden_edit_view_links' class='filelist'>\n";

    $handle=opendir ($datapath);
    while (false !== ($file = readdir ($handle))) {
        if ($file!='.' && $file!='..' ) {
            $o .= "<tr>\n<td><a href='?&$plugin&admxx=backupe$file'>$file</a> &nbsp; </td><td><small>"
               .  date ("d.m.Y \&\m\d\a\s\h\; H:i", filemtime($datapath.$file)) . "</small></td></tr>\n";
        }
    }
    closedir($handle);
    $o .= "</table>\n";

    // 2nd list for source code view
    //================================
    $o .= "<table id='hidden_source_view_links' class='filelist'>\n";
    $handle=opendir ($datapath);
    while (false !== ($file = readdir ($handle))) {
        if ($file!='.' && $file!='..') {
            $o .= "<tr>\n<td><a href='?&$plugin&admxx=backups$file'>$file</a> &nbsp; </td><td><small>"
               .  date ("d.m.Y \&\m\d\a\s\h\; H:i", filemtime($datapath.$file)) . "</small></td></tr>\n";
        }
    }
    closedir($handle);
    $o .= "</table>\n";

    // 3nd list for presets/templates
    //=================================
    $o .= "<table id='hidden_preset_links' class='filelist'>\n";
    $handle=opendir ($pth['folder']['plugins'].'/calendar/templates/');
    while (false !== ($file = readdir ($handle))) {
        if ($file!='.' && $file!='..') {
            list($f1,$f2) = explode ('.',$file,2);
            $f1array[] = $f1;
            $f2array[] = $f2;
        }
    }
    array_multisort($f2array,$f1array);
    foreach ($f2array as $key=>$value) {
        $templatefile =  $f1array[$key].'.'.$f2array[$key];
        $o .= "<tr>\n<td><a href='?&$plugin&admxx=backupt$templatefile'>$templatefile</a> &nbsp; </td><td><small>"
           .  date ("d.m.Y \&\m\d\a\s\h\; H:i", filemtime($pth['folder']['plugins'].'/calendar/templates/'.$templatefile)) . "</small></td></tr>\n";
     }
    closedir($handle);
    $o .= "</table>\n";

    return $o;
}



//*****************************************
//
//   showing and saving the source-code
//
//*****************************************
function sourceView($file,$template=0)
{
    global $plugin_tx,$pth,$plugin,$plugin_cf,$tx,$datapath;
    $notice = "";
    $name_error = FALSE;
    $o = '';

    $datapath2 = $template? $pth['folder']['plugins'].'/calendar/templates/' : $datapath;
    // saving function
    //===================================================
    $action = isset($_POST['action']) ? $_POST['action']  : '';
    $savedfile = isset($_POST['file']) ? $_POST['file']  : '';
    $data = isset($_POST['data']) ? $_POST['data']  : '';
    if(get_magic_quotes_gpc()) $data = stripslashes($data);
    $data = str_replace('¬','­',$data);

    // check if the new filename has unwanted characters
    if(preg_match('/[^(\w|.)]/',$savedfile)) {
        $notice .= "<span class='error'>"
        . $plugin_tx['calendar']['error_file_name_wrong'] . '</span>';
        $name_error = True;

    } else {
        if ($action == "savesource") {
            if(is_file($datapath2.$savedfile)) chmod($datapath2.$savedfile, 0666);
            $savedbytes = file_put_contents($datapath2.$savedfile,$data);
            if ($savedbytes!==FALSE) {
                $notice = "<span class='success'>$savedbytes "
                . $plugin_tx['calendar']['backup-admin_bytes_saved_in'] . " \"$savedfile\"</span>";
            } else {
                $notice = "<span class='error'>".$plugin_tx['calendar']['error_file_not_writable']."</span>";
            }
        }
    }

    // if the function performed an action, the file view has to be adjusted accordingly
    if($savedfile) $file = $savedfile;
    if(!$name_error) {$eventfile = file_get_contents($datapath2.$file);} else {$eventfile = $data;}

    // after actions: updating the file list
    if($action) {
        $o .= listFiles();
        $o .= $template? closeFileViewButton('t') : closeFileViewButton('s');
    }

    // This java script "presses the button" for source code or template view, so  that after an action you stay in the previous mode
    if(strpos($file,'.php') || strpos($file,'.tpl')) {
        $o .=  '<script type="text/javascript">
                            // <![CDATA[
               document.getElementById("edit").className = "calendar_button";
               document.getElementById("source").className = "calendar_button";
               document.getElementById("preset").className = "calendar_button_pressed";

               document.getElementById("hidden_edit_view_links").style.display = "none";
               document.getElementById("hidden_source_view_links").style.display = "none";
               document.getElementById("hidden_preset_links").style.display = "block";
                           // ]]>
                            </script>';
    } else {
        $o .=  '<script type="text/javascript">
                            // <![CDATA[
               document.getElementById("source").className = "calendar_button_pressed";
               document.getElementById("edit").className = "calendar_button";
               document.getElementById("preset").className = "calendar_button";

               document.getElementById("hidden_source_view_links").style.display = "block";
               document.getElementById("hidden_edit_view_links").style.display = "none";
               document.getElementById("hidden_preset_links").style.display = "none";
                           // ]]>
                            </script>';
    }

    // for bug hunting only
    //$o = "data: $data magic quotes:".get_magic_quotes_gpc() ;

    //===================================================
    //            displaying the source code
    //===================================================
    $o .= '<form method="POST" action="">';
    $o .= tag('input type="hidden" value="savesource" name="action"');
    $source_view_zindex = $plugin_cf['calendar']['z-index']? 2+$plugin_cf['calendar']['z-index']:2;
    $o .= '<table class="source_view" id="source_view" style="z-index:'.$source_view_zindex.'" >'."\n";
    $o .= '<tr>';
    $o .= '<td>' . tag('input class="submit" type="submit" value="' . ucfirst($tx['action']['save']).'" name="send"');
    $o .= tag('input type="text" value="'.$file.'" name="file" style="width:17em" class="filename"') . $notice;
    $o .= '</td></tr>';
    if(substr($file,-3)!='php' && substr($file,-3)!='tpl') {
        $o .= '<tr>';
        $o .= '<td class="filestructure">[*=' . $plugin_tx['calendar']['event_not_on_marquee'] . ']'
           . $plugin_tx['calendar']['event_start'].$plugin_tx['calendar']['event_date'] . '<span class="comma">,</span>'

           . '[*=' .$plugin_tx['calendar']['event_booked_out']. "]"
           . $plugin_tx['calendar']['event_end'].$plugin_tx['calendar']['event_date']   . '<span class="comma">,</span>'

           . '[*=' .$plugin_tx['calendar']['event_times_are_daily']. "]"
           . $plugin_tx['calendar']['event_end'].$plugin_tx['calendar']['event_time']   . '<span class="comma">,</span>'
           . $plugin_tx['calendar']['event_entry1']                                     . '<span class="comma">;</span>'

           . '[' . $plugin_tx['calendar']['event_additional'] . '|]'
           . $plugin_tx['calendar']['event_main_entry']                                 . '<span class="comma">;</span>'
           . '[###/#*#=' . $plugin_tx['calendar']['event_yearly']
           . '|***=' . $plugin_tx['calendar']['event_weekly']
           . '|*#*=' . $plugin_tx['calendar']['event_multiple']
           . '][' . $plugin_tx['calendar']['event_except'] . '|]'

           . $plugin_tx['calendar']['event_entry3']                                     . '<span class="comma">;</span>'
           . "[*=" .$plugin_tx['calendar']['event_icon']. "](ext:|int:)"
           . $plugin_tx['calendar']['hint_links']                                       . '<span class="comma">,</span>'
           . $plugin_tx['calendar']['event_link_text']                                  . '<span class="comma">,</span>'
           . "[*/+=" . $plugin_tx['calendar']['event_icon'] . "]"
           . $plugin_tx['calendar']['event_info_text']                                  . '<span class="comma">;</span>'
           . $plugin_tx['calendar']['event_start'].$plugin_tx['calendar']['event_time'] . '<span class="comma">;</span>'
           . $plugin_tx['calendar']['event_description'];
        $o .= '</td></tr>';
    }
    $eventfile = str_replace('­','¬',$eventfile);
    $o .= '<tr>';
    $o .= "<td><textarea  style='width:100%' name='data'>$eventfile</textarea></td>";
    $o .= '</tr>';
    $o .= '</table>';
    $o .= '</form>';
    $o .= '<script type="text/javascript">
                        // <![CDATA[
          var x = window.innerWidth;
           x = (x - 100);
           y = -x/2;
           document.getElementById("source_view").style.width = x + "px";
           document.getElementById("source_view").style.marginLeft = y + "px";
                       // ]]>
                        </script>';


return $o;
}


//*****************************************
//
//       deleting and merging files
//
//*****************************************
function deleteAndCombine()
{
    global $plugin_tx,$pth,$plugin,$datapath;
    $o      = '';
    $notice = '';

    if(isset($_POST['action']))
      $action = $_POST['action'];
    elseif(isset($_GET['action']))
      $action = $_GET['action'];
    else
      $action = "";

    // starting with the actions to be done

    if ($action == "") 	$notice =  "";

    // deleting files
    //=========================================================
    if ($action == "delete" || $action == "delete1"  || $action == "delete2" ) {

        $file = isset($_POST['file']) ? $_POST['file']  : $file;

        $datapath2 = $action == "delete2"? $pth['folder']['plugins'].'/calendar/templates/' : $datapath;

        if(is_file($datapath2.$file)) {
            $notice .= "$file <i>".$plugin_tx['calendar']['backup-admin_found']."</i>" . tag('br');

            chmod($datapath2.$file, 0666);
            $x = unlink($datapath2.$file);
            if($x===TRUE) {
                $notice .= "<span class='success'>$file <i>".$plugin_tx['calendar']['backup-admin_deleted']."</i></span>" . tag('br');

            } else $notice .= "<span class='error'>$file <i>".$plugin_tx['calendar']['backup-admin_not_deleted']."</i></span>" . tag('br');

         } else $notice .= "<span class='error'>$file <i>".$plugin_tx['calendar']['backup-admin_not_found']."</i></span>" . tag('br');

    $o .= listFiles();

    }

    // merging files
    //==========================================================
    if ($action == "merge" || $action == "merge1") {

        $firstfile = isset($_POST['firstfile']) ? $_POST['firstfile']  : '';
        $secondfile = isset($_POST['secondfile']) ? $_POST['secondfile']  : '';
        $f = 0;
        $s = 0;

        if(is_file($datapath.$firstfile)) {
            $firstfilearray = file($datapath.$firstfile,FILE_SKIP_EMPTY_LINES);
            $f = count($firstfilearray);
            // checking the version of the event file
            if (isset($firstfilearray[0]) && substr($firstfilearray[0],0,19)=="Calendar eventfile ") {
                $version1 = substr($firstfilearray[0],19,3);print_r($firstfilearray);
                array_shift($firstfilearray);print_r($firstfilearray);
                $f--;
            } else {
                $version1 = FALSE;
            }
            $notice .= "<b>$firstfile </b><i>".$plugin_tx['calendar']['backup-admin_found_with']." $f "
                    . $plugin_tx['calendar']['backup-admin_events']."</i>" . tag('br');
         } else $notice .= "<span class='error'>".$plugin_tx['calendar']['backup-admin_1st_file']
                        .  " $firstfile <i>".$plugin_tx['calendar']['backup-admin_not_found']."</i></span>" . tag('br');


        if(is_file($datapath.$secondfile)) {
            chmod($datapath.$secondfile, 0666);
            $secondfilearray = file($datapath.$secondfile,FILE_SKIP_EMPTY_LINES);
            $s = count($secondfilearray);
            if(!strpos("\n",$secondfilearray[($s-1)])) $secondfilearray[($s-1)] .= "\n";
            // checking the version of the event file
            if (isset($secondfilearray[0]) && substr($secondfilearray[0],0,19)=="Calendar eventfile ") {
                $version2 = substr($secondfilearray[0],19,3);print_r($secondfilearray);
                $s--;
            } else {
                $version2 = FALSE;
            }
            $notice .= "<b>$secondfile </b><i>".$plugin_tx['calendar']['backup-admin_found_with']." $s "
                    . $plugin_tx['calendar']['backup-admin_events']."</i>" . tag('br');
         } else $notice .= "<span class='error'>".$plugin_tx['calendar']['backup-admin_2nd_file']
                        .  " $secondfile <i>".$plugin_tx['calendar']['backup-admin_not_found']."</i></span>" . tag('br');

        if($f && $s && $version1==$version2) {
            // merge the file arrays
            $secondfilearray = array_merge($secondfilearray,$firstfilearray);
            // remove duplicates
            $secondfilearray = array_unique($secondfilearray);
            // count number of events in resulting file
            $sf = count($secondfilearray);
            if($version2) $sf--;print_r($secondfilearray);
            if (($f + $s) > $sf) $notice .= "<i>".(($f + $s) - $sf) . " ".$plugin_tx['calendar']['backup-admin_duplicates_deleted']."</i>". tag('br');
            // save the resulting file or give an error notice
            $success = file_put_contents($datapath.$secondfile,$secondfilearray);
            if ($success!==false) {
                $notice .= "<span class='success'>$secondfile <i>". $plugin_tx['calendar']['backup-admin_saved_with']
                        . "</i> $sf <i>".$plugin_tx['calendar']['backup-admin_events'].'</i></span>'. tag('br'). tag('br');
            } else $notice .= "<span class='error'>ERROR: could not save $secondfile</span>". tag('br'). tag('br');
        } elseif($f && $s && $version1!=$version2) {
            $notice .= '<span class="error">'
                    .  $plugin_tx['calendar']['error_cant_combine_eventfiles_of_different_versions']
                    .  '</span>'
                    .  tag('br'). tag('br');
        } else $notice .= '<span class="error">'.$plugin_tx['calendar']['backup-admin_no_merge'].'</span>'. tag('br'). tag('br');

    $o .= listFiles();

    }

    //"merge files" button switching the following dialogue on and off
    //==========================================================================================================
	$o .= "<input type='button' id='merge' value='".$plugin_tx['calendar']['backup-admin_merge_files']."' class='calendar_button'
           onclick=\"
           if (document.getElementById('hidden_merge_menu').style.display != 'block') {
           document.getElementById('merge').value = '".$plugin_tx['calendar']['backup-admin_close_merge_dialog']."';
           document.getElementById('hidden_merge_menu').style.display = 'block';
           document.getElementById('notice').innerHTML = '';

           document.getElementById('edit').className = 'calendar_button';
           document.getElementById('hidden_edit_view_links').style.display = 'none';

           document.getElementById('source').className = 'calendar_button';
           document.getElementById('hidden_source_view_links').style.display = 'none';

           document.getElementById('preset').className = 'calendar_button';
           document.getElementById('hidden_preset_links').style.display = 'none';

           } else {
           document.getElementById('merge').className = 'calendar_button';
           document.getElementById('merge').value = '".$plugin_tx['calendar']['backup-admin_merge_files']."';
           document.getElementById('hidden_merge_menu').style.display = 'none';

           document.getElementById('edit').className = 'calendar_button_pressed';
           document.getElementById('hidden_edit_view_links').style.display = 'block';

           document.getElementById('preset').className = 'calendar_button';
           document.getElementById('hidden_preset_links').style.display = 'none';

           }\">"
        . "<div id='hidden_merge_menu'><small>".$plugin_tx['calendar']['backup-admin_merge_instruction'] ."</small>". tag('br') ."\n"
        . "<form method='POST' action=''>"
        . "<table class='filelist'><tr><td>";

    // starting the merge dialogue
    // make a list of files that can be chosen as initial files of the merging process
    $handle=opendir ($datapath);
    while (false !== ($files = readdir ($handle))) {
        if ($files != "." && $files != ".." && substr($files,-3)!='php') {
            $o .= "<input type='radio' style='vertical-align: bottom;' name='firstfile' value='$files'>$files" . tag('br') ."\n";
        }
    }
    closedir($handle);
    // display a big arrow
    $o .= "</td><td style='font-size:200%'>&nbsp; &rarr; &nbsp;</td><td>";

    // again a list of files that can be chosen receiving files in the merging process
    $handle=opendir ($datapath);
    while (false !== ($files = readdir ($handle))) {
        if ($files != "." && $files != ".." && substr($files,-3)!='php') {
            $o .= "<input type='radio' style='vertical-align: bottom;' name='secondfile' value='$files'>$files" . tag('br') ."\n";
        }
    }
    closedir($handle);
    $o .= "</td></tr></table>"
        . "<input type='hidden' value='merge' name='action'>"
        . "<input type='hidden' id='returnfrommerge' value='' name='returnview'>"
        . "<input type='submit' value='".$plugin_tx['calendar']['backup-admin_merge']
        . "' style='color:#060;font-weight:bold;' class='calendar_button' name='send'>"
        . "</form>" . tag('br') . "\n</div>";
    // end of merge dialogue


    //"delete files" (edit-mode) button switching the following dialogue on and off (button alway visible))
    //===========================================================================================================
	$o .= "<input type='button' id='delete' class='calendar_button' value='".$plugin_tx['calendar']['backup-admin_delete_files']."'
           onclick=\"
           if (document.getElementById('delete').className == 'calendar_button') {

           document.getElementById('delete').className = 'calendar_button_pressed';
           document.getElementById('hidden_delete_menu').style.display = 'block';
           document.getElementById('notice').innerHTML = '';

           } else {

           document.getElementById('delete').className = 'calendar_button';
           document.getElementById('hidden_delete_menu').style.display = 'none';

           }\">";

    //"delete files" (source mode) button
    //===========================================================================================================
	$o .= "<input type='button' id='delete1' class='calendar_button' style='display:none' value='".$plugin_tx['calendar']['backup-admin_delete_files']."'
           onclick=\"
           if (document.getElementById('delete1').className == 'calendar_button') {

           document.getElementById('delete1').className = 'calendar_button_pressed';
           document.getElementById('hidden_delete1_menu').style.display = 'block';
           document.getElementById('notice').innerHTML = '';

           } else {

           document.getElementById('delete1').className = 'calendar_button';
           document.getElementById('hidden_delete1_menu').style.display = 'none';

           }\">";

    $handle=opendir($datapath);
    $filearray = array();
    while (false !== ($file = readdir($handle))) {
    	if($file != "." && $file != "..") {
    		$filearray[] = $file;
    		}
    	}
    closedir($handle);
    natcasesort($filearray);
    $files_select = '';
    foreach($filearray as $file){
    	$files_select .= "\n<option value=$file>$file</option>";
    }

    //div controlled by delete button (edit mode)
    $o .= "<div id='hidden_delete_menu'><small>".$plugin_tx['calendar']['backup-admin_delete_instruction'] ."</small>". tag('br') ."\n"
        . "<form method='POST' action=''>";
    $o .= '<div>'
       .  "<select name='file'>"
       .  "\n" . '<option value="">' . $plugin_tx['calendar']['config_select'] . '</option>'
       .  "\n" . $files_select
       .  "\n</select>";
    $o .= "</div>\n";
    $o .= "<input type='hidden' value='delete' name='action'>\n"
        . "<input type='submit' value='".$plugin_tx['calendar']['backup-admin_delete']
        . "' style='color:red;font-weight:bold;' class='calendar_button' name='send'>"
        . "</form>\n</div>\n\n";

    //div controlled by delete button (source mode)
    $o .= "<div id='hidden_delete1_menu' style='display:none'><small>".$plugin_tx['calendar']['backup-admin_delete_instruction'] ."</small>". tag('br') ."\n"
        . "<form method='POST' action=''>\n";
    $o .= '<div>'
       .  "<select name='file'>"
       .  "\n" . '<option value="">' . $plugin_tx['calendar']['config_select'] . '</option>'
       .  "\n" . $files_select
       .  "\n</select>";
    $o .= "</div>\n";
    $o .= "<input type='hidden' value='delete1' name='action'>\n"
        . "<input type='submit' value='".$plugin_tx['calendar']['backup-admin_delete']
        . "' style='color:red;font-weight:bold;' class='calendar_button' name='send'>"
        . "</form>\n</div>\n\n";


    //"delete templates" button switching the following dialogue on and off (button always visible))
    //===========================================================================================================
	$o .= "<input type='button' id='delete2' class='calendar_button' style='display:none' value='".$plugin_tx['calendar']['backup-admin_delete_files']."'
           onclick=\"
           if (document.getElementById('delete2').className == 'calendar_button') {

           document.getElementById('delete2').className = 'calendar_button_pressed';
           document.getElementById('hidden_delete2_menu').style.display = 'block';
           document.getElementById('notice').innerHTML = '';

           } else {

           document.getElementById('delete2').className = 'calendar_button';
           document.getElementById('hidden_delete2_menu').style.display = 'none';

           }\">"
        . "<div id='hidden_delete2_menu' style='display:none'><small>".$plugin_tx['calendar']['backup-admin_delete_instruction'] ."</small>". tag('br') ."\n"
        . "<form method='POST' action=''>\n";

    $handle=opendir ($pth['folder']['plugins'].'/calendar/templates/');
    while (false !== ($file = readdir ($handle))) {
        if ($file!='.' && $file!='..') {
            list($f1,$f2) = explode ('.',$file,2);
            $f1array[] = $f1;
            $f2array[] = $f2;
        }
    }
    closedir($handle);
    array_multisort($f2array,$f1array);
    $files_select = '';
    foreach ($f2array as $key=>$value) {
        $templatefile =  $f1array[$key].'.'.$f2array[$key];
    	$files_select .= "\n<option value=$templatefile>$templatefile</option>";
    }
    $o .= '<div>'
       .  "<select name='file'>"
       .  "\n" . '<option value="">' . $plugin_tx['calendar']['config_select'] . '</option>'
       .  "\n" . $files_select
       .  "\n</select>";
    $o .= "</div>\n";

    $o .= "<input type='hidden' value='delete2' name='action'>\n"
        . "<input type='submit' value='".$plugin_tx['calendar']['backup-admin_delete']
        . "' style='color:red;font-weight:bold;' class='calendar_button' name='send'>"
        . "</form>\n</div>\n\n";

    $o .= "<p id='notice'>$notice</p>\n";

    return $o;
}


//*****************************************
//
//       select font and fontsize
//
//*****************************************
function selectFont($name=0,$fontvalue=0,$size=0,$sizevalue=0,$height=0,$heightvalue=0)
{
    global $plugin_tx,$plugin_cf;
    $o      = '';

    if($name) {
        $array = explode(";", $plugin_cf['calendar']['selectable_fonts']);
        $values_select = '';
        $j = 0;
        foreach ($array as $option) {
        	$selected = '';
        	if($option == $fontvalue) {$selected = ' selected'; $j = 1;}
        	$values_select .= "\n<option value='$option'$selected>$option</option>";
        }
        $preselect = (!$j && $fontvalue && $fontvalue!='inherit')? '<option value="'.$fontvalue.'" selected>' . $fontvalue . '</option>' : '';
        $o .= "<select name='$name'>"
           .  "\n\t<option value='inherit'>".$plugin_tx['calendar']['config_no_special_font'].'</option>'
           .  $preselect . $values_select."</select>";
    }

    if($size) {
        $array = explode(",", $plugin_cf['calendar']['selectable_fontsizes']);
        $values_select = '';
        $j = 0;
        foreach ($array as $option) {
        	$selected = '';
        	if($option == $sizevalue) {$selected = ' selected'; $j = 1;}
        	$values_select .= "\n<option value='$option'$selected>$option</option>";
        }
        $preselect = (!$j && $sizevalue && $sizevalue!='inherit')? '<option value="'.$sizevalue.'" selected>' . $sizevalue . '</option>' : '';
        $o .= "<select name='$size'>"
           .  "\n\t<option value='inherit'> – </option>"
           .  $preselect . $values_select.'</select>';
    }

    if($height) {
        $array = explode(",", $plugin_cf['calendar']['selectable_lineheights']);
        $values_select = '';
        $j = 0;
        foreach ($array as $option) {
        	$selected = '';
        	if($option == $heightvalue) {$selected = ' selected'; $j = 1;}
        	$values_select .= "\n<option value='$option'$selected>$option</option>";
        }
        $preselect = (!$j && $heightvalue && $heightvalue!='inherit')? '<option value="'.$heightvalue.'" selected>' . $heightvalue . '</option>' : '';
        $o .= "<select name='$height'>"
           .  "\n\t<option value='inherit'> – </option>"
           .  $preselect . $values_select.'</select>';
    }
    return $o;
}

?>
