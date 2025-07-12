# Upload Store
a web tool with which you can let files get uploaded to you if you have a server, interested uploader needs to have an email address

## Requirements
- a server (hardware)
- server software which obeys .htaccess files such as Apache web server
- https
- a domain
- PHP >= 8.1 on your server
- mail capability from your server
- an email address on your server or externally (depends)
- access to your server by non-web means such as SFTP

## Installation
- copy to any directory on your server you can access via the web
- open setup.php in that directory via the web or open config.json in directory "private" without quotation marks in that directory by SFTP or similar
- complete setup and save

## Features
- whitelist email addresses of potential uploaders
- manual approval requests of interested uploaders to your email address
- uploaded files only accessible by you