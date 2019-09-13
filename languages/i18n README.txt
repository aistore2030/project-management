The CQPIM PO/MO files are split into backend and frontend.

Frontend is for the client dashboard and contains translations for all files in the templates folder.

Backend is for the WP Admin translations and includes the files in the following directories - 

cpt
metaboxes

As well as the following files - 

capabilities.php
cqpim_functions.php
dashboard.php
plugin-settings.php
support-tickets.php
tasks.php

In order to translate CQPIM, you should use PoEdit to generate a new translation file 
for your language using the existing cqpim-en_GB.po file in each directory.. This should
be saved in the admin or frontend directory in the following format - 

cqpim-"lang"_"region".po
cqpim-"lang"_"region".mo

EG. cqpim-fr_FR.po

Ensure you have the catalogue in PoEdit set to detect the following keywords - 

__
_e
_x

You also need to exclude the assets and js folders in the CQPIM directory.

In order for WordPress to display the correct lanuage from your files, you must make sure you 
have set WPLANG to your language and region (EG. fr_FR). This is done in the options.php file at - 

http://www.yourdomain.com/wp-admin/options.php

If you translate CQPIM, it would be great if you could email support@timeless-interactive.com with your files 
so that we can include them in future releases.

Have fun!
