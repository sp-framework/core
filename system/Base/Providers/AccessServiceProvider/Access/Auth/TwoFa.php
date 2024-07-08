<?php

namespace System\Base\Providers\AccessServiceProvider\Access\Auth;

use OTPHP\HOTP;
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;
use System\Base\BasePackage;

class TwoFa extends BasePackage
{
    public function validateTwoFaCode($security, $data, $viaLogin = false)
    {
        if ((isset($this->core->core['settings']['security']) && $this->core->core['settings']['security'] == 'false') ||
             !isset($this->core->core['settings']['security']['twofaSettings']['twofaUsing'])
         ) {
            return true;
        }

        if (!is_array($this->core->core['settings']['security']['twofaSettings']['twofaUsing'])) {
            $this->core->core['settings']['security']['twofaSettings']['twofaUsing'] = $this->helper->decode($this->core->core['settings']['security']['twofaSettings']['twofaUsing'], true);
        }

        if ($data['twofa_using'] === 'email' && in_array('email', $this->core->core['settings']['security']['twofaSettings']['twofaUsing'])) {
            if (time() > $security->twofa_email_code_sent_on + ($this->core->core['settings']['security']['twofaSettings']['twofaEmailCodeTimeout'] ?? 60)) {
                $security->twofa_email_code_sent_on = null;
                $security->twofa_email_code = null;

                if ($this->config->databasetype === 'db') {
                    $security->update();
                } else {
                    $securityStore = $this->ff->store('basepackages_users_accounts_security');

                    $securityStore->update((array) $security);
                }

                $this->addResponse('Code Expired! Request new code...', 1);

                return false;
            }

            if ($this->secTools->checkPassword($data['code'], $security->twofa_email_code)) {
                $this->access->auth->account['security']['twofa_email_code_sent_on'] = null;
                $this->access->auth->account['security']['twofa_email_code'] = null;

                return true;
            }

            if ($viaLogin) {
                $this->addResponse('Error: Username/Password/2FA Code incorrect!', 1);
            } else {
                $this->addResponse('Error: 2FA Code incorrect!', 1);
            }

            return false;
        } else if ($data['twofa_using'] === 'otp' && in_array('otp', $this->core->core['settings']['security']['twofaSettings']['twofaUsing'])) {
            if ($viaLogin &&
                (isset($security->twofa_otp_secret) && !$this->verifyOtp($data['code'], $security->twofa_otp_secret))
            ) {
                $this->addResponse('Error: Username/Password/2FA Code incorrect!', 1);

                return false;
            } else {
                if (!$security->twofa_otp_status) {
                    $this->addResponse('2FA OTP not enabled. Please enable or use email option (if available)', 1);

                    return false;
                }

                if (isset($security->twofa_otp_secret) &&
                    !$this->verifyOtp($data['code'], $security->twofa_otp_secret)
                ) {
                    $this->addResponse('Error: 2FA Code incorrect!', 1);

                    return false;
                }
            }

            return true;
        }
    }

    public function sendTwoFaEmail(array $data)
    {
        $validate = $this->access->auth->validateData($data, 'auth2faEmail');

        if ($validate !== true) {
            $this->addResponse($validate, 1);

            return false;
        }

        if (!$this->access->auth->account) {
            $this->checkAccount($data);
        }

        $codeLength = 12;
        if (isset($this->core->core['settings']['security']['twofaSettings']['twofaEmailCodeLength'])) {
            $codeLength = (int) $this->core->core['settings']['security']['twofaSettings']['twofaEmailCodeLength'];
        }

        $code = $this->secTools->random->base62($codeLength);

        $security = $this->access->auth->getAccountSecurityObject();

        if (isset($security->twofa_email_code_sent_on)) {
            if (time() < $security->twofa_email_code_sent_on + ($this->core->core['settings']['security']['twofaSettings']['twofaEmailCodeTimeout'] ?? 60)
            ) {
                $this->addResponse(
                    'Email already sent, please wait to send another code...',
                    1,
                    [
                        'code_sent_on' => $security->twofa_email_code_sent_on,
                        'email_timeout' => $this->core->core['settings']['security']['twofaSettings']['twofaEmailCodeTimeout'] ?? 60
                    ]
                );

                return false;
            }

            $security->twofa_email_code_sent_on = time();
        } else {
            $security->twofa_email_code_sent_on = time();
        }

        $security->twofa_email_code = $this->secTools->hashPassword($code, $this->config->security->passwordWorkFactor);

        if ($this->config->databasetype === 'db') {
            $security->update();
        } else {
            $securityStore = $this->ff->store('basepackages_users_accounts_security');

            $securityStore->update((array) $security);
        }

        if ($this->emailTwoFaEmailCode($code)) {
            $this->logger->log
                ->info('New 2FA code requested for account ' .
                       $this->access->auth->account['email'] .
                       ' via authentication. New code was emailed to the account.'
                );

            $this->addResponse(
                'Email Sent!',
                0,
                ['email_timeout' => $this->core->core['settings']['security']['twofaSettings']['twofaEmailCodeTimeout'] ?? 60]
            );

            return;
        }

        $this->addResponse('Please contact administrator.', 1);

        $this->packagesData->redirectUrl = $this->links->url('auth');
    }

