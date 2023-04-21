<?php

namespace System\Base\Providers\WebSocketServiceProvider;

use Psr\Http\Message\RequestInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\CloseResponseTrait;
use Ratchet\Http\OriginCheck;
use Ratchet\MessageComponentInterface;

class WssOriginCheck extends OriginCheck
{
    use CloseResponseTrait;

    private $logger;

    public function __construct(MessageComponentInterface $component, array $allowed = [], $logger, $domains) {
        $this->logger = $logger;

        $allowed = ['localhost'];

        foreach ($domains as $domain) {
            array_push($allowed, $domain['name']);
        }

        parent::__construct($component, $allowed);
    }

    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null) {
        $header = (string)$request->getHeader('Origin')[0];
        $origin = parse_url($header, PHP_URL_HOST) ?: $header;

        if (!in_array($origin, $this->allowedOrigins)) {
            $this->logger->log->debug('Permission denied as origin domain is not in allowed domains. Client IP Address: ' . $request->getHeader('X-Forwarded-For')[0] . '. For domain: ' . $request->getHeader('X-Forwarded-Server')[0]);
            $this->logger->commit();

            return $this->close($conn, 403);
        }

        return $this->_component->onOpen($conn, $request);
    }
}