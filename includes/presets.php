<?php
//==================================
//
// presets for Calendar Plugin
// March 2012 by svasti
//
//==================================


global  $pth,$plugin,$plugin_tx,$plugin_cf,$cf,$tx,$sl,$hjs;
$o = $notice = $error = '';
$css_ok = $lang_ok = $config_ok = FALSE;

// Security check
if ((!function_exists('sv')))die('Access denied');

$preset       = isset($_POST['preset'])       ? $_POST['preset']       : '';
$backup       = isset($_POST['backup'])       ? $_POST['backup']       : '';
$settingsname = isset($_POST['settingsname']) ? $_POST['settingsname'] : '';


if ($preset){
    //read the preset-file
    include_once ($pth['folder']['plugins'].'/calendar/templates/' . $preset );

    //read the config2 data
    //====================
    $configfile = file_get_contents($pth['folder']['plugins'] . $plugin . '/config/config2.php');

    //collect possible config2 variables
    $values = array(
        'bigcalendar_write_time'                => 'writetime',
        'bigcalendar_write_event'               => 'writeevent',
        'bigcalendar_write_entry3'              => 'writeentry3',
        'bigcalendar_write_entry1'              => 'writeentry1',
        'bigcalendar_anniversary_write_entry3'  => 'birthdayentry3',
        'bigcalendar_anniversary_write_age'     => 'birthdayage',
        'bigcalendar_month_year_headline_style' => 'headline',
        'bigcalendar_line_between_entries'      => 'linebetween',
        'bigcalendar_symbol_if_no_time_given'   => 'notimesymbol',

        'calendar-popup_big'                    => 'smallcalpopup',

        'dont_underline_longevents'             => 'underlongevent',

        'icon-set'                              => 'iconset',

        'show_description_nr_of_cells_indented' => 'indented',
        'show_event_description'                => 'description',
        'show_event_entry3'                     => 'entry3',
        'show_event_entry1'                     => 'entry1',
        'show_event_link'                       => 'link',
        'show_event_time'                       => 'time',
        'show_field_booked_out'                 => 'showbookedout',
        'show_field_daily_times'                => 'showdailytimes',
        'show_field_exceptions'                 => 'showexceptions',
        'show_field_multievent'                 => 'showmultievent',
        'show_field_no_marquee'                 => 'nomarquee',
        'show_field_weekly'                     => 'showweekly',
        'show_field_yearly'                     => 'showyearly',
        'show_future_months'                    => 'futuremonths',
        'show_past_months'                      => 'pastmonths',
        'show_grey_past_events'                 => 'greypastevents',
        'show_no_past_event'                    => 'nopastevent',
        'show_field_mark2'                      => 'showmark2',
        'show_period_of_events'                 => 'showperiod',

        'test_event_list_template'              => 'listtemplate',
        'test_calendar_template'                => 'calendartemplate',

        'titleattributepopup_entry3'            => 'entry3popup',
        );
    //delete variables not used
    foreach ($values as $key=>$value) if(isset($$value)) $values[$key] = $$value; else unset($values[$key]);
    //change values
    $configfile = changevalue($values,$configfile,1);

    //save the changed values
    $config_ok = file_put_contents($pth['folder']['plugins'] . $plugin . '/config/config2.php',$configfile);
    $error .= $config_ok? '' : $plugin_tx['calendar']['error_could_not_change_config_file'].', ';
    //and let the changed config values take effect
    include ($pth['folder']['plugins'] . $plugin .'/config/config2.php');


    //read the language file
    //======================
    $languagefile = file_get_contents($pth['folder']['plugins'] . $plugin . "/languages/$sl.php");

    //collect possible language variables
    $values=array(
        '_event_page'           => 'eventlistpage',
        'event_date'                 => 'datename',
        'event_time'                 => 'timename',
        'event_main_entry'           => 'eventname',
        'event_entry3'               => 'entry3name',
        'event_entry1'               => 'entry1name',
        'event_link_etc'             => 'linkname',
        'hint_mouseover_in_calendar' => 'hintmouseover',
    );
    //delete variables not used
    foreach ($values as $key=>$value) if(isset($$value)) $values[$key] = $$value; else unset($values[$key]);
    //change values
    $languagefile = changevalue($values,$languagefile,1);

    $lang_ok = file_put_contents($pth['folder']['plugins'] . $plugin . "/languages/$sl.php",$languagefile);
    $error .= $lang_ok? '' : $plugin_tx['calendar']['error_could_not_change_language_file'].', ';

    include ($pth['folder']['plugins'] . $plugin ."/languages/$sl.php");

    //get the css-data
    //================
    $cssfile = file_get_contents($pth['folder']['plugins'] . $plugin . '/css/stylesheet.css');
    $cssfilelength = strlen($cssfile); //just for security, to prevent accidential deletion of the file later

    //collect possible language variables
    $values=array(
        'a1'       => 'normaldaycolor',
        'a2'       => 'weekendcolor',
        'a3'       => 'holidaycolor',
        'a4'       => 'eventdaycolor',
        'a5,!im'   => 'eventdaybgcolor',
        'a6'       => 'underlongcolor',
        'a7'       => 'underlongwidth',
        'a8'       => 'mark2color',
        'a9'       => 'mark2width',
        'a10'      => 'eventdaybold',

        'c1'       => 'headlinecolor',
        'c2'       => 'backgroundcolor',
        'c3,url'   => 'backgroundimage',
        'c4'       => 'bordercolor',
        'c5'       => 'borderwidth',
        'c6'       => 'dayspacing',
        'c7'       => 'nowrapdata',
        'c10'      => 'daynamespadtop',
        'c11'      => 'daynamespadbot',
        'c13'      => 'dayheight',
        'c14'      => 'topmargin',
        'c18'      => 'daynames',
        'c19,!im'  => 'cssbirthdayfield',
        'c20'      => 'roundcorners',
        'c21'      => 'shadow',
        'c22'      => 'opacitynoevent',
        'c31,sU'   => 'headlineconfig',
        'c32,sU'   => 'daynameconfig',
        'c33,sU'   => 'todaynameconfig',
        'c34'      => 'bigcalfont',
        'c35'      => 'bigcalfontsize',
        'c36'      => 'dayfieldborder',
        'c37'      => 'bordercollapse',
        'c38'      => 'dayfieldpadding',

        'sc1'      => 'smallcallineheight',
        'sc2'      => 'smallcalfont',
        'sc3'      => 'smallcalfontsize',
        'sc4,url!' => 'birthdayimage',
        'sc5'      => 'todaycolor',
        'sc6,!im'  => 'csstodaybgcolor',
        'sc7'      => 'csstodaybold',

        'p1'       => 'popupbordercolor',
        'p2'       => 'popupborderwidth',
        'p3'       => 'popupbackground',
        'p4'       => 'popupfont',
        'p5'       => 'popupfontsize',

        'b1'       => 'datewidth',
        'b2'       => 'timewidth',
        'b3'       => 'eventwidth',
        'b4'       => 'eventcolor',
        'b5'       => 'entry3width',
        'b6'       => 'entry3color',
        'b7'       => 'linkwidth',
        'b8'       => 'linkcolor',
        'b9'       => 'datecolor',
        'b10'      => 'timecolor',
        'b11'      => 'birthdaycolor',
        'b12'      => 'eventfontweight',
        'b13'      => 'entry1width',
        'b14'      => 'entry1color',
        'b15'      => 'listfont',
        'b16'      => 'listfontsize',
        'b17'      => 'subheadfontsize',
        'b18'      => 'monthfontsize',
        );
    //delete variables not used
    foreach ($values as $key=>$value) if(isset($$value)) $values[$key] = $$value; else unset($values[$key]);
    //change values
    $cssfile = changevalue($values,$cssfile);

    if(strlen($cssfile) > ($cssfilelength - 500)) //to prevent accidental deletion of css-file
    {
        $css_ok = file_put_contents($pth['folder']['plugins'] . '/calendar/css/stylesheet.css',$cssfile);
        //to ensure the browser of the user is reading the changed file
        $hjs .= '<link rel="stylesheet" href="'.$pth['folder']['plugins'].'/calendar/css/stylesheet.css" type="text/css">';
    } else $error .= 'Too many css-changes,';//$css_ok = FALSE;

    $error .= $css_ok===FALSE? $plugin_tx['calendar']['error_could_not_change_css_file'].', ' : '';
    $error = trim($error, ', ');

    //trying to force reloading of the css-file
    $x = rand (1,100);
    $hjs .= '<link rel="stylesheet" href="'.$pth['folder']['plugins'].'/calendar/css/overwrite.css?reload='.$x.'" type="text/css">'."\n"
         .  '<link rel="stylesheet" href="'.$pth['folder']['plugins'].'/calendar/css/stylesheet.css?reload='.$x.'" type="text/css">'."\n";

    if ($css_ok && $lang_ok && $config_ok) {
        $notice .= sprintf('<p class="success" style="clear:both">' . $plugin_tx['calendar']['notice_preset_applied']
                . "</p>\n",substr($preset, 0, strpos($preset,'.')));
    } else {
        $notice .= '<p class="error" style="clear:both">' . $plugin_tx['calendar']['error_occured'] . ': ' . $error . "</p>\n";
    }
}


