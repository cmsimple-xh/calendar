<?php
//*****************************************************
// Main function of the front end:
//
//                  displaying the
//
//            L I S T   of   E V E N T S
//
//                    on a page
// April 2012
//****************************************************

global $plugin_cf,$calendar_cf,$cf,$plugin_tx,$sl,$h,$l,$u,$s,$lang,$datapath;

// Security check
if ((!function_exists('sv')))die('Access denied');
$o = "\n\n<!-- CALENDAR EVENT LIST -->\n\n";
$today = date("Ymd");
$day = substr($today, 6);
$presentmonth = substr($today, 4,2);
$presentyear  = substr($today, 0,4);
if($month == '') $month = $presentmonth;
if($year == '')  $year  = $presentyear;

//decide how to treat past events
$show_no_past_event = FALSE;
$markpast = TRUE;
if(!$pastevents && !$calendar_cf['show_grey_past_events']) $markpast = FALSE;
if(!$pastevents && $calendar_cf['show_no_past_event'])  $show_no_past_event = $markpast = TRUE;
if($pastevents==3) $show_no_past_event= $markpast = TRUE;
if($pastevents==1) $markpast = FALSE;

// links to downloads folder from secondary language: filepaths must start from the main language
$root = $sl!=$cf['language']['default']? 'http://'.$_SERVER['SERVER_NAME'].CMSIMPLE_ROOT : '' ;

// spreadsheet table style or template driven style?
if($style == 1) {
    $tablestyle = true;
    $templatefile = '';
}
elseif($style) {
    $tablestyle = false;
    $templatefile = $style;
}
else {
    $tablestyle = $plugin_cf['calendar']['eventlist_template']? false : true;
    $templatefile = $plugin_cf['calendar']['eventlist_template'];
    $templatefile = strpos($templatefile,'.tpl')? $templatefile : $templatefile.'.tpl';
}
if(!$tablestyle  && is_file($pth['folder']['plugins'].'calendar/templates/'.$templatefile)) {
    $template = file_get_contents($pth['folder']['plugins'].'calendar/templates/'.$templatefile);
    $template = explode('===',$template);
} else {
    $template = array('','','','','','','','','','','','','');
    $tablestyle = true;
    $templatefile = '';
}



//find out the period to display
//===============================
// first the start
if($past_month == '') {$past_month = $calendar_cf['show_past_months'];}
if(!$past_month || $show_no_past_event) {$past_month = 0;}
$month = $month - $past_month;
while ($month < 1) {
    $year  = $year - 1;
    $month = 12 + $month;
}
// Now $month and $year give the dates for the start of the event list

// in case events are clicked in a calendar that is not the present month,
// the event list either is (1) adjusted to that month or, (2) is enlarged if necessary
// to prevent XSS injection htmlspecialchars is used by advice of cmb
$new_start = FALSE;
$new_end_month = 0;
$showlinktopresentmonth = FALSE;
if(!$file) {
    $month_input  = isset($_GET['month'])  ? htmlspecialchars($_GET['month'])  : '';
    $month_input .= isset($_POST['month']) ? htmlspecialchars($_POST['month']) : '';
    $month_input = (int) $month_input;
    $year_input   = isset($_GET['year'])   ? htmlspecialchars($_GET['year'])   : '';
    $year_input  .= isset($_POST['year'])  ? htmlspecialchars($_POST['year'])  : '';
    $year_input = (int) $year_input;
    $date_input   = $year_input * 100 + $month_input;

    if (   $plugin_cf['calendar']['eventlist_start_moves_with_clicked_event']
        && ($month_input || $year_input)) {
        $year = $year_input;
        $month = $month_input;
        $show_no_past_events = FALSE;
        $markpast = FALSE;
        $showlinktopresentmonth = TRUE;
    }
    if(  !$plugin_cf['calendar']['eventlist_start_moves_with_clicked_event']
       && $month_input && $year_input && $date_input < $year * 100 + $month) {
        $new_end_month = $month + ($year-$year_input)*12 - $month_input;
        $year = $year_input;
        $month = $month_input;
        $show_no_past_events = FALSE;
        $markpast = FALSE;
        $new_start = TRUE;
    }
}

//find out the end month of the displayed event list
if($end_month == ''){
    if($calendar_cf['show_future_months']){
        $end_month = $calendar_cf['show_future_months'];
    } else $end_month = "1";
}
// in case earlier events are requested the displqaed period is enlarged to the past
If($new_start) $end_month = $new_end_month + $end_month;

$display_end_month = $month + $end_month -1 + $past_month;
$display_end_year = $year;
while ($display_end_month > 12){
    $display_end_year  = $display_end_year + 1;
    $display_end_month = $display_end_month - 12;
}
$end_month = $end_month + $past_month;


//in case events later are clicked the displayed period is enlarged to the future
if(   !$plugin_cf['calendar']['eventlist_start_moves_with_clicked_event']
   && !$file && $month_input && $year_input && $date_input > $display_end_year * 100 + $display_end_month) {
    $end_month = ($year_input-$year)*12 - $month + 1 + $month_input;
    $display_end_year  = $year_input;
    $display_end_month = $month_input;
}



$event_time_array         = array();
$event_end_time_array     = array();
$event_dailytimes_array   = array();
$event_day_array          = array();
$event_day_time_array     = array();
$event_end_day_array      = array();
$event_month_array        = array();
$event_end_month_array    = array();
$event_year_array         = array();
$event_end_year_array     = array();
$event_yearmonth_array    = array();
$event_notpast_array      = array();
$event_time2_array        = array();
$event_weekly_array       = array();
$event_exceptions_array   = array();
$event_weekday_array      = array();
$event_yearly_array       = array();
$event_yearly2_array      = array();
$event_entry1_array       = array();
$event_array              = array();
$event_entry3_array       = array();
$event_link_icon_array    = array();
$event_link_adr_array     = array();
$event_link_txt_array     = array();
$event_info_icon_array    = array();
$event_info_txt_array     = array();
$event_description_array  = array();
$event_bookedout_array    = array();
$oldlong_array            = array();
$exceptions               = '';


// determining which file to read
if ($file) {
    $eventfile = $datapath.$file;
}else {
    $eventfile = $datapath."eventcalendar$lang.txt";
}

