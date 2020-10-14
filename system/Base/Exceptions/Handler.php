<?php

namespace System\Base\Exceptions;

use Phalcon\Flash\Session;
use Phalcon\Http\Response;
use Phalcon\Mvc\View;
use Phalcon\Session\Manager;
use ReflectionClass;

class Handler
{
    protected $exception;

    protected $session;

    protected $response;

    protected $view;

    protected $flash;

    public function __construct(
        \Exception $exception,
        Manager $session,
        Response $response,
        View $view,
        Session $flash
    ) {
        $this->exception = $exception;

        $this->session = $session;

        $this->response = $response;

        $this->views = $view;

        $this->flash = $flash;
    }

    public function respond()
    {
        $class = (new ReflectionClass($this->exception))->getShortName();

        if (method_exists($this, $method = "handle{$class}")) {
            return $this->{$method}($this->exception);
        }

        return $this->unhandledException($this->exception);
    }

    protected function handleValidationException(Exception $e)
    {
        $this->session->set([
            'errors' => $e->getErrors(),
            'old' => $e->getOldInput(),
        ]);

        return redirect($e->getPath());
    }

    protected function handleCsrfTokenException(\Exception $e)
    {
        $this->flash->now('warning', 'Session expired, please login again.');

        return redirect('/auth/login');
    }

    protected function handleNotFoundException(\Exception $e)
    {
        return $this->views->render(
            $this->response,
            'Admin/Default/html/errors/' . $this->exception->getStatusCode() . '.html',
            $this->viewsData,
            'Error'
        );
    }

    // protected function handleLoaderError(\Exception $e)
    // {
    //     return $this->views->render(
    //         $this->response,
    //         'Errors/templateerror.html',
    //         $this->viewsData,
    //         'Error'
    //     );
    // }

    protected function unhandledException(\Exception $e)
    {
        throw $e;
    }
}
