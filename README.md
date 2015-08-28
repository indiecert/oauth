# Introduction
OAuth 2.0 server for IndieCert. This server provides "OAuth as a Service" for
resource servers that want to use IndieCert (or IndieAuth) as their means of
authenticating users.

# Development
We assume that your web server runs under the `apache` user and your user 
account is called `fkooman` in group `fkooman`.

    $ cd /var/www
    $ sudo mkdir indiecert-oauth
    $ sudo chown fkooman.fkooman indiecert-oauth
    $ git clone https://github.com/fkooman/indiecert-oauth.git
    $ cd indiecert-oauth
    $ /path/to/composer.phar install
    $ mkdir -p data
    $ sudo chown -R apache.apache data
    $ sudo semanage fcontext -a -t httpd_sys_rw_content_t '/var/www/indiecert-oauth/data(/.*)?'
    $ sudo restorecon -R /var/www/indiecert-oauth/data
    $ cp config/server.ini.example config/server.ini

Now to initialize the database:

    $ sudo -u apache php bin/indiecert-oauth-init-db.php

# Apache
Place this in `/etc/httpd/conf.d/indiecert-oauth.conf`:

    Alias /oauth /var/www/indiecert-oauth/web

    <Directory /var/www/indiecert-oauth/web>
        AllowOverride None

        Require local
        #Require all granted

        RewriteEngine on
        RewriteBase /indiecert-oauth
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^(.*)$ index.php/$1 [L,QSA]

        SetEnvIfNoCase ^Authorization$ "(.+)" HTTP_AUTHORIZATION=$1
    </Directory>

# Configuration
## Server
The server configuration is done in `config/server.ini`. 

## Resource Servers
Resource servers are registered in the IndieCert database and are retrieved 
from there. You need to configure a connection to the IndieCert database in
`config/server.ini`.

# Endpoints
There are three endpoints defined:
* `/authorize`
* `/token`
* `/introspect`

The OAuth clients will use the `/authorize` and `/token` endpoints. The 
protocol is described in [RFC 6749](https://tools.ietf.org/html/rfc6749). 
Resource servers will use the `/introspect` endpoint to validate the access 
tokens used by the clients. Resource servers need to be registered and use 
Basic authentication to validate access tokens. The protocol is described in 
[draft-ietf-oauth-introspection-11.txt](https://tools.ietf.org/html/draft-ietf-oauth-introspection).
