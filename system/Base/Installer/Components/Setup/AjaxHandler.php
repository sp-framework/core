<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Components\Setup
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Components\Setup;

/**
 * Handles AJAX requests for password generation, password strength analysis, and progress polling.
 */
class AjaxHandler
{
    /**
     * View rendering instance.
     *
     * @var mixed
     */
    protected mixed $view;

    /**
     * HTTP response service.
     *
     * @var mixed
     */
    protected mixed $response;

    /**
     * Random generator service.
     *
     * @var mixed
     */
    protected mixed $random;

    /**
     * Progress service instance.
     *
     * @var mixed
     */
    protected mixed $progress;

    /**
     * Password checker instance.
     *
     * @var PasswordChecker
     */
    protected PasswordChecker $passwordChecker;

    /**
     * AjaxHandler constructor.
     *
     * @param mixed                $view            View service instance.
     * @param mixed                $response        Response service instance.
     * @param mixed                $random          Random generator instance.
     * @param mixed                $progress        Progress tracker instance.
     * @param PasswordChecker|null $passwordChecker Password checker instance.
     */
    public function __construct(
        mixed $view,
        mixed $response,
        mixed $random,
        mixed $progress = null,
        ?PasswordChecker $passwordChecker = null
    ) {
        $this->view = $view;
        $this->response = $response;
        $this->random = $random;
        $this->progress = $progress;
        $this->passwordChecker = $passwordChecker ?? new PasswordChecker();
    }

    /**
     * Handles AJAX endpoints.
     *
     * @param array<string, mixed> $postData Request POST payload.
     *
     * @return mixed HTTP response content.
     */
    public function handle(array $postData): mixed
    {
        if (isset($postData['checkPwStrength'], $postData['pass'])) {
            $strength = $this->passwordChecker->checkPwStrength((string) $postData['pass']);

            if ($strength !== false) {
                if ($this->view) {
                    $this->view->responseCode = 0;
                    $this->view->responseMessage = 'Ok';
                    $this->view->responseData = ($strength === 0) ? 1 : $strength;
                }
            } else {
                if ($this->view) {
                    $this->view->responseCode = 1;
                    $this->view->responseMessage = 'Error retrieving strength.';
                }
            }
        } elseif (isset($postData['generatePw'])) {
            $newPass = ($this->random && method_exists($this->random, 'base62'))
                ? $this->random->base62(12)
                : bin2hex(random_bytes(6));

            if ($newPass) {
                if ($this->view) {
                    $this->view->responseCode = 0;
                    $this->view->responseMessage = 'Ok';
                    $this->view->responseData = $newPass;
                }
            } else {
                if ($this->view) {
                    $this->view->responseCode = 1;
                    $this->view->responseMessage = 'Error retrieving new pass.';
                }
            }
        } else {
            $sessionId = (string) ($postData['session'] ?? '');
            $progress = ($this->progress && method_exists($this->progress, 'getProgress'))
                ? $this->progress->getProgress($sessionId, true)
                : null;

            if ($progress && $this->view) {
                $this->view->responseCode = 0;
                $this->view->responseData = $progress;
            }
        }

        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setHeader('Cache-Control', 'no-store');

        if ($this->response->isSent() !== true) {
            $params = ($this->view && method_exists($this->view, 'getParamsToView')) ? $this->view->getParamsToView() : [];

            $this->response->setJsonContent($params);

            return $this->response->send();
        }

        return null;
    }
}
