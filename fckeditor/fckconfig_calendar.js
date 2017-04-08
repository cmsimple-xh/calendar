/*******************************************************************************************
FCKeditor-Integration for Calendar-Plugin for CMSimple_XH by svasti (www.svasti.de)

based on work of
© 2007-2009 Connie Müller-Gödecke, Holger Irmler, Klaus Treichler, especially
Version 1.6 bei Holger Irmler supplied with CMSImple_XM 1.4
This work is licensed under GNU General Public License Version 2 or later (GPL),
********************************************************************************************/

// Name and path to your custom EditorAreaCSS file
// Here you can precisely simulate the output of your site inside FCKeditor, including background colors,
// font styles, sizes and your custom CSS definitions
// Remember to comment out / remove the line with the include of your template-stylesheet css in ./cmsimple/fckeditor.php!
// FCKConfig.EditorAreaCSS = '' ;

//You can precisely simulate the output of your site inside FCKeditor, including background colors, font styles and
//sizes. The EditorAreaStyles  option is similar to EditorAreaCSS. The difference is that you define your custom style
//inside the configuration file or inline in the page when creating the editor instance.
//For example: FCKConfig.EditorAreaStyles = 'body { color: Grey } h1 { color: Orange }';
//FCKConfig.EditorAreaStyles = 'body {margin:0;padding:0;font:normal normal 11px/1.4 Verdana} p,li {margin:0; padding:0;} ul,ol {margin-top:0;margin-bottom:0;} i {color:#006;letter-spacing:0.05em;} b {font-size:13px;color:#447}';

// ToolbarComboPreviewCSS makes it possible to point the Style and Format toolbar combos to
// a different CSS, avoiding conflicts with the editor area CSS.
// Example:
// FCKConfig.ToolbarComboPreviewCSS = '/mycssstyles/toolbar.css' ;
// FCKConfig.ToolbarComboPreviewCSS =  '../custom_configurations/toolbar.css' ;

// This option sets the DOCTYPE to be used in the editable area. The actual rendering depends on the value set here.
// For example, to make the editor rendering engine work under the XHTML 1.0 Transitional:
// FCKConfig.DocType = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' ;
// As of FCKeditor 2.6.1, if the DocType setting is explicetlly set to the HTML4 doctype, the editor will not produce tags like <br /> but <br> instead.
// FCKConfig.DocType = '<!DOCTYPE html>' ;

// Name and path to your custom fckstyles.xml file for the "Style" toolbar:
// Here you can offer a complete set of predefined formatting definitions to the end-user (writer)
// so the text can be well designed without messing up the HTML
// FCKConfig.StylesXmlPath = '../custom_configurations/custom_fckstyles.xml' ;
// Or you can define a list of custom styles like below
//
// FCKConfig.CustomStyles =
// {
// 	'Red Title'	: { Element : 'h3', Styles : { 'color' : 'Red' } }
// };
FCKConfig.CustomStyles = '' ;


// FCKConfig.TemplatesXmlPath	= '' ;

//FCKConfig.EnableMoreFontColors = true ;
//FCKConfig.FontColors = '000000,993300,333300,003300,003366,000080,333399,333333,800000,FF6600,808000,808080,008080,0000FF,666699,808080,FF0000,FF9900,99CC00,339966,33CCCC,3366FF,800080,999999,FF00FF,FFCC00,FFFF00,00FF00,00FFFF,00CCFF,993366,C0C0C0,FF99CC,FFCC99,FFFF99,CCFFCC,CCFFFF,99CCFF,CC99FF,FFFFFF' ;

FCKConfig.FontFormats	= 'p;h3;h4;h5;h6' ;
//FCKConfig.FontNames		= 'Arial;Arial Black;Comic Sans MS;Courier New;Georgia;Tahoma;Times New Roman;Trebuchet MS;Verdana' ;
//FCKConfig.FontSizes		= '8px;9px;10px;11px;12px;13px;14px;15px;16px;17px;18px;19px;20px;22px;24px;26px;28px;30px;32px;34px;36px;38px;40px' ;