    protected function emailTwoFaEmailCode($twofaCode)
    {
        $emailData['app_id'] = $this->app['id'];
        $emailData['domain_id'] = $this->domains->getDomain()['id'];
        $emailData['status'] = 1;
        $emailData['priority'] = 1;
        $emailData['confidential'] = 1;
        $emailData['to_addresses'] = $this->helper->encode([$this->access->auth->account['email']]);
        $emailData['subject'] = '2FA code for ' . $this->domains->getDomain()['name'];
        $emailData['body'] = $twofaCode;

        return $this->basepackages->emailQueue->addToQueue($emailData);
    }

    public function canUse2fa()
    {
        $canUse2fa = [];

        if (isset($this->core->core['settings']['security']['twofa']) &&
            $this->core->core['settings']['security']['twofa'] == 'true'
        ) {
            if (isset($this->core->core['settings']['security']['twofaSettings']['twofaUsing'])) {
                if (is_string($this->core->core['settings']['security']['twofaSettings']['twofaUsing']) &&
                    $this->core->core['settings']['security']['twofaSettings']['twofaUsing'] !== ''
                ) {
                    $this->core->core['settings']['security']['twofaSettings']['twofaUsing'] =
                        $this->helper->decode($this->core->core['settings']['security']['twofaSettings']['twofaUsing']);

                    if (is_array($this->core->core['settings']['security']['twofaSettings']['twofaUsing']) &&
                        count($this->core->core['settings']['security']['twofaSettings']['twofaUsing']) > 0 &&
                        in_array('otp', $this->core->core['settings']['security']['twofaSettings']['twofaUsing'])
                    ) {
                        array_push($canUse2fa, 'otp');
                    }
                }
            }
        }
        if ($this->basepackages->email->setup()) {
            array_push($canUse2fa, 'email');
        }

        return $canUse2fa;
    }

    protected function initOtp($secret, $verify = false)
    {
        if ((!isset($this->core->core['settings']['security']['twofa']) ||
             !isset($this->core->core['settings']['security']['twofaSettings']['twofaOtp'])) ||
             (isset($this->core->core['settings']['security']['twofa']) &&
              $this->core->core['settings']['security']['twofa'] == 'false'
             )
        ) {
            throw new \Exception('OTP is not configured. Please configure it via Core settings.');
        }

        if ($this->core->core['settings']['security']['twofaSettings']['twofaOtp'] === 'totp') {
            $this->otp = TOTP::create($secret);
        } else if ($this->core->core['settings']['security']['twofaSettings']['twofaOtp'] === 'hotp') {
            $this->otp = HOTP::create($secret);
        }

        $this->otp->setDigest('sha256');

        if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpAlgorithm'])) {
            $this->otp->setDigest($this->core->core['settings']['security']['twofaSettings']['twofaOtpAlgorithm']);
        }

        $period = 30;

        if ($this->core->core['settings']['security']['twofaSettings']['twofaOtp'] === 'totp') {
            $period = 30;

            if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpTotpTimeout'])) {
                $period =
                    $this->core->core['settings']['security']['twofaSettings']['twofaOtpTotpTimeout'] >= 30 &&
                    $this->core->core['settings']['security']['twofaSettings']['twofaOtpTotpTimeout'] <= 300
                    ?
                    $this->core->core['settings']['security']['twofaSettings']['twofaOtpTotpTimeout']
                    :
                    30;
            }

