<?php

//=================================================
//
//  function for displaying a calendar view
//  April 2012
//
//=================================================

// Security check
if ((!function_exists('sv')) || preg_match('!calendar'.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'calendar.php!i', sv('PHP_SELF')))die('Access denied');

global $plugin_cf,$calendar_cf,$plugin_tx,$sl,$sn,$su,$admxx,$lang,$datapath;
$o = '';

if(!$number) $number = 1; //preset number of month to show to 1
$yeardisplay = ($month==1 && $number==12)? TRUE : FALSE;
$navigation = $plugin_cf['calendar']['calendar_shows_previous_and_next_month_button']? TRUE : FALSE;

//determine to which eventpage links should go
$eventpage = $useeventpage2?
             $plugin_cf['calendar']['second-calendar_eventpage'] :
             $plugin_tx['calendar']['_event_page'];
$eventpage = $specialeventpage? $specialeventpage : $eventpage;

//2 methods of providing the eventpage: either through the pagename or through the relative URL-address starting with "?" or "./"
if($eventpage) {
    $eventpage = (substr($eventpage,0,1)=='?' || substr($eventpage,0,1)=='.') ? $eventpage : '?'.pagenameToUrl($eventpage);
}

//headline style for yeardisplay and months in columns
if($plugin_cf['calendar']['style_headline_year']) {
    if(strpos($plugin_cf['calendar']['style_headline_year'],'%s')) {
        $head = $plugin_cf['calendar']['style_headline_year'];
    } elseif(preg_match('!^[h|p]d?$!', $plugin_cf['calendar']['style_headline_year'])) {
        $head = '<'.$plugin_cf['calendar']['style_headline_year'].'>%s</'.$plugin_cf['calendar']['style_headline_year'].'>';
    } else $head = '<h4>%s</h4>';
} else $head = '<h4>%s</h4>';

//choosing the event data file
if ($file) {
    $eventfile = $datapath . $file;
}else {
    $eventfile = $datapath . "eventcalendar$lang.txt";
}

//should the week start on Mondays?
$startmon = $plugin_cf[$plugin]['calendar_week_starts_monday'];

//determine with which month to start
//====================================
if(!$month) $month = date("n",time());
if(!$year)  $year  = date("Y",time());

// to prevent XSS injection htmlspecialchars is used by advice of cmb
if(isset($_GET['month'])) $month = htmlspecialchars($_GET['month']);
if(isset($_GET['year']))  $year  = htmlspecialchars($_GET['year']);


// determine period on display to prevent useless calculation of events outside this period
$calendarstart = mktime(NULL,NULL,NULL,$month,1,$year);
$calendarend   = strtotime("+$number months -1 day",$calendarstart);

// if months of different years are to be shown holiday list has to be looped twice
$years = ($month + $number - 1 > 12)? $year.'/'.($year + 1) : $year;
$one_year_only = $years==$year? TRUE : FALSE;



$event_day_array        = array();
$event_month_array      = array();
$event_year_array       = array();
$event_yearmonth_array  = array();
$event_array            = array();
$yearly_array           = array();
$yearly2_array          = array();
$event_type_array       = array();
$event_popup_array      = array();
$holiday_array          = array();
$big_array              = array(); //for entries for the big calendar
$timesort_array         = array(); //used to sort different events on the same day according to starting time

$eventdates       = '';
$event_date_start = '';
$event_date_end   = '';
$event_time       = '';
$event_end_time   = '';
$event_day        = '';
$event_end_day    = '';
$event_month      = '';
$event_end_month  = '';
$event_year       = '';
$event_end_year   = '';
$event_yearmonth  = '';
$event            = '';
$event_entry3     = '';
$event_entry1     = '';
$event_today      = '';
$event_title      = '';


