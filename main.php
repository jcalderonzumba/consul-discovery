<?php
require __DIR__ . '/vendor/autoload.php';
use Zumba\Consul\Features\Discovery;

$discovery = new Discovery([
  "consul_url" => "YOUR_CONSUL_HTTP_API"
]);

$service = $discovery->getService("pigeon");
var_dump($service);
