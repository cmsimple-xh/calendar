<?php
//===================================================
// HELPER FUNCTION
// S A V E S  the event input data and makes backups
//===================================================

global $plugin_cf,$plugin_tx,$pth,$sl,$plugin,$lang,$datapath;

// Security check
if (!defined("CMSIMPLE_XH_VERSION")) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

$backups = $plugin_cf['calendar']['backups'];

// $notice contains the information, if saving was successful or not
$notice = '';

if ($file) {
    $eventfile = $datapath . $file;
}else {
    $eventfile = $datapath . "eventcalendar$lang.txt";
}
// make file writable
if(is_file($eventfile)) chmod($eventfile, 0666);

// make backups only after normal editing. Not in Calendar backup file editing
if($backups && $standardmode){

    // delete the oldest backup file
    if(is_file($eventfile . ".bak" . $backups)) unlink($eventfile . ".bak" . $backups);

    for ( $i = $backups - 1 ; $i>0 ; $i-- ) {
        if(is_file($eventfile. ".bak".$i)) rename($eventfile. ".bak".$i , $eventfile. ".bak" . ($i+1));
    }
    if(is_file($eventfile)) {
        if($backups>0) rename($eventfile, $eventfile. ".bak1");
    }
}

// only for backup admin file handling
if(!$standardmode) {
    if(preg_match('/[^(\w|.)]/',$file)) {
        $notice = "<span class='error'>" . $plugin_tx['calendar']['error_file_name_wrong'] . "</span>";
    }
}

if(!$notice) {
    // no obstacle encountered, so proceed with saving the file

    // now convert the event entries to usable format and enter them line by line into the eventfile
    $newfile = array();
    foreach($array as $entry) {

        $no_marquee = $entry['no_marquee']? '*' : '';
        $dailytimes = $entry['dailytimes']? '*' : '';
        $bookedout  = $entry['bookedout']?  '*' : '';
        $mark2      = $entry['mark2']?      '*' : '';

        // Converting text marked up with "Simple Formatting"
        $event_entry1 = simpleMarkupToHtml ($entry['event_entry1']);
        $event        = simpleMarkupToHtml ($entry['event']);
        $event_entry3 = simpleMarkupToHtml ($entry['event_entry3']);
        $infotxt      = simpleMarkupToHtml ($entry['infotxt']);
        $linktxt      = simpleMarkupToHtml ($entry['linktxt']);

        $eventdates = $entry['datestart'] . "," . $bookedout . $entry['dateend'] . "," . $dailytimes . $entry['endtime'];
        $event_time_start = $entry['starttime'];

        $repeating_event = '';
        if($entry['yearly2'])    $repeating_event = '#*#';
        if($entry['yearly'])     $repeating_event = '###';
        if($entry['weekly'])     $repeating_event = "***";

        $exceptions = $entry['exceptions'] ? $entry['exceptions'] . '|' : '';
        $additional = $entry['additional'] ? $entry['additional'] . '|' : '';

        if ($entry['linkint'] || $entry['linkadd']) {
            $linkicon = $entry['linkicon']? '*' : '';
        } else {
            $linkicon = '';
        }
        $linkadd = $entry['linkadd'];
        $linkint = $entry['linkint']? 'int:'.$entry['linkint'] : '';
        $linkint = ($linkint && $linkadd)? $linkint.'|' : $linkint;

        if($entry['infotxt'] && $entry['infoicon']==1) {$infoicon = '*';}
        elseif ($entry['infotxt'] && $entry['infoicon']==2) {$infoicon = '+';}
        else $infoicon = '';

        $description = $entry['description'];
        $line = $no_marquee
              . $eventdates
              . ','
              . $mark2
              . $event_entry1
              . ';'
              . $additional
              . $event
              . ';'
              . $repeating_event
              . $exceptions
              . $event_entry3
              . ';'
              . $linkicon
              . $linkint
              . $linkadd
              . ','
              . $linktxt
              . ','
              . $infoicon
              . $infotxt
              . ';'
              . $event_time_start
              . ';'
              . $description
              . "\n";
        array_push($newfile,$line);
    }

    // write the version nr at the beginning of the array
    $version = "Calendar eventfile 1.3\n";
    array_unshift($newfile,$version);

    // save the file
    $result = file_put_contents($eventfile, $newfile);

    if($result ===FALSE)
    {
        $notice = "<span class='error'>" . $plugin_tx['calendar']['error_file_not_writable'] . "</span>";
        // $notice = "<span class='error'>". $plugin_tx['calendar']['error_file_not_saved']."</span>";
    }
    else
    {
        $filename = $file? $file : "eventcalendar".$lang.".txt";
        $notice = "<span class='success'>" . count($array) . " "
                . $plugin_tx['calendar']['notice_number_of_events_saved_in_file'] . " \"" . $filename . "\"</span>";
        //Now change time/date of last edit of content, so that this date can be displayed on the website (many thanx to cmb for this line of code)
        touch($pth['file']['content']);
    }


}
