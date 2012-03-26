Lemon is a tiny RESTful micro framework based on [Symfony Components](http://symfony.com/components).

At this moment, it's very basic and supports loading Resouces only. Named routing,
content negotiation, expected format, etc will be added soon.

How to get started
==================

Clone the repository in your local machine. Install composer and download the
required dependencies (in the Lemon directory):

    curl -s http://getcomposer.org/installer | php
    php composer.phar install

Visit to the resource URL. Optionally, set up a VirtualHost to the web root:

    http://localhost/Lemon/web/hello
    http://lemon.dev/hello

Enjoy!