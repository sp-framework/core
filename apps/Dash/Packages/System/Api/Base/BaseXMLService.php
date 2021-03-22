<?php

namespace Apps\Dash\Packages\System\Api\Base;

use Apps\Dash\Packages\System\Api\Base\ConfigurationResolver;
use Apps\Dash\Packages\System\Api\Base\BaseFunctions;
use Apps\Dash\Packages\System\Api\Base\Parser\XmlParser;
use Apps\Dash\Packages\System\Api\Base\Types\BaseType;
use Apps\Dash\Packages\System\Api\Base\UriResolver;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

/**
 * The base class for every eBay service class.
 */
abstract class BaseXMLService
{
    /**
     * Helper constent when build requests that contain attachments.
     */
    const CRLF = "\r\n";

    /**
     * HTTP header constant. Tells the server the encoding in which the client desires the response.
     */
    const HDR_RESPONSE_ENCODING = 'Accept-Encoding';

    /**
     * @var \DTS\eBaySDK\ConfigurationResolver Resolves configuration options.
     */
    protected $resolver;

    /**
     * @var array Associative array storing the current configuration option values.
     */
    protected static $config;

    /**
     * @var string The sandbox URL for the service.
     */
    protected $sandboxUrl;

    /**
     * @param array $config Configuration option values.
     */
    public function __construct(array $config) {
        self::$config = $config;

        $this->resolver = new ConfigurationResolver(static::getConfigDefinitions());

        $this->uriResolver = new UriResolver();

        self::$config = $this->resolver->resolve($config);
    }

    /**
     * Method to get the service's configuration.
     *
     * @param string|null $option The name of the option whos value will be returned.
     *
     * @return mixed Returns an associative array of configuration options if no parameters are passed,
     * otherwise returns the value for the specified configuration option.
     */
    public function getConfig($option = null)
    {
        return $option === null
            ? self::$config
            : (isset(self::$config[$option])
                ? self::$config[$option]
                : null);
    }

    /**
     * Set multiple configuration options.
     *
     * @param array $configuration Associative array of configuration options and their values.
     */
    public function setConfig(array $configuration)
    {
        self::$config = BaseFunctions::arrayMergeDeep(
            self::$config,
            $this->resolver->resolveOptions($configuration)
        );
    }

    /**
     * Helper method to return the value of the credentials configuration option.
     *
     * @return \DTS\eBaySDK\Credentials\CredentialsInterface
     */
    public function getCredentials()
    {
        return $this->getConfig('credentials');
    }

    /**
     * Sends an asynchronous API request.
     *
     * @param string $name The name of the operation.
     * @param BaseType $request Request object containing the request information.
     * @param string $responseClass The name of the PHP class that will be created from the XML response.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface A promise that will be resolved with an object created from the XML response.
     */
    protected function callOperationAsync($name, BaseType $request, $responseClass)
    {
        $url = $this->getUrl($name);
        $body = $this->buildRequestBody($request);
        $headers = $this->buildRequestHeaders($name, $request, $body);
        $debug = $this->getConfig('debug');
        $httpHandler = $this->getConfig('httpHandler');
        $httpOptions = $this->getConfig('httpOptions');

        if ($debug !== false) {
            $this->debugRequest($url, $headers, $body);
        }

        $request = new Request('POST', $url, $headers, $body);

        return $httpHandler($request, $httpOptions)->then(
            function (ResponseInterface $res) use ($debug, $responseClass) {
                list($xmlResponse, $attachment) = $this->extractXml($res->getBody()->getContents());

                if ($debug !== false) {
                    $this->debugResponse($xmlResponse);
                }

                $xmlParser = new XmlParser($responseClass);

                $response = $xmlParser->parse($xmlResponse);
                $response->attachment($attachment);

                return $response;
            }
        );
    }

    /**
     * Builds the request body string.
     *
     * @param BaseType $request Request object containing the request information.
     *
     * @return string The request body.
     */
    protected function buildRequestBody(BaseType $request)
    {
        if (!$request->hasAttachment()) {
            return $request->toRequestXml();
        } else {
            return $this->buildXopDocument($request).$this->buildAttachmentBody($request->attachment());
        }
    }

    /**
     * Builds the XOP document part of the request body string.
     *
     * @param BaseType $request Request object containing the request information.
     *
     * @return string The XOP document part of request body.
     */
    protected function buildXopDocument(BaseType $request)
    {
        return sprintf(
            '%s%s%s%s%s',
            '--MIME_boundary'.self::CRLF,
            'Content-Type: application/xop+xml;charset=UTF-8;type="text/xml"'.self::CRLF,
            'Content-Transfer-Encoding: 8bit'.self::CRLF,
            'Content-ID: <request.xml@bazaari.com.au>'.self::CRLF.self::CRLF,
            $request->toRequestXml().self::CRLF
        );
    }

    /**
     * Builds the attachment part of the request body string.
     *
     * @param array $attachment The attachment
     *
     * @return string The attachment part of request body.
     */
    protected function buildAttachmentBody(array $attachment)
    {
        return sprintf(
            '%s%s%s%s%s%s',
            '--MIME_boundary'.self::CRLF,
            'Content-Type: '.$attachment['mimeType'].self::CRLF,
            'Content-Transfer-Encoding: binary'.self::CRLF,
            'Content-ID: <attachment.bin@bazaari.com.au>'.self::CRLF.self::CRLF,
            $attachment['data'].self::CRLF,
            '--MIME_boundary--'
        );
    }

