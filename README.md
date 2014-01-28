# Fuel Queue

[![Latest Stable Version](https://poser.pugx.org/indigophp/fuel-queue/v/stable.png)](https://packagist.org/packages/indigophp/fuel-queue)
[![Total Downloads](https://poser.pugx.org/indigophp/fuel-queue/downloads.png)](https://packagist.org/packages/indigophp/fuel-queue)
[![License](https://poser.pugx.org/indigophp/fuel-queue/license.png)](https://packagist.org/packages/indigophp/fuel-queue)

This package is a wrapper around [indigophp/queue](https://github.com/indigophp/queue) package.


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

1.Update your `config/queue.php`

``` php
/**
 * Predefined queue instances
 */
'queue' => array(
    'email' => null,
    'process' => 'beanstalk'
),

/**
 * Default connector
 */
'default' => 'beanstalk',

/**
 * Connector instances
 */
'connector' => array(
    'beanstalk' => function () {
        $pheanstalk = new Pheanstalk_Pheanstalk('localhost', 11300);
        return new Indigo\Queue\Connector\BeanstalkdConnector($pheanstalk);
    }
),

/**
 * Logger instance for worker (can be a Closure)
 * Must evaluate to Psr\Log\LoggerInterface
 */
'logger' => \Log::instance(),
```

2.Create queue instance

``` php
// Simple way
\Queue::forge('process');

// Predefined connector
\Queue::forge('process', 'beanstalk');

// Connector injected
$pheanstalk = new Pheanstalk_Pheanstalk('localhost', 11300);
$connector = Indigo\Queue\Connector\BeanstalkdConnector($pheanstalk);
\Queue::forge('process', $connector);

// New queue
\Queue::forge('new_queue', 'beanstalk');
```

3.Run your worker

``` bash
oil r worker process
```

``` bash
oil r worker *queue* [connector] [--quiet]
```


## Credits

- [Márk Sági-Kazár](https://github.com/sagikazarmark)
- [All Contributors](https://github.com/indigophp/fuel-queue/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/indigophp/fuel-queue/blob/develop/LICENSE) for more information.