if(is_file($eventfile)){

    $file_array = file($eventfile);
    if($addfile && is_file($datapath.$addfile)) {
        $addfile_array = file($datapath.$addfile);
        $file_array  = array_merge($file_array,$addfile_array);
    }

    // processing the data file line by line
    //==========================================
    foreach ( $file_array as $line) {

        $oldlongevent = 0;

        if (strpos($line,';')) {
            // var_dump($line);

            // dividing the lines into fields (explode limit necessary to make ";" in description possible)
            list($eventdates,$event,$event_entry3,$link,$event_time,$description)    = explode( ";", $line,6);

            //for sorting
            $event_time2 = $event_time? str_pad(substr($event_time,0,5),5,'0',STR_PAD_LEFT):'20:00';

            // dividing the remaining data
            list($event_date_start,$event_date_end,$event_end_time,$event_entry1)    = explode(",",$eventdates,4);

            // eliminating any * before the date (this being the mark of "don't show on marquee")
            if (substr($event_date_start,0,1)=="*") $event_date_start = substr($event_date_start,1);

            // eliminating any * before the end date (this being the mark of "event booked out")
            if (substr($event_date_end,0,1)=="*") {
                $event_date_end = substr($event_date_end,1);
                $bookedout = TRUE;
            } else $bookedout = FALSE;

            // checking if there is an * before the endtime, meaning given times of multi day events are daily times
            if (substr($event_end_time,0,1)=="*") {
                $event_end_time = substr($event_end_time,1); // eliminating any *
                $dailytimes = TRUE;
            } else $dailytimes = FALSE;

            // checking if there is an * before the event_entry1, which means alternative marking in calendar
            if (substr($event_entry1,0,1)=="*") {
                $event_entry1 = substr($event_entry1,1); // eliminating any *
            } 

            if($event_date_start) {
                list($event_day,$event_month,$event_year) = explode( dpSeperator(), $event_date_start);
                $event_year = str_pad($event_year,4,'0',STR_PAD_LEFT); // for dates bevore the year 1000
                $notpast = $event_year.$event_month.$event_day < $today ? 0 : 1;
            } else {
                $event_day = '';
                $event_month = '';
                $event_year = '';
                $notpast = 0;
            }

            if($event_date_end) {
                list($event_end_day,$event_end_month,$event_end_year)  = explode( dpSeperator(), $event_date_end);

                $notpast = $event_end_year.$event_end_month.$event_end_day < $today ? 0 : 1;
                $not_outside_period = $event_end_year*100 + $event_end_month < $year*100 + $month ? 0 : 1;

                //old long lasting events which started before the considered period and reach into it are marked
                $condition = $show_no_past_event? $notpast : $not_outside_period;
                $oldlongevent = ($event_year*100 + $event_month < $year*100 + $month && $condition)? 1 : 0;
 

            } else {
                $event_end_day = '';
                $event_end_month = '';
                $event_end_year = '';
            }

            $yearly     = FALSE;
            $yearly2    = FALSE;
            $weekly     = FALSE;
            $exceptions = '';
            $additional = '';
            $additionaldatesarray = array();

            //checking for birthday events
            if (substr($event_entry3,0,3)=='###') {
                $event_entry3 = substr($event_entry3,3); // eliminating any ###
                $yearly = TRUE;
            }
            //checking for other yearly events where age shout not be calculated
            if (substr($event_entry3,0,3)=='#*#') {
                $event_entry3 = substr($event_entry3,3); // eliminating any ###
                $yearly2 = TRUE;
            }

            // legacy code, checking for multiple events, not used any more
            if (substr($event_entry3,0,3)=="*#*") {
                $event_entry3 = substr($event_entry3,3); // eliminating any #*#
            }

            //checking for weekly events (coded in such a way as to keep the old file structure)
            if (substr($event_entry3,0,3)=="***") {
                $event_entry3 = substr($event_entry3,3); // eliminating any ***
                $oldlongevent = 0; //only one off oldlongevents are considered

                //in case no end date has been given, 10 years duration is supposed. It must fall within the display period
                if (!$event_date_end) {
                    $supposed_end = ($event_year + 10) . str_pad($event_month,2,"0",STR_PAD_LEFT);
                    if($supposed_end > $presentyear . str_pad($presentmonth,2,"0",STR_PAD_LEFT)) {
                        $endofweeklyevents = $supposed_end;
                    } else $endofweeklyevents = '';
                }
                else {
                    $endofweeklyevents=$event_end_year . str_pad($event_end_month,2,"0",STR_PAD_LEFT);
                }
                $startofweeklyevents = $event_year . str_pad($event_month,2,"0",STR_PAD_LEFT);
                if (    $endofweeklyevents >= $year . str_pad($month,2,"0",STR_PAD_LEFT)
                    &&  $startofweeklyevents <= $display_end_year . str_pad($display_end_month,2,"0",STR_PAD_LEFT))
                {
                    $weekly = TRUE;
                }
                else
                //eleminating all weekly events not fitting in the time window
                {
                    $event_day = $event_month = $event_year = '';
                }
                if($show_no_past_event && $weekly
                    && $endofweeklyevents.str_pad($event_end_day,2,"0",STR_PAD_LEFT) < $today) {
                    $weekly = FALSE;
                    $event_day = $event_month = $event_year = '';
                }
            }

            // exceptions
            if(strpos($event_entry3,'|')){
                $exceptions = substr($event_entry3,0,strpos($event_entry3,'|'));
                $event_entry3 = substr($event_entry3,(strpos($event_entry3,'|')+1)); // eliminating exception data
            }

            // additions
            if(strpos($event,'|')){
                $additional = substr($event,0,strpos($event,'|'));
                $additionaldates = explode(",",$additional);
                foreach ($additionaldates as $value) {
                    //deleting empty spaces
                    $value = ltrim($value);
                    $additionaldatesarray[] = explode(dpSeperator(),$value);
                }
                // eliminating additional dates from entry1
                $event = substr($event,(strpos($event,'|')+1));
            }

            // dividing the new subfunctions of $link into its elements
            list($event_link_adr,$event_link_txt,$event_info_txt) = explode(",",$link,3);

            // Now further refining the subfunctions...  keeping compatibility to the age old event file structure

            // detecting icon for links
            if (substr($event_link_adr,0,1)=="*") {
                $event_link_icon = TRUE;
                $event_link_adr = substr($event_link_adr,1);
            }  else $event_link_icon = FALSE;

            // detecting icon for info text
            if (substr($event_info_txt,0,1)=="*") {
                $event_info_icon = 1;
            } elseif (substr($event_info_txt,0,1)=="+") {
                $event_info_icon = 2;
            }  else $event_info_icon = 0;

            // deleting icon marking
            if ($event_info_icon) $event_info_txt = substr($event_info_txt,1);

            // for sorting algorithm that puts weekly events orderd by weekday first
            $event_time_stamp = mktime(0,0,0,(int)$event_month,(int)$event_day,(int)$event_year);
            if($weekly) $weekday = 1 + date('w',$event_time_stamp); else $weekday = '';
            // 1 was added to avoid "0"

            // special case: same event calendar for all languages = on, plus list is shown on secondary language page
            // now image addresses have to be adjusted
            if($plugin_cf['calendar']['same-event-calendar_for_all_subsites'] && $cf['language']['default']!=$sl) {
                $description = str_replace('src="./images','src="../images', $description);
            }

            array_push($event_year_array,       $event_year);
            array_push($event_end_year_array,   $event_end_year);
            array_push($event_month_array,      $event_month);
            array_push($event_end_month_array,  $event_end_month);
            if (!$weekly) {
                array_push($event_yearmonth_array,($event_month.".".$event_year));
            } else {
                array_push($event_yearmonth_array,'');
            }
            array_push($event_notpast_array,    $event_year.$event_month.$notpast);
            array_push($event_weekday_array,    $weekday);
            array_push($event_day_array,        $event_day);
            array_push($event_day_time_array,   $event_day.$event_time);
            array_push($event_end_day_array,    $event_end_day);
            array_push($event_entry1_array,     $event_entry1);
            array_push($event_array,            $event);
            array_push($event_entry3_array,     $event_entry3);
            array_push($event_weekly_array,     $weekly);
            array_push($event_exceptions_array, $exceptions);
            array_push($event_yearly_array,     $yearly);
            array_push($event_yearly2_array,    $yearly2);
            array_push($event_link_icon_array,  $event_link_icon);
            array_push($event_link_adr_array,   $event_link_adr);
            array_push($event_link_txt_array,   $event_link_txt);
            array_push($event_info_icon_array,  $event_info_icon);
            array_push($event_info_txt_array,   $event_info_txt);
            array_push($event_time_array,       $event_time);
            array_push($event_end_time_array,   $event_end_time);
            array_push($event_dailytimes_array, $dailytimes);
            array_push($event_time2_array,   $event_time2);
            array_push($event_description_array,$description);
            array_push($event_bookedout_array,  $bookedout);
            array_push($oldlong_array,          $oldlongevent);

            if($additional) {
                foreach ($additionaldatesarray as $value) {
                    $addbookedout = substr($value[2],-1)=='*'? TRUE : FALSE;
                    $value[2] = rtrim($value[2],'*');
                    $notpast = 20 . $value[2].$value[1].$value[0] < $today ? 0 : 1;

                    array_push($event_year_array,       20 . $value[2]);
                    array_push($event_end_year_array,   '');
                    array_push($event_month_array,      $value[1]);
                    array_push($event_end_month_array,  '');
                    array_push($event_yearmonth_array,  ($value[1] .'.20' . $value[2]));
                    array_push($event_notpast_array,     $value[2].$value[1].$notpast);
                    array_push($event_weekday_array,    '');
                    array_push($event_day_array,        $value[0]);
                    array_push($event_day_time_array,   $event_day.$event_time);
                    array_push($event_end_day_array,    '');
                    array_push($event_entry1_array,     $event_entry1);
                    array_push($event_array,            $event);
                    array_push($event_entry3_array,     $event_entry3);
                    array_push($event_weekly_array,     '');
                    array_push($event_exceptions_array, '');
                    array_push($event_yearly_array,     '');
                    array_push($event_yearly2_array,    '');
                    array_push($event_link_icon_array,  $event_link_icon);
                    array_push($event_link_adr_array,   $event_link_adr);
                    array_push($event_link_txt_array,   $event_link_txt);
                    array_push($event_info_icon_array,  $event_info_icon);
                    array_push($event_info_txt_array,   $event_info_txt);
                    array_push($event_time_array,       $event_time);
                    array_push($event_end_time_array,   $event_end_time);
                    array_push($event_dailytimes_array, '');
                    array_push($event_time2_array,   $event_time2);
                    array_push($event_description_array,$description);
                    array_push($event_bookedout_array,  $addbookedout);
                    array_push($oldlong_array,          '');
                }
            }

        }
    }
}

