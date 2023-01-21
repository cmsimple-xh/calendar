<?php
//********************************
//
//    MAIN FUNCTION of Backend
//
//     Editing the eventfile
//
//         September 2013
//********************************

// Security check
if ((!function_exists('sv')))die('Access denied');


global $plugin_cf,$plugin_tx,$pth,$sl,$plugin,$tx,$lang;
$o = '';

$events = loadEventFile($file);

$dataset           = isset($_POST['dataset'])       ? $_POST['dataset'] : '';
$add               = isset($_POST['add'])           ? TRUE : FALSE ;
$copy              = isset($_POST['copy'])          ? TRUE : FALSE ;
$delete            = isset($_POST['delete'])        ? TRUE : FALSE ;
$change            = isset($_POST['change'])        ? TRUE : FALSE ;
$newfilename       = isset($_POST['newfilename'])   ? TRUE : FALSE ;



$file              = isset($_POST['file'])              ? $_POST['file']               : $file;
$standardmode      = isset($_POST['standardmode'])      ? $_POST['standardmode']       : $standardmode;
$calendar2         = isset($_POST['calendar2'])         ? $_POST['calendar2']          : $calendar2;
$no_marquee        = isset($_POST['no_marquee'])        ? $_POST['no_marquee']         : array();
$datestart         = isset($_POST['datestart'])         ? $_POST['datestart']          : array();
$starttime         = isset($_POST['starttime'])         ? $_POST['starttime']          : array();
$dateend           = isset($_POST['dateend'])           ? $_POST['dateend']            : array();
$endtime           = isset($_POST['endtime'])           ? $_POST['endtime']            : array();
$dailytimes        = isset($_POST['dailytimes'])        ? $_POST['dailytimes']         : array();
$event_entry1      = isset($_POST['event_entry1'])      ? $_POST['event_entry1']       : array();
$event             = isset($_POST['event'])             ? $_POST['event']              : array();
$event_entry3      = isset($_POST['event_entry3'])      ? $_POST['event_entry3']       : array();
$weekly            = isset($_POST['weekly'])            ? $_POST['weekly']             : array();
$exceptions        = isset($_POST['exceptions'])        ? $_POST['exceptions']         : array();
$additional        = isset($_POST['additional'])        ? $_POST['additional']         : array();
$yearly            = isset($_POST['yearly'])            ? $_POST['yearly']             : array();
$yearly2           = isset($_POST['yearly2'])           ? $_POST['yearly2']            : array();
$linkicon          = isset($_POST['linkicon'])          ? $_POST['linkicon']           : array();
$linkint           = isset($_POST['linkint'])           ? $_POST['linkint']            : array();
$linkadr           = isset($_POST['linkadr'])           ? $_POST['linkadr']            : array();
$linktxt           = isset($_POST['linktxt'])           ? $_POST['linktxt']            : array();
$infoicon          = isset($_POST['infoicon'])          ? $_POST['infoicon']           : array();
$infotxt           = isset($_POST['infotxt'])           ? $_POST['infotxt']            : array();
$description       = isset($_POST['description'])       ? $_POST['description']        : array();
$bookedout         = isset($_POST['bookedout'])         ? $_POST['bookedout']          : array();
$mark2             = isset($_POST['mark2'])             ? $_POST['mark2']              : array();


