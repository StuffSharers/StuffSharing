# Developer's Guide

1. Use phpPgAdmin to create database `stuffsharing` and role `stuffsharers` (you can set your own password; make sure to check `Can login?`). Grant all privileges on `stuffsharing` to `stuffsharers`.

2. Edit file `<BitnamiRoot>/php/php.ini` and set:
 - `opcache.enable=0`
 - `display_errors = On`

3. Make a symbolic link from folder `stuffsharing` in repo to folder `<BitnamiRoot>/apache2/htdocs/stuffsharing`:
 - `ln -s /path/to/repo/stuffsharing /path/to/htdocs/stuffsharing` (Unix)
 - `mklink /D C:\path\to\htdocs\stuffsharing C:\path\to\repo\stuffsharing` (Windows)

4. Create file `<BitnamiRoot>/apache2/htdocs/stuffsharing/include/secrets.php` with the following content:

```
<?php
define("DB_HOST", "localhost");
define("DB_USER", "stuffsharers");
define("DB_PASS", "stuffsharers");
define("DB_NAME", "stuffsharing");
?>
```

Change the value of `DB_PASS` if you used a different password in step 1.

5. Verify correct setup by visiting `<BitnamiHost>/stuffsharing` in your browser, e.g. <http://localhost:8080/stuffsharing> or <http://localhost/stuffsharing> depending on your server config.

6. Indent using 4 spaces when editing PHP files!
