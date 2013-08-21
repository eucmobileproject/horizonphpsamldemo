hzsamldemo
==========

Horizon SAML integration sample

These are the requirements for this project:* sqlite3: SQLite 3.7.9 or above. Or, a database system you would like to use supported by PDO
* php5: PHP 5.3.x configured with the web server of your choice
* php5-sqlite: PHP 5.3.x with PDO support for sqlite, or PDO support for the database type you would like to use
  Before running the web application, a sqlite database needs to be initialized and with two tables created:
  CREATE TABLE accounts (  nameid text,  externalid text,  principalname text,  logincount int);
  CREATE TABLE notes (  id INTEGER PRIMARY KEY AUTOINCREMENT,  author text,  note text);
  Please also ensure that the web server process has the necessary permissions to read to and write from the sqlite database file, 
  and the directory holding that file.The database access string also needs to be placed inside the settings.php file with the actual 
  location of the database file:  $settings->dsn = 'sqlite:/usr/share/nginx/www/samldemo/database.db';

Certificate setup
  Grab Horizon Workspace idP certificate from url: https://<your horizon gateway url>/SAAS/admin/settings/publicCertificate
  and place into demo/settings.php , look for certificate section and replace "<place your iDP certificate here>" with
  certificate there.