if ($dataset=='' && !$newfilename) {
    //if no button is pressed the subfunction is called to build the input table
    $saving_notice = '';
    $o .= eventForm($events,$saving_notice,$file,$standardmode,$calendar2);

} elseif($newfilename) {

    // check if the new filename has unwanted characters
    if(preg_match('/[^(\w|.)]/',$file)) {
        $saving_notice = "<span class='error'>"
                       . $plugin_tx['calendar']['error_file_name_wrong']
                       . '</span>';
    } else {
        $saving_notice = saveEventFile($events,$file,$standardmode);
    }
    $o .= listFiles()
       . closeFileViewButton()
       . eventForm($events,$saving_notice,$file,$standardmode,$calendar2);

} elseif($delete) {
    array_splice($events,($dataset-1),1);
    $saving_notice = '<span class="success">'
                   . $plugin_tx['calendar']['notice_deleted']
                   . '</span>'
                   . saveEventFile($events,$file,$standardmode);

    $o .= eventForm($events,$saving_notice,$file,$standardmode,$calendar2);

} elseif($add) {

    $entry = array(
        'no_marquee'  => FALSE,
        'datestart'   => '',
        'starttime'   => '',
        'dateend'     => '',
        'endtime'     => '',
        'dailytimes'  => FALSE,
        'event'       => '',
        'event_entry3'=> '',
        'event_entry1'=> '',
        'multievent'  => FALSE,
        'additional'  => '',
        'weekly'      => FALSE,
        'weekday'     => 0,
        'exceptions'  => '',
        'yearly'      => FALSE,
        'yearly2'     => FALSE,
        'linkicon'    => TRUE,
        'linkint'     => '',
        'linkadd'     => '',
        'linktxt'     => '',
        'infoicon'    => 1,
        'infotxt'     => '',
        'description' => '',
        'bookedout'   => FALSE,
        'mark2'       => FALSE
        );
    array_splice($events,$dataset,0,array($entry));
    $saving_notice = '<span class="success">'
                   . $plugin_tx['calendar']['notice_added']
                   . '</span>'
                   . saveEventFile($events,$file,$standardmode);

    $o .= eventForm($events,$saving_notice,$file,$standardmode,$calendar2);

} elseif($change || $add || $copy) {

    $newevent = array();
    $weekday  = array();
    $linkadd  = array();

    $j = $dataset;

    // filling missing indices
    if (!isset($no_marquee[$j]))   $no_marquee[$j]   = '';
    if (!isset($datestart[$j]))    $datestart[$j]    = '';
    if (!isset($dateend[$j]))      $dateend[$j]      = '';
    if (!isset($starttime[$j]))    $starttime[$j]    = '';
    if (!isset($endtime[$j]))      $endtime[$j]      = '';
    if (!isset($dailytimes[$j]))   $dailytimes[$j]   = '';
    if (!isset($event[$j]))        $event[$j]        = '';
    if (!isset($event_entry3[$j])) $event_entry3[$j] = '';
    if (!isset($event_entry1[$j])) $event_entry1[$j] = '';
    if (!isset($weekly[$j]))       $weekly[$j]       = '';
    if (!isset($exceptions[$j]))   $exceptions[$j]   = '';
    if (!isset($additional[$j]))   $additional[$j]   = '';
    if (!isset($yearly[$j]))       $yearly[$j]       = '';
    if (!isset($yearly2[$j]))      $yearly2[$j]      = '';
    if (!isset($linkicon[$j]))     $linkicon[$j]     = '';
    if (!isset($linkint[$j]))      $linkint[$j]      = '';
    if (!isset($linkadr[$j]))      $linkadr[$j]      = '';
    if (!isset($linktxt[$j]))      $linktxt[$j]      = '';
    if (!isset($infoicon[$j]))     $infoicon[$j]     = '';
    if (!isset($infotxt[$j]))      $infotxt[$i]      = '';
    if (!isset($description[$j]))  $description[$j]  = '';
    if (!isset($bookedout[$j]))    $bookedout[$j]    = '';
    if (!isset($mark2[$j]))        $mark2[$j]        = '';
    if (!isset($test[$j]))         $test[$j]         = '';


    //expanding single digit day, months and double digit years (in case datepicker is off)
    $datestart[$j] = preg_replace(array(
                                       '/([\.|\-|\/])(\d)[\.|\-|\/]/',   //single digit months
                                        '/([\.|\-|\/])(\d\d)$/',         //double digit years
                                        '/^(\d)([\.|\-|\/])/'            //single digit days
                                       ),
                                       array(
                                       dpSeperator().'0$2'. dpSeperator(),
                                       dpSeperator().'20$2',
                                       '0$1'.dpSeperator()
                                       ),
                                       $datestart[$j]);
    $dateend[$j] = preg_replace(array(
                                        '/([\.|\-|\/])(\d)[\.|\-|\/]/',  //single digit months
                                        '/([\.|\-|\/])(\d\d)$/',         //double digit years
                                        '/^(\d)([\.|\-|\/])/'            //single digit days
                                       ),
                                       array(
                                       dpSeperator().'0$2'. dpSeperator(),
                                       dpSeperator().'20$2',
                                       '0$1'.dpSeperator()
                                       ),
                                       $dateend[$j]);
    //Checking the date format. Entering ?, ??, -, -- is permitted. Some impossible dates can be given, but don't crash the plugin.
    $pattern = '/[\d\d\|\?{1-2}|\-{1-2}]\\'.dpSeperator().'\d\d\\'.dpSeperator().'\d*$/';
    if (!preg_match($pattern,$datestart[$j])) $datestart[$j] = "";
    if (!preg_match($pattern,$dateend[$j])) $dateend[$j] = "";


    // check exception dates
    if($exceptions[$j]) {
        $dates = explode(",",$exceptions[$j]);
        foreach ($dates as $key => $value) {
            $dates[$key] = trim($value);
            list($day,$month,$year) = explode(dpSeperator(),$dates[$key]);
            $timestamp = mktime(null,null,null,$month,$day,$year);
            //exceptions must be later than the event start
            if($timestamp > $event_start_timestamp) {
                $dates[$key] = date('d' .dpSeperator(). 'm' .dpSeperator() . 'y', $timestamp) ;
            } else $dates[$key] =false;
        }
        //taking out not accepted values
        $dates = array_filter($dates);
        $exceptions[$j] = implode(", ",$dates);
        //$exceptions = 'test';
    }

    // check additional dates
    if($additional[$j]) {
        $dates = explode(",",$additional[$j]);
        foreach ($dates as $key => $value) {
            $addeventfull = '';
            $dates[$key] = trim($value);
            list($day,$month,$year) = explode(dpSeperator(),$dates[$key]);
            if (substr($year,-1)=='*') {
                $addeventfull = '*';
                $year = trim($year,'*');
            }
            $timestamp = mktime(null,null,null,$month,$day,$year);
            //exceptions must be later than the event start
            if($timestamp > $event_start_timestamp) {
                $dates[$key] = date('d' .dpSeperator(). 'm' .dpSeperator() . 'y', $timestamp).$addeventfull ;
            } else $dates[$key] =false;
        } 
        //taking out not accepted values
        $dates = array_filter($dates);
        $additional[$j] = implode(", ",$dates);
        //$exceptions = 'test';
    }



    //If the same date is entered as beginning and as end, the end date is superfluous and will be erased
    if ($datestart[$j]==$dateend[$j]) $dateend[$j] = "";

    //events are limited to 10 years in order to make a stop somewhere
    if (((int) substr($dateend[$j],-4,4) - (int) substr($datestart[$j],-4,4))>10) $dateend[$j] = '';

    //if weekly and yearly are both checked, one has to be unchecked
    if ($weekly[$j] && ($yearly[$j] || $yearly2[$j]) && $dateend[$j]) {$yearly[$j] = FALSE; $yearly2[$j] = FALSE;}
    if ($weekly[$j] && ($yearly[$j] || $yearly2[$j]) && !$dateend[$j]) $weekly[$j] = FALSE;

    //dailytimes allowed only in multiday events
    if ($dailytimes[$j] && $weekly[$j]) $dailytimes[$j] = FALSE;
    if ($dailytimes[$j] && ($yearly[$j] || $yearly2[$j])) $dailytimes[$j] = FALSE;

    // yearly events can last only 1 day (could be changed but needs more changes elsewhere)
    if (($yearly[$j] || $yearly2[$j]) && $dateend[$j]) $dateend[$j] = '';
    // yearly either with age calculation or without, file encoding doesn't allow both
    if ($yearly[$j] && $yearly2[$j]) $yearly2[$j] = '';


    //in case the user gave a link without link text nor icon, icon will be set as "on"
    if ($linkadr[$j] && !$linktxt[$j]) $linkicon[$j]= TRUE;

    //eliminating superfluous http://
    $linkadr[$j] = str_replace ('http://','',$linkadr[$j]);

    // converting $linkadr, which may be just page names or file names to $linkadd ready for saving
    $linkadd[$j] = '';
    if ($linkadr[$j]) {
        $links = explode('|', $linkadr[$j]);
        foreach ($links as $key=>$value) {
            $linkadd[$j] .= $linkadd[$j]? '|':'';
            $linktype = findLinkType($value);
            if($linktype=='in?:' || $linktype=='int:') $value = pagenameToUrl($value);
            // urlencode only the filename, not the address
            if($linktype=='pdf:' || $linktype=='pfx:' || $linktype=='doc:') {
                if(strpos($value,'/')) {
                    $value = substr($value, 0, strrpos($value,'/')+1) . rawurlencode(substr($value,strrpos($value,'/')+1));
                } else $value = rawurlencode($value);
            }
            $linkadd[$j] .= $linktype . $value;
        }
    }


    //Converting time data xx.xx to xx:xx and x:xx to 0x:00
    $starttime[$j] = preg_replace('#(\d?\d)\.(\d{2})#','\1:\2',$starttime[$j]);
    $starttime[$j] = preg_replace('#^(\d)\:(\d{2})#','0\1:\2', $starttime[$j]);
    $endtime[$j]   = preg_replace('#(\d?\d)\.(\d{2})#','\1:\2',$endtime[$j]);
    $endtime[$j]   = preg_replace('#^(\d)\:(\d{2})#','0\1:\2', $endtime[$j]);

    // deleating linebreaks
    $description[$j] = str_replace(array(chr(10),chr(13)),'',$description[$j]);
    // cleaning a bit the output of ckeditor
    $description[$j] = preg_replace('#>\s+#','> ',$description[$j]);

    //deleating empty lines at the end of an entry (spaces in the regex are &nbsp;)
    if($plugin_cf['calendar']['editor_deletes_empty_lines_at_the_end']) {
        $description[$j] = preg_replace(
            array(
                '!(<br( \/)?>&#160;)*</p>$!',
                '!(<p> <\/p>)*$!',
                '!(<br( \/)?>( )?)*(<p>( )?<\/p>)*(<br( \/)?>( )?)*$!',
                '!(\s*<p> <\/p>\s*)+$!',
                '! *$!',
            ),
            array(
                '</p>',
                 '',
                 '',
                 '',
                 '',
            ),
            $description[$j]
        );
    }

    //filling the array, deleting "," and ";" which would break the file structure
    $entry = array(
        'no_marquee'  => $no_marquee[$j],
        'datestart'   => str_replace(';',' ',$datestart[$j]),
        'starttime'   => str_replace(array(';',','),' ',$starttime[$j]),
        'dateend'     => str_replace(';',' ',$dateend[$j]),
        'endtime'     => str_replace(array(';',','),' ',$endtime[$j]),
        'dailytimes'  => $dailytimes[$j],
        'event_entry1'=> stsl(str_replace(';',' ',$event_entry1[$j])),
        'event'       => stsl(str_replace(';',' ',$event[$j])),
        'event_entry3'=> stsl(str_replace(';',' ',$event_entry3[$j])),
        'weekly'      => $weekly[$j],
        'exceptions'  => str_replace(';',' ',$exceptions[$j]),
        'additional'  => str_replace(';',' ',$additional[$j]),
        'yearly'      => $yearly[$j],
        'yearly2'     => $yearly2[$j],
        'linkicon'    => $linkicon[$j],
        'linkint'     => $linkint[$j],
        'linkadd'     => str_replace(array(';',','),' ',$linkadd[$j]),
        'linktxt'     => stsl(str_replace(array(';',','),' ',$linktxt[$j])),
        'infoicon'    => $infoicon[$j],
        'infotxt'     => stsl(str_replace(';',' ',$infotxt[$j])),
        'description' => stsl($description[$j]),
        'bookedout'   => $bookedout[$j],
        'mark2'       => $mark2[$j]
        );

    array_splice($events,($dataset-1),1,array($entry));
    if($copy) array_splice($events,$dataset,0,array($entry));

    foreach($events as $j => $i){

        // finding out on which day of the week a weekly event starts
        // used for SORTING ALL events according to $weekday

        if (!isset($events[$j]['datestart']))    $events[$j]['datestart']    = ''.dpSeperator().''.dpSeperator().'';
        if (!isset($events[$j]['weekly']))       $events[$j]['weekly']       = '';
        if (!isset($events[$j]['additional']))   $events[$j]['additional']   = '';
        if (!isset($events[$j]['yearly']))       $events[$j]['yearly']       = '';
        if (!isset($events[$j]['yearly2']))      $events[$j]['yearly2']      = '';

        @list($day,$month,$year) = explode( dpSeperator(), $events[$j]['datestart']);
        $event_start_timestamp = mktime(null,null,null,(int)$month,(int)$day,(int)$year);
        if ($events[$j]['weekly']) {
            $events[$j]['weekday'] = date('w',$event_start_timestamp);
            //sort first weekly events on sundays

        } elseif ($events[$j]['yearly'] || $events[$j]['yearly2']) {
            $events[$j]['weekday']=7;
            // sort next yearly events

        } elseif ($events[$j]['additional']) {
            $events[$j]['weekday']=8;
            // sort next multiple events

        } else $events[$j]['weekday']=9;
            // sort last single one off events according to start time

    }
    

    // sorting new event inputs, an idea of manu, communicated in the CMSimple forum
    // sorting will be done only on saving the event file

    usort($events,'dateSort');

    $change_notice = $copy? $plugin_tx['calendar']['notice_copied']:$plugin_tx['calendar']['notice_changed'];
    $saving_notice = '<span class="success">'
                   . $change_notice
                   . '</span>'
                   . saveEventFile($events,$file,$standardmode);

    $o .= eventForm($events,$saving_notice,$file,$standardmode,$calendar2);
}

