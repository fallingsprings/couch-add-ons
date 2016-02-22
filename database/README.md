#Couch Database Utility - Backup and Restore
This is a simple utility that provides basic tools for backing up and restoring the database on your Couch site.

To install it, download and unzip the attached file. Place the entire "database" folder in your couch folder.

##Quick Start
Go to site.com/couch/database/ while logged in as an admin. Push the buttons.

##Backup
To create a database backup, hit the "Create a Backup" button. A backup will be save to couch/database/backups.

##Download
The "Download a Backup" button can be used to just grab a current backup file. Use the File Manager to download a previously saved backup.

##Restore
To restore a database backup, select one of the backup files from the dropdown list and hit the restore button. This erases your database and installs the selected one. Be sure you have a good backup before continuing.

##Sync
To sync between a live site and your local copy, simply take a backup from one and restore it to the other.

##Manage Backups
A simple file manager allows you to download, rename, or delete your saved backup files.

##Cautions
As with any database operation, be sure you have a good backup before proceeding with the restore operation.

This utility backs up and restores the entire database, not just the Couch tables. If your site has a database to itself, like most sites, then that's a good thing. But if your site shares a database - for example by using prefixes to install multiple Couch instances on the same database - it could cause conflicts.

##System Paths
The functions used by this utility for backing up and restoring the database are system commands (like you would type into a terminal), not PHP functions. The path to these system commands is different depending on your system and server configuration. This utility does its best to automatically determine the correct path, but if the utility throws errors or creates 0 byte files, you will can configure the paths manually in the couch/database/config.php file.

Some hosts may not allow PHP to access the system commands for security purposes. If that's the case, then this utility simply won't work.