// print_r($oldlong_array);
// print_r($event_array);


// headline at the beginning of the event list, giving the period of events displayed
// ===================================================================================
$textmonth  = date("F",mktime(1,1,1,$month,1,$year));
$monthnames = explode(",", $plugin_tx['calendar']['names_of_months']);

//text annoucing the shown period
if(!$calendar_cf['show_future_months'] && !$calendar_cf['show_past_months']) {
    $periodtext1 =  $plugin_tx['calendar']['notice_telling_month_of_events'];
    $startperiod =  $monthnames[$month - 1]." ".$year;
    $periodtext2 =  '';
    $endperiod   =  '';
} else {
    $periodtext1 =  $plugin_tx['calendar']['notice_telling_period_of_events'];
    $startperiod =  $monthnames[$month - 1]." ".$year;
    $periodtext2 =  $plugin_tx['calendar']['event_date_till_date'];
    $endperiod   =  $monthnames[$display_end_month-1]." ".$display_end_year;
}
$gotopresentmonth = $showlinktopresentmonth ?
                    '<a href="?'.$u[($s)].'">'.$plugin_tx['calendar']['link_to_present_month'].'</a>' : '';

if(!$tablestyle) {
    //template driven display
    $o .=$gotopresentmonth? '<p><small>'.$gotopresentmonth."</small></p>\n" : '';
    $o .= str_replace(
        array(
        '%start%',
        '%end%',
        '%till%',
        '%period%'
        ),
        array(
        $startperiod,
        $endperiod,
        $periodtext2,
        $periodtext1,
        ),$template[1]);
} elseif ($calendar_cf['show_period_of_events']){
    // spreadsheet table style
    $o .="<p class='period_of_events'>$periodtext1 <span>$startperiod</span> $periodtext2 <span>$endperiod</span>".tag('br')."$gotopresentmonth</p>\n";
} else $o .=$gotopresentmonth? '<p><small>'.$gotopresentmonth."</small></p>\n" : '';

// the number of table columns is calculated
// starting with minimum number of columns (date + main entry)
$tablecols = 2;
// adding columns according to config settings
if ($calendar_cf['show_event_time'])   $tablecols++;
if ($calendar_cf['show_event_entry3']) $tablecols++;
if ($calendar_cf['show_event_entry1']) $tablecols++;
if ($calendar_cf['show_event_link'])   $tablecols++;



