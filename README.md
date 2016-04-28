# CONSUL SDK
Lightweight **CONSUL** service discovery for PHP

## Requirements

You need to have [CONSUL](https://www.consul.io) agent up and running.

## Installation
This library can be installed with composer:
```
composer require jcalderonzumba/consul-discovery
```

## Usage
The usage depends on the feature of CONSUL you want to use, for the moment we support:
* **Service discovery**

### Service discovery
Discovery of a service was never easier just use:
```php
use Zumba\Consul\Features\Discovery;

$discovery = new Discovery([
  "consul_url" => "YOUR_CONSUL_HTTP_API"
]);

$service = $discovery->getService("pigeon");
echo $service->getID();
echo $service->getAddress();
echo $service->getPort();
```
By default we search **ONLY** for **healthy** service nearest to the agent.
If you want this behavior to change open a issue and let's talk about it.

## TODO
Add more features to the SDK.
