<?php

namespace System\Base\Providers\AccessServiceProvider\Access\Auth;

use System\Base\BasePackage;

class Password extends BasePackage
{
    protected $passwordPolicyErrors = [];

    public function init()
    {
        return $this;
    }

    public function forgotPassword(array $data)
    {
        $validate = $this->access->auth->validateData($data, 'forgot');

        if ($validate !== true) {
            $this->addResponse($validate, 1);

            return false;
        }

        $account = $this->basepackages->accounts->checkAccount($data['user'], true);

        if ($account) {
            $account['email_new_password'] = '1';
            $account['forgotten_request'] = '1';
            $account['forgotten_request_session_id'] = $this->session->getId();
            $account['forgotten_request_ip'] = $this->request->getClientAddress();
            $account['forgotten_request_agent'] = $this->request->getUserAgent();
            $account['forgotten_request_sent_on'] = time();

            if ($this->basepackages->accounts->updateAccount($account)) {
                $this->logger->log->info('New password requested for account ' . $account['email'] . ' via forgot password. New password was emailed to the account.');
            } else {
                $this->logger->log->critical('Trying to send new password for ' . $account['email'] . ' via forgot password failed.');
            }
        }

        $this->addResponse('Email Sent. Please follow password reset instructions from the email.');

        return true;
    }

