#Couch Database Utility - Backup and Restore
This is a simple database utility that provides basic tools for backing up and restoring your Couch site's database.

To install it, download and unzip the attached file. Place the entire "database" folder in the root of your couch folder, so it becomes couch/database/ .

###Back up
To create a database backup, visit couch/database/backup.php in your browser while logged in as an admin. Hit the button to create a backup of the current state of the site's database. A backup will be saved to couch/database/backups. You will then have the option to download a copy of the backup.

###Restore
To restore a database backup, visit couch/database/restore.php in your browser while logged in as an admin. Select one of the files in your 'backups' folder from the dropdown list (the most recent should be listed first) and hit the button. This erases your database and installs the selected one. Be sure you have a good backup before proceeding.

###Sync
To sync a live site to your local machine, take a backup and download it. Place the downloaded file in the couch/database/backups folder of your local installation, then use the restore tool to update the local database.

Of course, uploaded files and images will need to be synced using ftp.

##Caveats
This is a simple utility that backs up and restores the entire database, not just the Couch tables. This could create problems if your site shares a database, for example if using a prefix to install multiple Couch instances on the same database. But if your site has a database to itself, it's not a problem.

As with any database operation, be sure you have a good backup before proceeding with the restore operation.

You may need to fiddle with your configuration if the tools don't work on your local server. PHP needs to have access to the mysql system tools.