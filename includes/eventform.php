<?php
//************************************************
//
//    MAIN HELPER FUNCTION of editevents.php
//
//    makes I N P U T  table
//    Jun 2013
//************************************************

global $hjs,$cf,$cl,$l,$h,$u,$plugin_cf,$calendar_cf,$plugin_tx,$pth,$sl,$sn,$plugin,$tx,$lang,$formatting_hints,$adm,$calendareditor;

$o = "\n\n<!-- Calendar: Edit Events -->\n\n";

// Security check
if ((!function_exists('sv')))die('Access denied');


// which editor?
//if html is set in plugin config, don't use any editor
$calendareditor = $plugin_cf['calendar']['editor']=='html'? '':$calendareditor;
//otherwise prepare the use of either fckeditor, ckeditor, tinymce
if ($calendareditor == 'fckeditor'){
    include_once ($pth['folder']['plugins'].'calendar/fckeditor/fckeditor_for_calendar.php');
    // is called at the end of this file just after the "description"-field
} elseif ($calendareditor == 'ckeditor') {
    include_once($pth['folder']['plugins'].'calendar/editorconfigs/ckeditorconfig_for_calendar.php');
    init_editor(array('description'),$ckconfig);
// this is for any other editor except tinymce. Depending on the editor it may not work, therefore plugin config can override this
} elseif ($calendareditor && $calendareditor != 'tinymce') {
    init_editor(array('description'),'minimal');
}