if(is_file($eventfile))
{
    $file_array = file($eventfile);

    // checking the version of the data file
    //=========================================
    if (isset($file_array[0]) && substr($file_array[0],0,19)=="Calendar eventfile ") {
        $version = array_shift($file_array);
        $version = substr($version,19,3);
    } else {
        $version = FALSE;
    }

    // in case a second data file is added to the calendar
    if($addfile && is_file($datapath.$addfile)) {
        $addfile_array = file($datapath.$addfile);
        //checking the version
        if (isset($addfile_array[0]) && substr($addfile_array[0],0,19)=="Calendar eventfile ") {
            $addversion = array_shift($addfile_array);
            $addversion = substr($addversion,19,3);
        } else {
            $addversion = FALSE;
        }
        // if of the same version, merger can go ahead
        if($version==$addversion) {
            $file_array  = array_merge($file_array,$addfile_array);
        } else $o .= '<p class="error">'.$plugin_tx['calendar']['error_cant_combine_eventfiles_of_different_versions']."</p>\n";
    }


    // processing the data file line by line
    //==========================================
    foreach ( $file_array as $line) {

        //process only lines containing data
        if(strpos($line,';'))
        {
            list($eventdates,$event,$event_entry3,,$event_time) = explode( ";", $line);

            $timesort = $event_time? str_replace(':','',$event_time) : 2400 ;

             //eliminate possible "*"-markings meaning "don't show event on marquee"
            if (substr($eventdates,0,1)=="*") $eventdates = substr($eventdates,1);

            if(stristr($eventdates, ',')){
                list($event_date_start,$event_date_end,$event_end_time,$event_entry1) = explode(",",$eventdates,4);


                // checking if there is an * before the endtime, meaning given times of multi day events are daily times
                if (substr($event_end_time,0,1)=="*") {
                    $event_end_time = substr($event_end_time,1); // eliminating any *
                    $daily = TRUE;
                } else $daily = FALSE;

                // checking if there is an * before the enddate, which means event is overbooked
                if (substr($event_date_end,0,1)=="*") {
                    $event_date_end = substr($event_date_end,1); // eliminating any *
                }
                // checking if there is an * before the event_entry1, which means alternative marking in calendar
                if (substr($event_entry1,0,1)=="*") {
                    $event_entry1 = substr($event_entry1,1); // eliminating any *
                    $alt = '3';
                } else $alt = '';


                if($event_date_start){
                    list($event_day,$event_month,$event_year) = explode( dpSeperator(), $event_date_start);
                    $event_start = mktime(0,0,0,$event_month,(int)$event_day,$event_year);
                } else {
                    $event_day        = '';
                    $event_month      = '';
                    $event_year       = '';
                }

                if($event_date_end){
                    list($event_end_day,$event_end_month,$event_end_year) = explode( dpSeperator(), $event_date_end);
                    $event_end = mktime(0,0,0,$event_end_month,(int)$event_end_day,$event_end_year);
                    $short_event_date_end = $event_end_day . dpSeperator() . $event_end_month . dpSeperator() . substr($event_end_year,2,2);
                } else {
                    $event_end_day        = '';
                    $event_end_month      = '';
                    $event_end_year       = '';
                    $event_end            = '';
                    $short_event_date_end = '';
                }

            } else {
                $event_date_start = $eventdates;
                list($event_day,$event_month,$event_year) = explode( dpSeperator(), $event_date_start);
                $event_start = mktime(0,0,0,$event_month,(int)$event_day,$event_year);
                $event_date_end = '';
                $event_end_time = '';
                list($event_day,$event_month,$event_year) = explode( dpSeperator(), $event_date_start);
            }

            //checking for yearly or weekly events (coded in such a way as to keep the original file structure)
            $yearly     = FALSE;
            $yearly2    = FALSE;
            $weekly     = FALSE;
            $exceptions = '';
            $additional = '';
            $exceptionsarray = array();
            $additionaldatesarray = array();

            if (substr($event_entry3,0,3)=="###") {
                $event_entry3 = substr($event_entry3,3); // eliminating any ###
                $yearly = TRUE;
            }
            if (substr($event_entry3,0,3)=="#*#") {
                $event_entry3 = substr($event_entry3,3); // eliminating any #*#
                $yearly2 = TRUE;
            }
            //legacy code (checking for multievents), not used any more
            if (substr($event_entry3,0,3)=="*#*") {
                $event_entry3 = substr($event_entry3,3); // eliminating any #*#
            }
            if (substr($event_entry3,0,3)=="***") {
                $event_entry3 = substr($event_entry3,3); // eliminating any ***
                $weekly = TRUE;

                //only recent data will be considered to save memory
                if($event_start < strtotime("-10 years")) $weekly = FALSE;
            }

            if(strpos($event,'|')){
                $additional = substr($event,0,strpos($event,'|'));
                $dates = explode(",",$additional);
                foreach ($dates as $value) {
                    $trimmeddates = trim($value);
                    list($d,$m,$y) = explode(dpSeperator(),$trimmeddates);
                    $y = rtrim($y,'*');
                    $timestamp = mktime(null,null,null,$m,$d,$y);
                    $additionaldatesarray[] = $timestamp;
                }
                // eliminating additional dates from $event
                $event = substr($event,(strpos($event,'|')+1));
            }
            if(strpos($event_entry3,'|')){
                $exceptions = substr($event_entry3,0,strpos($event_entry3,'|'));
                $dates = explode(",",$exceptions);
                foreach ($dates as $value) {
                    $trimmeddates = trim($value);
                    list($d,$m,$y) = explode(dpSeperator(),$trimmeddates);
                    $timestamp = mktime(null,null,null,$m,$d,$y);
                    $exceptionsarray[] = $timestamp;
                }
                // eliminating exception dates from entry3
                $event_entry3 = substr($event_entry3,(strpos($event_entry3,'|')+1));
            }
            if($size) {
//                $event = strip_tags($event,'<br><br/>');
//                $event_entry3 = strip_tags($event_entry3,'<br><br/>');
            }


        }
        //=========================================================
        // case 1: long events = events lasting longer than 1 day
        //=========================================================
        if($event_date_end && !$weekly)
        {
            //first day of long events
            //=============================

            //give the start and end date a nice form, e.g.: 5.5.-6.6.1930
            $date_txt = $event_day;
            if ($event_month!=$event_end_month || $event_year!=$event_end_year) $date_txt .= dpSeperator().$event_month ;
            if ($event_year!=$event_end_year) $date_txt .= dpSeperator().$event_year ;
            if ($event_year==$event_end_year && dpSeperator()=='.') $date_txt.= ".";
            $date_txt .= " ".$plugin_tx['calendar']['event_date_till_date'] . " ";
            $date_txt .= $event_end_day.dpSeperator().$event_end_month.dpSeperator().$event_end_year;

            // small title attribute popup text
            $title_txt = $event ;
            if($event_entry3 && $calendar_cf['titleattributepopup_entry3']) $title_txt .= ' ' . $event_entry3;
            $title_txt .= ' ' . $date_txt;
            if($daily) {
                $title_txt .= '  ' . $plugin_tx['calendar']['event_daily'] . ' ' . $event_time;
                if($event_end_time) {
                    $title_txt .= $plugin_tx['calendar']['event_time_till_time'] . $event_end_time ;
                }
            }
            if($exceptions) $title_txt .= ' ' . $plugin_tx['calendar']['event_except'] . ' ' . $exceptions;

            // big popup text
            $popup_txt = '';
            if($daily) {
                $popup_txt .= "<span class='time_popup'>" .  $event_time;
                if($event_end_time) {
                    $popup_txt .= $plugin_tx['calendar']['event_time_till_time'] . $event_end_time ;
                }
                $popup_txt .= ' (' . $plugin_tx['calendar']['event_daily'] . ")</span>\n";
            } elseif ($event_time) {
                $popup_txt = "\n<span class='time_popup'>" . $event_time . ' ' . $plugin_tx['calendar']['event_start'] . "</span>\n";
            } 
            $popup_txt .= "\n<span class='date_popup'>$date_txt";
            if($exceptions) $popup_txt .= ' ' . $plugin_tx['calendar']['event_except'] . ' ' . $exceptions;
            $popup_txt .= "</span>\n";
            if($event_entry1) $popup_txt .= "\n<span class='entry1_popup'>$event_entry1</span>\n";
            $popup_txt .= '<span class="event_popup">'  . $event  . "</span>\n";
            if($event_entry3) $popup_txt .= '<span class="entry3_popup">' . $event_entry3 . "</span>\n";

            // text for big calendar
            $big_txt = '<div class="big_entry">';
            if ($calendar_cf['bigcalendar_write_time']){
                if($event_time) {
                    $big_txt .= '<span class="big_time">' . $event_time . '</span> ';
                } else {
                    $big_txt .= '<span class="big_time">' . $calendar_cf['bigcalendar_symbol_if_no_time_given'] . '</span> ';
                }
            }
            $big_txt .= $calendar_cf['bigcalendar_write_entry1']? '<span class="big_entry1">' . $event_entry1 . '</span> ' : '';
            $big_txt .= $calendar_cf['bigcalendar_write_event']?  '<span class="big_event">'  . $event . '</span> ' : '';
            $big_txt .= $calendar_cf['bigcalendar_write_entry3']? '<span class="big_event">'  . $event_entry3 . '</span>' : '';
            $big_txt .= '</div>';

            //enter first day of long events into array
            array_push($event_year_array,   $event_year);
            array_push($event_month_array,  $event_month);
            array_push($event_day_array,    $event_day);
            array_push($event_array,        $title_txt);
            array_push($event_type_array,   'startevent'.$alt);
            array_push($event_popup_array,  $popup_txt);
            array_push($yearly_array,       $yearly);
            array_push($yearly2_array,      $yearly2);
            array_push($holiday_array,      '');
            array_push($big_array,          $big_txt);
            array_push($timesort_array,     $timesort);

            // last day of long events
            //=============================
            // big popup text for last day
            if (!$daily) {
                $popup_txt  = "\n<span class='date_popup'>" . $date_txt;
                if($exceptions) $popup_txt .= ' ' . $plugin_tx['calendar']['event_except'] . ' ' . $exceptions;
                $popup_txt .= "</span>\n";
                if($event_entry1) $popup_txt .= "\n<span class='entry1_popup'>$event_entry1</span>\n";
                $popup_txt .= "\n<span class='event_popup'>"  . $event  . "</span>\n";
                if($event_entry3) $popup_txt .= "\n<span class='entry3_popup'>$event_entry3</span>\n";
                if($event_end_time) $popup_txt .= "\n<span class='endtime_popup'>" . $event_end_time . ' ' . $plugin_tx['calendar']['event_end'] . "</span>\n";

                // text for big calendar
                $big_txt = '<div class="big_entry">';
                if ($calendar_cf['bigcalendar_write_time']){
                    $big_txt .= '<span class="big_time">' . $calendar_cf['bigcalendar_symbol_if_no_time_given'] . '</span> ';
                }
                $big_txt .= $calendar_cf['bigcalendar_write_entry1']? '<span class="big_entry1">' . $event_entry1 . '</span> ' : '';
                $big_txt .= $calendar_cf['bigcalendar_write_event']?  '<span class="big_event">'  . $event . '</span> ' : '';
                $big_txt .= $calendar_cf['bigcalendar_write_entry3']? '<span class="big_event">'  . $event_entry3 . '</span>' : '';
                $big_txt .= '</div>';
            }

            //enter last day of long events into array
            array_push($event_year_array,   date('Y',$event_end));
            array_push($event_month_array,  date('m',$event_end));
            array_push($event_day_array,    date('d',$event_end));
            array_push($event_array,        $title_txt);
            array_push($event_type_array,   'endevent'.$alt);
            array_push($event_popup_array,  $popup_txt);
            array_push($yearly_array,       $yearly);
            array_push($yearly2_array,      $yearly2);
            array_push($holiday_array,      '');
            array_push($big_array,          $big_txt);
            if($daily){
                array_push($timesort_array, $timesort);
            } else {
                array_push($timesort_array, 2400);
            }

            // middle days of long events
            //================================

            // big-popup text for mid event days
            if (!$daily) {
                $popup_txt  = "\n<span class='date_popup'>$date_txt";
                if($exceptions) $popup_txt .= ' ' . $plugin_tx['calendar']['event_except'] . ' ' . $exceptions;
                $popup_txt .= "</span>\n";
                if($event_entry1) $popup_txt .= "\n<span class='entry1_popup'>$event_entry1</span>\n";
                $popup_txt .= '<span class="event_popup">'  . $event  . "</span>\n";
                if($event_entry3) $popup_txt .= '<span class="entry3_popup">' . $event_entry3 . "</span>\n";
            }

            // enter mid event day into array, limit number of mid event days to 90 days
            // php memory limit may pose problems
            if($event_end - $event_start < 7776000) {
                $i = 1;
                $date = strtotime("+".$i." day",$event_start);
                while($date < $event_end) {
                    $exception = FALSE;
                    if($exceptions){
                        foreach ($exceptionsarray as $value) {
                            if($value==$date) {$exception = TRUE;break;}
                        }
                    }
                    if(!$exception){
                        array_push($event_year_array,   date('Y',$date));
                        array_push($event_month_array,  date('m',$date));
                        array_push($event_day_array,    date('d',$date));
                        array_push($event_array,        $title_txt );
                        array_push($event_type_array,   'midevent'.$alt);
                        array_push($event_popup_array,  $popup_txt);
                        array_push($yearly_array,       $yearly);
                        array_push($yearly2_array,      $yearly2);
                        array_push($holiday_array,      '');
                        array_push($big_array,          $big_txt);
                        if($daily){
                            array_push($timesort_array, $timesort);
                        } else {
                            array_push($timesort_array, 2400);
                        }
                    }
                    $i++;
                    $date = strtotime("+".$i." day",$event_start);
                }
            }
            $date_txt = '';


        //=========================
        // case 2: weekly events
        //=========================
        } elseif($weekly) {

            //in case no end is given or end is too far in future, 10 years planning maximum for weekly events
            $twoyearsahead = strtotime("+10 years", $event_start);
            if(!$event_end)  $event_end = $twoyearsahead;
            if($event_end - $event_start > 63072000) $event_end = $twoyearsahead;

            // small title attribute popup text
            $title_txt = $event;
            if($event_entry3 && $calendar_cf['titleattributepopup_entry3']) {$title_txt .= ': ' . $event_entry3;}
            if($event_time) {$title_txt .= ' ' . $event_time;}
            if($event_end_time) {$title_txt .=  $plugin_tx['calendar']['event_time_till_time'].$event_end_time;}
            $title_txt .=  ' (' . $plugin_tx['calendar']['event_weekly'];
            if($event_date_end) {$title_txt .=  ' ' . $plugin_tx['calendar']['event_date_till_date']  . ' ' . $short_event_date_end;}
            if($exceptions) $title_txt .= ' ' . $plugin_tx['calendar']['event_except'] . ' ' . $exceptions;
            $title_txt .=  ')';

            // text for big calendar
            $big_txt = '<div class="big_entry">';
            if ($calendar_cf['bigcalendar_write_time']){
                if($event_time) {
                    $big_txt .= '<span class="big_time">' . $event_time . '</span> ';
                } else {
                    $big_txt .= '<span class="big_time">' . $calendar_cf['bigcalendar_symbol_if_no_time_given'] . '</span> ';
                }
            }
            $big_txt .= $calendar_cf['bigcalendar_write_entry1']? '<span class="big_entry1">' . $event_entry1 . '</span> ' : '';
            $big_txt .= $calendar_cf['bigcalendar_write_event']? '<span class="big_event">' . $event . '</span> ' : '';
            $big_txt .= $calendar_cf['bigcalendar_write_entry3']? '<span class="big_event">' . $event_entry3 . '</span>' : '';
            $big_txt .= '</div>';

            // big popup text
            if($event_time) $popup_txt = "\n<span class='time_popup'>" . $event_time . $plugin_tx['calendar']['event_time_till_time'] . $event_end_time . "</span>\n";
            if($event_entry1) $popup_txt .= '<span class="entry1_popup">' . $event_entry1 . "</span>\n";
            $popup_txt .= '<span class="event_popup">'  . $event  . "</span>\n";
            if($event_entry3) $popup_txt .= '<span class="entry3_popup">' . $event_entry3 . "</span>\n";
            $popup_txt .=  '<span class="date_popup">' .' (' . $plugin_tx['calendar']['event_weekly'];
            if($event_date_end) {$popup_txt .=  ' ' . $plugin_tx['calendar']['event_date_till_date'] . '&nbsp;' . $short_event_date_end;}
            if($exceptions) $popup_txt .= ' ' . $plugin_tx['calendar']['event_except'] . ' ' . $exceptions;
            if($additional) $popup_txt .= ' ' . $plugin_tx['calendar']['event_additional'] . ' ' . $additional;
            $popup_txt .=  ')' . "</span>\n";

            //determining the dates of the weekly event
            $weekly_event = $event_start;
            while($weekly_event < $event_end) {
                $exception = FALSE;
                if($exceptions){
                    foreach ($exceptionsarray as $value) {
                        if($value==$weekly_event) {$exception = TRUE;break;}
                    }
                }
                if( !$exception
                    && $weekly_event >= $calendarstart
                    && $weekly_event <= $calendarend) {
                    array_push($event_year_array,   date('Y',$weekly_event));
                    array_push($event_month_array,  date('m',$weekly_event));
                    array_push($event_day_array,    date('d',$weekly_event));
                    array_push($event_array,        $title_txt );
                    array_push($event_type_array,   'shortevent'.$alt);
                    array_push($event_popup_array,  $popup_txt);
                    array_push($yearly_array,       '');
                    array_push($yearly2_array,      '');
                    array_push($holiday_array,      '');
                    array_push($big_array,          $big_txt);
                    array_push($timesort_array,     $timesort);
                }
                $weekly_event = strtotime("+1 week",$weekly_event);
            }
            if($additional){
                foreach ($additionaldatesarray as $value) {
                    array_push($event_year_array,   date('Y',$value));
                    array_push($event_month_array,  date('m',$value));
                    array_push($event_day_array,    date('d',$value));
                    array_push($event_array,        $title_txt);
                    array_push($event_type_array,   'shortevent'.$alt);
                    array_push($event_popup_array,  $popup_txt);
                    array_push($yearly_array,       '');
                    array_push($yearly2_array,      '');
                    array_push($holiday_array,      '');
                    array_push($big_array,          $big_txt);
                    array_push($timesort_array,     $timesort);
                }
            }



        //=====================================================================================
        // case 3 single day events (can have same event on additional days), yearly events
        //=====================================================================================
        } else {

            //time styling
            $time_txt = '';
            if($event_time) $time_txt .= $event_time;
            if($event_end_time) $time_txt .= $plugin_tx['calendar']['event_time_till_time'] . $event_end_time ;

            // small title attribute popup text
            $title_txt = '';
            if($time_txt) $title_txt = $time_txt . ' ';
            $title_txt .=  $event;
            if($event_entry3 && $calendar_cf['titleattributepopup_entry3']) $title_txt .= ' ' . $event_entry3;

            // big calendar text
            if ($yearly) {
                $big_txt = '<div class="anniversary">';
                if ($calendar_cf['bigcalendar_write_time']){
                    if($event_time) {
                        $big_txt .= '<span class="big_time">' . $event_time . '</span> ';
                    } else {
                        $big_txt .= '<span class="big_time">' . $calendar_cf['bigcalendar_symbol_if_no_time_given'] . '</span> ';
                    }
                }
                $big_txt .= $calendar_cf['bigcalendar_write_entry1']? '<span class="big_entry1">' . $event_entry1 . '</span> ' : '';
                $big_txt .= $event . ' ';
                $big_txt .= $calendar_cf['bigcalendar_anniversary_write_entry3']? $event_entry3.' ' : '';
            } else {
                $big_txt  = '<div class="big_entry">';
                if ($calendar_cf['bigcalendar_write_time']){
                    if($event_time) {
                        $big_txt .= '<span class="big_time">' . $event_time . '</span> ';
                    } else {
                        $big_txt .= '<span class="big_time">' . $calendar_cf['bigcalendar_symbol_if_no_time_given'] . '</span> ';
                    }
                }
                $big_txt .= $calendar_cf['bigcalendar_write_entry1']? '<span class="big_entry1">' . $event_entry1 . '</span> ' : '';
                $big_txt .= $calendar_cf['bigcalendar_write_event']? '<span class="big_event">' . $event . '</span> ' : '';
                $big_txt .= $calendar_cf['bigcalendar_write_entry3']? '<span class="big_event">' . $event_entry3 . '</span>' : '';
                $big_txt .= '</div>';
            }


            // popup text
            $popup_txt = '';
            $y = $yearly? ' anniversary' : '';
            if($time_txt) $popup_txt .= "\n<span class='time_popup$y'>" . $time_txt . "</span>\n";
            if($event_entry1) $popup_txt .= "<span class='entry1_popup$y'>" . $event_entry1 . "</span>\n";
            $popup_txt .= "<span class='event_popup$y'>"  . $event  . "</span>\n";
            if($event_entry3) $popup_txt .= "<span class='entry3_popup$y'>" . $event_entry3 . "</span>\n";


            array_push($event_year_array,     $event_year);
            array_push($event_month_array,    $event_month);
            array_push($event_day_array,      $event_day);
            array_push($event_array,          $title_txt);
            if($yearly) {
                array_push($event_type_array, 'birthday');
            } else {
                array_push($event_type_array, 'shortevent'.$alt);
            }
            array_push($event_popup_array,    $popup_txt);
            array_push($yearly_array,         $yearly);
            array_push($yearly2_array,        $yearly2);
            array_push($holiday_array,        '');
            array_push($big_array,            $big_txt);
            $birthdaytime = ($yearly && !$event_time)? 0 : $timesort; //if no time is given, birthday will be announced first
            array_push($timesort_array,       $birthdaytime);

            if($additional){
                foreach ($additionaldatesarray as $value) {
                    array_push($event_year_array,   date('Y',$value));
                    array_push($event_month_array,  date('m',$value));
                    array_push($event_day_array,    date('d',$value));
                    array_push($event_array,        $title_txt);
                    array_push($event_type_array,   'shortevent'.$alt);
                    array_push($event_popup_array,  $popup_txt);
                    array_push($yearly_array,       '');
                    array_push($yearly2_array,      '');
                    array_push($holiday_array,      '');
                    array_push($big_array,          $big_txt);
                    array_push($timesort_array,     $timesort);
                }
            }

        }
    }
}



