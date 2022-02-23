<?php

namespace System\Base\Providers\SessionServiceProvider;

class SessionTools
{
    protected $session;

    protected $connection;

    protected $localContent;

    protected $sessionPath;

    public function __construct($session, $connection, $localContent)
    {
        $this->session = $session;

        $this->connection = $connection;

        $this->localContent = $localContent;

        $this->sessionPath = 'var/storage/cache/session/';
    }

    public function clearSession(string $sessionId)
    {
        $this->session->destroy($sessionId);
    }

    public function removeSessionKey(string $sessionKey)
    {
        $this->session->remove($sessionKey);
    }

    public function clearSessions()
    {
        //Housecall for cleaning sessions that dont exists in the account['session_id']
    }
}