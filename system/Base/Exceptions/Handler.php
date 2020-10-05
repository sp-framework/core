<?php

namespace System\Base\Exceptions;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;
use System\Base\Providers\ModulesServiceProvider\Views;
use System\Base\Providers\ModulesServiceProvider\Views\ViewsData;
use System\Base\Providers\SessionServiceProvider\Flash;
use System\Base\Providers\SessionServiceProvider\SessionStore;

class Handler
{
    protected $exception;

    protected $session;

    protected $views;

    protected $viewsData;

    protected $response;

    public function __construct(
        Throwable $exception,
        SessionStore $session,
        ResponseInterface $response,
        Views $views,
        Flash $flash,
        ViewsData $viewsData
    ) {
        $this->exception = $exception;

        $this->session = $session;

        $this->response = $response;

        $this->views = $views;

        $this->viewsData = $viewsData;
    }

    public function respond()
    {
        $class = (new ReflectionClass($this->exception))->getShortName();

        if (method_exists($this, $method = "handle{$class}")) {
            return $this->{$method}($this->exception);
        }

        return $this->unhandledException($this->exception);
    }

    protected function handleValidationException(Throwable $e)
    {
        $this->session->set([
            'errors' => $e->getErrors(),
            'old' => $e->getOldInput(),
        ]);

        return redirect($e->getPath());
    }

    protected function handleCsrfTokenException(Throwable $e)
    {
        $this->flash->now('warning', 'Session expired, please login again.');

        return redirect('/auth/login');
    }

    protected function handleNotFoundException(Throwable $e)
    {
        return $this->views->render(
            $this->response,
            'Admin/Default/html/errors/' . $this->exception->getStatusCode() . '.html',
            $this->viewsData,
            'Error'
        );
    }

    // protected function handleLoaderError(Throwable $e)
    // {
    //     return $this->views->render(
    //         $this->response,
    //         'Errors/templateerror.html',
    //         $this->viewsData,
    //         'Error'
    //     );
    // }

    protected function unhandledException(Throwable $e)
    {
        // if ($e->getCode() === 0) {
        //     return $this->views->render(
        //         $this->response,
        //         $this->views->getApplicationName() . '/' .
        //         $this->views->getViewsName() . '/html/errors/404.html',
        //         $this->viewsData,
        //         'Error'
        //     );
        // } else {
            throw $e;
        // }
    }
}