    /**
     * Builds the XML payload part of a multipart/form-data request body.
     *
     * @param BaseType $request Request object containing the request information.
     *
     * @return string The XML payload part of a multipart/form-data request body.
     */
    protected function buildMultipartFormDataXMLPayload(BaseType $request)
    {
        return sprintf(
            '%s%s%s',
            '--boundary'.self::CRLF,
            'Content-Disposition: form-data; name="XML Payload"'.self::CRLF.self::CRLF,
            $request->toRequestXml().self::CRLF
        );
    }


    /**
     * Builds the file part of a multipart/form-data request body.
     *
     * @param string $name
     * @param array $attachment
     *
     * @return string The file part of a multipart/form-data request body.
     */
    protected function buildMultipartFormDataFilePayload($name, $attachment)
    {
        return sprintf(
            '%s%s%s%s%s',
            '--boundary'.self::CRLF,
            'Content-Disposition: form-data; name="'.$name.'"; filename="picture"'.self::CRLF,
            'Content-Type: '.$attachment['mimeType'].self::CRLF.self::CRLF,
            $attachment['data'].self::CRLF,
            '--boundary--'
        );
    }

    /**
     * Helper function that builds the HTTP request headers.
     *
     * @param string $name The name of the operation.
     * @param BaseType $request Request object containing the request information.
     * @param string $body The request body.
     *
     * @return array An associative array of HTTP headers.
     */
    protected function buildRequestHeaders($name, $request, $body)
    {
        $headers = [];

        if ($request->hasAttachment()) {
            $headers['Content-Type'] = 'multipart/related;boundary=MIME_boundary;type="application/xop+xml";start="<request.xml@bazaari.com.au>";start-info="text/xml"';
        } else {
            $headers['Content-Type'] = 'text/xml';
        }

        if ($this->getConfig('compressResponse')) {
            $headers[self::HDR_RESPONSE_ENCODING] = 'application/gzip';
        }

        $headers['Content-Length'] = strlen($body);

        return array_merge($headers, $this->getEbayHeaders($name));
    }

    /**
     * Extracts the XML from the response if it contains an attachment.
     *
     * @param string $response The XML response body.
     *
     * @return array first item is the XML part of response body and the second
     * is an attachement if one was present in the API response.
     */
    protected function extractXml($response)
    {
        /**
         * Ugly way of seeing if an attachment is present in the response.
         */
        if (strpos($response, 'application/xop+xml') === false) {
            return [$response, ['data' => null, 'mimeType' => null]];
        } else {
            return $this->extractXmlAndAttachment($response);
        }
    }

    /**
     * Extracts the XML and the attachment from the response if it contains an attachment.
     *
     * @param string $response The XML response body.
     *
     * @return string The XML part of response body.
     */
    protected function extractXmlAndAttachment($response)
    {
        $attachment = ['data' => null, 'mimeType' => null];

        preg_match('/\r\n/', $response, $matches, PREG_OFFSET_CAPTURE);
        $boundary = substr($response, 0, $matches[0][1]);

        $xmlStartPos = strpos($response, '<?xml ');
        $xmlEndPos = strpos($response, $boundary, $xmlStartPos) - 2;
        $xml = substr($response, $xmlStartPos, $xmlEndPos - $xmlStartPos);

        preg_match('/\r\n\r\n/', $response, $matches, PREG_OFFSET_CAPTURE, $xmlEndPos);
        $attachmentStartPos = $matches[0][1] + 4;
        $attachmentEndPos = strpos($response, $boundary, $attachmentStartPos) - 2;
        $attachment['data'] = substr($response, $attachmentStartPos, $attachmentEndPos - $attachmentStartPos);

        $mimeTypeStartPos = strpos($response, 'Content-Type: ', $xmlEndPos) + 14;
        preg_match('/\r\n/', $response, $matches, PREG_OFFSET_CAPTURE, $mimeTypeStartPos);
        $mimeTypeEndPos = $matches[0][1];
        $attachment['mimeType'] = substr($response, $mimeTypeStartPos, $mimeTypeEndPos - $mimeTypeStartPos);

        return [$xml, $attachment];
    }

    /**
     * Sends a debug string of the request details.
     *
     * @param string $url API endpoint.
     * @param array  $headers Associative array of HTTP headers.
     * @param string $body The XML body of the POST request.
      */
    protected function debugRequest($url, array $headers, $body)
    {
        $str = $url.PHP_EOL;

        $str .= array_reduce(array_keys($headers), function ($str, $key) use ($headers) {
            $str .= $key.': '.$headers[$key].PHP_EOL;
            return $str;
        }, '');

        $str .= $body;

        $this->debug($str);
    }

    /**
     * Sends a debug string of the response details.
     *
     * @param string $body The XML body of the response.
      */
    protected function debugResponse($body)
    {
        $this->debug($body);
    }

    /**
     * Sends a debug string via the attach debugger.
     *
     * @param string $str
     */
    protected function debug($str)
    {
        $debugger = $this->getConfig('debug');
        $debugger($str);
    }
}