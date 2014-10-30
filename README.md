## Build Status
[![Build Status](https://magnum.travis-ci.com/maman/sar.svg?token=tS5hnxRJT8zLdS5t58t1)](https://magnum.travis-ci.com/maman/sar)

## Requirements
* `PHP >= 5.4.*`
* Oracle 11g Enterprise
* [Composer](https://getcomposer.org)
* [Node.js](http://nodejs.org)
* `npm install grunt-cli`

## Uses
* [Slim](http://www.slimframework.com)
* [Pimple](http://pimple.sensiolabs.org)
* [Monolog](https://github.com/Seldaek/monolog)
* [Grunt](http://gruntjs.com)
* [Webpack](http://webpack.github.io)
* [Twitter Bootstrap](http://getbootstrap.com)
* [jQuery](http://jquery.com)
* [Raphaël](http://raphaeljs.com)
* [Moment.js](http://momentjs.com)

## Howto

Clone this repository to your Apache sites directory

```sh
git clone https://github.com/maman/sar.git $YOUR_APACHE_VHOST
```

Install composer dependencies

```sh
composer install && composer dump-autoload --optimize
```

Install Node.js dependencies

```sh
npm install
```

Run preparations, and build the assets

```sh
grunt prepare && grunt build
```

Open your sites on browser.

## License
GNU GPL v2.0