//==========================
// processing holyday list
//==========================
// first check if function exists (thanx to cmb!)
if (!function_exists('easter_date')) {
    function easter_date($year) {
    $g = $year % 19 + 1;
    $s = (11 * $g - 6) % 30;
    $s = $s == 0 ? $s - 1 : ($s == 1 && $g >= 12 ? $s - 2 : $s);
    $fm = mktime(0, 0, 0, 4, 19 - $s, $year);
    $w = date('w', $fm);
    $e = mktime(0, 0, 0, 4, 19 - $s + 7 - $w, $year);
    return $e;
    }
}
// now start processiong holyday list
// list has to be looped twice in case display extends over Dec-Jan
for($i=0 ;$i<2 ;$i++){
    $displayyear = $year + $i;
    $easter = date("m/d/Y",easter_date($displayyear));

    $holidayarray = explode(";",$plugin_tx['calendar']['holydays']);
    foreach ($holidayarray as $value) {
        list($holiday_date,$holiday_name) = explode(",",$value);
        // holidays depending on the easter date
        if(strpos($holiday_date,'easter')!==FALSE){
            $easter_add = str_replace('easter','',$holiday_date);
            $holiday_day   = date("d",strtotime("$easter $easter_add"));
            $holiday_month = date("m",strtotime("$easter $easter_add"));
        // holidays on a certain weekday depending on another fixed date
        } elseif (strpos($holiday_date,'day')!==FALSE) {
            $complex_array = explode(" ",$holiday_date);
            $complex_starting_date = array_pop($complex_array);
            list($complex_day,$complex_month) = explode(dpSeperator(),$complex_starting_date);
            $complex_add = implode(' ',$complex_array);
            $holiday_day   = date("d",strtotime("$complex_add", mktime(0,0,0,$complex_month,$complex_day,$displayyear)));
            $holiday_month = date("m",strtotime("$complex_add", mktime(0,0,0,$complex_month,$complex_day,$displayyear)));
        // fixed holidays
        } else {
            list($holiday_day,$holiday_month) = explode(dpSeperator(),$holiday_date);
        }
        array_push($event_year_array,  $displayyear);
        array_push($event_month_array, $holiday_month);
        array_push($event_day_array,   $holiday_day);
        array_push($event_array,       '');
        array_push($event_type_array,  'holiday');
        array_push($event_popup_array, '');
        array_push($yearly_array,      '');
        array_push($yearly2_array,     '');
        array_push($holiday_array,     $holiday_name);
    }
    if($one_year_only) break;
}
// end of entering holidays
//··························