//========================================================
//
// making the table for weekly events ordered by weekdays
//
//========================================================
if($tablestyle) {$o .="<table class='eventlist'>\n";} else {$o .= "<div class='eventlist'>\n";}

$longdayname_array  = explode(",", $plugin_tx['calendar']['names_of_days_longform']);
$shortdayname_array = explode(",", $plugin_tx['calendar']['names_of_days']);

// if description has extra text, the subhead (= Date, Time, Event etc.) is repeated again,
// normally it appears only after the month of the events is displayed
$extrasubhead = 0;

// subhead (= Date, Time, Event etc.) comes after the month/weekday as headline
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


//preparing headline single events
$headline_singleevents = '';
$print_headline_singleevents = 0; //counter to make sure this headline is printed only once, when counter == 1

//headline annoncing weekly section, will be shown always when there are weekly events, even if no other events are there
if(in_array(TRUE,$event_weekly_array)) {
    if($tablestyle) {
        $o .= sprintf($plugin_cf['calendar']['style_headline_eventtype'],$plugin_tx['calendar']['headline_weekly_events']);
    } else {
        //template driven display
        $o .= str_replace('%weekly%',$plugin_tx['calendar']['headline_weekly_events'],$template[2]);
    }
} else $print_headline_singleevents = 2; //2 meaning, the headline for singleevents will not be printed, as no weekly events are there

$x = 1;
while($x<=7){

    $longweekday = '';
    $shortweekday = '';
    $weekdayheader = $template[3];

    // false is default value, as there may be no weekly event on the considered weekday
    $table=false;

    // checking if weekly events happen on the considered day
    // the decision, if a weekly event is past or not, has already been taken
    if (in_array($x,$event_weekday_array)){
        $table=true;
    }
    // building headings starting with dayname
    $y = $x-1; //subtraction to get [0] for sundays
    if($table && $tablestyle){
        $o .="<tr>\n";
        $o .="<td class='event_monthyear' colspan='$tablecols'>$longdayname_array[$y]</td>\n";
        $o .="</tr>\n";
        $o .= $subhead;
        $extrasubhead = 0;
    } elseif($table) {
        // template driven display
        $longweekday = $longdayname_array[$y];
        $shortweekday = $shortdayname_array[$y];
        $weekdayheader = str_replace(array('%longweekday%','%shortweekday%'),
                                  array($longweekday,$shortweekday), $weekdayheader);
        $o .= $weekdayheader;
    } 



    foreach($event_weekday_array as $keys=>$temp){

    // template driven display
    $date      = '';
    $time      = '';
    $field1    = '';
    $mainfield = '';
    $field3    = '';
    $field4    = '';
    $weeklyevents = $template[4];
    $pastweeklyevents = $template[5];

        if($event_weekday_array[$keys]==$x){

            // enddate determines if an event is past or not
            If($event_end_day_array[$keys]) {
                $eventenddate = $event_end_year_array[$keys].$event_end_month_array[$keys].$event_end_day_array[$keys];
                $past = ($eventenddate < $today && $markpast)? ' past_event' : '';
            } else $past = '';


            if($extrasubhead) {
                $o .= $subhead;
                $extrasubhead = 0;
            }

            // separate Data and design, 1st: create the Data
            // ==============================================
            // if beginning and end dates are there, these are put one under the other
            if ($event_end_day_array[$keys]) {
                $date .= $event_day_array[$keys];
                if (   $event_month_array[$keys] != $event_end_month_array[$keys]
                    || $event_year_array[$keys]  != $event_end_year_array[$keys]) $date .= dpSeperator().$event_month_array[$keys] ;
                if ($event_year_array[$keys]!=$event_end_year_array[$keys]) $date .= dpSeperator().$event_year_array[$keys] ;
                if ($event_year_array[$keys]==$event_end_year_array[$keys] && dpSeperator()=='.') $date.= ".";
                $date .= "&nbsp;".$plugin_tx['calendar']['event_date_till_date'] . ' ';
                $date .= $event_end_day_array[$keys].dpSeperator().$event_end_month_array[$keys].dpSeperator().$event_end_year_array[$keys];
            } elseif ($today < $event_year_array[$keys].$event_month_array[$keys].$event_day_array[$keys]) {
                $date .= $plugin_tx['calendar']['event_weekly_starting_on']
                   .  ' ' . $event_day_array[$keys].dpSeperator().$event_month_array[$keys].dpSeperator().$event_year_array[$keys];
            } else {
                $date .= $plugin_tx['calendar']['event_weekly_ongoing'];
            }
            if ($event_exceptions_array[$keys]) {
                $date .= ' ' . $plugin_tx['calendar']['event_except'] . ' '
                   .  $event_exceptions_array[$keys];
            }

            // create time field
            $time = $event_time_array[$keys];
            if ($event_end_time_array[$keys]) {
                $time .= '&nbsp;' . $plugin_tx['calendar']['event_time_till_time'] . ' ' . $event_end_time_array[$keys];
            }

            // create field1 = event_entry1 field
            $field1 = $event_entry1_array[$keys];


            // create $mainfield = event field
            $mainfield = $event_array[$keys];

            // create $field3 = event_entry3 field
            $field3 = $event_entry3_array[$keys];

            // create $field4 = link and link-text or info-text field
            $spacer = ($event_link_txt_array[$keys]) ? ' ' : '';

            // if a link address has been given, there are different cases
            $field4 = '';
            if ($event_link_adr_array[$keys]) {
                $links = explode('|', $event_link_adr_array[$keys]);
                $linktexts = explode('|', $event_link_txt_array[$keys]);
                foreach ($links as $key=>$value) {
                    $addr = substr($value,4);
                    $type = substr($value, 0,4);
                    $icon = $event_link_icon_array[$keys]? icon($type,$past) . $spacer : '';
                    if(!isset($linktexts[$key])) $linktexts[$key] = '';

                    switch ($type) {
                        case 'pfx:':
                        case 'ext:': $field4 .= "<a href='http://".$addr."' target='_blank' title='"
                                             .  strip_tags($addr) .  "'>". $icon . $linktexts[$key] .'</a>';
                            break;

                        case 'int:': $field4 .= '<a href="?'. $addr . '" title="' . urlToPagename($addr)
                                             .  '">'. $icon  . $linktexts[$key] . "</a>\n";
                            break;

                        case 'in?:': $field4 .= '<a href="'.$root . ltrim($addr,'/') . '" title="' . urlToPagename($addr)
                                             .  '">'. $icon  . $linktexts[$key] . "</a>\n";
                            break;

                        case 'doc:':
                        case 'pdf:': $field4 .= '<a href="'.$pth['folder']['downloads']
                                             .  $addr . '" title="' . rawurldecode($addr)
                                             .  '">'. $icon . $linktexts[$key] ."</a>\n";
                            break;
                    }
                }
            }
            $spacer = ($event_link_icon_array[$keys]) ? ' ' : $spacer;
            if($event_info_icon_array[$keys] && $event_info_txt_array[$keys]) {
                $wider = $event_info_icon_array[$keys]==2? ' wider':'';
                $field4 .= "$spacer<a class='info_pop-up$wider' href='javascript:;'>"
                        . icon("info",$past) . "<span>" . $event_info_txt_array[$keys] . "</span></a>";
            }
            if(!$event_info_icon_array[$keys] && $event_info_txt_array[$keys]) {
                 $field4 .= $spacer . $event_info_txt_array[$keys];
            }


            $field5 = $event_description_array[$keys];

            if($event_bookedout_array[$keys] && !$past) {
                $eventfull  = ' eventfull';
                $noticefull = '<tr style="height:0"><td colspan='.$tablecols.'><p class="bookedout">'
                            . $plugin_tx['calendar']['event_booked_out']
                            . '</p></td></tr>';
                $noticefull2 = $plugin_tx['calendar']['event_booked_out'];
            } else $eventfull = $noticefull = $noticefull2 = '';


            // Now put the data into the design, i.e. the table or the template
            // ================================================================
            if($tablestyle) {

                // use date field data
                $o .="$noticefull<tr class='event_data_row$past$eventfull'>\n";
                $o .="<td class='event_data event_date'>";
                $o .= $date;
                $o .= "</td>\n";

                // use time field
                if ($calendar_cf['show_event_time'] && $tablestyle){
                    $o .="<td class='event_data event_time'>$time</td>\n";
                }

                // field 1, the extra field
                if ($calendar_cf['show_event_entry1']) {
                    $o .="<td class='event_data event_entry1'>$field1</td>\n";
                }

                // main entry field
                $o .="<td class='event_data event_main_entry'>$mainfield</td>\n";

                // $field3 secondary entry field
                if ($calendar_cf['show_event_entry3']) {
                    $o .="<td class='event_data event_entry3'>$field3</td>\n";
                }

                // use $field4, the event link etc field
                if ($calendar_cf['show_event_link']) {
                    $o .="<td class='event_data event_link'>$field4</td>\n</tr>\n";
                }

                // use $field 5, the additional description field
                if ($calendar_cf['show_event_description']) {
                    // how much indented is the description going to start?
                    $indentation = $calendar_cf['show_description_nr_of_cells_indented'];
                    if(!$indentation) $indentation=0;
                    // preventing that $indentation gets too big
                    if ($indentation >= $tablecols) $indentation = $tablecols - 1;

                    if (strlen($event_description_array[$keys]) > 1) {
                        if(!$past) $extrasubhead = 1;
                        $o .= "<tr class='event_data_row".$past."'>\n";
                        // if indentation is wanted, here it comes
                        if ($indentation) $o .= "<td  class='event_data event_description' colspan='$indentation'>&nbsp;</td>\n";
                        $o .= "<td class='event_data event_description' colspan='" . ($tablecols - $indentation) . "'>$field5</td>\n</tr>\n";
                    }
                }

            //template driven display
            } elseif($past) {
                $o .=  str_replace(array('%longweekday%','%shortweekday%','%date%','%time%','%field1%',
                                         '%mainfield%','%field3%','%infolink%','%description%','%ended%'),
                                  array($longweekday,$shortweekday,$date,$time,$field1,
                                          $mainfield,$field3,$field4,$field5,$plugin_tx['calendar']['event_has_ended']),
                                  $pastweeklyevents);
            } else {
                $o .=  str_replace(array('%bookedout%','%longweekday%','%shortweekday%','%date%','%time%','%field1%','%mainfield%','%field3%','%infolink%','%description%'),
                                   array($noticefull2,$longweekday,$shortweekday,$date,$time,$field1,$mainfield,$field3,$field4,$field5), $weeklyevents);
            }
        }
    }
    $x++;
}

