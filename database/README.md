#Couch Database Utility - Backup and Restore
This is a simple utility that provides basic tools for backing up and restoring the database on your Couch site.

To install it, download and unzip the attached file. Place the entire "database" folder in your couch folder.

##Restore
To restore a database backup, visit site.com/couch/database/restore.php in your browser while logged in as an admin. Select one of the backup files from the dropdown list (the most recent should be listed first) and hit the button. This erases your database and installs the selected one. Be sure you have a good backup before continuing.

##Sync
To sync between a live site and your local copy, simply take a backup from one and restore it to the other.

##Caveats
As with any database operation, be sure you have a good backup before proceeding with the restore operation.

This utility backs up and restores the entire database, not just the Couch tables. If your site has a database to itself, it's not a problem. But if your site shares a database - for example if using a prefix to install multiple Couch instances on the same database - that could lead to problems.

You may need to fiddle with your server configuration on your local server. PHP needs access to the mysql system tools. For MAMP servers on OSX, open the 3 files and you'll see where you can correct them for MAMP's quirky configuration.