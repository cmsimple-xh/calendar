<?php

// Security check
if (!defined("CMSIMPLE_XH_VERSION")) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

//get the css-data
$cssfile = file_get_contents($pth['folder']['plugins'] . $plugin . '/css/stylesheet.css');
$cssfilelength = strlen($cssfile); //just for security not to delete the file later



$eventlistsettings['$comment1'] = "\n//css settings of event list\n//==========================";

preg_match("!\/\*b1\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$datewidth'] = $datewidth = trim($matches[1]);

preg_match("!\/\*b2\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$timewidth'] = $timewidth = trim($matches[1]);

preg_match("!\/\*b3\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$eventwidth'] = $eventwidth = trim($matches[1]);

preg_match("!\/\*b4\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$eventcolor'] = $eventcolor = trim($matches[1]);

preg_match("!\/\*b5\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$entry3width'] = $entry3width = trim($matches[1]);

preg_match("!\/\*b6\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$entry3color'] = $entry3color = trim($matches[1]);

preg_match("!\/\*b7\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$linkwidth'] = $linkwidth = trim($matches[1]);

preg_match("!\/\*b8\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$linkcolor'] = $linkcolor = trim($matches[1]);

preg_match("!\/\*b9\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$datecolor'] = $datecolor = trim($matches[1]);

preg_match("!\/\*b10\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$timecolor'] = $timecolor = trim($matches[1]);

preg_match("!\/\*b11\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$birthdaycolor'] = $birthdaycolor = trim($matches[1]);

preg_match("!\/\*b12\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$eventfontweight'] = $eventfontweight = (trim($matches[1]) == 'bold')? 1:0;

preg_match("!\/\*b13\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$entry1width'] = $entry1width = trim($matches[1]);

preg_match("!\/\*b14\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$entry1color'] = $entry1color = trim($matches[1]);

preg_match("!\/\*b15\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$listfont'] = $listfont = trim($matches[1]);

preg_match("!\/\*b16\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$listfontsize'] = $listfontsize = trim($matches[1]);

preg_match("!\/\*b17\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$subheadfontsize'] = $subheadfontsize = trim($matches[1]);

preg_match("!\/\*b18\*\/(.*);!",$cssfile,$matches);
$eventlistsettings['$monthfontsize'] = $monthfontsize = trim($matches[1]);



$calendarsettings['$comment2'] = "\n//css settings of pop-ups\n//=======================";

preg_match("!\/\*p1\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$popupbordercolor'] = $popupbordercolor = trim($matches[1]);

preg_match("!\/\*p2\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$popupborderwidth'] = $popupborderwidth = trim($matches[1]);

preg_match("!\/\*p3\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$popupbackground'] = $popupbackground = trim($matches[1]);

preg_match("!\/\*p4\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$popupfont'] = $popupfont = trim($matches[1]);

preg_match("!\/\*p5\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$popupfontsize'] = $popupfontsize = trim($matches[1]);



$calendarsettings['$comment3'] = "\n//css settings of big calendar\n//============================";

preg_match("!\/\*c1\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$headlinecolor'] = $headlinecolor = trim($matches[1]);

preg_match("!\/\*c2\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$backgroundcolor'] = $backgroundcolor = trim($matches[1]);

preg_match("!\/\*c3\*\/(.*);!",$cssfile,$matches);
$urlbackgroundimage = trim($matches[1]);
$calendarsettings['$backgroundimage'] = $backgroundimage = trim(basename($urlbackgroundimage),')');

preg_match("!\/\*c4\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$bordercolor'] = $bordercolor = trim($matches[1]);

preg_match("!\/\*c5\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$borderwidth'] = $borderwidth = trim($matches[1]);

preg_match("!\/\*c6\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$dayspacing'] = $dayspacing = trim($matches[1]);

preg_match("!\/\*c7\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$nowrapdata'] = $nowrapdata = trim($matches[1]);
$nowrap = ($nowrapdata == 'nowrap')? 1 : '';

preg_match("!\/\*c10\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$daynamespadtop'] = $daynamespadtop = trim($matches[1]);

preg_match("!\/\*c11\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$daynamespadbot'] = $daynamespadbot = trim($matches[1]);

preg_match("!\/\*c13\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$dayheight'] = $dayheight = trim($matches[1]);

preg_match("!\/\*c14\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$topmargin'] = $topmargin = trim($matches[1]);

preg_match("!\/\*c18\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$daynames'] = $daynames = trim($matches[1]);

preg_match("!\/\*c19\*\/(.*);!",$cssfile,$matches);
$x = ($matches[1]);
$x = str_replace('!important','',$x);
$calendarsettings['$birthdayfield'] = $birthdayfield = trim($x);

preg_match("!\/\*c20\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$roundcorners'] = $roundcorners = trim($matches[1]);

preg_match("!\/\*c21\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$shadow'] = $shadow = trim($matches[1]);

preg_match("!\/\*c22\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$opacitynoevent'] = $opacitynoevent = trim($matches[1]);

preg_match("!\/\*c31\*\/(.*)\}!sU",$cssfile,$matches);
$headlineconfig = $matches[1];
$calendarsettings['$headlineconfig'] = addslashes($headlineconfig);

preg_match("!\/\*c32\*\/(.*)\}!sU",$cssfile,$matches);
$daynameconfig = $matches[1];
$calendarsettings['$daynameconfig'] = addslashes($daynameconfig);

preg_match("!\/\*c33\*\/(.*)\}!sU",$cssfile,$matches);
$todaynameconfig = $matches[1];
$calendarsettings['$todaynameconfig'] = addslashes($todaynameconfig);

preg_match("!\/\*c34\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$bigcalfont'] = $bigcalfont = trim($matches[1]);

preg_match("!\/\*c35\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$bigcalfontsize'] = $bigcalfontsize = trim($matches[1]);

preg_match("!\/\*c36\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$dayfieldborder'] = $dayfieldborder = trim($matches[1]);

preg_match("!\/\*c37\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$bordercollapse'] = $bordercollapse = trim($matches[1]);

preg_match("!\/\*c38\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$dayfieldpadding'] = $dayfieldpadding = trim($matches[1]);



$calendarsettings['$comment4'] = "\n//css settings only small calendar\n//================================";

preg_match("!\/\*sc1\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$smallcallineheight'] = $smallcallineheight = trim($matches[1]);

preg_match("!\/\*sc2\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$smallcalfont'] = $smallcalfont = trim($matches[1]);

preg_match("!\/\*sc3\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$smallcalfontsize'] = $smallcalfontsize = trim($matches[1]);

preg_match("#\/\*sc4\*\/(.*);#",$cssfile,$matches);
$urlbirthdayimage = str_replace('!important','',$matches[1]);
$urlbirthdayimage = trim($urlbirthdayimage);
$calendarsettings['$birthdayimage'] = $birthdayimage = trim(basename($urlbirthdayimage),')');

preg_match("!\/\*sc5\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$todaycolor'] = $todaycolor = trim($matches[1]);

preg_match("!\/\*sc6\*\/(.*);!",$cssfile,$matches);
$x = ($matches[1]);
$x = str_replace('!important','',$x);
$calendarsettings['$todaybgcolor'] = $todaybgcolor = trim($x);

preg_match("!\/\*sc7\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$todaybold'] = $todaybold = trim($matches[1]);




$calendarsettings['$comment4'] = "\n//css eventday colors for small and big calendar\n//==============================================";


preg_match("!\/\*a1\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$normaldaycolor'] = $normaldaycolor = trim($matches[1]);

preg_match("!\/\*a2\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$weekendcolor'] = $weekendcolor = trim($matches[1]);

preg_match("!\/\*a3\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$holidaycolor'] = $holidaycolor = trim($matches[1]);

preg_match("!\/\*a4\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$eventdaycolor'] = $eventdaycolor = trim($matches[1]);

preg_match("!\/\*a5\*\/(.*);!",$cssfile,$matches);
$x = ($matches[1]);
$x = str_replace('!important','',$x);
$calendarsettings['$eventdaybgcolor'] = $eventdaybgcolor = trim($x);

preg_match("!\/\*a6\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$underlongcolor'] = $underlongcolor = trim($matches[1]);

preg_match("!\/\*a7\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$underlongwidth'] = $underlongwidth = trim($matches[1]);

preg_match("!\/\*a8\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$mark2color'] = $mark2color = trim($matches[1]);

preg_match("!\/\*a9\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$mark2width'] = $mark2width = trim($matches[1]);

preg_match("!\/\*a10\*\/(.*);!",$cssfile,$matches);
$calendarsettings['$eventdaybold'] = $eventdaybold = trim($matches[1]);