    public function resetPassword(array $data, $viaProfile = null)
    {
        if ($data['pass'] === $data['newpass']) {
            $this->addResponse('Old and new password match!', 1);

            return false;
        }
        $validate = $this->access->auth->validateData($data, 'reset');

        if ($validate !== true) {
            $this->addResponse($validate, 1);

            return false;
        }

        if (!$this->access->auth->checkAccount($data, $viaProfile)) {
            return false;
        }

        if (!$this->access->auth->account()['security']['force_pwreset'] && !$this->access->auth->account()) {
            $this->addResponse('Cannot reset password using this tool. Please login and reset using profile.', 1);

            return false;
        }

        if (isset($this->core->core['settings']['security']['twofa']) &&
            $this->core->core['settings']['security']['twofa'] == true &&
            isset($this->core->core['settings']['security']['twofaSettings']['twofaPwresetNeed2fa']) &&
            $this->core->core['settings']['security']['twofaSettings']['twofaPwresetNeed2fa'] == true
        ) {
            if (!$this->access->auth->twoFa->validateTwoFaCode($this->access->auth->getAccountSecurityObject(), $data)) {
                $this->addResponse(
                    $this->access->auth->twoFa->packagesData->responseMessage,
                    $this->access->auth->twoFa->packagesData->responseCode,
                    $this->access->auth->twoFa->packagesData->responseData ?? []
                );

                return false;
            }
        }

        $passwordPolicy = false;
        if (isset($this->core->core['settings']['security']['passwordPolicy']) &&
            $this->core->core['settings']['security']['passwordPolicy'] == 'true'
        ) {
            $passwordPolicy = true;
            $this->passwordPolicyErrors['passwordPolicyBlockPreviousPasswords'] = false;

            if (!$this->checkPwPolicy($data)) {
                $this->addResponse('New password failed password policy. Please try again...', 1, ['passwordPolicyErrors' => $this->passwordPolicyErrors]);

                return false;
            }
        }

        $security = $this->access->auth->getAccountSecurityObject();

        $security->password = $this->secTools->hashPassword($data['newpass'], $this->config->security->passwordWorkFactor);
        $security->force_pwreset = null;
        $security->password_set_on = time();

        if ($passwordPolicy) {
            if ($this->core->core['settings']['security']['passwordPolicySettings']['passwordCheckHibp'] == true) {
                $this->passwordPolicyErrors['passwordCheckHibp'] = false;

                if ($this->basepackages->utils->checkPwHibp($data['newpass']) !== false) {
                    if ($this->basepackages->utils->packagesData->responseData['pwned']) {
                        $this->passwordPolicyErrors['passwordCheckHibp'] = true;

                        $this->addResponse('New password failed password policy. Please try again...', 1, ['passwordPolicyErrors' => $this->passwordPolicyErrors]);

                        return false;
                    }
                }
            }

            $security = $this->setPasswordHistory($data, $security);
        }

        if ($this->basepackages->accounts->addUpdateSecurity($this->access->auth->account()['id'], (array) $security)) {
            $this->logger->log->info('Password reset successful for account ' . $this->access->auth->account()['email'] . ' via pwreset.');


            if ($this->session->redirectUrl && $this->session->redirectUrl !== '/') {
                $this->packagesData->redirectUrl = $this->links->url($this->session->redirectUrl, true);
            } else {
                $this->packagesData->redirectUrl = $this->links->url('home');
            }

            if ($viaProfile) {
                $this->addResponse('Password change successful.');

                //Check if we need to relogin or not.
                if (isset($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyForceReloginAfterPwreset']) &&
                    $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyForceReloginAfterPwreset'] == true
                ) {
                    $this->logout();

                    return true;
                }

                unset($this->packagesData->redirectUrl);
                unset($this->packagesData->responseData);
            } else {
                $this->addResponse('Password changed. Redirecting...');
            }

            return true;
        } else {
            $this->addResponse($this->basepackages->accounts->packagesData->responseMessage, 1);

            return false;
        }
    }

    protected function checkPasswordHistory($data)
    {
        if (isset($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords']) &&
            (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords'] > 0
        ) {
            $security = $this->access->auth->getAccountSecurityObject();

            if ($security->password_history && $security->password_history !== '') {
                if (is_string($security->password_history)) {
                    $security->password_history = $this->helper->decode($security->password_history, true);
                }

                $security->password_history = array_reverse($security->password_history, true);//reverse to check last password first and so on.

                if (count($security->password_history) > 0) {
                    $count = 1;
                    foreach ($security->password_history as $history) {
                        if ($this->secTools->checkPassword($data['newpass'], $history)) {
                            return true;
                        }

                        if ($count === (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords']) {
                            return false;//we only check x amount of password configured, rest we ignore.
                        }

                        $count++;
                    }
                }
            }
        }

        return false;
    }

    protected function setPasswordHistory($data, $security)
    {
        if (isset($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords']) &&
            (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords'] > 0
        ) {
            if ($security->password_history && $security->password_history !== '') {
                if (is_string($security->password_history)) {
                    $security->password_history = $this->helper->decode($security->password_history, true);
                }

                if (count($security->password_history) < (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords']) {
                    $security->password_history[$security->password_set_on] = $security->password;
                } else if (count($security->password_history) === (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords']) {
                    //remove oldest password and add the new one.
                    $security->password_history = array_slice($security->password_history, 1, null, true);
                    $security->password_history[$security->password_set_on] = $security->password;
                } else if (count($security->password_history) > (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords']) {
                    $historyLengthShouldBe = count($security->password_history) - (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyBlockPreviousPasswords'];
                    $security->password_history = array_slice($security->password_history, $historyLengthShouldBe + 1, null, true);//remove more then defined + 1 for the last password.
                    $security->password_history[$security->password_set_on] = $security->password;
                }
            } else {
                $security->password_history[$security->password_set_on] = $security->password;
            }
        }

        return $security;
    }

    protected function checkPwPolicy($data)
    {
        if ($this->checkPasswordHistory($data)) {
            $this->passwordPolicyErrors['passwordPolicyBlockPreviousPasswords'] = true;

            return false;
        }

        if (isset($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyComplexity'])) {
            if ($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyComplexity'] === 'simple') {
                return $this->checkPwPolicySimple($data);
            } else if ($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyComplexity'] === 'complex') {
                return $this->checkPwPolicyComplex($data);
            }
        }

        return true;
    }

    protected function checkPwPolicySimple($data)
    {
        $this->passwordPolicyErrors['passwordPolicySimpleAcceptableLevel'] = false;

        $checkPwStrength = $this->basepackages->utils->checkPwStrength($data['newpass']);

        if (isset($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicySimpleAcceptableLevel']) &&
            (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicySimpleAcceptableLevel'] > 0
        ) {
            if ($checkPwStrength !== false &&
                $checkPwStrength < (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicySimpleAcceptableLevel']
            ) {
                $this->passwordPolicyErrors['passwordPolicySimpleAcceptableLevel'] = true;

                return false;
            }
        }

        return true;
    }

    protected function checkPwPolicyComplex($data)
    {
        //Min & Max Length check
        $this->passwordPolicyErrors['passwordPolicyLengthMin'] = false;
        $this->passwordPolicyErrors['passwordPolicyLengthMax'] = false;

        $this->validation->init();

        $passCheckArr = [];
        $stringLengthArr = [];

        array_push($passCheckArr, 'checkLength');
        $data['checkLength'] = $data['newpass'];
        $stringLengthArr['min']['checkLength'] = (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyLengthMin'];
        $stringLengthArr['max']['checkLength'] = (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyLengthMax'];
        $stringLengthArr['messageMinimum']['checkLength'] = "passwordPolicyLengthMin|Password minimum length requirement failed.";
        $stringLengthArr['messageMaximum']['checkLength'] = "passwordPolicyLengthMax|Password maximum length requirement failed.";
        $stringLengthArr['includedMinimum']['checkLength'] = false;
        $stringLengthArr['includedMaximum']['checkLength'] = false;

        //Uppercase
        if ($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyUppercase'] == true) {
            $regex = '/[' . $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyUppercaseInclude'] . ']/m';
            preg_match_all($regex, $data['newpass'], $uppercaseMatches);

            $this->passwordPolicyErrors['passwordPolicyUppercaseMinCount'] = false;
            $this->passwordPolicyErrors['passwordPolicyUppercaseMaxCount'] = false;

            if (count($uppercaseMatches[0]) > 0) {
                if ($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyUppercaseMinCount'] &&
                    (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyUppercaseMinCount'] > 0
                ) {
                    if (!$this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyUppercaseMaxCount']) {
                        $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyUppercaseMaxCount'] =
                            $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyLengthMax'];
                    }
                    array_push($passCheckArr, 'checkUpperLength');
                    $data['checkUpperLength'] = implode('', $uppercaseMatches[0]);
                    $stringLengthArr['min']['checkUpperLength'] = (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyUppercaseMinCount'];
                    $stringLengthArr['max']['checkUpperLength'] = (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyUppercaseMaxCount'];
                    $stringLengthArr['messageMinimum']['checkUpperLength'] = "passwordPolicyUppercaseMinCount|Password minimum length requirement failed.";
                    $stringLengthArr['messageMaximum']['checkUpperLength'] = "passwordPolicyUppercaseMaxCount|Password maximum length requirement failed.";
                    $stringLengthArr['includedMinimum']['checkUpperLength'] = false;
                    $stringLengthArr['includedMaximum']['checkUpperLength'] = false;
                }

                $this->passwordPolicyErrors['passwordPolicyUppercaseInclude'] = false;

                $password = $data['newpass'];

                foreach ($uppercaseMatches[0] as $match) {
                    $password = str_replace($match, '', $password);
                }

                $regex = '/[A-Z]/m';
                preg_match($regex, $password, $passwordIncludes);

                if (count($passwordIncludes) > 0) {
                    $this->passwordPolicyErrors['passwordPolicyUppercaseInclude'] = true;
                    array_push($passCheckArr, 'checkUpperInclude');
                    $data['checkUpperInclude'] = $passwordIncludes[0];

                    $stringLengthArr['min']['checkUpperInclude'] = 0;
                    $stringLengthArr['max']['checkUpperInclude'] = 0;
                    $stringLengthArr['messageMinimum']['checkUpperInclude'] = "passwordPolicyUppercaseInclude|Password has invalid uppercase character.";
                    $stringLengthArr['messageMaximum']['checkUpperInclude'] = "passwordPolicyUppercaseInclude|Password has invalid uppercase character.";
                    $stringLengthArr['includedMinimum']['checkUpperInclude'] = false;
                    $stringLengthArr['includedMaximum']['checkUpperInclude'] = false;
                }
            }

            if (count($uppercaseMatches[0]) === 0) {
                $this->passwordPolicyErrors['passwordPolicyUppercaseMinCount'] = true;
                $this->passwordPolicyErrors['passwordPolicyUppercaseMaxCount'] = true;
                $this->passwordPolicyErrors['passwordPolicyUppercaseInclude'] = true;
            }
        }

        //Lowercase
        if ($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyLowercase'] == true) {
            $regex = '/[' . $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyLowercaseInclude'] . ']/m';
            preg_match_all($regex, $data['newpass'], $lowercaseMatches);

            $this->passwordPolicyErrors['passwordPolicyLowercaseMinCount'] = false;
            $this->passwordPolicyErrors['passwordPolicyLowercaseMaxCount'] = false;

            if (count($lowercaseMatches[0]) > 0) {
                if ($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyLowercaseMinCount'] &&
                    (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyLowercaseMinCount'] > 0
                ) {
                    if (!$this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyLowercaseMaxCount']) {
                        $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyLowercaseMaxCount'] =
                            $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyLengthMax'];
                    }

                    array_push($passCheckArr, 'checkLowerLength');
                    $data['checkLowerLength'] = implode('', $lowercaseMatches[0]);
                    $stringLengthArr['min']['checkLowerLength'] = (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyLowercaseMinCount'];
                    $stringLengthArr['max']['checkLowerLength'] = (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyLowercaseMaxCount'];
                    $stringLengthArr['messageMinimum']['checkLowerLength'] = "passwordPolicyLowercaseMinCount|Password minimum length requirement failed.";
                    $stringLengthArr['messageMaximum']['checkLowerLength'] = "passwordPolicyLowercaseMaxCount|Password maximum length requirement failed.";
                    $stringLengthArr['includedMinimum']['checkLowerLength'] = false;
                    $stringLengthArr['includedMaximum']['checkLowerLength'] = false;
                }

                $this->passwordPolicyErrors['passwordPolicyLowercaseInclude'] = false;

                $password = $data['newpass'];

                foreach ($lowercaseMatches[0] as $match) {
                    $password = str_replace($match, '', $password);
                }

                $regex = '/[a-z]/m';
                preg_match($regex, $password, $passwordIncludes);

                if (count($passwordIncludes) > 0) {
                    $this->passwordPolicyErrors['passwordPolicyLowercaseInclude'] = true;
                    array_push($passCheckArr, 'checkLowerInclude');
                    $data['checkLowerInclude'] = $passwordIncludes[0];

                    $stringLengthArr['min']['checkLowerInclude'] = 0;
                    $stringLengthArr['max']['checkLowerInclude'] = 0;
                    $stringLengthArr['messageMinimum']['checkLowerInclude'] = "passwordPolicyLowercaseInclude|Password has invalid lowercase character.";
                    $stringLengthArr['messageMaximum']['checkLowerInclude'] = "passwordPolicyLowercaseInclude|Password has invalid lowercase character.";
                    $stringLengthArr['includedMinimum']['checkLowerInclude'] = false;
                    $stringLengthArr['includedMaximum']['checkLowerInclude'] = false;
                }
            }

            if (count($lowercaseMatches[0]) === 0) {
                $this->passwordPolicyErrors['passwordPolicyLowercaseMinCount'] = true;
                $this->passwordPolicyErrors['passwordPolicyLowercaseMaxCount'] = true;
                $this->passwordPolicyErrors['passwordPolicyLowercaseInclude'] = true;
            }
        }

        //Numbers
        if ($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyNumbers'] == true) {
            $regex = '/[' . $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyNumbersInclude'] . ']/m';
            preg_match_all($regex, $data['newpass'], $numbersMatches);

            $this->passwordPolicyErrors['passwordPolicyNumbersMinCount'] = false;
            $this->passwordPolicyErrors['passwordPolicyNumbersMaxCount'] = false;

            if (count($numbersMatches[0]) > 0) {
                if ($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyNumbersMinCount'] &&
                    (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyNumbersMinCount'] > 0
                ) {
                    if (!$this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyNumbersMaxCount']) {
                        $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyNumbersMaxCount'] =
                            $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyLengthMax'];
                    }

                    array_push($passCheckArr, 'checkNumbersLength');
                    $data['checkNumbersLength'] = implode('', $numbersMatches[0]);
                    $stringLengthArr['min']['checkNumbersLength'] = (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyNumbersMinCount'];
                    $stringLengthArr['max']['checkNumbersLength'] = (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyNumbersMaxCount'];
                    $stringLengthArr['messageMinimum']['checkNumbersLength'] = "passwordPolicyNumbersMinCount|Password minimum length requirement failed.";
                    $stringLengthArr['messageMaximum']['checkNumbersLength'] = "passwordPolicyNumbersMaxCount|Password maximum length requirement failed.";
                    $stringLengthArr['includedMinimum']['checkNumbersLength'] = false;
                    $stringLengthArr['includedMaximum']['checkNumbersLength'] = false;
                }

                $this->passwordPolicyErrors['passwordPolicyNumbersInclude'] = false;

                $password = $data['newpass'];

                foreach ($numbersMatches[0] as $match) {
                    $password = str_replace($match, '', $password);
                }

                $regex = '/[0-9]/m';
                preg_match($regex, $password, $passwordIncludes);

                if (count($passwordIncludes) > 0) {
                    $this->passwordPolicyErrors['passwordPolicyNumbersInclude'] = true;
                    array_push($passCheckArr, 'checkNumbersInclude');
                    $data['checkNumbersInclude'] = $passwordIncludes[0];

                    $stringLengthArr['min']['checkNumbersInclude'] = 0;
                    $stringLengthArr['max']['checkNumbersInclude'] = 0;
                    $stringLengthArr['messageMinimum']['checkNumbersInclude'] = "passwordPolicyNumbersInclude|Password has invalid numbers.";
                    $stringLengthArr['messageMaximum']['checkNumbersInclude'] = "passwordPolicyNumbersInclude|Password has invalid numbers.";
                    $stringLengthArr['includedMinimum']['checkNumbersInclude'] = false;
                    $stringLengthArr['includedMaximum']['checkNumbersInclude'] = false;
                }
            }

            if (count($numbersMatches[0]) === 0) {
                $this->passwordPolicyErrors['passwordPolicyNumbersMinCount'] = true;
                $this->passwordPolicyErrors['passwordPolicyNumbersMaxCount'] = true;
                $this->passwordPolicyErrors['passwordPolicyNumbersInclude'] = true;
            }
        }

        //Symbols
        if ($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicySymbols'] == true) {
            $regex = '/[' . preg_quote($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicySymbolsInclude'], '/') . ']/m';
            preg_match_all($regex, $data['newpass'], $symbolsMatches);

            $this->passwordPolicyErrors['passwordPolicySymbolsMinCount'] = false;
            $this->passwordPolicyErrors['passwordPolicySymbolsMaxCount'] = false;

            if (count($symbolsMatches[0]) > 0) {
                if ($this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicySymbolsMinCount'] &&
                    (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicySymbolsMinCount'] > 0
                ) {
                    if (!$this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicySymbolsMaxCount']) {
                        $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicySymbolsMaxCount'] =
                            $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicyLengthMax'];
                    }

                    array_push($passCheckArr, 'checkSymbolsLength');
                    $data['checkSymbolsLength'] = implode('', $symbolsMatches[0]);
                    $stringLengthArr['min']['checkSymbolsLength'] = (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicySymbolsMinCount'];
                    $stringLengthArr['max']['checkSymbolsLength'] = (int) $this->core->core['settings']['security']['passwordPolicySettings']['passwordPolicySymbolsMaxCount'];
                    $stringLengthArr['messageMinimum']['checkSymbolsLength'] = "passwordPolicySymbolsMinCount|Password minimum length requirement failed.";
                    $stringLengthArr['messageMaximum']['checkSymbolsLength'] = "passwordPolicySymbolsMaxCount|Password maximum length requirement failed.";
                    $stringLengthArr['includedMinimum']['checkSymbolsLength'] = false;
                    $stringLengthArr['includedMaximum']['checkSymbolsLength'] = false;
                }

                $this->passwordPolicyErrors['passwordPolicySymbolsInclude'] = false;

                $password = $data['newpass'];

                foreach ($symbolsMatches[0] as $match) {
                    $password = str_replace($match, '', $password);
                }

                $regex = '/[' . preg_quote("!@$%^&*()<>,.?/[]{}-=_+", '/') . ']/m';
                preg_match($regex, $password, $passwordIncludes);

                if (count($passwordIncludes) > 0) {
                    $this->passwordPolicyErrors['passwordPolicySymbolsInclude'] = true;
                    array_push($passCheckArr, 'checkSymbolsInclude');
                    $data['checkSymbolsInclude'] = $passwordIncludes[0];

                    $stringLengthArr['min']['checkSymbolsInclude'] = 0;
                    $stringLengthArr['max']['checkSymbolsInclude'] = 0;
                    $stringLengthArr['messageMinimum']['checkSymbolsInclude'] = "passwordPolicySymbolsInclude|Password has invalid symbols.";
                    $stringLengthArr['messageMaximum']['checkSymbolsInclude'] = "passwordPolicySymbolsInclude|Password has invalid symbols.";
                    $stringLengthArr['includedMinimum']['checkSymbolsInclude'] = false;
                    $stringLengthArr['includedMaximum']['checkSymbolsInclude'] = false;
                }
            }

            if (count($symbolsMatches[0]) === 0) {
                $this->passwordPolicyErrors['passwordPolicySymbolsMinCount'] = true;
                $this->passwordPolicyErrors['passwordPolicySymbolsMaxCount'] = true;
                $this->passwordPolicyErrors['passwordPolicySymbolsInclude'] = true;
            }
        }

        $this->validation->add($passCheckArr, StringLength::class, $stringLengthArr);

        $validated = $this->validation->validate($data)->jsonSerialize();

        if (count($validated) > 0) {
            foreach ($validated as $key => $value) {
                $value['message'] = explode('|', $value['message']);

                $this->passwordPolicyErrors[$value['message'][0]] = true;
            }
        }

        if (in_array(true, $this->passwordPolicyErrors, true)) {
            return false;
        }

        return true;
    }
}