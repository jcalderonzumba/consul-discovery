<?php
namespace Zumba\Consul\Features;

use Zumba\Consul\Client\CurlClient;
use Zumba\Consul\Exception\ConsulConnectionException;
use Zumba\Consul\Exception\ConsulException;
use Zumba\Consul\Exception\ServiceNotFoundException;
use Zumba\Consul\Model\Service;

/**
 * Class Discovery
 * @package Zumba\Consul
 */
class Discovery {
  /** @var CurlClient */
  protected $apiClient;
  /** @var  mixed */
  protected $options;

  /**
   * Discover constructor.
   * @param mixed $options
   */
  public function __construct($options) {
    $this->options = [
      "consul_url" => "http://127.0.0.1:8500"
    ];
    if (isset($options["consul_url"])) {
      $this->options["consul_url"] = $options["consul_url"];
    }
    $this->apiClient = new CurlClient();
  }

  /**
   *  This function will search for a HEALTHY service
   * We only want to issue request to services that are OK
   * @param $serviceName
   * @return Service
   * @throws ConsulException
   */
  public function getService($serviceName) {
    //We will get the NEAREST service by default because that's fast.
    $discoverUrl = sprintf("%s/v1/health/service/%s?passing&near=_agent", $this->options["consul_url"], $serviceName);
    $discoveryResponse = $this->apiClient->get($discoverUrl);
    if ($discoveryResponse === null) {
      throw new ConsulConnectionException("Error while communicating to Consul");
    }
    if ($this->apiClient->getStatusCode() != 200) {
      //Something bad is happening to the agent
      throw new ConsulConnectionException(sprintf("Consul returned %s HTTP status code", $this->apiClient->getStatusCode()));
    }
    $serviceResponse = $this->apiClient->getJsonResponse();
    if (empty($serviceResponse)) {
      throw new ServiceNotFoundException(sprintf("No healthy %s service found", $serviceName));
    }
    //By default we are asking healthy service and sorted by nearest to the agent so we will return the first node
    return new Service($serviceResponse[0]["Service"]);
  }
}
