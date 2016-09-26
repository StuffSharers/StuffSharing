# Developer's Guide

1. Use phpPgAdmin to create database `stuffsharing` and role `stuffsharers` (you can set your own password). Grant all privileges on `stuffsharing` to `stuffsharers`.

2. Edit file `<BitnamiRoot>/php/php.ini` and set:
 - opcache.enable=0
 - display_errors = On

3. Copy folder `stuffsharing` from repo into folder `<BitnamiRoot>/apache2/htdocs/`.

4. In folder `<BitnamiRoot>/apache2/htdocs/stuffsharing/include/`, rename `secrets_sample.php` to `secrets.php`. Change the value of `DB_PASS` if you used a different password in step 1.

5. Verify correct setup by visiting `<BitnamiHost>/stuffsharing` in your browser.

6. Indent using 4 spaces when editing PHP files!
