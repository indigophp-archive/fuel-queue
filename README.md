# Fuel Queue

[![Build Status](https://travis-ci.org/indigophp/fuel-queue.svg?branch=develop)](https://travis-ci.org/indigophp/fuel-queue)
[![Latest Stable Version](https://poser.pugx.org/indigophp/fuel-queue/v/stable.png)](https://packagist.org/packages/indigophp/fuel-queue)
[![Total Downloads](https://poser.pugx.org/indigophp/fuel-queue/downloads.png)](https://packagist.org/packages/indigophp/fuel-queue)
[![License](https://poser.pugx.org/indigophp/fuel-queue/license.png)](https://packagist.org/packages/indigophp/fuel-queue)
[![Dependency Status](http://www.versioneye.com/user/projects/53d02699ead8b3f410000009/badge.svg?style=flat)](http://www.versioneye.com/user/projects/53d02699ead8b3f410000009)

**This package is a wrapper around [indigophp/queue](https://github.com/indigophp/queue) package.**


## Install

Via Composer

``` json
{
    "require": {
        "indigophp/fuel-queue": "@stable"
    }
}
```


## Usage

``` php
// Simple way
\Queue::forge('process');
```

Run your worker

``` bash
oil r worker process
```

``` bash
oil r worker *queue* [connector] [--quiet]
```


## Configuration

``` php
'process' => function () {
    $pheanstalk = new Pheanstalk_Pheanstalk('localhost', 11300);
    return new Indigo\Queue\Connector\BeanstalkdConnector($pheanstalk);
}
```


## Credits

- [Márk Sági-Kazár](https://github.com/sagikazarmark)
- [All Contributors](https://github.com/indigophp/fuel-queue/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/indigophp/fuel-queue/blob/develop/LICENSE) for more information.
