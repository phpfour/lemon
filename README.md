Lemon
=====

Lemon is a tiny RESTful framework based on Symfony Components. At this moment, it's
very basic and supports loading named Resouces only. Routing configuration will be
added soon.

How to get started
==================

1. Clone the repository in your local machine

2. Install composer (in the Lemon directory):

    curl -s http://getcomposer.org/installer | php

3. Run the composer to download the required dependencies:

    php composer.phar install

4. Visit to the resource URL:

    http://localhost/Lemon/web/hello

5. Optionally, set up a VirtualHost to point to the web root:

    http://lemon.dev/hello