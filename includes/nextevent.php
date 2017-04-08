<?php
// ***********************************************************************
// *                                                                     *
// *        Announcing the Next Coming Event(s) Marquee-Style            *
// *                                                                     *
// *        v. 1.4 beta Jan 2012                                         *
// ***********************************************************************

global $hjs,$calendar_cf,$plugin_tx,$sl,$lang,$calendar_jqueryplugin,$datapath;

// Security check
if ((!function_exists('sv')))die('Access denied');

$now = strtotime('now');

//$spacer is used in case several multiday events are happening now. They will be separated by 60 seconds in the sorting list
$spacer = 60;
$empty  = '';
$event_sorting_array  = array();
$event_1stline_array  = array();
$event_2ndline_array  = array();
$event_headline_array = array();

$o = "\n\n<!-- Calendar Plugin Marquee Funktion -->\n\n";
$remember_event = '';

// determining which file to read
if ($file) {
    $eventfile = $datapath.$file;
}else {
    $eventfile = $datapath."eventcalendar$lang.txt";
}

if(is_file($eventfile))
{
    $file_array = file($eventfile);
    if($addfile && is_file($datapath.$addfile)) {
        $addfile_array = file($datapath.$addfile);
        $file_array  = array_merge($file_array,$addfile_array);
    }

    // processing the data file line by line
    //==========================================
    foreach ( $file_array as $line) {

        if (strpos($line,';') && substr($line,0,1)!="*") {

            $yearly               = FALSE;
            $yearly2              = FALSE;
            $weekly               = FALSE;
            $exceptions           = '';
            $additional           = '';
            $exceptionsarray      = array();
            $additionaldatesarray = array();
            $txt                  = '';

            $line = str_replace(array('<br>','<br/>'),', ',$line);
            $line = strip_tags($line);
            list($eventdates,$event,$event_entry3,,$eventtime) = explode( ";", $line);
            list($event_date_start,$event_end_date,$event_end_time,$event_entry1) = explode(",",$eventdates,4);

            // checking if there is an * before the endtime, meaning given times of multi day events are daily times
            if (substr($event_end_time,0,1)=="*") {
                $event_end_time = substr($event_end_time,1); // eliminating any *
                $dailytimes = TRUE;
            } else $dailytimes = FALSE;

            // checking if there is an * before the enddate, which means event is overbooked
            if (substr($event_end_date,0,1)=="*") {
                $event_end_date = substr($event_end_date,1); // eliminating any *
            } 
            // checking if there is an * before the event_entry1, which means alternative marking in calendar
            if (substr($event_entry1,0,1)=="*") {
                $event_entry1 = substr($event_entry1,1); // eliminating any *
            }

            //extracting the dates
            list($event_day,$event_month,$event_year) = explode( dpSeperator(), $event_date_start);
            if($event_end_date) {
                list($event_end_day,$event_end_month,$event_end_year) = explode( dpSeperator(), $event_end_date);
            } else {
                $event_end_day = $event_end_month = $event_end_year = '';
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

            //getting the timestamps
            $time = $eventtime;
            if(!preg_match('![012]\d:[0-5]\d!',$time)) $time = '20:00';
            $startmidnight = strtotime("$event_month/$event_day/$event_year");
            $start = strtotime("$event_month/$event_day/$event_year $time");

            $time = $event_end_time;
            if(!preg_match('![012]\d:[0-5]\d!',$time)) $time = '20:00';
            $end = strtotime("$event_end_month/$event_end_day/$event_end_year $time");

            //checking for yearly or weekly events (coded in such a way as to keep the old file structure)
            if (substr($event_entry3,0,3)=="###") {
                $event_entry3 = substr($event_entry3,3); // erasing the ###
                $yearly = TRUE;
            }
            if (substr($event_entry3,0,3)=="#*#") {
                $event_entry3 = substr($event_entry3,3); // erasing the #*#
                $yearly2 = TRUE;
            }
            if (substr($event_entry3,0,3)=="***") {
                $event_entry3 = substr($event_entry3,3); // erasing the ***
                $weekly = TRUE;
            }
            //legacy code (was used to indicate that there are additional days)
            if (substr($event_entry3,0,3)=="*#*") {
                $event_entry3 = substr($event_entry3,3); // eliminating any #*#
            }

            //checking for exceptions
            if(strpos($event_entry3,'|')){
                $exceptions = substr($event_entry3,0,strpos($event_entry3,'|'));
                $dates = explode(",",$exceptions);
                foreach ($dates as $value) {
                    $trimmed_dates = trim($value);
                    list($d,$m,$y) = explode(dpSeperator(),$trimmed_dates);
                    $timestamp = mktime(null,null,null,$m,$d,$y);
                    $exceptionsarray[] = $timestamp;
                }
                // eliminating exception dates from entry3
                $event_entry3 = substr($event_entry3,(strpos($event_entry3,'|')+1));
            }

            //yearly events
            //=============
            if ($yearly || $yearly2) {

                $anniversary = $thisyear = date("Y");
                $age = $thisyear - $event_year;

                // check present and following year
                while ($anniversary < ($thisyear + 2)) {
                    if    ($age < 2)             {$age_txt = ' ' . $age . ' ' . $plugin_tx['calendar']['age_1_year_old'];}
                    elseif($age > 1 && $age < 5) {$age_txt = ' ' . $age . ' ' . $plugin_tx['calendar']['age_2-4_years_old'];}
                    else                         {$age_txt = ' ' . $age . ' ' . $plugin_tx['calendar']['age_5_and_more_years_old'];}

                    //in which line to put the date
                    $age_txt_date = $calendar_cf['marquee_age']=='date'? ' &mdash;'.$age_txt:'';
                    $age_txt_1    = $calendar_cf['marquee_age']=='1'   ? $age_txt:'';
                    $age_txt_2    = $calendar_cf['marquee_age']=='2'   ? $age_txt:'';

                    array_push($event_sorting_array, strtotime("$event_month/$event_day/$anniversary 23:59"));
                    array_push($event_1stline_array, $$calendar_cf['marquee_1stline'] . $age_txt_1);
                    array_push($event_headline_array, $event_day . dpSeperator() . $event_month . dpSeperator() . $anniversary . $age_txt_date);
                    if($yearly) {
                        array_push($event_2ndline_array, $$calendar_cf['marquee_2ndline'] . ' ' . $age_txt_2);
                    } else {
                        array_push($event_2ndline_array, $$calendar_cf['marquee_2ndline']);
                    }
                    $anniversary++;
                    $age++;
                }


            //weekly events
            //=============
            } elseif ($weekly) {

                if(!$event_end_date) $end = strtotime("+10 years", $startmidnight);
                $weekly_event = $startmidnight;

                //don't calculate, if events are over
                if ($end > $now) {

                    //look only 2 month ahead
                	while($weekly_event < $end && $weekly_event < strtotime("+2 month")) {
                        $exception = FALSE;
                        if($exceptions){
                            foreach ($exceptionsarray as $value) {
                                if($value == $weekly_event) {$exception = TRUE; break;}
                            }
                        }
                        if(!$exception){
                            $headline = date("d",$weekly_event) . dpSeperator() . date("m",$weekly_event) . dpSeperator() . date("Y",$weekly_event);
                            if($eventtime) {$headline .= " &mdash; " . $eventtime;}

                            array_push($event_sorting_array, $weekly_event);
                            array_push($event_1stline_array, $$calendar_cf['marquee_1stline']);
                            array_push($event_headline_array, $headline);
                            array_push($event_2ndline_array, $$calendar_cf['marquee_2ndline']);
                        }
                        $weekly_event = strtotime("+1 week", $weekly_event);
                    }
                    if($additional) {
                        foreach ($additionaldatesarray as $value) {
                            $headline = date("d",$value) . dpSeperator() . date("m",$value) . dpSeperator() . date("Y",$value);
                            if($eventtime) {$headline .= " &mdash; " . $eventtime;}

                            array_push($event_sorting_array, $value);
                            array_push($event_1stline_array, $$calendar_cf['marquee_1stline']);
                            array_push($event_headline_array, $headline);
                            array_push($event_2ndline_array, $$calendar_cf['marquee_2ndline']);
                        }
                    }
                }
            
            //Normal events
            //=============
            } elseif (!$weekly && !$yearly && !$yearly2) {

                if($event_end_date) {
                    $txt = $event_day;
                    if ($event_month!=$event_end_month || $event_year!=$event_end_year) $txt .= dpSeperator(). $event_month;
                    if ($event_year!=$event_end_year) $txt .= dpSeperator(). $event_year;
                    if (dpSeperator()=='.' && $event_year==$event_end_year) $txt .= ".";
                    $txt .= " ". $plugin_tx['calendar']['event_date_till_date'] . " " .$event_end_date ;
                }else{
                     $txt = $event_date_start;
                     if($eventtime) $txt .= " &mdash; " . $eventtime ;
                }

                if($event_end_date && $start < $now && $end > $now) {
                    array_push($event_sorting_array, ($now + $spacer));
                } else {
                    array_push($event_sorting_array, $start);
                }
                array_push($event_1stline_array, $$calendar_cf['marquee_1stline']);
                array_push($event_headline_array, /*'<b>normal</b>' .*/ $txt);
                array_push($event_2ndline_array, $$calendar_cf['marquee_2ndline']);

                if($additional) {
                    foreach ($additionaldatesarray as $value) {
                        $headline = date("d",$value) . dpSeperator() . date("m",$value) . dpSeperator() . date("Y",$value);
                        if($eventtime) {$headline .= " &mdash; " . $eventtime;}

                        array_push($event_sorting_array, $value);
                        array_push($event_1stline_array, $$calendar_cf['marquee_1stline']);
                        array_push($event_headline_array, $headline);
                        array_push($event_2ndline_array, $$calendar_cf['marquee_2ndline']);
                    }
                }

                $spacer += 60;
            }
        }
    }
}

//suggestion from cmb
array_multisort($event_sorting_array,$event_headline_array,$event_1stline_array,$event_2ndline_array);

foreach($event_sorting_array as $event_date){
    if($event_date > $now){
        if($remember_event == '') $remember_event = $event_date;
    }
}

//test
//$o .= '<br>$remember_event: ' . $remember_event . ' ' . date("d.m.Y, H:i", $remember_event) . ' <br>';


if($remember_event > $now){

    //check how many events should be shown
    if(!$number) $number = $calendar_cf['marquee_number_of_events_shown'];

    //if nothing is specified, default is 1 event. However 0 can be specified in config
    if($number==="") $number = 1;

    //find the array number of the first event to be shown
    $i = array_search($remember_event, $event_sorting_array);

    //limit the number of events to be on marquee to events that are available.
    $max_next_events = (count($event_sorting_array)) - $i;
    If($max_next_events < $number) $number = $max_next_events;

    //height unit default is px
    $height_unit = ($calendar_cf['marquee_height_unit'])? $calendar_cf['marquee_height_unit'] : "px";


    if ($number == 1) {
        $height = $calendar_cf['marquee_height'] - $calendar_cf['marquee_headline_height'];
        $o .= "<div class='calendar_marquee'>\n";
        $o .= "<p class='nextevent_dateline' style='line-height:"
           .  $calendar_cf['marquee_headline_height']
           .  $height_unit
           .  "'>"
           .  $event_headline_array[$i] . "</p>\n";
        if(!$checkcontents) {
            $o .= "<marquee onmouseover='this.stop();' onmouseout='this.start();' direction='up' scrollamount='"
               .  $calendar_cf['marquee_speed']
               .  "' style='height:$height$height_unit;'>\n";
        }
        $o .= "<p class='nextevent_1stline'>".$event_1stline_array[$i]."</p>\n";
        $o .= "<p class='nextevent_2ndline'>".$event_2ndline_array[$i]."</p>\n";
        $o .= "</marquee>\n</div>";
     }
     elseif ($number > 1) {
        $height = $calendar_cf['marquee_height'];

        $o .= "<div class='calendar_marquee'>";
        if(!$checkcontents) {
            $o .= "<marquee onmouseover='this.stop();' onmouseout='this.start();' direction='up' scrollamount='"
               .  $calendar_cf['marquee_speed']
               .  "' style='height:$height$height_unit' >\n";
        }

        //substract "1" as in the end one event will be added anyway
        $number --;

        for ( $count = 0 ; $count < $number ; $count++) {
            $o .= "<p class='nextevent_dateline color$count' style='line-height:"
               .  $calendar_cf['marquee_headline_height']
               .  $height_unit
               .  "'>\n"
               .  $event_headline_array[$i + $count]  . "</p>\n";
            $o .= "<p class='nextevent_1stline'>"  . $event_1stline_array[$i + $count]."</p>\n";
            $o .= "<p class='nextevent_2ndline'>" . $event_2ndline_array[$i + $count]."</p>\n";

            //add space betwenn the different event announcements
            $o .= "<div style='height:$height$height_unit;margin:0;'>&nbsp;</div>\n";
        }
        //last line without added space (the marquee function adds space)
        $o .= "<p class='nextevent_dateline color$number' style='line-height:"
           .  $calendar_cf['marquee_headline_height']
           .  $height_unit
           .  "'>\n"
           .  $event_headline_array[$i + $number] . "</p>\n";
        $o .= "<p class='nextevent_1stline'>"  . $event_1stline_array[$i + $number]."</p>\n";
        $o .= "<p class='nextevent_2ndline'>" . $event_2ndline_array[$i + $number]."</p>\n";
        //now the marquee row is finished. The marquee function adds space automatically
        //betwenn the event announcements
        $o .= "</marquee>\n</div>\n";
    }
}

// if there are no events planned, one can give a notice to that effect (bug correction by cmb)
elseif ($plugin_tx['calendar']['notice_no_next_event_scheduled']) {
        $o .= "<div class='calendar_marquee'>\n"
           .  "<p class='nextevent_dateline'>\n"
           .  $plugin_tx['calendar']['notice_no_next_event_scheduled']
           .  "</p>\n</div>\n";
}