if ($backup) {

    if(preg_match('/[^(\w)]/',$settingsname)) {
        $notice = '<p class="error" style="clear:both">' . $plugin_tx['calendar']['error_settings_name_wrong'] . "</p>\n";
    } else {

        include($pth['folder']['plugins'].'calendar/includes/readcss.php');

        $settingsfile = '<?php'."\n";

        $cssvalues = $filetype.'settings';
        foreach ($$cssvalues as $key=>$value) {
            $settingsfile .= "\t$key = '$value';\n";
        }

        // config2 data
        include ($pth['folder']['plugins'] . $plugin .'/config/config2.php');
        if($filetype=='calendar') {

            $values = array(
                'bigcalendar_write_time'                => 'writetime',
                'bigcalendar_write_event'               => 'writeevent',
                'bigcalendar_write_entry3'              => 'writeentry3',
                'bigcalendar_write_entry1'              => 'writeentry1',
                'bigcalendar_anniversary_write_entry3'  => 'birthdayentry3',
                'bigcalendar_anniversary_write_age'     => 'birthdayage',
                'bigcalendar_month_year_headline_style' => 'headline',
                'bigcalendar_line_between_entries'      => 'linebetween',
                'bigcalendar_symbol_if_no_time_given'   => 'notimesymbol',

                'calendar-popup_big'                    => 'smallcalpopup',

                'dont_underline_longevents'             => 'underlongevent',

                'test_calendar_template'                => 'calendartemplate',

                'titleattributepopup_entry3'            => 'entry3popup',
                );

            $settingsfile .= "\n"
                          .  '//Config2 Calendar Settings'."\n"
                          .  '//========================='."\n";

            foreach ($values as $key=>$value) {
                $settingsfile .=  "\t".'$'.$value.' = \''.$calendar_cf[$key]."';\n";
            }
            $settingsfile .= "\n"
                          .  '//Language-Values of Big Calendar'."\n"
                          .  '//==============================='."\n"
                          .  "\t".'$hintmouseover = \''    .$plugin_tx['calendar']['hint_mouseover_in_calendar']."';\n"
                          ;
        } else {

            $settingsfile .= "\n"
                          .  '//Config2 Event-List Settings'."\n"
                          .  '//==========================='."\n";
            $values = array(
                'icon-set'                              => 'iconset',

                'show_description_nr_of_cells_indented' => 'indented',
                'show_event_description'                => 'description',
                'show_event_entry3'                     => 'entry3',
                'show_event_entry1'                     => 'entry1',
                'show_event_link'                       => 'link',
                'show_event_time'                       => 'time',
                'show_field_booked_out'                 => 'showbookedout',
                'show_field_daily_times'                => 'showdailytimes',
                'show_field_exceptions'                 => 'showexceptions',
                'show_field_multievent'                 => 'showmultievent',
                'show_field_no_marquee'                 => 'nomarquee',
                'show_field_weekly'                     => 'showweekly',
                'show_field_yearly'                     => 'showyearly',
                'show_future_months'                    => 'futuremonths',
                'show_past_months'                      => 'pastmonths',
                'show_grey_past_events'                 => 'greypastevents',
                'show_no_past_event'                    => 'nopastevent',
                'show_field_mark2'                      => 'showmark2',
                'show_period_of_events'                 => 'showperiod',

                'test_event_list_template'              => 'listtemplate',
                );

            foreach ($values as $key=>$value) {
                $settingsfile .=  "\t".'$'.$value.' = \''.$calendar_cf[$key]."';\n";
            }


            $settingsfile .= "\n"
                          .  '//Language-Values of Event-List'."\n"
                          .  '//============================='."\n";
            $values=array(
                '_event_page'           => 'eventlistpage',
                'event_date'                 => 'datename',
                'event_time'                 => 'timename',
                'event_main_entry'           => 'eventname',
                'event_entry3'               => 'entry3name',
                'event_entry1'               => 'entry1name',
                'event_link_etc'             => 'linkname',
                );

            foreach ($values as $key=>$value) {
                $settingsfile .=  "\t".'$'.$value.' = \''.$plugin_tx['calendar'][$key]."';\n";
            }
        }

        $settingsfile .=  "\n?>\n";

        if(file_put_contents($pth['folder']['plugins'].'/calendar/templates/' .$settingsname . '.' . $filetype . '.php', $settingsfile)) {
            $notice = sprintf('<p class="success" style="clear:both"> ' . $plugin_tx['calendar']['notice_settings_data_saved']
                    . "</p>\n", $settingsname);
        } else {
            $notice = '<p class="error" style="clear:both>' . $plugin_tx['calendar']['error_settings_not_saved'] . "</p>\n";
        }
    }
}

