# Lemon

Lemon is a tiny RESTful micro framework based on [Symfony Components](http://symfony.com/components).

Its primary objective is act as a light-weight request/response framework, focused towards building
RESTful API layer. Due to the nature of it, it does not support any templating but if you need one,
its easy to add.

## Features

### Version 0.2

* Named routing
* Method restriction
* Default parameter values
* Configuration support
* More robust route handling

### Version 0.1

* Loading resouces with strict RESTful convention only

## How to get started

Clone the repository in your local machine. Install composer and download the
required dependencies (in the Lemon directory):

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar install

Visit to the resource URL. Optionally, set up a VirtualHost to the web root:

    http://localhost/Lemon/web/index.php/hello OR
    http://lemon.dev/hello

Enjoy!
