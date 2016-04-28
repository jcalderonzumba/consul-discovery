<?php
namespace Zumba\Consul\Client;

/**
 * Class CurlClient
 * @package Zumba\Consul\Client
 */
class CurlClient {

  /** @var mixed */
  protected $error;
  /** @var mixed */
  protected $options;
  /** @var  mixed */
  private $requestResponse;

  /**
   * CurlClient constructor.
   * @param $options
   */
  public function __construct($options = []) {
    $this->options = $options;
    $this->error = null;
    $this->requestResponse = null;
  }

  /**
   *  Pretty basic GET client for consul service discovery
   * We want things to be FAST, so we avoid fancy stuff
   * @param $url
   * @param $options
   * @return mixed
   */
  public function get($url, $options = []) {
    $requestOptions = array_merge($this->options, $options);
    try {
      $this->error = null;
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      if (isset($requestOptions["headers"])) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $requestOptions["headers"]);
      }
      $response = [
        "body"     => curl_exec($curl),
        "info"     => curl_getinfo($curl),
        "error"    => curl_errno($curl),
        "error_no" => curl_errno($curl)
      ];
      curl_close($curl);
      $this->requestResponse = $response;
      return $response;
    } catch (\Exception $e) {
      $this->error["raw"] = $e->getMessage();
    }
    return null;
  }

  /**
   * @return int
   */
  public function getStatusCode() {
    if ($this->requestResponse === null) {
      return null;
    }
    return intval($this->requestResponse["info"]["http_code"]);
  }

  /**
   *  Gets the json response of a request
   * @return mixed
   */
  public function getJsonResponse() {
    if ($this->requestResponse === null) {
      return null;
    }
    if (strstr($this->requestResponse["info"]["content_type"], "json") === false) {
      //Can not decode something that is not json
      $this->error["raw"] = "NOT_JSON_RESPONSE";
      return null;
    }
    $jsonBody = json_decode($this->requestResponse["body"], true);
    if ($jsonBody === null) {
      //Error while decoding the body
      $this->error["raw"] = json_last_error_msg();
      return null;
    }
    return $jsonBody;
  }
}