if($tablestyle) {$o .="</table>\n";} else {$o .= "</div>\n";}


//==============================================================
//
// making the table for single one-off events listed by month
//
//==============================================================

// sorting all events according to date and time
asort($event_day_time_array);

//print_r($event_time2_array);
//print_r($event_array);


// headline single events section
// to separate these events from weekly events, in case there are weekly and also other events
if(in_array(TRUE,$event_weekly_array) && in_array(FALSE,$event_weekly_array)) {
    if($tablestyle) {
        $headline_singleevents = sprintf($plugin_cf['calendar']['style_headline_eventtype'],$plugin_tx['calendar']['headline_single_events']);
    } else {
        //template driven display
        $headline_singleevents = str_replace('%single%',$plugin_tx['calendar']['headline_single_events'],$template[6]);
    }
}

if($tablestyle) {$o .="<table class='eventlist'>\n";} else {$o .= "<div class='eventlist'>\n";}

// Now all months of the considered period will be gone through one by one
// first run ($x=0) is for old long lasting events started before the considered period and still going on
$x=0;
while($x<=$end_month){

    // initializing template values
    $monthheader = $template[7];

    $textmonth = $monthnames[$month - 1];

    // false is default value, as there may be no event in the considered month
    $table=false;

    // checking if an event is in the considered month
    // 1st case no past events. Past event will be eliminated later, now the headlines
    // have to be checked. Special care is taken for the present month, here the array with
    // comparision of the event day against the present day is used
    if($x && $show_no_past_event) {
        if($month == $presentmonth && in_array($year.$month.'1',$event_notpast_array)) {
           $table=true;
        } elseif($month != $presentmonth && in_array(($month.".".$year),$event_yearmonth_array)) {
           $table=true;
        }
    }
    // 2nd case, all events, even if past, of the month in calculation will tigger the header
    if ($x && !$show_no_past_event && in_array(($month.".".$year),$event_yearmonth_array)){
       $table=true;
    }
    if (!$x && in_array('1',$oldlong_array)) $table=true;

    // headline single events?
    if($table) {
        $print_headline_singleevents++;
        $o .= $print_headline_singleevents==1? $headline_singleevents : '';
    }

    // month header
    // ------------
    $printtextmonth = $x? $textmonth:$plugin_tx['calendar']['headline_started_earlier'];
    $printyear = $x? $year:'';

    if($table && $tablestyle){
        $o .= "<tr>\n";
        $o .= "<td class='event_monthyear' colspan='$tablecols'>$printtextmonth $printyear".tag('br')."</td>\n";
        $o .= "</tr>\n";
        $o .= $subhead;
        $extrasubhead = 0;

    } elseif($table && !$tablestyle) {
        // for template driven display
        $monthheader = str_replace(array('%month%','%year%'),
                                  array($printtextmonth,$printyear), $monthheader);
        $o .= $monthheader;
    }


    foreach($event_day_array as $keys=>$temp){

    // initializing values for template driven display
    $date      = '';
    $time      = '';
    $field1    = '';
    $mainfield = '';
    $field3    = '';
    $field4    = '';
    $yearlyevents     = $template[8];
    $yearly2events    = $template[9];
    $pastyearlyevents = $template[10];
    $singleevents     = $template[11];
    $pastsingleevents = $template[12];


        //=====================================================================================//
        // Y E A R L Y   E V E N T S  with (=yearly) or without (=yearly2) ) age calculation   //
        //=====================================================================================//
        $extracondition = ($show_no_past_event && $year.$event_month_array[$keys].$event_day_array[$keys]<$today)? FALSE:TRUE;
        if( $x
            && ($event_yearly_array[$keys] || $event_yearly2_array[$keys])
            && ($event_month_array[$keys] == $month)
            && $extracondition) {

            $month = str_pad($month,2,'0',STR_PAD_LEFT);

            $age = $year - $event_year_array[$keys];
            if ($age >= 0){

                // headline single events?
                if($table) {
                    $print_headline_singleevents++;
                    $o .= $print_headline_singleevents==1? $headline_singleevents : '';
                }


                if (!$table && $tablestyle){

                    // headline with month has to be generated in case it has not yet been done
                    $table=true;
                    $print_headline_singleevents++;
                    $o .= $print_headline_singleevents==1? $headline_singleevents : '';

                    $o .= "<tr>\n";
                    $o .= "<td class='event_monthyear' colspan='5'>$textmonth $year".tag('br')."</td>\n";
                    $o .= "</tr>\n";
                    $o .= $subhead;
                    $extrasubhead = 0;

                } elseif (!$table && !$tablestyle) {
                    // same for template driven display
                    $o .=  str_replace(array('%month%','%year%'),array($textmonth,$year), $monthheader);
                }

                // checking if the birthday-event to be shown is in the past
                $eventdate = $year.$event_month_array[$keys].$event_day_array[$keys];

                $pastbirthday = ($eventdate < $today && $markpast)? " past_event" : '';


                $date = $event_day_array[$keys].dpSeperator().$month.dpSeperator().$year;

                $time = $event_time_array[$keys];
                if ($event_end_time_array[$keys]) {
                    $time .= '&nbsp;'.$plugin_tx['calendar']['event_time_till_time'].' '. $event_end_time_array[$keys];
                }

                $field1 = $event_entry1_array[$keys];

                $mainfield = $event_array[$keys];
                
                //preparing the age output
                if($event_yearly_array[$keys]) {
                    $agefield = '<span class="nowrap">';
                    if ($age >= 5){
                        $agefield .= "$age ".$plugin_tx['calendar']['age_5_and_more_years_old'];
                    }
                    elseif ($age >= 2 and $age < 5){
                        $agefield .= "$age ".$plugin_tx['calendar']['age_2-4_years_old'];
                    }
                    else {
                        $agefield .= "$age ".$plugin_tx['calendar']['age_1_year_old'];
                    }
                    $agefield .= '</span>';
                } else $agefield = '';

                $field3 = $event_entry3_array[$keys];

                // link and link-text or info-text field

                $spacer = ($event_link_txt_array[$keys]) ? ' ' : '';

                // if a link address has been given, there are different cases
                $field4 = '';
                if ($event_link_adr_array[$keys]) {
                    $links = explode('|', $event_link_adr_array[$keys]);
                    $linktexts = explode('|', $event_link_txt_array[$keys]);
                    foreach ($links as $key=>$value) {
                        $addr = substr($value,4);
                        $type = substr($value, 0,4);
                        $icon = $event_link_icon_array[$keys]? icon($type,$pastbirthday) . $spacer : '';
                        if(!isset($linktexts[$key])) $linktexts[$key] = '';

                        switch ($type) {
                            case 'pfx:':
                            case 'ext:': $field4 .= "<a href='http://".$addr."' target='_blank' title='"
                                                 .  strip_tags($addr) .  "'>". $icon . $linktexts[$key] .'</a>';
                                break;

                            case 'int:': $field4 .= '<a href="?'. $addr . '" title="' . urlToPagename($addr)
                                                 .  '">'. $icon  . $linktexts[$key] . "</a>\n";
                                break;

                            case 'in?:': $field4 .= '<a href="'.$root . ltrim($addr,'/') . '" title="' . urlToPagename($addr)
                                                 .  '">'. $icon  . $linktexts[$key] . "</a>\n";
                                break;

                            case 'doc:':
                            case 'pdf:': $field4 .= '<a href="'.$pth['folder']['downloads']
                                                 .  $addr . '" title="' . rawurldecode($addr)
                                                 .  '">'. $icon . $linktexts[$key] ."</a>\n";
                                break;
                        }
                    }
                }

                $spacer = ($event_link_icon_array[$keys]) ? ' ' : $spacer;
                if($event_info_icon_array[$keys] && $event_info_txt_array[$keys]) {
                    $wider = $event_info_icon_array[$keys]==2? ' wider':'';
                    $field4 .= "$spacer<a class='info_pop-up$wider' href='javascript:;'>"
                            . icon("info",$pastbirthday) . "<span>" . $event_info_txt_array[$keys] . "</span></a>";
                }
                if(!$event_info_icon_array[$keys] && $event_info_txt_array[$keys]) {
                     $field4 .= $spacer . $event_info_txt_array[$keys];
                }

                $field5 =  $event_description_array[$keys] ;

                if($event_bookedout_array[$keys] && !$pastbirthday) {
                    $eventfull  = ' eventfull';
                    $noticefull = '<tr style="height:0"><td colspan='.$tablecols.'><p class="bookedout">'
                                . $plugin_tx['calendar']['event_booked_out']
                                . '</p></td></tr>';
                    $noticefull2 = $plugin_tx['calendar']['event_booked_out'];
                } else $eventfull = $noticefull = $noticefull2 = '';


                // Now put the data into the design, i.e. the table or the template
                // ================================================================
                if($tablestyle) {

                    if($extrasubhead) {
                        $o .= $subhead;
                        $extrasubhead = 0;
                    }

                    // use date field data
                    if($event_yearly_array[$keys]) {$o .= "$noticefull<tr class='birthday_data_row$pastbirthday$eventfull'>\n";}
                    else {$o .= "$noticefull<tr class='event_data_row$pastbirthday$eventfull'>\n";}

                    $o .= '<td class="event_data event_date">';
                    $o .= $date;
                    $o .= "</td>\n";

                    // use time field
                    if ($calendar_cf['show_event_time']){
                        $o .= "<td class='event_data event_time'>$time</td>\n";
                    }

                    // field 1, the extra field
                    if ($calendar_cf['show_event_entry1']) {
                        $o .= "<td class='event_data event_entry1'>$field1</td>\n";
                    }

                    // main entry field
                    $o .= "<td class='event_data event_main_entry'>$mainfield $agefield</td>\n";

                    // $field3 secondary entry field
                    if ($calendar_cf['show_event_entry3']) {
                        $o .="<td class='event_data event_entry3'>$field3</td>\n";
                    }

                    // use $field4, the event link etc field
                    if ($calendar_cf['show_event_link']) {
                        $o .="<td class='event_data event_link'>$field4</td>\n</tr>\n";
                    }

                    // use $field 5, the additional description field
                    if ($calendar_cf['show_event_description']) {
                        // how much indented is the description going to start?
                        $indentation = $calendar_cf['show_description_nr_of_cells_indented'];
                        if(!$indentation) $indentation=0;
                        // preventing that $indentation gets too big
                        if ($indentation >= $tablecols) $indentation = $tablecols - 1;

                        if (strlen($event_description_array[$keys]) > 2) {
                            if(!$pastbirthday) $extrasubhead = 1; 
                            $o .= "<tr class='event_data_row".$pastbirthday."'>\n";
                            // if indentation is wanted, here it comes
                            if ($indentation) $o .= "<td  class='event_data event_description' colspan='$indentation'>&nbsp;</td>\n";
                            $o .= "<td class='event_data event_description' colspan='" . ($tablecols - $indentation) . "'>$field5</td>\n</tr>\n";
                        }
                    }


                //template driven display
                } elseif($pastbirthday) {
                    $o .=  str_replace(array('%date%','%time%','%field1%','%mainfield%','%field3%','%infolink%','%description%','%age%','%ended%'),
                                       array($date,$time,$field1,$mainfield,$field3,$field4,$field5,$agefield,$plugin_tx['calendar']['event_has_ended']), $pastyearlyevents);
                } elseif($event_yearly_array[$keys]) {
                    $o .=  str_replace(array('%bookedout%','%date%','%time%','%field1%','%mainfield%','%field3%','%infolink%','%description%','%age%'),
                                       array($noticefull2,$date,$time,$field1,$mainfield,$field3,$field4,$field5,$agefield), $yearlyevents);
                } else {
                    $o .=  str_replace(array('%bookedout%','%date%','%time%','%field1%','%mainfield%','%field3%','%infolink%','%description%'),
                                       array($noticefull2,$date,$time,$field1,$mainfield,$field3,$field4,$field5), $yearly2events);
                }

            } // end of at least 0 years age conditon
        } // end of yearly events condition

        //===============================//
        //   now  N O R M A L  events    //
        //===============================//

        // checking if the event is past to display it in gray
        // in long events the enddate is considered
        If($event_end_day_array[$keys] && !$event_yearly_array[$keys]) {
            $eventdate = $event_end_year_array[$keys].$event_end_month_array[$keys].$event_end_day_array[$keys];
        } else {
            $eventdate = $event_year_array[$keys].$event_month_array[$keys].$event_day_array[$keys];
        }
        $reallypast = ($eventdate < $today)? " past_event" : '';
        $past = $markpast? $reallypast : '';

        // checking if a long event started before the shown period and continues into the shown period (would be forgotten if not specially considered!)
        //if($x == 0 && $event_year_array[$keys] * 100 + $event_month_array[$keys] < $year * 100 + $month && !$reallypast) $oldlonglastingevent = 1;

        // define the conditions for an event to be selected under a specific month heading
        //----------------------------------------------------------------------------------
        $addcondition = $show_no_past_event? !$past : TRUE;
        if(!$event_yearly_array[$keys]
           && !$event_yearly2_array[$keys]
           && !$event_weekly_array[$keys]
           && (
                ($x && $event_year_array[$keys] == $year  &&  $event_month_array[$keys] == $month && $addcondition)
                || (!$x && $oldlong_array[$keys])
              )
          )

        {
            $month = str_pad($month,2,'0',STR_PAD_LEFT);

            // separate Data and design, 1st: create the Data
            // ==============================================

            //date field
            $date = $event_day_array[$keys];
            // if beginning and end dates are there, these are put one under the other
            if ($event_end_day_array[$keys]) {

                if ($month!=$event_end_month_array[$keys]
                    || $year!=$event_end_year_array[$keys]
                    || $oldlong_array[$keys]) $date .= dpSeperator().$event_month_array[$keys] ;
                if ($year!=$event_end_year_array[$keys]
                    || ($oldlong_array[$keys]
                        && $event_year_array[$keys]!=$event_end_year_array[$keys])) {
                    $date .= dpSeperator().$event_year_array[$keys] ;
                } elseif (dpSeperator()=='.') $date.= ".";
                $date .= '&nbsp;'.$plugin_tx['calendar']['event_date_till_date'] . ' ';
                $date .= $event_end_day_array[$keys].dpSeperator().$event_end_month_array[$keys].dpSeperator().$event_end_year_array[$keys];

            } else $date .= dpSeperator()."$month".dpSeperator().$year;
            if ($event_exceptions_array[$keys]) {
                $date .= ' ' . $plugin_tx['calendar']['event_except'] . ' '
                   .  $event_exceptions_array[$keys];
            }


            //time field
            $startday = '';
            $endday = '';
            if ($event_end_day_array[$keys] && $event_end_time_array[$keys] && !$event_dailytimes_array[$keys]) {

                $startdaynr = date('w',mktime(0,0,0,$event_month_array[$keys],(int)$event_day_array[$keys],$event_year_array[$keys]));
                $startday = ' ' . $shortdayname_array[$startdaynr];
                $enddaynr = date('w',mktime(0,0,0,$event_end_month_array[$keys],(int)$event_end_day_array[$keys],$event_end_year_array[$keys]));
                $endday = ' ' . $shortdayname_array[$enddaynr];
            }
            if ($calendar_cf['show_event_time']){
                $time .= $event_dailytimes_array[$keys]? $plugin_tx['calendar']['event_daily'].' ' : '';
                $time .= $event_time_array[$keys];
                $time .= $startday;
                if ($event_end_time_array[$keys]) {
                    $time .= '&nbsp;' . $plugin_tx['calendar']['event_time_till_time'] . ' ' . $event_end_time_array[$keys] . $endday;
                }
            }

            // create field1 = event_entry1 field
            $field1 = $event_entry1_array[$keys];

            // create $mainfield = event field
            $mainfield = $event_array[$keys];

            // create $field3 = event_entry3 field
            $field3 = $event_entry3_array[$keys];

            // create $field4 = link and link-text or info-text field
            $spacer = ($event_link_txt_array[$keys]) ? ' ' : '';

            // if a link address has been given, there are different cases
            $field4 = '';
            if ($event_link_adr_array[$keys]) {
                $links = explode('|', $event_link_adr_array[$keys]);
                $linktexts = explode('|', $event_link_txt_array[$keys]);
                foreach ($links as $key=>$value) {
                    $addr = substr($value,4);
                    $type = substr($value, 0,4);
                    $icon = $event_link_icon_array[$keys]? icon($type,$past) . $spacer : '';
                    if(!isset($linktexts[$key])) $linktexts[$key] = '';


                    switch ($type) {
                        case 'pfx:':
                        case 'ext:': $field4 .= "<a href='http://".$addr."' target='_blank' title='"
                                             .  strip_tags($addr) .  "'>". $icon . $linktexts[$key] .'</a>';
                            break;

                        case 'int:': $field4 .= '<a href="?'. $addr . '" title="' . urlToPagename($addr)
                                             .  '">'. $icon  . $linktexts[$key] . "</a>\n";
                            break;

                        case 'in?:': $field4 .= '<a href="'.$root . ltrim($addr,'/') . '" title="' . urlToPagename($addr)
                                             .  '">'. $icon  . $linktexts[$key] . "</a>\n";
                            break;

                        case 'doc:':
                        case 'pdf:': $field4 .= '<a href="'.$pth['folder']['downloads']
                                             .  $addr . '" title="' . rawurldecode($addr)
                                             .  '">'. $icon . $linktexts[$key] ."</a>\n";
                            break;
                    }
                }
            }
            $spacer = ($event_link_icon_array[$keys]) ? " " : $spacer;
            if($event_info_icon_array[$keys] && $event_info_txt_array[$keys]) {
                $wider = $event_info_icon_array[$keys]==2? ' wider':'';
                $field4 .= "$spacer<a class='info_pop-up$wider' href='javascript:;'>"
                        . icon("info",$past) . "<span>" . $event_info_txt_array[$keys] . "</span></a>";
            }
            if(!$event_info_icon_array[$keys] && $event_info_txt_array[$keys]) {
                 $field4 .= $spacer . $event_info_txt_array[$keys];
            }


            $field5 = $event_description_array[$keys];

            if($event_bookedout_array[$keys] && !$past) {
                $eventfull  = ' eventfull';
                $noticefull = '<tr style="height:0"><td colspan='.$tablecols.'><p class="bookedout">'
                            . $plugin_tx['calendar']['event_booked_out']
                            . '</p></td></tr>';
                $noticefull2 = $plugin_tx['calendar']['event_booked_out'];
            } else $eventfull = $noticefull = $noticefull2 = '';


            // Now put the data into the design, i.e. the table or the template
            // ================================================================
            if($tablestyle) {

                if($extrasubhead) {
                    $o .= $subhead;
                    $extrasubhead = 0;
                }

                // use date field data
                $o .="$noticefull<tr class='event_data_row$past$eventfull'>\n";
                $o .="<td class='event_data event_date'>";
                $o .= $date;
                $o .= "</td>\n";

                // use time field
                if ($calendar_cf['show_event_time'] && $tablestyle){
                    $o .="<td class='event_data event_time'>$time</td>\n";
                }

                // field 1, the extra field
                if ($calendar_cf['show_event_entry1']) {
                    $o .="<td class='event_data event_entry1'>$field1</td>\n";
                }

                // main entry field
                $o .="<td class='event_data event_main_entry'>$mainfield</td>\n";

                // $field3 secondary entry field
                if ($calendar_cf['show_event_entry3']) {
                    $o .="<td class='event_data event_entry3'>$field3</td>\n";
                }

                // use $field4, the event link etc field
                if ($calendar_cf['show_event_link']) {
                    $o .="<td class='event_data event_link'>$field4</td>\n</tr>\n";
                }

                // use $field 5, the additional description field
                if ($calendar_cf['show_event_description']) {
                    // how much indented is the description going to start?
                    $indentation = $calendar_cf['show_description_nr_of_cells_indented'];
                    if(!$indentation) $indentation=0;
                    // preventing that $indentation gets too big
                    if ($indentation >= $tablecols) $indentation = $tablecols - 1;

                    if (strlen($event_description_array[$keys]) > 2) {
                        if(!$past) $extrasubhead = 1; 
                        $o .= "<tr class='event_data_row".$past."'>\n";
                        // if indentation is wanted, here it comes
                        if ($indentation) $o .= "<td  class='event_data event_description' colspan='$indentation'>&nbsp;</td>\n";
                        $o .= "<td class='event_data event_description' colspan='" . ($tablecols - $indentation) . "'>$field5</td>\n</tr>\n";
                    }
                }

            //template driven display
            } elseif($past) {
                $o .=  str_replace(array('%date%','%time%','%field1%','%mainfield%','%field3%','%infolink%','%description%','%ended%'),
                                   array($date,$time,$field1,$mainfield,$field3,$field4,$field5,$plugin_tx['calendar']['event_has_ended']), $pastsingleevents);
            } else {
                 $o .=  str_replace(array('%bookedout%','%date%','%time%','%field1%','%mainfield%','%field3%','%infolink%','%description%'),
                                   array($noticefull2,$date,$time,$field1,$mainfield,$field3,$field4,$field5), $singleevents);
            }

        } // end of conditions to use an event under a specific month
    } // end of foreach loop, looping through all event and selecting the ones from the currend month

    if($x) {
        if($month==12) {
            $year++;$month=1;
        } else {
            $month++;
        }
    }  
    $x++;

} // end of while loop, looping through the months
if($tablestyle) {$o .= "</table>\n";} else {$o .= "</div>\n";}
