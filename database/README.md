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

##System Paths
The functions used by this utility for backing up and restoring the database are system commands (like you would type into a terminal), not PHP functions. The path to these system commands is different depending on your system and server configuration. This utility does its best to automatically determine the correct path, but if the utility throws errors or creates 0 byte files, you will can configure the paths manually in the couch/database/config.php file.

Some hosts may not allow PHP to access the system commands for security purposes. If that's the case, then this utility simply won't work.

##Automatic Backups
The thing about Database Backups is that you almost never need one, but when you do, you need it really, really bad. This makes automatic backups much preferable to manual backups which you're likely to forget or fall behind on.

This utility includes a script for making automatic backups by setting up a Cron Job. Don't be scared off if the idea of a Cron Job is unfamiliar to you. It's really easy. You'll find a tab in your CPanel for setting up Cron Jobs.

Start by setting the interval for running the backup script. For my relatively simple sites, I save a backup once a week. For a more active site or one with crucial data, you may want to save backups more frequently.

Then enter the command that you want to run. A button in the utility will help you create the command that will call the backup script.

###Host Restrictions
There's one last potential complication. Some hosts won't allow you to call a Cron Job from your public_html folder. Normally, they require scripts to be in the cgi-bin. You will have to hard code the script with your database credentials and put it where your host requires.

But never fear. There is a generator that will create the script for you. Simply download it and place it in your cgi-bin (or wherever the host requires). You will need to use the correct path in the command when you set up the Cron Job, but that won't be too hard to figure out.

If you run into complications, don't get frustrated. Automatic database backups are well worth the effort.
