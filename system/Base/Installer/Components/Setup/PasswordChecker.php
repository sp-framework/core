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

use ZxcvbnPhp\Zxcvbn;

/**
 * Evaluates password strength lazily using Zxcvbn.
 */
class PasswordChecker
{
    /**
     * Lazily instantiated Zxcvbn instance.
     *
     * @var Zxcvbn|null
     */
    protected ?Zxcvbn $tool = null;

    /**
     * Checks password strength.
     *
     * @param string $pass Password string.
     *
     * @return int|false Score from 0 to 4, or false on error.
     */
    public function checkPwStrength(string $pass): int|false
    {
        if (!class_exists(Zxcvbn::class)) {
            return 3;
        }

        if ($this->tool === null) {
            $this->tool = new Zxcvbn();
        }

        $result = $this->tool->passwordStrength($pass);

        if (is_array($result) && isset($result['score'])) {
            return (int) $result['score'];
        }

        return false;
    }
}