            $this->otp->setPeriod($period);
        } else if ($this->core->core['settings']['security']['twofaSettings']['twofaOtp'] === 'hotp') {
            $this->otp->setCounter(0);

            if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpHotpCounter'])) {
                $this->otp->setCounter($this->core->core['settings']['security']['twofaSettings']['twofaOtpHotpCounter']);
            }

            if ($verify) {
                $security = $this->getAccountSecurityObject();
                if ($security->twofa_otp_hotp_counter !== null) {
                    $this->otp->setCounter($security->twofa_otp_hotp_counter);
                }
            }
        }

        if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpLabel'])) {
            $this->otp->setLabel($this->core->core['settings']['security']['twofaSettings']['twofaOtpLabel']);
        }

        if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpIssuer'])) {
            $this->otp->setIssuer($this->core->core['settings']['security']['twofaSettings']['twofaOtpIssuer']);
        }

        if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpDigitsLength'])) {
            $this->otp->setDigits($this->core->core['settings']['security']['twofaSettings']['twofaOtpDigitsLength']);
        }

        if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpLogo']) &&
            $this->core->core['settings']['security']['twofaSettings']['twofaOtpLogo'] !== ''
        ) {
            $logoLink = $this->core->get2faLogoLink($this->core->core['settings']['security']['twofaSettings']['twofaOtpLogo'], 80);

            if ($logoLink) {
                $this->otp->setParameter('image', $logoLink);
            }
        }
    }

    public function enableTwoFaOtp(array $data = null)
    {
        if ($data) {
            $validate = $this->validateData($data, 'auth');

            if ($validate !== true) {
                $this->addResponse($validate, 1);

                return false;
            }
        }

        if ($data && !$this->checkAccount($data)) {
            $this->access->ipFilter->bumpFilterHitCounter(null, false, true);

            return false;
        }

        $security = $this->getAccountSecurityObject();

        if ($security->twofa_otp_status && $security->twofa_otp_status == '1') {
            $this->addResponse('2FA already enabled! Contact Administrator.', 1);

            return false;
        }

        try {
            $this->initOtp($this->updateTwoFaOtpSecret());

            $this->packagesData->provisionUrl = $this->otp->getProvisioningUri();

            $this->packagesData->qrcode =
                $this->basepackages->qrcodes->generateQrCode(
                    $this->otp->getProvisioningUri(),
                    [
                        'showLabel'     => 'true',
                        'labelFontSize' => '8',
                        'labelText'     => $this->otp->getSecret(),
                        'labelColor'    =>
                        [
                            'r'         => '0',
                            'g'         => '0',
                            'b'         => '0',
                            'a'         => '0'
                        ]
                    ]
                );

            $this->packagesData->secret = $this->otp->getSecret();

            $this->addResponse('Generated 2FA Code');

            $security = $this->getAccountSecurityObject();

            $security = $this->updateTwoFaOtpHotpCounter($security);

            if ($this->config->databasetype === 'db') {
                $security->update();
            } else {
                $securityStore = $this->ff->store('basepackages_users_accounts_security');

                $securityStore->update((array) $security);
            }

            return true;
        } catch (\Exception $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }
    }

    protected function updateTwoFaOtpHotpCounter($security)
    {
        //Update user counter
        if ($this->core->core['settings']['security']['twofaSettings']['twofaOtp'] === 'hotp') {
            if ($security->twofa_otp_hotp_counter !== null) {
                $security->twofa_otp_hotp_counter = $this->otp->getCounter();
            } else {
                if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpHotpCounter'])) {
                    $security->twofa_otp_hotp_counter = $this->otp->getCounter();
                } else {
                    $security->twofa_otp_hotp_counter = 0;
                }
            }
        }

        return $security;
    }

    public function verifyTwoFaOtp(array $data)
    {
        if (isset($data['user']) && isset($data['pass'])) {
            $validate = $this->validateData($data, 'auth');

            if ($validate !== true) {
                $this->addResponse($validate, 1);

                return false;
            }
        }

        if (isset($data['user']) && isset($data['pass']) && !$this->checkAccount($data)) {
            $this->access->ipFilter->bumpFilterHitCounter(null, false, true);

            return false;
        }

        $security = $this->getAccountSecurityObject();

        if ($security->twofa_otp_status && $security->twofa_otp_status == '1') {
            $this->addResponse('2FA already enabled! Contact Administrator.', 1);

            return false;
        }

        if ($this->verifyOtp($data['code'], $security->twofa_otp_secret)) {
            $security->twofa_otp_status = '1';

            $security = $this->updateTwoFaOtpHotpCounter($security);

            if ($this->config->databasetype === 'db') {
                $security->update();
            } else {
                $securityStore = $this->ff->store('basepackages_users_accounts_security');

                $securityStore->update((array) $security);
            }

            return true;
        }
    }

    public function disableTwoFaOtp(int $code)
    {
        $security = $this->getAccountSecurityObject();

        try {
            $this->initOtp($security->twofa_otp_secret, true);
        } catch (\Exception $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        if ($this->otp->verify($code, null, 5)) {
            $security->twofa_otp_status = null;
            $security->twofa_otp_secret = null;
            $security->twofa_otp_hotp_counter = null;

            if ($this->config->databasetype === 'db') {
                $security->update();
            } else {
                $securityStore = $this->ff->store('basepackages_users_accounts_security');

                $securityStore->update((array) $security);
            }

            $this->addResponse('2FA disabled.');
        } else {
            $this->addResponse('2FA disable failed.', 1);
        }
    }

    public function verifyOtp($code, $secret)
    {
        try {
            $this->initOtp($secret, true);
        } catch (\Exception $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        if ($this->otp->verify($code, null, 5)) {
            $this->addResponse('2FA verification success.');

            return true;
        } else {
            $this->addResponse('2FA verification failed.', 1);

            return false;
        }
    }

    protected function updateTwoFaOtpSecret()
    {
        $secretSize = 16;
        if (isset($this->core->core['settings']['security']['twofaSettings']['twofaOtpSecretSize'])) {
            $secretSize = $this->core->core['settings']['security']['twofaSettings']['twofaOtpSecretSize'];
        }
        $twoFaSecret = trim(Base32::encodeUpper(random_bytes($secretSize)), '=');

        $security = $this->getAccountSecurityObject();

        $security->twofa_otp_secret = $twoFaSecret;

        if ($this->config->databasetype === 'db') {
            $security->update();
        } else {
            $securityStore = $this->ff->store('basepackages_users_accounts_security');

            $securityStore->update((array) $security);
        }

        return $twoFaSecret;
    }
}