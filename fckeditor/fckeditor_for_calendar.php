<?php
/*
CMSimple Calendar-Plugin FCKeditor integration, 6/2011 by svasti,
improved Jan 2012 for Calendar 1.4,
added support for editor height, deleated possibility to use inbuild FCKeditor (it seems nobody used this possibility)

jan 31, 2012 added security fix by cmb

code inspiration from CMSimple/fckeditor.php,
Gert's RealBlog fckeditor integration,
the Wellrad shop, the comments in the fckeditor files
and lots of trial and error.
*/


//calls FCKeditor as page head JavaScript command, using the $hjs variable
//if($plugin_cf['calendar']['editor']=="fckeditor+") {
    $hjs.='<script type="text/javascript" src="'.$pth['folder']['plugins'].'/calendar/fckeditor/FCKeditor/fckeditor.js"></script>'."\n";
//} else {
//    $hjs.='<script type="text/javascript" src="http://'.$_SERVER[SERVER_NAME].CMSIMPLE_ROOT.'FCKeditor/fckeditor.js"></script>'."\n";
//}
//initialising the connector to the filemanager in case the filemanager hasn't been called already by other actions of the user
if(session_id() == ''){
    session_start();
}

//puts the FCKEditor Html Editor into a chosen textarea
function fckeditor()
{
    global $plugin_cf,$plugin_tx,$pth,$sl,$plugin,$sn,$su,$cf,$adm;


    if ($adm) {
	$_SESSION["_VALID_FCKeditor"] = "enabled";
    }

    //for multi language sites, if you are not in main language pages
    $sl != $cf['language']['default'] ? $repl = $sl . "/index.php" : $repl = "index.php";
    $CMSimple_root_folder = str_replace($repl, "", $_SERVER['SCRIPT_NAME']);

    //for bugfixing -- looking for the values of the variables
    //$x="<p>cf['language']['default']: ".$cf['language']['default']."</p><p>$sl</p><p>$repl</p><p>".$_SERVER['SCRIPT_NAME']."</p><p>.$CMSimple_root_folder.</p>";

    $upload_folder = $CMSimple_root_folder;
    $_SESSION["upload_folder"] = $upload_folder;

    $isInSubfolder = substr_count($upload_folder, '/');

    $_SESSION["lang_active"] = $sl;

    if($sl == $cf['language']['default']) {
    		$isDefaultLanguage = 1;
    	} else {$isDefaultLanguage = 0; }

    if(isset($cf['fckeditor']['folder']) && $cf['fckeditor']['folder']=='')$cf['fckeditor']['folder']='FCKeditor';



    // Decide if html or Xhtml should be used
    $DocType = '';
    if ($cf['xhtml']['endtags'] != 'true') {
	   $DocType = '<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">' ;
    }
    // Look for the custom configuration file with tool bar
    $ccp = $CMSimple_root_folder
            .'plugins/calendar/fckeditor/fckconfig_calendar.js';

    // Look for the ccs-file in the editor window
    $css = $CMSimple_root_folder
            .'plugins/calendar/css/editor.css,';
    $css .= $pth['folder']['template']."stylesheet.css";

    // Look for the xml-style file
    $fckstyles = $CMSimple_root_folder
            .'plugins/calendar/fckeditor/fckstyles_calendar.xml';

    // Look for the template file
    $templatepath = $CMSimple_root_folder
            .'plugins/calendar/fckeditor/fcktemplate_calendar.xml';

    // Give the path to FCKeditor in the CMSimple installation
    $fckpath = $CMSimple_root_folder . 'plugins/calendar/fckeditor/FCKeditor/';
    $connector = $CMSimple_root_folder . 'plugins/calendar/fckeditor/FCKeditor/editor/filemanager/connectors/php/connector.php';
    $browserPath = $CMSimple_root_folder . 'plugins/calendar/fckeditor/FCKeditor/editor/filemanager/browser/cmsimple/browser.html?';

    // Editor height
    $height = $plugin_cf['calendar']['editor_height'];

    // Now call FCKEditor via java script
    $o = '<script type="text/javascript">
        function openeditor(button,editorarea,clicktext,reclicktext,hidearea)
    {
            var field = editorarea;
            var myBaseHref = "http://'.$_SERVER['HTTP_HOST'].$sn.'" ;
            var FCKDocType = "'.$DocType.'";
            var FCKccp = "'.$ccp.'";
            var EditorAreaCss = "'.$css.'";
            var FCKStyles = "'.$fckstyles.'";
            var FCKpath = "'.$fckpath.'";
            var Templatepath = "'.$templatepath.'"
            var oFCKeditor = new FCKeditor(field,"100%","'.$height.'");
            var connector = "Connector='.$connector.'" ;
            var browserPath = "'.$browserPath.'" ;
            oFCKeditor.BasePath = FCKpath ;
            oFCKeditor.ToolbarSet = "calendar" ;
            oFCKeditor.Config["StylesXmlPath"] = FCKStyles ;
            oFCKeditor.Config["EditorAreaCSS"] = EditorAreaCss ;
            oFCKeditor.Config["DocType"] = FCKDocType ;
            oFCKeditor.Config["CustomConfigurationsPath"] = FCKccp ;
            oFCKeditor.Config["TemplatesXmlPath"] = Templatepath ;
            oFCKeditor.Config["BaseHref"] = myBaseHref ;
            //HI 2009-10-07 var connector = "Connector=../../connectors/php/connector.php" ;
oFCKeditor.Config["LinkBrowser"] = '.($adm ? 'true' : 'false').';
oFCKeditor.Config["ImageBrowser"] = '.($adm ? 'true' : 'false').';
oFCKeditor.Config["FlashBrowser"] = '.($adm ? 'true' : 'false').';
oFCKeditor.Config["LinkBrowserURL"]	= browserPath + connector + "&defaultLanguage='.$isDefaultLanguage.'" + "&isInSubfolder='.$isInSubfolder.'";
oFCKeditor.Config["ImageBrowserURL"] = browserPath + "Type=Image&" + connector + "&defaultLanguage='.$isDefaultLanguage.'" + "&isInSubfolder='.$isInSubfolder.'";
oFCKeditor.Config["FlashBrowserURL"] = browserPath + "Type=Flash&" + connector + "&defaultLanguage='.$isDefaultLanguage.'" + "&isInSubfolder='.$isInSubfolder.'"
            oFCKeditor.ReplaceTextarea() ;

            document.getElementById(hidearea).style.display = "none";
    }
            </script>';
    return $o;
}
?>
