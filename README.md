# elisa
Elisa - simple FTP management panel

Elisa is a simple FTP panel written in pure php, HTML 5, CSS 3 with Bootstrap CDN.

# How is it built?

Elisa was built basing on raw plain MVC architectonical pattern.
There are no other patterns used.
All utility classes are just final static classes and views are normal public classes so they can be instantiated using defined constructor functions.

# Technical information

I used php __7.0__ to built this code. I tested it primarly on CentOS 7 and Debian 9 using Apache 2.4 WWW Server.
There is a need that u use proftpd server and it is configured to work with mysql server set up anywhere in the LAN.
I tested Elisa with MariaDB 10.0. It is important to use it as well, because Oracle MySQL Server not always want to work properly.

# How to set up?

I highly recommend just ___NOT___  to use this software, but if you are very eager to test it follow these steps:

Linux: 

I assume that you are fammiliar with bash and you are able to install php-7.0 package on your own. Let me replace /your/path/to/www/root/ with this: {wwwroot}. I will do it like on my own servers so it is specific for configuration: Debian 9 with Apache2.4


* $ git clone https://github.com/Obsidiam/elisa/
* $ sudo mkdir elisa
* $ sudo mkdir {wwwroot}/elisa/data
* $ sudo chown -R www-data:www-data {wwwroot}/elisa
* $ sudo cp -r elisa {wwwroot}/elisa/*
* $ sudo systemctl restart apache2
* $ set enforce 0 - this is optional, but it may help if even with owned directories in {wwwroot}/data WWW Server will still have problems writing anything there.

Windows:
Oh, please, no, am not gonna write that for this shitty OS.

# Bugs

Am not really planning to improve this software however any bugs can be reported in Issues on this repo, if any will appear to be critical one I will take a look on it. You can always do pull requests, I will highly appreciate those.