$o .= '<form method="POST" style="float:left;padding:0 .5em .5em 0;" name"choosepresets" action="">' . "\n";
$o .= tag('input type="hidden" value="preset" name="preset"') . "\n";


$handle=opendir($pth['folder']['plugins'].'/calendar/templates/');
$settings = array();
while (false !== ($file = readdir($handle))) {
	if(strpos($file, $filetype.'.php')) {
		$settings[$file] = substr($file, 0, (strlen($file)-strlen($filetype)-5));
	}
}
closedir($handle);
natcasesort($settings);
$preset_select = '';
foreach($settings as $file=>$settingsname){
	$preset_select .= "\n<option value='$file'>" . $settingsname . '</option>';
}


$o .=  '<span class="title" title="'.$plugin_tx['calendar']['help_config_presets'].'">'.$plugin_tx['calendar']['config_presets'] .'</span>: '
   .  "<select name='preset' OnChange ='this.form.submit()'>"
   .  "\n" . '<option value="">' . $plugin_tx['calendar']['config_select'] . '</option>'
   .  "\n" . $preset_select
   .  "\n</select>";

//==============
// apply presets
//==============
//$o .= tag('input type="submit"   value="'.$plugin_tx['calendar']['menu_apply_preset'].'"');

$o .=  "</form>";


//==========================
// create settings back up
//==========================
$o .= '<form method="POST"  style="float:left;" action="">' . "\n";
$o .= tag('input type="hidden" value="backup" name="backup"') . "\n";

$o .= tag('input type="submit"   value="'.$plugin_tx['calendar']['menu_preset_backup'].'"')
   .  tag('input type="text"  value="'.$plugin_tx['calendar']['menu_settings_backupname'].'" name="settingsname" style="width:9em"');

//$o .=  "</div>\n";


$o .=  "</form>";

//if($notice)
$o .= $notice;

$o .= '<div style="clear:both;"></div>';