$event_popup_with_time  = '';
$event_popup            = '';
$showtoday              = '';
$shortevent             = '';
$startevent             = '';
$midevent               = '';
$endevent               = '';
$shortevent3            = '';
$startevent3            = '';
$midevent3              = '';
$endevent3              = '';
$birthday               = '';
$borderstartevent       = '';
$bordermidevent         = '';
$borderendevent         = '';
$linkstart              = '';
$linkend                = '';
$holiday                = '';
$dayname                = '';
$day_name               = '';
$popup_divider          = '';
$bigcal_txt             = '';
$zcalculation           = '';
$event_titles           = array();
$bigcal_txts            = array();
$event_popups           = array();
$timesorting            = array();


$o .= "\n\n<!-- Calendar Plugin -->\n\n";


//writing a headline for multi months and year calendars in columns
if($columns) {
    if($yeardisplay){
        $year_prev = $year - 1;
        $year_next = $year + 1;
        if($navigation) {
        // provide correct href in case calendar is called via admin
        $hrefpart = $admxx ? "&calendar&admxx=$admxx" : "$su";

            $calhead  = $year_prev + 3 > date("Y")
                      ? "\n<a href='$sn?$hrefpart&amp;year=$year_prev' rel='nofollow' title='"
                      . $plugin_tx['calendar']['link-button_prev_year'] . "'>&lsaquo;&lsaquo;&nbsp; "
                      . "</a>\n"
                      : "\n".'&nbsp; &nbsp;&nbsp;';
            $calhead .= "</a>\n&nbsp;$year&nbsp;\n";
            $calhead .= $year_next - 3 < date("Y")
                      ? "<a href='$sn?$hrefpart&amp;year=$year_next' rel='nofollow' title='"
                      . $plugin_tx['calendar']['link-button_next_year'] . "'> &nbsp;&rsaquo;&rsaquo;"
                      . "</a>\n"
                      : "\n".'&nbsp; &nbsp;&nbsp;';
        } else {
            $calhead = $year;
        }

    } elseif($navigation) {

        if($month <= 1){$month_prev = 12; $year_prev = $year - 1;}
        else {$month_prev = $month - 1; $year_prev = $year;}
        if($month >= 12){$month_next=1; $year_next = $year + 1;}
        else {$month_next = $month + 1; $year_next = $year;}

        $calhead  = $year_prev + 2 > date("Y")
                  ? "\n<a href='$sn?$su&amp;month=$month_prev&amp;year=$year_prev' rel='nofollow' title='"
                  . $plugin_tx['calendar']['link-button_prev_month'] . "'>&lsaquo;&lsaquo;&nbsp; "
                  . "</a>\n"
                  : "\n".'&nbsp; &nbsp;&nbsp;';
        $calhead .= "</a>\n&nbsp;$years&nbsp;\n";
        $calhead .= $year_next - 2 < date("Y")
                  ? "<a href='$sn?$su&amp;month=$month_next&amp;year=$year_next' rel='nofollow' title='"
                  . $plugin_tx['calendar']['link-button_next_month'] . "'> &nbsp;&rsaquo;&rsaquo;"
                  . "</a>\n"
                  : "\n".'&nbsp; &nbsp;&nbsp;';

    } else {
        $calhead = $years;
    }

    $o .= '<div style="clear:both"></div>'
       .  "\n<div class='calendarheadline'>\n"
       .  sprintf($head, $calhead)
       .  "\n</div>\n\n"
       .  "<table class='columns' style='width:100%;table-layout:fixed;'>\n<tr>\n";
}  


