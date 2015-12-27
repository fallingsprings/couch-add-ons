#Couch Database Utility - Backup and Restore
This is a simple utility that provides basic tools for backing up and restoring the database on your Couch site.

To install it, download and unzip the attached file. Place the entire "database" folder in your couch folder.

##Quick Start
Go to site.com/couch/database/ while logged in as an admin. Push the buttons.

##Back up
To create a database backup, hit the "Create a Backup" button. A backup will be save to couch/database/backups, and you will also have the option to download a copy.

##Restore
To restore a database backup, hit the "Restore a Backup" button. Select one of the backup files from the dropdown list (the most recent should be listed first) and hit the restore button. This erases your database and installs the selected one. Be sure you have a good backup before continuing.

##Sync
To sync between a live site and your local copy, simply take a backup from one and restore it to the other.

##Caveats
As with any database operation, be sure you have a good backup before proceeding with the restore operation.

This utility backs up and restores the entire database, not just the Couch tables. If your site has a database to itself, like most sites, then that's a good thing. But if your site shares a database - for example by using prefixes to install multiple Couch instances on the same database - it could cause issues.