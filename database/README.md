This is a simple database utility that provides basic tools for backing up and restoring your Couch site's database.

To install it, download and unzip the attached file. Place the entire "database" folder in your couch folder.

###Back up
To create a database backup, visit site.com/couch/database/backup.php in your browser while logged in as an admin. Hit the button to create a backup of the current state of the site's database. A backup will be save to couch/database/backups. You will then have the option to download a copy of the backup.

###Restore
To restore a database backup, visit site.com/couch/database/restore.php in your browser while logged in as an admin. Select one of the files in your 'backups' folder from the dropdown list (the most recent should be listed first) and hit the button. This erases your database and installs the selected one. Be sure your backups are in good order before proceeding.

###Sync
To sync your live site to your local machine, take a backup and download it. Place the downloaded file in the couch/database/backups folder of your local installation. Use the 
restore tool to update the local database.

This will sync the database, but uploaded files and images will need to be synced using ftp.

##Caveats
This is a simple utility that backs up and restores the entire database, not just the Couch tables. This could create problems if your site shares a database, for example if using a prefix to install multiple Couch instances on the same database. But if your site has a database to itself, it's not a problem.

As with any database operation, be sure you have a good backup before proceeding with the restore operation.

You may need to fiddle with your configuration if the tools don't work on your local server. PHP needs access to the mysql system tools.