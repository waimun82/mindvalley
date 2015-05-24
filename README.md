# Prototype System for Mindvalley Challenge

## About

* Written by Waimun Chin (waimun82@hotmail.com) for Mindvalley Challenge.
* System is written in PHP, Javascript, Smarty framework and MySQL database.
* Demo is available at http://www.munster.me.

## System Requirements

* PHP 5.4.0 or higher
* MySQL 5.0 or higher

## Installation

1. Download the source code in this repository and upload to your web server.

2. Setup the database by importing database.sql.

3. Edit library/config.inc.php and setup the following mandatory constants:

* DB_USERNAME: The database username.
* DB_PASSWORD: The database password.
* SYSTEM_TINY_URL_HOST: The tiny URL host to be used for the Bookmark module.
* SYSTEM_PREVIEW_URL_HOST: The preview URL host or sub-domain to be used for the Bookmark module. Access using the preview URL will not increase the view count of the bookmark record.

## System Features

### Accessing The System

1. The main application can be accessed through the web folder.

2. User may login with an existing email/password or click on register to create a new user.

### My Bookmarks

1. User will be able to create personal bookmarks by adding a URL.

2. When a bookmark record is created, a short hashkey will be generated by encoding the URL in md5 format.

3. The tiny URL is created by combining the tiny URL host (which is pointed to the index page in the root folder) and the hashkey as virtual folder. HTACCESSS file in the root folder will be updated with a rewrite rule to redirect the tiny URL to index.php with the hashkey as parameter.

4. When a request comes from the tiny URL, the index page will search for the full URL from the database record using the hashkey parameter and update the view count of the bookmark record. The index page will then redirect to the intended full URL.

## Limitations

* Creating a bookmark using an existing tiny URL may cause infinite loop.

## Future Enhancements

* Able to edit bookmarks.
* Implement bookmark web services for easier integration with other websites or applications.
* Implement description indexing using meta tags of the bookmarked URL.
* Fix limitation with bookmarking existing tiny URL and cause infinite loop.
