<?php
	

$tiny = '

//    mode    : "specific_textareas",
//    editor_selector : /%INIT_CLASSES%/,

    theme : "advanced",

    element_format : "%ELEMENT_FORMAT%",

//    relative_urls      : false,
//    remove_script_host : true,
//    document_base_url : "%BASE_URL%",
    language : "%LANGUAGE%",
    plugins : "autosave,pagebreak,style,layer,table,save,advimage,advlink,advhr,emotions,iespell,"
            + "insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,"
            + "noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount",
  
    style_formats : [
        {title : "Big", inline : "span", classes : "big"},
        {title : "Small", inline : "small"},
        {title : "Red", inline : "span", classes : "red"},
        {title : "image left", selector : "img", classes : "left"},
        {title : "image left under", selector : "img", classes : "left_under"},
        {title : "image left2", selector : "img", classes : "left2"},
        {title : "image right", selector : "img", classes : "right"},
        {title : "image right under", selector : "img", classes : "right_under"},
    ],
    removeformat : [
    {selector : "big,small", remove : "all", split : true, expand : false, block_expand : true, deep : true},
    {selector : "span", attributes : ["style", "class"], remove : "empty", split : true, expand : false, deep : true},
    {selector : "*", attributes : ["style", "class"], remove : "all", split : false, expand : false, deep : true}
    ],

    // Theme options
    theme_advanced_buttons1           : "fullscreen,code,removeformat,image,|,bold,italic,underline,outdent,indent,|,styleselect,formatselect,|,bullist,numlist",
    theme_advanced_buttons2           : "",
    theme_advanced_buttons3           : "",
    theme_advanced_toolbar_location   : "top",
    theme_advanced_toolbar_align      : "center",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing           : true,
    theme_advanced_blockformats       : "%BLOCKFORMATS%",
    theme_advanced_font_sizes         : "8px=8px,10px=10px,12px=12px,14px=14px,16px=16px,18px=18px,20px=20px,24px=24px,28px=28px,32px=32px,36px=36px,42px=42px",

	width                             : "100%",
    height : "%HEIGHT%",
    content_css   : "%STYLESHEET%",

    external_image_list_url : "%TINY_FOLDER%cms_image_list.js",
    external_link_list_url  : "%TINY_FOLDER%cms_link_list.js",

    // Extra
    apply_source_formatting : true,
    relative_urls : true,
    entity_encoding : "raw",
    inline_styles : true,

    file_browser_callback: "%FILEBROWSER_CALLBACK%",


    fullscreen_new_window   : false,
    fullscreen_settings     : {
    body_id                 : "fullscreen",
    theme_advanced_buttons1 : "save,|,fullscreen,code,removeformat,|,bold,italic,underline,strikethrough,sub,sup,outdent,indent,|justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,hr|,styleselect,formatselect,fontselect,fontsizeselect,|,link,unlink,image,cleanup,|,forecolor,backcolor,|,charmap,emotions,|,undo,redo",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    theme_advanced_statusbar_location : "bottom",


    }

    ';



    $elementFormat = $cf['xhtml']['endtags'] == 'true' ? 'xhtml' : 'html';
    $tiny = str_replace('%ELEMENT_FORMAT%', $elementFormat, $tiny);

    $tiny_language = file_exists($pth['folder']['plugins'] . 'tinymce/' . 'tiny_mce/langs/' . $sl . '.js') ? $sl
	: (file_exists($pth['folder']['plugins'] . 'tinymce/' . 'tiny_mce/langs/' . $cf['language']['default'] . '.js') ? $cf['language']['default']
	: 'en');
    $tiny = str_replace('%LANGUAGE%', $tiny_language, $tiny);

    $tiny_css = $pth['folder']['template'] . 'stylesheet.css' . ',' .$pth['folder']['plugins'] . 'calendar/css/editor.css';
    $tiny = str_replace('%STYLESHEET%', $tiny_css, $tiny);

    $tiny = str_replace('%TINY_FOLDER%', $pth['folder']['plugins'] . 'tinymce/', $tiny);

	$tiny = str_replace("%FILEBROWSER_CALLBACK%", $_SESSION['tinymce_fb_callback'], $tiny);

    $blockformat = "p,";
    for ($i=7 - $cf['menu']['levels'];$i<7;$i++) {
        $blockformat .= "h$i,";
    }
    $tiny = str_replace("%BLOCKFORMATS%", $blockformat, $tiny);

    $tiny = str_replace("%HEIGHT%", $plugin_cf['calendar']['editor_height'], $tiny);









?>