// define which Skin you want to use, more skins can be downloaded at:
// http://sourceforge.net/tracker/?group_id=75348&atid=740153
// FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/silver/' ;
FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/default/' ;
// FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/office2003/' ;

FCKConfig.ProcessHTMLEntities	= false ;
FCKConfig.IncludeLatinEntities	= false ;
FCKConfig.IncludeGreekEntities	= false ;

FCKConfig.ProcessNumericEntities = false ;

FCKConfig.AdditionalNumericEntities = ''  ;		// Single Quote: "'"

FCKConfig.FillEmptyBlocks	= true ;

FCKConfig.FormatSource		= false ;
FCKConfig.FormatOutput		= false ;
FCKConfig.FormatIndentator	= '    ' ;


FCKConfig.ForcePasteAsPlainText	= true ;
FCKConfig.IgnoreEmptyParagraphValue = true ;


FCKConfig.PluginsPath = FCKConfig.BasePath + 'plugins/' ;
// PlugIns can be activated by commenting / uncommenting them
FCKConfig.Plugins.Add('nbsp','de,en');
FCKConfig.Plugins.Add('sitelink','de,en');

FCKConfig.TemplateReplaceAll = false ;

FCKConfig.ToolbarSets["calendar"] = [
	['Bold','Italic','Underline'],
    ['OrderedList','UnorderedList'],['Undo'],
    ['FitWindow','Image'],
    ['JustifyLeft','JustifyCenter','JustifyRight'],
    ['Outdent','Indent'],
    ['Link','Unlink','sitelink'],
    ['Style'],['FontFormat'],['RemoveFormat'],['Source']
] ;

FCKConfig.ProtectedSource.Add( /<\?[\s\S]*?\?>/g ) ;	// PHP style server side code
// FCKConfig.ProtectedSource.Add( /#CMSimple[\s\S]*?#/g ) ; // CMSimple scripting

FCKConfig.EnterMode = 'p' ;			// p | div | br
FCKConfig.ShiftEnterMode = 'br' ;	// p | div | br

FCKConfig.Keystrokes = [
	[ CTRL + 65 /*A*/, true ],
	[ CTRL + 67 /*C*/, true ],
	[ CTRL + 70 /*F*/, true ],
	[ CTRL + 83 /*S*/, true ],
	[ CTRL + 84 /*T*/, true ],
	[ CTRL + 88 /*X*/, true ],
	[ CTRL + 86 /*V*/, 'Paste' ],
	[ CTRL + 45 /*INS*/, true ],
	[ SHIFT + 45 /*INS*/, 'Paste' ],
	[ CTRL + 88 /*X*/, 'Cut' ],
	[ SHIFT + 46 /*DEL*/, 'Cut' ],
	[ CTRL + 90 /*Z*/, 'Undo' ],
	[ CTRL + 89 /*Y*/, 'Redo' ],
	[ CTRL + SHIFT + 90 /*Z*/, 'Redo' ],
	[ CTRL + 76 /*L*/, 'Link' ],
	[ CTRL + 66 /*B*/, 'Bold' ],
	[ CTRL + 73 /*I*/, 'Italic' ],
	[ CTRL + 85 /*U*/, 'Underline' ],
	[ CTRL + ALT + 13 /*ENTER*/, 'FitWindow' ],
	[ SHIFT + 32 /*SPACE*/, 'Nbsp' ],
    [ CTRL + ALT + 83 /*S*/, 'Source' ],
] ;

FCKConfig.AutoDetectLanguage	= true ;
FCKConfig.DefaultLanguage		= 'de' ;
FCKConfig.ContentLangDirection	= 'ltr' ;