// calling tinyMCE, if set. (coding idea from cmb)
// Part of the initialization already done in index.php outside this function
if ($calendareditor == 'tinymce'){
    include_tinymce();
    include_once($pth['folder']['plugins'].'calendar/editorconfigs/tinymceconfig_for_calendar.php');
    $hjs .= '<script type="text/javascript">/* <![CDATA[ */
    '.tinymce_filebrowser().'
    CALENDAR_TINY_CONFIG = {'.$tiny.'}

    function openedit(button,hidearea,clicktext,reclicktext,displaytype,antihidearea)
    {
        if(document.getElementById(button).className == "calendar_edit_off") {
            try {new tinymce.Editor("description"+button.substr(6)+"", CALENDAR_TINY_CONFIG).render()} catch(e) {};
    '."\n";
} else {
    $hjs .= '<script type="text/javascript">
    function openedit(button,hidearea,clicktext,reclicktext,displaytype,antihidearea)
    {
        if(document.getElementById(button).className == "calendar_edit_off") {
    '."\n";
}
$hjs .= '
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
    function crossout(area)
    {
        if(document.getElementById(area).className == "eventlist") {
            document.getElementById(area).className = "crossout";
        } else {
            document.getElementById(area).className = "eventlist";
        }
    }
    /* ]]> */</script>'."\n";

// calling the date picker, if set in config
if ($plugin_cf['calendar']['date_picker']){
    $hjs .= '<script type="text/javascript" src="' . $pth['folder']['plugins'] . $plugin . '/dp/datepicker.js">{ "lang":"'.$sl.'" }</script>'."\n";
    $hjs .= tag('link rel="stylesheet" type="text/css" href="' . $pth['folder']['plugins'] . $plugin . '/dp/datepicker.css"')."\n";
}

// getting the button-images
$imageFolder = $pth['folder']['plugins'] . $plugin . "/images";

// links to downloads folder from secondary language: filepaths must start from the main language
$root = $sl!=$cf['language']['default']? 'http://'.$_SERVER['SERVER_NAME'].CMSIMPLE_ROOT : '' ;



// now information is prepared for the user
//=================================================

// number of events not necessary when $saving_notice is given as this also gives the number of events
$number_of_events = $saving_notice? '' : count($events) . ' ' .$plugin_tx['calendar']['notice_event_entries'];

// Informing the user in which fields simple markup works
$formatfields = "<span style='color:#a00;text-decoration:underline'>\n"
              . $plugin_tx['calendar']['hint_formattable_fields']
              . "</span>\n". tag('br')
              . $plugin_tx['calendar']['event_entry1']      . ', '
              . $plugin_tx['calendar']['event_main_entry']  . ', '
              . $plugin_tx['calendar']['event_entry3']      . ', '
              . $plugin_tx['calendar']['event_info_text']   . ', '
              . $plugin_tx['calendar']['event_link_text']   . tag('br') . tag('br') ;

//writing this informtion using a pop-up icon
$formatting_help_button = " &nbsp; "
                        . $plugin_tx['calendar']['hint_event_formatting']
                        . "<a class='info_pop-up' href='#'>"
                        . tag('img src="'
                        . $imageFolder
                        . '/help_icon.png" width="16" height="16" alt="Help"') . "<span>\n"
                        . $formatfields . $formatting_hints . "</span></a>\n";

// instructions on how to enter links
$link_help = $link_help_button = '';
if($standardmode){
    $link_help = "<span style='color:#a00;text-decoration:underline'>\n"
               . $plugin_tx['calendar']['hint_how_to_enter_links']
               . '</span>'
               . tag('br') . tag('br')
               . $plugin_tx['calendar']['hint_internal_links']
               . tag('br') . tag('br')
               . $plugin_tx['calendar']['hint_external_links']. tag('br')
               . icon('ext') . ' www.cmsimple-xh.com'
               . tag('br') . tag('br')
               . $plugin_tx['calendar']['hint_blog_links']. tag('br')
               . icon('int') . ' ?Start&action=view'
               . tag('br') . tag('br')
               . $plugin_tx['calendar']['hint_subsite_links']. tag('br')
               . icon('int') . ' /nl/?Start:Page ... etc.'
               . tag('br') . tag('br')
               . $plugin_tx['calendar']['hint_file_links']. tag('br')
               . icon('pdf'). ' &nbsp; Abcdefg.pdf'. tag('br')
               . icon('doc'). ' &nbsp; Abcdefg.doc(x)'. tag('br'). tag('br')
               . $plugin_tx['calendar']['hint_external_pdf_links'];

    $link_help_button = ' &nbsp; '
                      . $plugin_tx['calendar']['hint_links']
                      . '<a class="info_pop-up" href="#">'
                      . tag('input type="image" src="'
                      . $imageFolder
                      . '/help_icon.png" style="width:16;height:16;vertical-align:baseline;" alt="Help"') . "<span>\n"
                      . $link_help
                      . "</span></a>\n";
}

// informing the user about event page setting
$eventpage_error_notice = $eventpage_notice = '';
$eventpage = $calendar2? $plugin_cf['calendar']['second-calendar_eventpage'] : $plugin_tx['calendar']['_event_page'];
if($standardmode){
    // event page not set/not found
   if(!$eventpage) {
        $eventpage_notice = $calendar2?
                            ' &nbsp;' . $plugin_tx['calendar']['notice_no_eventlist2_page']:
                            ' &nbsp;' . $plugin_tx['calendar']['notice_no_eventlist_page'];
    } elseif(pagenameToUrl($eventpage)===0) {
        $eventpage_notice = ' &nbsp; <span class="error">"' . $eventpage . '" '
                          . $plugin_tx['calendar']['error_event_page_not_found'] . '</span>';
   
    // event page set, but more than one page with the same name exists
    } elseif(pagenameToUrl($eventpage)===FALSE) {
        $eventpage_notice = ' &nbsp; <span class="error">"'
                          . $eventpage . '" '
                          . $plugin_tx['calendar']['error_event_page_occurs_more_than_once']
                          . '</span>';
    }
}

// link errors
$link_error_notice = '';
$unclear = 0;
$name_double = 0;
foreach($events as $entry) {
    if(strpos($entry['linkadd'],'err:')!==FALSE) $unclear++;
    if(strpos($entry['linkadd'],'er2:')!==FALSE) $name_double++;
}
if($unclear==1)                  $link_error_notice .= $unclear . " " . $plugin_tx['calendar']['error_link_unclear'];
if($unclear>1)                   $link_error_notice .= $unclear . " " . $plugin_tx['calendar']['error_links_unclear'];
if($unclear>0 && $name_double>0) $link_error_notice .= ", ";
if($name_double==1)              $link_error_notice .= $name_double . " " . $plugin_tx['calendar']['error_pagename_double'];
if($name_double>1)               $link_error_notice .= $name_double . " " . $plugin_tx['calendar']['error_pagenames_double'];
if($link_error_notice) $link_error_notice = " &nbsp; <span class='error'>" . $link_error_notice . "</span>\n";


// giving out all the info
$notice = $saving_notice
        . $number_of_events
        . $formatting_help_button
        . $link_help_button
        . $eventpage_notice
        . $link_error_notice;

// same event calendar for all subsites means relative addresses won't work when entered from a subsite
if($plugin_cf['calendar']['same-event-calendar_for_all_subsites'] && $cf['language']['default']!=$sl) {
    $notice .= ' &nbsp; <span class="error">' . $plugin_tx['calendar']['error_do_not_edit_from_subsite'] . "</span>\n";
}

// end of preparing information ===============================================================
//print_r($events);



// begin with a general information field
//=======================================
$o .= "<table class='calendar_input'>\n<tr>\n";
if (!$standardmode) {
    $o .=  '<td>'
       .  '<form method="POST" action="">'
       .  tag('input type="text" class="filename" value="'.$file.'" name="file"')
       .  tag('input type="image" src="'
       .  $imageFolder
       .  '/ok.png" style="width:16;height:16" name="newfilename[0]" title="'
       .  $plugin_tx['calendar']['hint_icon_save_new_name'] . '"')
       .  '</form>'
       .  '</td><td>'
       .  '</td>'
       . "\n";
}

$o .= "<td class='eventfile_notice' >$notice</td>\n";
$o .= '<td style="text-align:right;vertical-align:middle">'

// FORM: add new event on top of list
//=====================================
   .  '<form method="POST" action="">'."\n"
   .  tag('input type="image" src="'.$imageFolder
   .  '/add.png" style="width:16;height:16;" name="add[0]" title="'
   .  $plugin_tx['calendar']['hint_icon_add'] . '"') . "\n"
   .  tag('input type="hidden" value="0" name="dataset"') . "\n"
   .  '</form>'
// end form
//================

   .  "\n</td>\n</tr>\n";
$o .= '</table>';


// headline at the beginning of the event list, giving the period of events displayed
$past_month   = ($calendar_cf['show_past_months'] && !$calendar_cf['show_no_past_event'])? $calendar_cf['show_past_months']  : '0';
$future_month = $calendar_cf['show_future_months']? $calendar_cf['show_future_months']: '1';
$future_month--; //subtraction necessary so that present month is also taken into account
$display_start_month = date("m",strtotime("-$past_month month"));
$display_start_year  = date("Y",strtotime("-$past_month month"));
$display_end_month   = date("m",strtotime("+$future_month month"));
$display_end_year    = date("Y",strtotime("+$future_month month"));

$monthnames = explode(",", $plugin_tx['calendar']['names_of_months']);

if ($calendar_cf['show_period_of_events'] && $standardmode){
    $o .= "<p class='period_of_events'>"
       .  $plugin_tx['calendar']['notice_telling_period_of_events']
       .  " <span>"
       .  $monthnames[$display_start_month - 1]." ".$display_start_year
       .  "</span> "
       .  $plugin_tx['calendar']['event_date_till_date']
       .  " <span>"
       .  $monthnames[$display_end_month - 1]." ".$display_end_year
       .  "</span></p>\n";
}



// the number of tablecolumns is calculated
// starting with minimum number of columns (date + main entry)
$tablecols = 2;
// adding columns according to config settings
if ($calendar_cf['show_event_time']) $tablecols++;
if ($calendar_cf['show_event_entry3']) $tablecols++;
if ($calendar_cf['show_event_entry1']) $tablecols++;
if ($calendar_cf['show_event_link']) $tablecols++;


// preparing eventdata display
$oldheading  = '';
$event_day   = '';
$event_month = '';
$event_year  = '';
$make_headline_weeklyevents = 0;
$make_headline_yearlyevents = 0;
$make_headline_multievents  = 0;
$make_headline_singleevents = 0;
$extrasubhead = 0;
$i = 1;


$subhead  = "<tr class='event_heading_row'>\n";
$subhead .= "<td class='event_heading event_date'>".$plugin_tx['calendar']['event_date']."</td>\n";
if ($calendar_cf['show_event_time']){
    $subhead .= "<td class='event_heading event_time'>".$plugin_tx['calendar']['event_time']."</td>\n";
}
if ($calendar_cf['show_event_entry1']){
    $subhead .= "<td class='event_heading event_entry1'>".$plugin_tx['calendar']['event_entry1']."</td>\n";
}
$subhead .= "<td class='event_heading event_main_entry'>".$plugin_tx['calendar']['event_main_entry']."</td>\n";
if ($calendar_cf['show_event_entry3']){
    $subhead .= "<td class='event_heading event_entry3'>".$plugin_tx['calendar']['event_entry3']."</td>\n";
}
if ($calendar_cf['show_event_link']){
    $subhead .= "<td class='event_heading event_link'>".$plugin_tx['calendar']['event_link_etc']."</td>\n";
}
$subhead .= "</tr>\n";


// processing all event entries
foreach($events as $entry) {

    //Headlines to separate different entry types from one another
    if($entry['weekly']){
        $make_headline_weeklyevents++;
        $make_headline_yearlyevents = 0;
        $make_headline_multievents  = 0;
        $make_headline_singleevents = 0;
    } elseif ($entry['yearly'] || $entry['yearly2']) {
        $make_headline_yearlyevents++;
        $make_headline_multievents  = 0;
        $make_headline_singleevents = 0;
    } elseif ($entry['additional']) {
        $make_headline_multievents++;
        $make_headline_singleevents = 0;
    } else {
        $make_headline_singleevents++;
    }

    if($make_headline_weeklyevents == 1) {
        $o .= sprintf($plugin_cf['calendar']['style_headline_eventtype'],$plugin_tx['calendar']['headline_weekly_events']) ."\n";
        $make_headline_weeklyevents++;
    }
    if($make_headline_yearlyevents == 1) {
        $o .= sprintf($plugin_cf['calendar']['style_headline_eventtype'],$plugin_tx['calendar']['headline_yearly_events']) ."\n";
        $make_headline_yearlyevents++;
    }
    if($make_headline_multievents == 1) {
        $o .= sprintf($plugin_cf['calendar']['style_headline_eventtype'],$plugin_tx['calendar']['headline_multiple_events']) ."\n";
        $make_headline_multievents++;
    }
    if($make_headline_singleevents == 1 && ($make_headline_weeklyevents+$make_headline_yearlyevents+$make_headline_multievents)) {
        $o .= sprintf($plugin_cf['calendar']['style_headline_eventtype'],$plugin_tx['calendar']['headline_single_events']) ."\n";
        $make_headline_singleevents++;
        $oldheading = 'x'; //comparision with possible heading decides if heading is necessary
    }


    //finding day of the week and month of an event
    if($entry['datestart']) list($event_day,$event_month,$event_year) = explode( dpSeperator(), $entry['datestart']);
    $timestamp = mktime(0,0,0,(int)$event_month,(int)$event_day,(int)$event_year);
    $longdayname_array = explode(",", $plugin_tx['calendar']['names_of_days_longform']);
    $longdayname[$i] = $longdayname_array[date('w',$timestamp)];
    $shortdayname_array = explode(",", $plugin_tx['calendar']['names_of_days']);
    $shortdayname = $shortdayname_array[date('w',$timestamp)];
    $monthname_array = explode(",", $plugin_tx['calendar']['names_of_months']);
    if($event_month)$monthname[$i] = $monthname_array[ltrim($event_month,0)-1]; else $monthname[$i]='';



//===========================================================
//***********************************************************
//
//   building a table visually similar to the event list
//   no entries here,   J U S T  for  S H O W  :-)
//
//***********************************************************
//===========================================================

    //start the table
    $o .="<table id='preview$i' class='eventlist'>\n";

    //headlines for weekly events in form of daynames, avoiding repetitive headings
    if($entry['weekly'] && ($i == 1 || $longdayname[$i] != $longdayname[$i-1])){
        $o .= "<tr>\n";
        $o .= "<td class='event_monthyear' colspan='$tablecols'>$longdayname[$i]</td>\n";
        $o .= "</tr>\n";
        $o .= $subhead;
        $oldheading = $longdayname[$i];
        $extrasubhead = 0;
    }

    //headlines for all other events in form of monthnames, avoiding repetitive headings
    if(!$entry['weekly'] && ($i == 1 || $monthname[$i] != $oldheading)){
        $o .="<tr>\n";
        $o .="<td class='event_monthyear' colspan='$tablecols'>$monthname[$i] $event_year</td>\n";
        $o .="</tr>\n";
        $o .= $subhead;
        $oldheading = $monthname[$i];
        $extrasubhead = 0;
    }

    $o .= $extrasubhead? $subhead : '';

    if($entry['bookedout']) {
        $eventfull  = ' eventfull';
        $noticefull = '<tr style="height:0"><td colspan='.$tablecols.'><p class="bookedout">'
                    . $plugin_tx['calendar']['event_booked_out']
                    . '</p></td></tr>';
    } else $eventfull = $noticefull = '';

    //data row
    if($entry['yearly']) {
        $o .="$noticefull<tr class='birthday_data_row$eventfull'>\n";
    } else {
        $o .="$noticefull<tr class='event_data_row$eventfull'>\n";
    }
    //date field
    $o .="<td class='event_data event_date'>";


    //button to open the editing table
    //================================
    // mark link errors by coloring the background of the button as well as the link field in the input table
    $link_error_marking = '';
    if (strpos($entry['linkadd'],'err:')!==FALSE) $link_error_marking = 'style="background:#faa;"';
    if (strpos($entry['linkadd'],'er2:')!==FALSE) $link_error_marking = 'style="background:#faf;"';
    //make a button
    $o .= "<input type='button' value='+' class='calendar_edit_off' $link_error_marking id='button$i'
    onclick=\"openedit('button$i','table$i','&ndash;','+','table');\">";


    // if beginning and end dates are there, these should be put nicely one under another as in the event list
    if ($entry['dateend'] ) {

        list($event_day,$event_month,$event_year)              = explode( dpSeperator(), $entry['datestart']);
        list($event_end_day,$event_end_month,$event_end_year)  = explode( dpSeperator(), $entry['dateend']);

        $o .= $event_day;
        if ($event_month!=$event_end_month || $event_year!=$event_end_year) {$o .= dpSeperator().$event_month ;}
        if ($event_year!=$event_end_year) {$o .= dpSeperator().$event_year ;}
        if ($event_year==$event_end_year && dpSeperator()=='.') {$o.= '.';}
        $o .= '&nbsp;' . $plugin_tx['calendar']['event_date_till_date'] . ' ';
        $o .= $event_end_day.dpSeperator().$event_end_month.dpSeperator().$event_end_year;

    } elseif ($entry['weekly']){
        $o .= $plugin_tx['calendar']['event_weekly_starting_on'] . ' ' . $entry['datestart'];

    } else {
        $o .= $entry['datestart'];
    }
    if ($entry['exceptions']) {
        $o .= ' ' . $plugin_tx['calendar']['event_except'] . ' '
           .  $entry['exceptions'];
    }
    if ($entry['additional']) {
        $o .= ' ' . $plugin_tx['calendar']['event_additional'] . ' '
           .  $entry['additional'];
    }
    $o .= "</td>\n";

    //time field
    // find the weekdays of start and end of an event
    $startday = '';
    $endday = '';
    if ($entry['dateend'] && $entry['endtime'] && !$entry['dailytimes'] && !$entry['weekly']) {

        $startdaynr = date('w',mktime(0,0,0,$event_month,(int)$event_day,$event_year));
        $startday = ' ' . $shortdayname_array[$startdaynr];
        $enddaynr = date('w',mktime(0,0,0,$event_end_month,(int)$event_end_day,$event_end_year));
        $endday = ' ' . $shortdayname_array[$enddaynr];
    }

    if ($calendar_cf['show_event_time']){
       $o .='<td class="event_data event_time">';
       $o .= $entry['dailytimes']? $plugin_tx['calendar']['event_daily'].' ' : '';
       $o .= $entry['starttime'] . $startday;
       if ($entry['endtime']) {
           $o .= '&nbsp;' . $plugin_tx['calendar']['event_time_till_time'] . ' ' . $entry['endtime'] . $endday;
       }
       $o .="</td>\n";
    }

    $event_entry1 = simpleMarkupToHtml ($entry['event_entry1']);
    $event = simpleMarkupToHtml ($entry['event']);
    $event_entry3 = simpleMarkupToHtml ($entry['event_entry3']);

    /*event_entry1 field*/
    if ($calendar_cf['show_event_entry1']){
        $o .="<td class='event_data event_entry1'>$event_entry1</td>\n";
    }

    //event field
    $o .="<td class='event_data event_main_entry'>$event</td>\n";

    /*event_entry3 field*/
    if ($calendar_cf['show_event_entry3']){
        $o .="<td class='event_data event_entry3'>$event_entry3</td>\n";
    }

    /*link, link-text and info-text field*/
    if ($calendar_cf['show_event_link']){
        $o .= "<td class='event_data event_link'>";

        $event_link_txt  = simpleMarkupToHtml($entry['linktxt']);
        $event_link_icon = $entry['linkicon'];

        //recombine values just as they are saved in the event file
        //necessary, because they have been separated in loading the event file
        $linkint = $entry['linkint']? 'int:'.$entry['linkint']:'';
        $linkadd = ($linkint && $entry['linkadd'])?
                   $linkint.'|'.$entry['linkadd'] :
                   $linkint.$entry['linkadd'];

        $spacer = ($event_link_txt) ? " " : "";

        // if a link address has been given, there are different cases
        $field4 = '';
        if ($linkadd) {
            $links = explode('|', $linkadd);
            $linktexts = explode('|', $event_link_txt);
            foreach ($links as $key=>$value) {
                $addr = substr($value,4);
                $type = substr($value, 0,4);
                $icon = $event_link_icon? icon($type) . $spacer : '';
                if(!isset($linktexts[$key])) $linktexts[$key] = '';

                switch ($type) {
                    case 'pfx:':
                    case 'ext:': $o .= "<a href='http://".$addr."' target='_blank' title='"
                                         .  strip_tags($addr) .  "'>". $icon . $linktexts[$key] .'</a>';
                        break;

                    case 'int:': $o .= '<a href="?'. $addr . '" title="' . urlToPagename($addr)
                                         .  '">'. $icon  . $linktexts[$key] . "</a>\n";
                        break;

                    case 'in?:': $o .= '<a href="'.$root . ltrim($addr,'/') . '" title="' . urlToPagename($addr)
                                         .  '">'. $icon  . $linktexts[$key] . "</a>\n";
                        break;

                    case 'doc:':
                    case 'pdf:': $o .= '<a href="'.$pth['folder']['downloads']
                                         .  $addr . '" title="' . rawurldecode($addr)
                                         .  '">'. $icon . $linktexts[$key] ."</a>\n";
                        break;
                }
            }
        }


        $event_info_icon = $entry['infoicon'];
        $event_info_txt  = simpleMarkupToHtml($entry['infotxt']);

        $spacer = ($event_link_icon) ? " " : $spacer;
        if($event_info_icon && $event_info_txt) {
            $wider = $event_info_icon==2? ' wider':'';
            $o .= "$spacer<a class='info_pop-up$wider' href='#'>"
               .  icon("info") . '<span>' . $event_info_txt . "</span></a>\n";
        }
        if(!$event_info_icon && $event_info_txt) {
             $o .= $spacer . $event_info_txt;
        }

        $o .= "</td>\n</tr>\n";
    }

        // description field
    if ($calendar_cf['show_event_description']) {

        $event_description = $entry['description'];

        // how much indented is the description going to start?
        $indentation = $calendar_cf['show_description_nr_of_cells_indented'];
        if(!$indentation) $indentation=0;
        // preventing that $indentation gets too big
        if ($indentation >= $tablecols) $indentation = $tablecols - 1;

        if (strlen($event_description) > 1) {
            $extrasubhead = 1;
            $o .= "<tr>\n";
            // if indentation is wanted, here it comes
            if ($indentation) $o .= "<td colspan='$indentation'>&nbsp;</td>\n";
            $o .= "<td class='event_description' colspan='" . ($tablecols - $indentation)
               .  "'>" . $event_description . "</td>\n</tr>\n";
        } else $extrasubhead = 0;
    }
    //end the event list table
    $o .= '</table>';



//===================================================
//***************************************************
//
//      start the input table for the same event
//      (which can be switched on or off)
//      -- here the input takes place
//
//***************************************************
//===================================================

    $no_marqueechecked  = $entry['no_marquee']  ? 'checked="checked"' : '';
    $bookedoutchecked   = $entry['bookedout']   ? 'checked="checked"' : '';
    $dailytimes_checked = $entry['dailytimes']  ? 'checked="checked"' : '';
    $linkiconchecked    = $entry['linkicon']    ? 'checked="checked"' : '';
    $weeklychecked      = $entry['weekly']      ? 'checked="checked"' : '';
    $yearlychecked      = $entry['yearly']      ? 'checked="checked"' : '';
    $yearly2checked     = $entry['yearly2']     ? 'checked="checked"' : '';
    $infoiconchecked    = $entry['infoicon']==1 ? 'checked="checked"' : '';
    $widepopupchecked   = $entry['infoicon']==2 ? 'checked="checked"' : '';
    $mark2checked       = $entry['mark2']       ? 'checked="checked"' : '';


// Start FORM
//===================
    $o .= '<form method="POST" action="">'."\n"
       .  '<table class="calendar_input" style="display:none;" id="table'.$i.'">'."\n";

    //first row (just labels + buttons)
    //=============================
    $o .= '<tr class="calendar_input_caption">'."\n"
       .  '<td class="column1">'
       .  $plugin_tx[$plugin]['event_start_date']
       .  "</td>\n";
    if($calendar_cf['show_event_time']){
        $o .= '<td class="column2">' . $plugin_tx[$plugin]['event_time'] . "</td>\n"
           .  '<td class="column3">' . $plugin_tx[$plugin]['event_time'] . "</td>\n";
    } else {
        $o .= '<td class="column2"></td>'."\n"
           .  '<td class="column3"></td>'."\n";
    }
    $o .= '<td class="column4">' . $plugin_tx[$plugin]['event_end_date'] . "</td>\n";

    $o .= '<td class="column5">' . $plugin_tx[$plugin]['event_main_entry'] . "</td>\n";

    //delete, copy, add and save buttons
    //===================================
    $o .= '<td class="column6" style="text-align:right;vertical-align:middle">'
       .  tag('input type="hidden" value="'.$i.'" name="dataset"') . "\n"
       .  tag('input type="image" src="' .  $imageFolder
       .  '/delete.png" style="width:16;height:16;" name="delete[0]" title="'
       .  $plugin_tx['calendar']['hint_icon_delete'] . '"'). "\n"

       .  tag('input type="image" src="'
       .  $imageFolder
       .  '/copy.png" style="width:16;height:16" name="copy[0]" title="'
       .  $plugin_tx['calendar']['hint_icon_copy'] . '"') . "\n"
       .  tag('input type="image" src="'.$imageFolder
       .  '/add.png" style="width:16;height:16;" name="add[0]" title="'
       .  $plugin_tx['calendar']['hint_icon_add'] . '"') . "\n"
       .  tag('input type="image" src="'
       .  $imageFolder
       .  '/ok.png" style="width:16;height:16" name="change[0]" title="'
       .  $plugin_tx['calendar']['hint_icon_ok'] . '"') . "\n"
       . "</td>\n"
       .  "</tr>\n";

    //2nd row; here data input starts
    //================================
    //column 1
    $o .= "<tr>\n"
       .  '<td>'
       .  tag('input type="text" class="calendar_input_date" maxlength="12" value="'
       .  $entry['datestart'] . '" name="datestart['.$i.']" id="datestart'.$i.'"')
       .  "</td>\n" ;

    //column 2+3
    if ($calendar_cf['show_event_time']){
        $o .= '<td>'
           .  tag('input type="text" class="calendar_input_time"  value="'
           .  $entry['starttime'] . '" name="starttime['.$i.']"')
           .  "</td>\n<td>"
           .  tag('input type="text" class="calendar_input_time"  value="'
           .  $entry['endtime']   . '" name="endtime['.$i.']"')
           .  "</td>\n";
    } else {
        $o .= tag('input type="hidden" value="'.$entry['starttime'].'"')
           .  '<td style="width:0"></td>'
           .  tag('input type="hidden" value="'.$entry['endtime'].'"')
           .  '<td style="width:0"></td>';
    }

    //column 4
    $o .= '<td>'
       .  tag('input type="text" class="calendar_input_date" maxlength="12" value="'
       .  $entry['dateend']   . '" name="dateend['.$i.']" id="dateend'.$i.'"');

    if ($plugin_cf['calendar']['date_picker']){
        $o .=  '<script type="text/javascript">
            // <![CDATA[
                var opts = {
                    formElements:{"datestart'.$i.'":"d-'.dpSeperator('dp').'-m-'.dpSeperator('dp').'-Y"},
                    showWeeks:true,
                    // Show a status bar and use the format "l-cc-sp-d-sp-F-sp-Y" (e.g. Friday, 25 September 2009)
                    statusFormat:"l-cc-sp-d-sp-F-sp-Y"
                };
            datePickerController.createDatePicker(opts);
            // ]]>
            </script>' .

            '<script type="text/javascript">
            // <![CDATA[
                var opts = {
                    formElements:{"dateend'.$i.'":"d-'.dpSeperator('dp').'-m-'.dpSeperator('dp').'-Y"},
                    showWeeks:true,
                    // Show a status bar and use the format "l-cc-sp-d-sp-F-sp-Y" (e.g. Friday, 25 September 2009)
                    statusFormat:"l-cc-sp-d-sp-F-sp-Y"
                };
            datePickerController.createDatePicker(opts);
            // ]]>
            </script>';
    }
    $o .=  "</td>\n";

    //column 5 -- main entry can be textarea or input line
    if($plugin_cf['calendar']['input-field_mainentry_as_textarea']) {
        $o .= '<td colspan="2" rowspan="2">';
        $o .= '<textarea  class="calendar_input_event" rows="2" cols="20" style="height:'
           .  $plugin_cf['calendar']['input-field_mainentry_as_textarea']
           .  ';" name="event['.$i.']">'
           .  $entry['event'] . '</textarea>';
        $o .= "</td>\n</tr>\n";
        $o .= "<tr><td colspan='4'>&nbsp;</td></tr>\n";

    } else {
        $o .= '<td colspan="2">' . tag('input class="calendar_input_event input_highlighting" type="text"  value="'
           .  $entry['event'] . '" name="event['.$i.']"') ."</td>\n</tr>\n";
    }


    // 3rd row  L E F T  S I D E
    //===========================
    $o .= "<tr>\n";
    //determine the rowspan
    $height_3rd_row = 6;
    if(!$calendar_cf['show_event_link'])   $height_3rd_row -=4;
    if(!$calendar_cf['show_event_entry3']) $height_3rd_row--;
    if(!($calendar_cf['show_event_entry1'] ||
        $plugin_cf['calendar']['input-field_entry1_show_in_input_always'])) $height_3rd_row--;
    $height_3rd_row = $height_3rd_row? "rowspan='$height_3rd_row'" : '';

    $o .= "<td  class='calendar_input_caption_leftaligned' colspan='3' $height_3rd_row>";

    // checkbox "mark2"
    if ($calendar_cf['show_field_mark2']) {
        $o .= tag('input type="checkbox" '.$mark2checked.' value="1" name="mark2['.$i.']"')
           .  $plugin_tx['calendar']['event_border_marked']
           .  tag('br');
    } else {
        $o .= tag('input type="hidden" value="'.$entry['mark2'].'" name="mark2['.$i.']"');
    }

    // checkbox "no marquee"
    if ($calendar_cf['show_field_no_marquee']) {
        $o .= tag('input type="checkbox" '.$no_marqueechecked.' value="1" name="no_marquee['.$i.']"')
           .  $plugin_tx['calendar']['event_not_on_marquee']
           .  ' &nbsp; ';
    } else {
        $o .= tag('input type="hidden" value="'.$entry['no_marquee'].'" name="no_marquee['.$i.']"');
    }

    // checkbox booked out
    if ($calendar_cf['show_field_booked_out']) {
        $o .= '<span class="nowrap">'
           .  tag('input type="checkbox" '.$bookedoutchecked.' value="1" name="bookedout['.$i.']"')
           .  $plugin_tx['calendar']['event_booked_out']
           .  '</span>';
    } else {
        $o .= tag('input type="hidden" value="'.$entry['bookedout'].'" name="bookedout['.$i.']"');
    }
    If ($calendar_cf['show_field_booked_out'] || $calendar_cf['show_field_no_marquee']) $o .= tag('br');


    // event yearly
    if ($calendar_cf['show_field_yearly']) {
        $o .= tag('input type="checkbox" '.$yearly2checked.' value="1" name="yearly2['.$i.']"')
           .  $plugin_tx['calendar']['event_yearly'] . ' ';
        $o .= tag('input type="checkbox" '.$yearlychecked.' value="1" name="yearly['.$i.']"')
           .  $plugin_tx['calendar']['event_yearly_with_age_calculation']
           .  tag('br');
    } else {
        $o .= tag('input type="hidden" value="'.$entry['yearly'].'" name="yearly['.$i.']"');
        $o .= tag('input type="hidden" value="'.$entry['yearly2'].'" name="yearly2['.$i.']"');
    }

    // are times daily in long events?
    if ($calendar_cf['show_field_daily_times']) {
        $o .= tag('input type="checkbox" '.$dailytimes_checked.' value="1" name="dailytimes['.$i.']"')
           .  $plugin_tx['calendar']['event_times_are_daily'] . ' (' . $plugin_tx['calendar']['config_long_event'] . ')'
           .  tag('br');
    } else {
        $o .= tag('input type="hidden" value="'.$entry['dailytimes'].'" name="dailytimes['.$i.']"');
    }

    // event weekly?
    if ($calendar_cf['show_field_weekly']) {
        $o .= tag('input type="checkbox" '.$weeklychecked.' value="1" name="weekly['.$i.']"')
           .  $plugin_tx['calendar']['event_weekly'] 
           .  tag('br');
    } else {
        $o .= tag('input type="hidden" value="'.$entry['weekly'].'" name="weekly['.$i.']"');
    }

    // exceptions?
    if ($calendar_cf['show_field_exceptions']) {
        $o .= $plugin_tx['calendar']['event_exceptions'] . ':'
           .  tag('br');
        // exceptions?
        $o .= tag('input type="text"  class="moredates" value="'
           .  $entry['exceptions']  . '" name="exceptions['.$i.']"')
           .  tag('br');
    } else {
        $o .=  tag('input type="hidden" value="'.$entry['exceptions'].'" name="exceptions['.$i.']"');
    }

    // additional dates
    if ($calendar_cf['show_field_multievent']) {
        $o .= $plugin_tx['calendar']['event_additional_days'];
        if($calendar_cf['show_field_booked_out']) $o .= ' ' . $plugin_tx['calendar']['config_additional_dates_bookedout'];
        $o .= ':' .  tag('br');
        if($plugin_cf['calendar']['input-field_additional_days_as_textarea']) {
            $o .= '<textarea  class="moredates" rows="2" cols="20" style="height:'
               .  $plugin_cf['calendar']['input-field_additional_days_as_textarea']
               .  ';" name="additional['.$i.']">'
               .  $entry['additional'] . '</textarea>';
        } else {
            $o .= tag('input type="text"  class="moredates"   value="'
               .  $entry['additional']  . '" name="additional['.$i.']"');
        }
        $o .= tag('br');
    } else {
        $o .= tag('input type="hidden" value="'.$entry['additional'].'" name="additional['.$i.']"');
    }

/*    // multiple event?
    if ($calendar_cf['show_field_multievent']) {
        $o .= tag('input type="checkbox" '.$multichecked.' value="1" name="multievent['.$i.']"')
           .  $plugin_tx['calendar']['event_same_on_other_days']
           .  tag('br');
    } else {
        $o .= tag('input type="hidden" value="'.$entry['multievent'].'" name="multievent['.$i.']"');
    }
*/


    $o .= "</td>\n";




    // 3 row  R I G H T  S I D E
    //==========================
    // field entry1 (could be used as location)
    if ($calendar_cf['show_event_entry1'] || $plugin_cf['calendar']['input-field_entry1_show_in_input_always']) {
        $o .= '<td  class="calendar_input_caption_rightaligned">'
           .  $plugin_tx['calendar']['event_entry1']
           .  "</td>\n"
           .  '<td colspan="2">';
        if($plugin_cf['calendar']['input-field_entry1_as_textarea']) {
            $o .= '<textarea  class="calendar_input_event" rows="2" cols="20" name="event_entry1['.$i.']" style="height:'
               .  $plugin_cf['calendar']['input-field_entry1_as_textarea']
               .  ';">'
               .  $entry['event_entry1'] . '</textarea>';
        } else {
            $o .= tag('input type="text"  class="calendar_input_event" value="'
               .  $entry['event_entry1']  . '" name="event_entry1['.$i.']"');
        }
        $o .= "</td>\n</tr>\n<tr>";
    } else {
        $o .= tag('input type="hidden" value="'.$entry['event_entry1'] . '" name="event_entry1[' . $i.']"');
    }

    // field entry3 (was location in former versions)
    if ($calendar_cf['show_event_entry3']) {
        $o .= '<td  class="calendar_input_caption_rightaligned">'
           .  $plugin_tx['calendar']['event_entry3']
           .  "</td>\n"
           .  '<td colspan="2">';
        if($plugin_cf['calendar']['input-field_entry3_as_textarea']) {
            $o .= '<textarea  class="calendar_input_event" rows="2" cols="20" name="event_entry3['.$i.']" style="height:'
               .  $plugin_cf['calendar']['input-field_entry3_as_textarea']
               .  ';">'
               .  $entry['event_entry3'] . '</textarea>';
        } else {
            $o .= tag('input type="text"  class="calendar_input_event" value="'
               .  $entry['event_entry3']  . '" name="event_entry3['.$i.']"');
        }
        $o .= "</td>\n</tr>\n<tr>";
    } else {
        $o .= tag('input type="hidden" value="'.$entry['event_entry3'] . '" name="event_entry3[' . $i.']"');
    }


    // converting the linkdata which has been saved as $linkadd to the simplified form $linkadr
    // which may be just pagenames or filenames
    $links = explode('|', $entry['linkadd']);
    $link = '';
    $linkadr = '';
    foreach ($links as $key=>$value) {
        $linkadr .= $link? '|':'';
        $link = substr($value, 4);
        $linktype = substr($value, 0, 4);
        if($linktype == 'in?:' || $linktype == 'int:') $link = urlToPagename($link);
        // urlencode only the filename, not the address
        if($linktype=='pdf:' || $linktype=='pfx:' || $linktype=='doc:') $link = rawurldecode($link);
        $linkadr .= $link;
    }

    // link field turned on
    if ($calendar_cf['show_event_link']) {

        //link icon checkbox
        $o .= '<td class="calendar_input_caption_rightaligned">'
           .  $plugin_tx['calendar']['event_icon']
           .  tag('input type="checkbox" '.$linkiconchecked.' value="1" name="linkicon['.$i.']" style="vertical-align:middle"')
           .  "</td>\n";

        $pages_select = '';
        for ($x=0;$x<$cl;$x++) {
            $levelindicator = '';
            for ($y = 1; $y < $l[$x]; $y++) {$levelindicator .= '&ndash; &nbsp; ';}
            $page = $levelindicator.$h[$x];
        	$selected = '';
        	if($entry['linkint'] == $u[$x]) {$selected = ' selected';}
        	$pages_select .= "\n".'<option value="' . $u[$x] . '"'. $selected.'> &nbsp; '.$page.' &nbsp; </option>';
        }
        //internal link field
        $o .= "<td colspan='2'>"
           .  '<select class="calendar_input_event" name="linkint['.$i.']">'
           .  "\n" . '<option value="">' . $plugin_tx['calendar']['config_select_internal_link'] . '</option>'
           .  "\n" . $pages_select . "\n</select>"
           .  "</td>\n</tr>\n";

        // other link caption
        $o .= '<tr><td class="calendar_input_caption_rightaligned">'
           . $plugin_tx['calendar']['event_other_link'] ."</td>\n"
        // other link field
           .  "<td colspan='2'>";
        $o .= tag('input type="text" class="calendar_input_event calendar_input_links" '.$link_error_marking.' value="'
           .  $linkadr . '" name="linkadr['.$i.']"')
           .  "</td>\n</tr>\n";

        // "link-text" label
        $o .= '<tr><td class="calendar_input_caption_rightaligned">'
           .  $plugin_tx['calendar']['event_link_text'] . "</td>\n"
        // "link-text" field
           .  '<td colspan="2">'
           .  tag('input type="text" class="calendar_input_event" value="'
           .  $entry['linktxt'] . '" name="linktxt['.$i.']"')
           .  "</td>\n</tr>\n";

        //checkbox info icon + info text label
        $o .= "<tr>\n"
           .  '<td class="calendar_input_caption_rightaligned" rowspan="2">'
           .  $plugin_tx['calendar']['event_info_text']
           .  tag('br')
           .  tag('input type="checkbox" '.$infoiconchecked.' value="1" name="infoicon['.$i.']"')
           .  $plugin_tx['calendar']['event_icon']
           .  tag('br')
           .  tag('input type="checkbox" '.$widepopupchecked.' value="2" name="infoicon['.$i.']"')
           .  $plugin_tx['calendar']['event_icon'] . tag('br') . $plugin_tx['calendar']['event_widepopup']
           .  "</td>\n";
        // info text field
        $o .= "<td rowspan='2' colspan='2'><textarea  class='calendar_input_event' rows='2' cols='20' name='infotxt[$i]'>"
           .  $entry['infotxt'] . "</textarea></td>\n"
           .  "</tr>\n";
    } else {
        //hidden fields to save link and info data
        $o .= tag('input type="hidden" value="'.$entry['linkicon']   .'" name="linkicon['.$i.']"')
           .  tag('input type="hidden" value="'.$entry['linkint']    .'" name="linkint['. $i.']"')
           .  tag('input type="hidden" value="'.$linkadr             .'" name="linkadr['. $i.']"')
           .  tag('input type="hidden" value="'.$entry['linktxt']    .'" name="linktxt['. $i.']"')
           .  tag('input type="hidden" value="'.$entry['infoicon']   .'" name="infoicon['.$i.']"')
           .  tag('input type="hidden" value="'.$entry['infotxt']    .'" name="infotxt['. $i.']"');
    }

    //make event description field
    if ($calendar_cf['show_event_description']) {

        if(!$calendareditor) {
            $description = preg_replace(array('!(<\/[h|p|li|ul|ol]?\d?>)!U','!(<br.*>)!U'),"$1\n", $entry['description']);
            $o .= "<tr class='calendar_input_caption'>\n"
               .  '<td colspan="3">'
               .  $plugin_tx['calendar']['event_description']
               .  tag('input type="button" value="+" class="calendar_edit_off" style="margin:0;" id="button2'.$i.'"
                  onclick=\'openedit("button2'.$i.'","description'.$i.'","&ndash;","+","block");\'')
               .  "</td>\n</tr>\n<tr>\n"
               .  '<td colspan="6">'
               .  "<textarea class='description' rows='2' cols='20' style='display:none;height:"
               .  $plugin_cf['calendar']['editor_height'] . "px' name='description[$i]' id='description$i'>"
               .  $description
               .  "</textarea></td>\n"
               .  "</tr>\n";
        } elseif($calendareditor == 'fckeditor') {
            $o .= "<tr class='calendar_input_caption'>\n"
               .  '<td colspan="3">'
               .  $plugin_tx['calendar']['event_description'];
            $o .= tag('input type="button" value="+" class="toggle" id="button2'.$i.'"
                  onclick=\'openeditor("button2'.$i.'","description'.$i.'","&ndash;","+","button2'.$i.'");\'');
            $o .= "</td>\n</tr>\n<tr>\n"
               .  '<td colspan="6">'
               .  "<textarea class='description' rows='2' cols='20' style='display:none;height:"
               .  $plugin_cf['calendar']['editor_height'] . "px;' "
               .  " name='description[$i]' id='description$i'>"
               .  $entry['description']
               .  "</textarea></td>\n"
               .  "</tr>\n";
            $o .= fckeditor();
        } else {
            $o .= "<tr class='calendar_input_caption'>\n"
               .  '<td colspan="3">'
               .  $plugin_tx['calendar']['event_description'];

            $o .= tag('input type="button" value="+" class="calendar_edit_off" id="button2'.$i.'" style="margin-left:0;"
                  onclick=\'openedit("button2'.$i.'","div'.$i.'","+","&ndash;","block");\'');

            $o .= "</td>\n</tr>\n<tr>\n"
               .  "<td colspan='6'><div style='display:none' id='div$i'>\n";

            $o .= "<textarea class='description' rows='2' cols='20' style='height:"
               .  $plugin_cf['calendar']['editor_height'] . "px' name='description[$i]' id='description$i'>"
               .  $entry['description']
               .  "</textarea></div></td>\n"
               .  "</tr>\n";
        }
    } else {
        //hidden field to save description data. Hm, hidden field seems not to work here. So a work around:
        $o .= '<div style="display:none;">'
           .  "<textarea class='description' style='display:none' name='description[$i]'>"
           .  $entry['description']
           .  "</textarea></div>\n";
    }

    //end input table
    $o .= '</table>'."\n"

// end FORM
//===================
       .  '</form>'."\n";

    $i++;
}

//prevent wrong alert from ckeditor on save via calendar save button
if ($calendareditor == 'ckeditor') {
    $o .=  '<script type="text/javascript">
                                // <![CDATA[
        if (typeof window.removeEventListener != "undefined") {
            window.removeEventListener("beforeunload", CKeditor_beforeUnload, false);
        } else {
            window.detachEvent("onbeforeunload", CKeditor_beforeUnload);
        }
                               // ]]>
                                </script>';
}
