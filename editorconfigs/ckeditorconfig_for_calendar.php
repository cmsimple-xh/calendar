<?php


$ckconfig = "{

//	baseHref : '%BASE_HREF%',
	contentsCss : %STYLESHEET%,
	//remove default styles
	stylesSet : [
    {name : 'red',             element : 'span', attributes : { 'class' : 'red'} },
    {name : 'big',             element : 'span', attributes : { 'class' : 'big'} },
    {name : 'small',           element : 'small' },
    {name : 'img left',        element : 'img', attributes : { 'class' : 'left'} },
    {name : 'img left under',  element : 'img', attributes : { 'class' : 'left_under'} },
    {name : 'img left2',       element : 'img', attributes : { 'class' : 'left2'} },
    {name : 'img right',       element : 'img', attributes : { 'class' : 'right'} },
    {name : 'img right under', element : 'img', attributes : { 'class' : 'right_under'} },
    ],
	height : '%EDITOR_HEIGHT%',
	defaultLanguage : 'en',
	language : '%LANGUAGE%',
	skin: '%SKIN%',

	entities : false,
	entities_latin : false,
	entities_greek : false,
	entities_additional : '', // '#39' (The single quote (') character.)

    toolbarCanCollapse : false,
    format_tags: '%BLOCKFORMATS%' ,
	toolbar : 'Calendar' ,

	toolbar_Calendar :
	[
    { name: 'document',    items : [ 'Source','ShowBlocks','RemoveFormat'] },
	// '/',
    { name: 'basicstyles', items : [ 'Bold','Italic','NumberedList','BulletedList','Outdent','Indent' ] },
    { name: 'undoredo',    items : [ 'Undo','Redo' ] },
    { name: 'image',       items : [ 'Image' ] },
    { name: 'extra1',      items : [ 'HorizontalRule' ] },
    { name: 'extra2',      items : [ 'TextColor','BGColor' ] },
    { name: 'link',        items : [ 'Link','Unlink'] },
    { name: 'extra3',      items : [ 'FontSize' ] },
	// '/',
    { name: 'styles',      items : ['Styles' ] },
    { name: 'format',      items : ['Format' ] },
	],

	//Filebrowser - settings
	filebrowserWindowHeight : '70%' ,
	filebrowserWindowWidth : '80%' ,

	%FILEBROWSER%

	extraPlugins : 'stylesheetparser',
    stylesheetParser_validSelectors : /(img|p|span)\.*/i,
    stylesheetParser_skipSelectors : /(^body\.|^caption\.|^div\.|^\.)/i

    }";


	$ckconfig = str_replace('%BASE_HREF%', 'http://'.$_SERVER['HTTP_HOST'].$sn, $ckconfig);

	$ckconfig = str_replace('%SKIN%', $plugin_cf['ckeditor']['skin'], $ckconfig);

    $blockformat = 'p;';
    for ($i=7 - $cf['menu']['levels'];$i<7;$i++) {
        $blockformat .= "h$i;";
    }
    $ckconfig = str_replace('%BLOCKFORMATS%', trim($blockformat,';') , $ckconfig);


    $ck_language = file_exists($pth['folder']['plugins'] . 'tinymce/' . 'tiny_mce/langs/' . $sl . '.js') ? $sl
	: (file_exists($pth['folder']['plugins'] . 'tinymce/' . 'tiny_mce/langs/' . $cf['language']['default'] . '.js') ? $cf['language']['default']
	: 'en');
    $ckconfig = str_replace('%LANGUAGE%', $ck_language, $ckconfig);

	$ck_css = "['". CMSIMPLE_ROOT . trim($pth['folder']['template'] . 'stylesheet.css','./')
            . "', '" .CMSIMPLE_ROOT . trim($pth['folder']['plugins'] . 'calendar/css/editor.css','./') ."']";
    $ckconfig = str_replace('%STYLESHEET%', $ck_css, $ckconfig);

    $ckconfig = str_replace("%EDITOR_HEIGHT%", $plugin_cf['calendar']['editor_height'], $ckconfig);

	//$ckconfig = str_replace('%FILEBROWSER%', ckeditor_filebrowser(), $ckconfig);








?>
