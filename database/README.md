# Couch Database Utility
This is a simple utility that provides basic tools for backing up and restoring the database on your Couch site. To install it, download and unzip the attached file. Place the entire "database" folder in your couch folder.

## Quick Start
Go to site.com/couch/database/ while logged in as an admin. Push the buttons.

## Backup, Download, Restore, Sync
The Database Utility includes simple functions to quickly save, download, or restore a database backup. To sync between a live site and your local copy, simply take a backup from one and restore it to the other.

## Manage Backups
A simple file manager allows you to download, rename, or delete your saved backup files.

## Automatic Backups
The thing about Database Backups is that you almost never need one, but when you do, you need it really, really bad. For this reason automatic backups are much preferable to manual backups which you're likely to forget or fall behind on. This utility now includes a script for making automatic backups using a cron job. Don't be scared off if the idea of a cron job is unfamiliar to you. It's really pretty easy.

You'll find a tab in your cPanel for setting up Cron Jobs. Start by setting the interval for running the backup script. For relatively simple sites, I save a backup once a week. For a more active site or one with crucial data, you may want to save backups more frequently. However, the file naming convention for cron backups does not support more than one backup per day.

Next enter the command that you want to run. The "Configure a Cron Job" button will help you create the command for calling the backup script.

## Host Restrictions
Some hosts won't allow you to call a cron job from your public_html folder. Normally, these hosts require scripts to be in the cgi-bin. In this case, you will need a hard-coded script to run outside of the Couch folder. But never fear. This utility can create the script for you. You can download a custom script and place it in your cgi-bin (or wherever the host requires). You will need to use the correct path/to/the/file when you set up the cron job, but that won't be too hard to figure out.

If you run into complications, don't get frustrated. Automatic database backups are well worth the effort.


## Cautions
As with any database operation, be sure you have a good backup before proceeding with the restore operation.

This utility backs up and restores the entire database, not just the Couch tables. If your site has a database to itself, like most sites, then that's a good thing. But if your site shares a database - for example by using prefixes to install multiple Couch instances on the same database - you might have conflicts.

## System Paths
The functions used by this utility for backing up and restoring the database are system commands (like you would type into a terminal), not PHP functions. The path to these system commands is different depending on your system and server configuration. This utility does its best to automatically determine the correct path, but if the utility throws errors or creates 0 byte files, you can configure the paths manually in the couch/database/config.php file.

Some hosts may not allow PHP to access the system commands for security purposes. If that's the case, then this utility simply won't work.
