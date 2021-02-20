<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay;

use GuzzleHttp\Psr7\Request;
use System\Base\BasePackage;

/**
 * @internal Sends PSR-7-compatible requests using a Guzzle client.
 */
class HttpHandler extends BasePackage
{
    /**
     * @var array Associative array of HTTP options that the SDK supports.
     */
    private static $validOptions = [
        'connect_timeout' => true,
        'curl'            => true,
        'debug'           => true,
        'delay'           => true,
        'http_errors'     => true,
        'proxy'           => true,
        'timeout'         => true,
        'verify'          => true
    ];

    /**
     * @param \Psr7Request|RequestInterface $request
     * @param array $options Http options for the client.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface Promise that will be resolved with a
     * Psr\Http\Message\ResponseInterface response object.
     */
    public function __invoke(Request $request, array $options)
    {
        // Remove options that are not supported.
        foreach (array_keys($options) as $key) {
            if (!isset(self::$validOptions[$key])) {
                unset($options[$key]);
            }
        }

        return $this->remoteContent->sendAsync($request, $options);
    }
}