$extramonths=0;
while ($extramonths < $number) {

    $month = $extramonths? ($month + 1) : $month;
    if($month > 12) {$month-=12;$year++;}

    $monthnames = explode(",", $plugin_tx['calendar']['names_of_months']);
    $textmonth = $monthnames[$month - 1];

    $today    =  date("j", time());
    // highlight today but only for current month & year
    $today    = ($month == date("n",time()) && $year == date("Y",time())) ? $today : 32;

    // find number of days in a given month
    $days     = date("t",mktime(1,1,1,$month,1,$year));
    $dayone   = date("w",mktime(1,1,1,$month,1,$year));
    $daylast  = date("w",mktime(1,1,1,$month,$days,$year));
    $dayarray = explode(",", $plugin_tx['calendar']['names_of_days']);

    //start new row depending on number of columns wanted
    if($columns && ($extramonths % $columns == 0) && $extramonths > 0) {
        $o .= "</tr>\n<tr>\n";
        $zcalculation++;
    } 
    if(!$columns && $extramonths>0) $zcalculation++;

    //============================
    //    building the calendar
    //============================

    //z-index calculation
    if($plugin_cf['calendar']['z-index'] > 0) {
        $z = $plugin_cf['calendar']['z-index'] - $zcalculation;
        $zindex = " style='position:relative;z-index:$z;'";
    } else $zindex = '';

    if($columns) $o .= '<td class="months_in_columns">'."\n";
    $o .= "<table class='${size}calendar_main'$zindex>\n";

    // 1st row: headline with month and year (year only if needed)
    //============================================================
    $o .= "<tr class='monthheadline'>\n<td colspan='7'>\n";
    $display_year = (!$one_year_only || $year != date("Y"))? true : false;
    if($columns) $display_year = !$one_year_only;
    $monthyear = $display_year? $textmonth.' '.$year : $textmonth;

    if($navigation && !$extramonths && !$columns) {
        if($month <= 1){$month_prev = 12; $year_prev = $year - 1;}
        else {$month_prev = $month - 1; $year_prev = $year;}
        if($month >= 12){$month_next=1; $year_next = $year + 1;}
        else {$month_next = $month + 1; $year_next = $year;}
        // provide correct href in case calendar is called via admin
        $hrefpart = $admxx ? "&calendar&admxx=$admxx" : "$su";

        if($year_prev + 2 > date("Y")) {
        $calhead =  "\n"
                 .  "<a class='prev_next_button' href='$sn?$hrefpart&amp;month=$month_prev&amp;year=$year_prev' title='"
                 .  $plugin_tx['calendar']['link-button_prev_month'];
        $calhead .= $size?  "'>&lsaquo;&lsaquo;&nbsp; " : "'>&lt;&lt;";
        $calhead .=  '</a>';
        } else {
            $calhead = "\n".'&nbsp; &nbsp;&nbsp;';
        }

        $calhead .= "&nbsp;$monthyear&nbsp;";

        if($year_prev - 2 < date("Y")) {
            $calhead .= "<a class='prev_next_button' href='$sn?$hrefpart&amp;month=$month_next&amp;year=$year_next' title='"
                     .  $plugin_tx['calendar']['link-button_next_month'];
            $calhead .= $size?  "'> &nbsp;&rsaquo;&rsaquo;" : "'>&gt;&gt;";
            $calhead .=  '</a>';
        } else {
            $calhead .= "\n".'&nbsp; &nbsp;&nbsp;';
        }

    } else {
        // adding an empty line when months are displayed one under another without columns
        $calhead = (!$columns && $extramonths)? tag('br').$monthyear:$monthyear;
    }

    if($size && $j=$calendar_cf['bigcalendar_month_year_headline_style']) {
        $o .= "<div class='bigcalendar_monthyear'><$j class='bigcalendar_monthyear_color'><span>$calhead</span></$j></div>\n";
    } elseif ($size) {
        $o .= "<div class='bigcalendar_monthyear bigcalendar_monthyear_color'><span>$calhead</span></div>\n";
    } else $o .= "<div class='calendar_monthyear'>$calhead</div>\n";

    $o .="</td>\n</tr>\n";


    // 2nd row: weekday names
    //===================
    if(!$extramonths && !$columns) {
        $o .="<tr class='weekdaynames'>\n";

        for($i=0; $i <= 6; $i++) {
            if($startmon){$j = $i+1;} else {$j = $i;}
            if($j==7)$j=0;
            if($j==date('w') && $month==date('n') && $year==date('Y')) {
                $o .= "<td class='calendar_daynames'><span class='today'>$dayarray[$j]</span></td>\n";
            } else {
                $o .= "<td class='calendar_daynames'><span>$dayarray[$j]</span></td>\n";
            }
        }
        $o .= "</tr>\n";
    }


    // starting the numbered day fields
    //==================================

    if($startmon){$span1 = $dayone-1;}else{$span1 = $dayone;}
    if($span1 == -1)$span1 = 6;

    if($startmon){$span2 = 7 - $daylast;}else{$span2 = 6 - $daylast;}
    if($span2 == 7)$span2 = 0;

    //$o .= print_r($event_year_array);
    //$o .= print_r($event_array);



    // going through all the days of the month
    for($i = 1; $i <= $days; $i++):
        $dayofweek = date("w",mktime(1,1,1,$month,$i,$year));

        if($startmon)$dayofweek = $dayofweek - 1;
        if($dayofweek == -1)$dayofweek = 6;


        // going through all the events and selecting the ones that occur on the day
        foreach($event_year_array as $keys=>$temp)
        {

            if( ($event_year_array[$keys] == $year
                && $event_month_array[$keys] == $month
                && $event_day_array[$keys] == $i
                && !$yearly_array[$keys])
              ||
                ($yearly2_array[$keys]
                and $event_month_array[$keys] == $month
                and $event_day_array[$keys] == $i)
              )
            {
                //producing variables ($shortevent, $startevent, $midevent, $endevent. $holiday) that can be filled with a date
                $$event_type_array[$keys]=$i;
                // extracting the holiday name
                $day_name = $holiday_array[$keys];

                //writing all occuring events of the day into arrays
                If($event_array[$keys]) {
                    $event_titles[] = strip_tags($event_array[$keys]);
                    $bigcal_txts[]  = $big_array[$keys];
                    $event_popups[] = $event_popup_array[$keys];
                    $timesorting[]  = $timesort_array[$keys];
                }
            }

            //Birthday case
            if($yearly_array[$keys]
                and $event_month_array[$keys] == $month
                and $event_day_array[$keys] == $i)
            {
                $birthday=$i;
                $age = $year - $event_year_array[$keys];
                if ($age >= 5){
                    $age .= " " . $plugin_tx['calendar']['age_5_and_more_years_old'];
                }
                elseif ($age >= 2 and $age < 5){
                    $age .= " " . $plugin_tx['calendar']['age_2-4_years_old'];
                }
                else {
                    $age .= " " . $plugin_tx['calendar']['age_1_year_old'];
                }
                $event_titles[] = strip_tags($event_array[$keys]);
                $event_popups[] =  $event_popup_array[$keys]
                                .  '<span class="date_popup anniversary">' . $age . "</span>\n";
                $age = $calendar_cf['bigcalendar_anniversary_write_age']? $age : '';
                $bigcal_txts[] = $big_array[$keys] . $age. "</div>\n";;
                $timesorting[] = $timesort_array[$keys];
            }
        }

        //sorting the events of the day according to time
        array_multisort($timesorting,$event_titles,$bigcal_txts,$event_popups);
        $event_title = implode(' &nbsp;|&nbsp; ',$event_titles);
        $bigcal_separator = $calendar_cf['bigcalendar_line_between_entries']? tag('hr') : '';
        $bigcal_txt = implode($bigcal_separator,$bigcal_txts);
        $event_popup = implode('<span class="cal_separator"></span>',$event_popups);


        $tableday = $i;
        if($i == 1 || $dayofweek == 0) {
            $o .= "<tr class=calendardays>\n";
            if($span1 > 0 && $i == 1) $o .= "<td class='calendar_noday' colspan='$span1'>&nbsp;</td>\n";
        }

        //Coloring the weekend days
        $weekend_day_1 = $plugin_cf['calendar']['calendar_weekend_day_1'] -1;
        $weekend_day_2 = $plugin_cf['calendar']['calendar_weekend_day_2'] -1;
        if ($dayofweek == $weekend_day_1 || $dayofweek == $weekend_day_2) {
            $daytype = "calendar_weekend";
        } else {
            $daytype = "calendar_workday";
        }

        $x = $calendar_cf['dont_underline_longevents']? '2':'';

        $showtoday   =  $i == $today       ?  " ${size}calendar_today"        : '';
        $shortevent  =  $i == $shortevent  ?  " calendar_shortevent"          : '';
        $startevent  =  $i == $startevent  ?  " calendar_startevent$x"        : '';
        $midevent    =  $i == $midevent    ?  " calendar_midevent$x"          : '';
        $endevent    =  $i == $endevent    ?  " calendar_endevent$x"          : '';
        $shortevent3 =  $i == $shortevent3 ?  " calendar_shortevent3"         : '';
        $startevent3 =  $i == $startevent3 ?  " calendar_startevent3"         : '';
        $midevent3   =  $i == $midevent3   ?  " calendar_midevent3"           : '';
        $endevent3   =  $i == $endevent3   ?  " calendar_endevent3"           : '';
        $birthday    =  $i == $birthday    ?  " ${size}calendar_birthday"     : '';
        $holiday     =  $i == $holiday     ?  " calendar_holiday"      : '';


        //setting the popup class, can be different for every weekday
        if($size) {
            $popup_class = $plugin_cf['calendar']['popup_direction_big_calendar']? $plugin_cf['calendar']['popup_direction_big_calendar'] : 'left';
        } else {
            $popup_class = $plugin_cf['calendar']['popup_direction_small_calendar']? $plugin_cf['calendar']['popup_direction_small_calendar'] : 'left';
        }
        if($popup_class == 'down') $popup_class = $popup_class.$dayofweek;
        $popup_class = " class='$popup_class'";

        $eventday = (   $startevent
                     || $midevent
                     || $endevent
                     || $shortevent
                     || $startevent3
                     || $midevent3
                     || $endevent3
                     || $shortevent3
                     || $birthday
                     || $holiday)? ' eventday':'';

        if($eventday){
            if(!$size && !$calendar_cf['calendar-popup_big']) {
                //default setting: title-style info in calendar
                $divider = ($holiday && ($event_title || $birthday))?  '&nbsp; | &nbsp;' : '';
                $linkstart = $eventpage ? "<a href='" . $eventpage . "&amp;month=$month&amp;year=$year'
                                          title='$day_name$divider$event_title'>"
                                          :
                                          "\n<a class='info_pop-up' href='javascript:;' style='cursor:default'
                                          title='$day_name$divider$event_title'>";
                $divider = '';
                $linkend = "</a>";
            } else {
                //popups in calendar
                if($holiday) $dayname = "\n<span class='holiday_name'>$day_name</span>";
                $linkstart = $eventpage ? "\n<a class='info_pop-up' href='" . $eventpage . "&amp;month=$month&amp;year=$year'>"
                                          :
                                          "\n".'<a class="info_pop-up" href="javascript:;" style="cursor:default">';
                $linkend = "<span$popup_class>$dayname$event_popup</span></a>";
            }
        }
        //If no event occurs, the day number of bigcalendar can be treated by css via <span>
        if($size && !$linkstart) {$linkstart = '<span class="no_event">'; $linkend ='</span>';}

        if($size) {
            $bigcalendar = '<div class="big_holidayname">' . $day_name . '</div>';
            $bigcalendar .= $bigcal_txt;
        } else $bigcalendar = '';

        $headnotice =  ($i == $today && $size)? ' '. $plugin_tx['calendar']['notice_today'] : '';


        $o .= '<td class="'
           .  $daytype
           .  $holiday
           .  $showtoday
           .  $eventday
           .  $shortevent
           .  $startevent
           .  $midevent
           .  $endevent
           .  $shortevent3
           .  $startevent3
           .  $midevent3
           .  $endevent3
           .  $birthday
           .  '">'
           .  $linkstart
           .  $tableday
           .  $headnotice
           .  $linkend
           .  $bigcalendar
           .  "</td>\n";

        if($i == $days && $span2 > 0)  $o .= "<td class='calendar_noday' colspan='$span2'>&nbsp;</td>\n";
        if($dayofweek == 6 || $i == $days) $o .= "</tr>\n";

        //resetting all variables
        $calendar_eventday      = '';
        $calendar_birthday      = '';
        $linkstart              = '';
        $linkend                = '';
        $event_title            = '';
        $event_popup_with_time  = '';
        $event_popup            = '';
        $border                 = '';
        $borderstartevent       = '';
        $bordermidevent         = '';
        $borderendevent         = '';
        $popup_class            = '';
        $birthday               = '';
        $age                    = '';
        $holiday                = '';
        $dayname                = '';
        $day_name               = '';
        $bigcal_txt             = '';
        $shortevent3            = '';
        $startevent3            = '';
        $midevent3              = '';
        $endevent3              = '';
        $event_titles           = array();
        $bigcal_txts            = array();
        $event_popups           = array();
        $timesorting            = array();

    endfor;

    $o .= "</table>\n";
    if($columns) $o .= "</td>\n";

    $extramonths++;
}

if($columns) $o .= "</tr></table>\n";
$o .= $size? '<p class="hint_under_bigcalendar">' . $plugin_tx['calendar']['hint_mouseover_in_calendar'] . "</p>\n" : '';

$o .= "\n\n<!-- End Calendar Plugin -->\n\n";
