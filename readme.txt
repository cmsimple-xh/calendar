Instructions: help/help_en.htm


Updating
===============
1.4.4 –> 1.4.5
Exchange:

  admin.php
  includes/...

  version.nfo
  changelog.txt
  help/...
  readme.txt


1.4.0 –> 1.4.5
Exchange:

  admin.php
  index.php
  includes/...
  editorconfigs/...
  languages/...

  version.nfo
  readme.txt
  changelog.txt
  help/...


1.4 beta –> 1.4.5
- Save old settings through Presets>backup for Event-list config and also Calendar config.
- Keep your calendar/content folder, the calendar/templates/ folder 
  and your config file (calendar/config/config.php) in case you have changed some config settings.
- Replace the remaining files.
- Activate your old presets by choosing Presets>backupname for the event-list and the calendar. 
- If you have kept your old config file, you need to add the line:
  $plugin_cf['calendar']['eventlist_start_moves_with_clicked_event']="1";

1.3.9 –> 1.4.5
- Re-use old content files
- Old function calls should still work.
- Link-entry of internal pages has changed: enter the page name and nothing else.
- Birthday-entry has changed: check the "yearly" check box.

=================


v 1.4      (2012) by svasti
v 1.3      (05-11/2011) by svasti
v 1.2      (04/2011) by Holger
v 1.1      (03/2011) by svasti
v 0.6-1.0  by Tory
unnamed    by Bob
v 0.1-0.5  by Michael Svarrer

=================

Depending on server configuration you may have
to give writing permissions (chmod 646)

CMSimple root
+ cmsimple
+ content
+ downloads
+ images
+ plugins
  + pluginloader
  + calendar
     + backgroundimages 
     + config
        - config.php     (chmod 646)
        - config2.php    (chmod 646)
     + content           (chmod 646)
        - *.*            (chmod 646)
     + css
        - stylesheet.css (chmod 646)
     + dp
     * fckeditor
     + help
     + images
     + includes
     + jscolor
     + languages
        - *.php          (chmod 646)
     - admin.php
     - index.php
     + templates         (chmod 646)
 
+ templates 