FCKConfig.CoreStyles =
{
	// Basic Inline Styles.
	'Bold'			: { Element : 'b', Overrides : 'strong' },
	'Italic'		: { Element : 'i', Overrides : 'em' },
	'Underline'		: { Element : 'u' },
	'StrikeThrough'	: { Element : 'strike' },
	'Subscript'		: { Element : 'sub' },
	'Superscript'	: { Element : 'sup' },

	// Basic Block Styles (Font Format Combo).
	'p'				: { Element : 'p' },
	'div'			: { Element : 'div' },
	'pre'			: { Element : 'pre' },
	'address'		: { Element : 'address' },
	'h1'			: { Element : 'h1' },
	'h2'			: { Element : 'h2' },
	'h3'			: { Element : 'h3' },
	'h4'			: { Element : 'h4' },
	'h5'			: { Element : 'h5' },
	'h6'			: { Element : 'h6' },

	// Other formatting features.
	'FontFace' :
	{
		Element		: 'span',
		Styles		: { 'font-family' : '#("Font")' },
		Overrides	: [ { Element : 'font', Attributes : { 'face' : null } } ]
	},

	'Size' :
	{
		Element		: 'span',
		Styles		: { 'font-size' : '#("Size","fontSize")' },
		Overrides	: [ { Element : 'font', Attributes : { 'size' : null } } ]
	},

	'Color' :
	{
		Element		: 'span',
		Styles		: { 'color' : '#("Color","color")' },
		Overrides	: [ { Element : 'font', Attributes : { 'color' : null } } ]
	},

	'BackColor'		: { Element : 'span', Styles : { 'background-color' : '#("Color","color")' } },

	'SelectionHighlight' : { Element : 'span', Styles : { 'background-color' : 'navy', 'color' : 'white' } }
};

// The following value defines which File Browser connector and Quick Upload
// "uploader" to use. It is valid for the default implementaion and it is here
// just to make this configuration file cleaner.
// It is not possible to change this value using an external file or even
// inline when creating the editor instance. In that cases you must set the
// values of LinkBrowserURL, ImageBrowserURL and so on.
// Custom implementations should just ignore it.
var _FileBrowserLanguage	= 'php' ;	// asp | aspx | cfm | lasso | perl | php | py
var _QuickUploadLanguage	= 'php' ;	// asp | aspx | cfm | lasso | perl | php | py

// HI: FileBrowser-Settings moved to FCKeditor.php with Version 2.4.0

FCKConfig.LinkUpload 					= false ; //Deactivate QuickUpload-Tab
FCKConfig.LinkUploadURL 				= FCKConfig.BasePath + 'filemanager/connectors/' + _QuickUploadLanguage + '/upload.' + _QuickUploadExtension ;
FCKConfig.LinkUploadAllowedExtensions	= ".(7z|aiff|asf|avi|bmp|csv|doc|fla|flv|gif|gz|gzip|jpeg|jpg|mid|mov|mp3|mp4|mpc|mpeg|mpg|ods|odt|pdf|png|ppt|pxd|qt|ram|rar|rm|rmi|rmvb|rtf|sdc|sitd|swf|sxc|sxw|tar|tgz|tif|tiff|txt|vsd|wav|wma|wmv|xls|xml|zip)$" ;			// empty for all
FCKConfig.LinkUploadDeniedExtensions	= "" ;	// empty for no one

FCKConfig.ImageUpload 					= false ; //Deactivate QuickUpload-Tab
FCKConfig.ImageUploadURL 				= FCKConfig.BasePath + 'filemanager/connectors/' + _QuickUploadLanguage + '/upload.' + _QuickUploadExtension + '?Type=Image' ;
FCKConfig.ImageUploadAllowedExtensions	= ".(jpg|gif|jpeg|png)$" ;		// empty for all
FCKConfig.ImageUploadDeniedExtensions	= "" ;							// empty for no one

FCKConfig.SmileyPath					= FCKConfig.BasePath + 'images/smiley/msn/' ;
FCKConfig.SmileyImages					= ['regular_smile.gif','sad_smile.gif','wink_smile.gif','teeth_smile.gif','confused_smile.gif','tounge_smile.gif','embaressed_smile.gif','omg_smile.gif','whatchutalkingabout_smile.gif','angry_smile.gif','angel_smile.gif','shades_smile.gif','devil_smile.gif','cry_smile.gif','lightbulb.gif','thumbs_down.gif','thumbs_up.gif','heart.gif','broken_heart.gif','kiss.gif','envelope.gif'] ;
FCKConfig.SmileyColumns 				= 8 ;
FCKConfig.SmileyWindowWidth				= 320 ;
FCKConfig.SmileyWindowHeight			= 240 ;
