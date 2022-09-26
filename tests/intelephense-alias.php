<?php

namespace mock;

class Exception extends \Exception {}

namespace mock\GuzzleHttp;

class Client extends \GuzzleHttp\Client {}
interface ClientInterface extends \GuzzleHttp\ClientInterface {}

namespace mock\GuzzleHttp\Psr7;

class Response extends \GuzzleHttp\Psr7\Response {}

namespace mock\Stancer\Core;

class AbstractObject extends \Stancer\Core\AbstractObject {}
class Logger extends \Stancer\Core\Logger {}
class Request extends \Stancer\Core\Request {}

namespace mock\Stancer\Http;

class Client extends \Stancer\Http\Client {}
class Response extends \Stancer\Http\Response {}

namespace mock\Stancer\Http\Verb;

class AbstractVerb extends \Stancer\Http\Verb\AbstractVerb {}

namespace mock\Psr\Log;

class LoggerInterface extends \Psr\Log\LoggerInterface {}

namespace mock\Psr\Http\Message;

class RequestInterface extends \Psr\Http\Message\RequestInterface {}
class ResponseInterface extends \Psr\Http\Message\ResponseInterface {}
class UriInterface extends \Psr\Http\Message\UriInterface {}
