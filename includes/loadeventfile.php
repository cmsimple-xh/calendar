<?php
//=================================
// HELPER FUNCTION
// reads the event file for editing
//=================================

global $plugin_cf,$plugin_tx,$pth,$sl,$plugin,$lang,$formatting_hints,$datapath;

// Security check
if (!defined("CMSIMPLE_XH_VERSION")) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

$event_array = array();
// determining which file to read
if ($file) {
    $eventfile = $datapath . $file;
}else {
    $eventfile = $datapath . "eventcalendar$lang.txt";
}

if(is_file($eventfile))
{
    $file_array = file($eventfile,FILE_SKIP_EMPTY_LINES);

        //===========================================================
        // checking if the plugin version is written into the file
        // this will be used in future versions to adjust file format
        //===========================================================
        if (isset($file_array[0]) && substr($file_array[0],0,19)=="Calendar eventfile ")
        {
            $version = array_shift($file_array);
            $version = substr($version,19,3);
        }
        else
        {
            $version = FALSE;
        }

    // processing the read file line by line
    foreach ( $file_array as $line) {

        $yearly     = FALSE;
        $yearly2    = FALSE;
        $weekly     = FALSE;
        $multievent = FALSE;
        $exceptions = '';
        $additional = '';


        // explode limit "6" makes "," in description possible
        @list($eventdates,$event,$event_entry3,$link,$event_time_start,$description) = explode( ";", $line,6);
        @list($event_date_start,$event_end_date,$event_end_time,$event_entry1) = explode(",",$eventdates,4);


        // checking if there is an * before the date, which means don't show this event on marquee
        if (substr($event_date_start,0,1)=="*") {
            $event_date_start = substr($event_date_start,1); // eliminating any *
            $no_marquee = TRUE;
        } else $no_marquee = FALSE;

        // checking if there is an * before the enddate, which means event is overbooked
        if (substr($event_end_date,0,1)=="*") {
            $event_end_date = substr($event_end_date,1); // eliminating any *
            $bookedout = TRUE;
        } else $bookedout = FALSE;

        // checking if there is an * before the endtime, meaning given times of long events are daily times
        if (substr($event_end_time,0,1)=="*") {
            $event_end_time = substr($event_end_time,1); // eliminating any *
            $dailytimes = TRUE;
        } else $dailytimes = FALSE;

        // checking if there is an * before the event1, meaning alternative marking of event
        if (substr($event_entry1,0,1)=="*") {
            $event_entry1 = substr($event_entry1,1); // eliminating any *
            $mark2 = TRUE;
        } else $mark2 = FALSE;

        //checking for birthday, weekly, multiple events (coded in such a way to stay compatible with the old event file structure)

        if (substr($event_entry3,0,3)=="###") {
            $event_entry3 = substr($event_entry3,3); // eliminating any ###
            $yearly = TRUE;
        }
        if (substr($event_entry3,0,3)=="#*#") {
            $event_entry3 = substr($event_entry3,3); // eliminating any #*#
            $yearly2 = TRUE;
        }
        if (substr($event_entry3,0,3)=="***") {
            $event_entry3 = substr($event_entry3,3); // eliminating any ***
            $weekly = TRUE;
        }
        //legacy code
        if (substr($event_entry3,0,3)=="*#*") {
            $event_entry3 = substr($event_entry3,3); // eliminating any ***
        }

        // extracting the list of exceptions
        if(strpos($event_entry3,'|')){
            $exceptions = substr($event_entry3,0,strpos($event_entry3,'|'));
            $event_entry3 = substr($event_entry3,(strpos($event_entry3,'|')+1));
        }
        // extracting the list of additional dates
        if(strpos($event,'|')){
            $additional = substr($event,0,strpos($event,'|'));
            $event = substr($event,(strpos($event,'|')+1));
        }


        // Further separation

        // explode limit ='3' makes commas in info text possible
        @list($event_link_adr,$event_link_txt,$event_info_txt) = explode(",",$link,3);

        // checking if the link uses an icon (marked by '*')
        if (substr($event_link_adr,0,1)=="*") {
            $event_link_icon = TRUE;
            $event_link_adr = substr($event_link_adr,1);
        } else $event_link_icon = FALSE;

        // separating the links in internal page links and other links
        $links = explode('|',$event_link_adr);
        if(substr($links[0],0,4) == 'int:') {
            $event_link_int = substr($links[0],4);
            array_shift($links);
            $event_link_adr = implode('|',$links);
        } else $event_link_int = '';

        // checking if an icon is wanted for info text
        if (substr($event_info_txt,0,1)=="*") {
            $event_info_icon = 1;
            $event_info_txt = substr($event_info_txt,1);
        } elseif (substr($event_info_txt,0,1)=="+") {
            $event_info_icon = 2;
            $event_info_txt = substr($event_info_txt,1);
        } else $event_info_icon = 0;


        // converting event files of Calendar v.1.2 build 5, which hadn't got $event_info_txt yet
        if(!$version && !$event_link_adr && $event_link_txt && !$event_info_txt) {
            $event_info_txt = $event_link_txt;
            $event_link_txt = '';
        }

        // removing the linefeed at the end of the line, otherwise this will be part of the description!!
        $description = rtrim($description);

        // apply Simple Markup
        $event_entry1   = htmlToSimpleMarkup($event_entry1);
        $event          = htmlToSimpleMarkup($event);
        $event_entry3   = htmlToSimpleMarkup($event_entry3);
        $event_info_txt = htmlToSimpleMarkup($event_info_txt);
        $event_link_txt = htmlToSimpleMarkup($event_link_txt);

        // Now the event array is written,
        // at least an event must be given (to prevent empty lines creating empty entries)
        $entry = array(
            'no_marquee'   => $no_marquee,
            'datestart'    => $event_date_start,
            'dateend'      => $event_end_date,
            'starttime'    => $event_time_start,
            'endtime'      => $event_end_time,
            'dailytimes'   => $dailytimes,
            'event_entry1' => $event_entry1,
            'event'        => $event,
            'event_entry3' => $event_entry3,
            'weekly'       => $weekly,
            'exceptions'   => $exceptions,
            'additional'   => $additional,
            'yearly'       => $yearly,
            'yearly2'      => $yearly2,
            'linkicon'     => $event_link_icon,
            'linkint'      => $event_link_int,
            'linkadd'      => $event_link_adr,
            'linktxt'      => $event_link_txt,
            'infoicon'     => $event_info_icon,
            'infotxt'      => $event_info_txt,
            'description'  => $description,
            'bookedout'    => $bookedout,
            'mark2'        => $mark2);
        $event_array[] = $entry;
    }
}

