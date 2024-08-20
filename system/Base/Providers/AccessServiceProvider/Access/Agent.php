<?php

namespace System\Base\Providers\AccessServiceProvider\Access;

use Phalcon\Filter\Validation\Validator\PresenceOf;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsAgents;

class Agent extends BasePackage
{
    public function init()
    {
        return $this;
    }

    public function checkAgent()
    {
        $update = false;
        $clientAddress = $this->request->getClientAddress();
        $userAgent = $this->request->getUserAgent();
        $sessionId = $this->session->getId();
        $agent = [];

        $this->basepackages->accounts->setFFRelations(true);
        $agentStore = $this->ff->store('basepackages_users_accounts_agents');

        $accountsObj = $this->basepackages->accounts->getFirst('id', $this->access->auth->account()['id']);

        if ($this->config->databasetype === 'db') {
            if ($accountsObj->agents) {
                $agentObj =
                    $accountsObj->agents::findFirst(
                        [
                            'conditions'    => 'session_id = :sid: AND account_id = :aid:',
                            'bind'          => [
                                'sid'       => $sessionId,
                                'aid'       => $this->access->auth->account()['id']
                            ]
                        ]
                    );

                if ($agentObj) {
                    $agent = $agentObj->toArray();
                } else {
                    $update = $this->addUpdateAgent($sessionId, $clientAddress, $userAgent);
                }
            } else {
                $update = $this->addUpdateAgent($sessionId, $clientAddress, $userAgent);
            }
        } else {
            $account = $accountsObj->toArray();

            if ($account['agents'] && count($account['agents']) > 0) {
                $agent = $agentStore->findOneBy([['session_id', '=', $sessionId], ['account_id', '=', $this->access->auth->account()['id']]]);

                if (!$agent) {
                    $update = $this->addUpdateAgent($sessionId, $clientAddress, $userAgent);
                }
            } else {
                $update = $this->addUpdateAgent($sessionId, $clientAddress, $userAgent);
            }
        }

        if ($agent && count($agent) > 0) {
            if ($agent['client_address'] === $clientAddress &&
                $agent['user_agent'] === $userAgent &&
                $agent['session_id'] === $sessionId &&
                $agent['account_id'] === $this->access->auth->account()['id'] &&
                $agent['verified'] == '1'
            ) {
                return true;
            } else if ($agent['client_address'] === $clientAddress &&
                $agent['user_agent'] === $userAgent &&
                $agent['session_id'] === $sessionId &&
                $agent['account_id'] === $this->access->auth->account()['id'] &&
                $agent['verified'] == '0'
            ) {
                if (!$this->basepackages->email->setup()) {
                    return true;
                }

                return false;
            } else if ($agent['client_address'] === $clientAddress &&
                $agent['user_agent'] !== $userAgent &&
                $agent['session_id'] === $sessionId &&
                $agent['account_id'] === $this->access->auth->account()['id'] &&
                $agent['verified'] == '1'
            ) {
                // Browser could have updated causing causing agent information change
                // We will remove the agent entry and ask for reauth, just in case.
                if ($this->config->databasetype === 'db') {
                    $agentObj->delete();
                } else {
                    $agentStore->deleteById($agent['id'], false);
                }

                $this->access->auth->account()['force_logout'] = '1';

                $this->basepackages->accounts->update($this->access->auth->account());

                $this->access->auth->logout();

                return false;
            } else if ($agent['session_id'] === $sessionId &&
                       $agent['verified'] == '1'
            ) {
                $this->logger->log->emergency('Same session being used by another browser! Probably session hijack!');

                $this->access->auth->account()['force_logout'] = '1';

                $this->basepackages->accounts->update($this->access->auth->account());

                $this->access->auth->logout();

                return false;
            }
        }

        //If Email is not configured, we cannot send new passcodes.
        //User has remember Identifier set and sessionID has changed.
        if (!$this->basepackages->email->setup() || $update === true) {
            return true;
        }

        return false;
    }

    protected function addUpdateAgent($sessionId, $clientAddress, $userAgent, $oldSessionId = null)
    {
        if ($oldSessionId === null) {
            $oldSessionId = $this->access->auth->getOldSessionId();
        }

        if (!$this->basepackages->email->setup() || $oldSessionId) {
            $verified = 1;
        } else {
            $verified = 0;
        }

        $agentsObj = new BasepackagesUsersAccountsAgents;
        $agentsStore = $this->ff->store($agentsObj->getSource());

        $oldAgent = [];

        if ($oldSessionId) {
            if ($this->config->databasetype === 'db') {
                $oldAgentObj = $agentsObj->findFirstBysession_id($oldSessionId);

                if ($oldAgentObj) {
                    $oldAgent = $oldAgentObj->toArray();
                }
            } else {
                $oldAgent = $agentsStore->findOneBy(['session_id', '=', $oldSessionId]);
            }

            if ($oldAgent && count($oldAgent) > 0) {
                $oldAgent['session_id'] = $sessionId;
                $oldAgent['account_id'] = $this->access->auth->account()['id'];
                $oldAgent['verified'] = $verified;

                try {
                    if ($this->config->databasetype === 'db') {
                        $oldAgentObj->assign($oldAgent);

                        $oldAgentObj->update();
                    } else {
                        $agentsStore->update($oldAgent);
                    }

                    $oldSessionId = null;

                    return true;
                } catch (\Exception $e) {
                    $this->access->auth->logout();

                    throw $e;
                }
            } else {
                return $this->addUpdateAgent($sessionId, $clientAddress, $userAgent, false);
            }
        } else {
            $newAgent =
                [
                    'account_id'        => $this->access->auth->account()['id'],
                    'session_id'        => $sessionId,
                    'client_address'    => $clientAddress,
                    'user_agent'        => $userAgent,
                    'verified'          => $verified
                ];

            try {
                if ($this->config->databasetype === 'db') {
                    $agentsObj->assign($newAgent);

                    $agentsObj->create();
                } else {
                    $agentsStore->insert($newAgent);
                }

                $this->sendVerificationEmail();

                return false;
            } catch (\Exception $e) {
                $this->access->auth->logout();

                throw $e;
            }
        }
    }

    public function sendVerificationEmail()
    {
        if (!$this->access->auth->account()) {
            $this->access->auth->setUserFromSession();
        }
        $this->basepackages->accounts->setFFRelations(true);
        $agentStore = $this->ff->store('basepackages_users_accounts_agents');

        $accountsObj = $this->basepackages->accounts->getFirst('id', $this->access->auth->account()['id']);

        if ($this->config->databasetype === 'db') {
            if ($accountsObj->agents) {
                $agentObj =
                    $accountsObj->agents::findFirst(
                        [
                            'conditions'    => 'session_id = :sid: AND account_id = :aid:',
                            'bind'          => [
                                'sid'       => $this->session->getId(),
                                'aid'       => $this->access->auth->account()['id']
                            ]
                        ]
                    );

                $agent = $agentObj->toArray();
            }
        } else {
            $account = $accountsObj->toArray();

            if ($account['agents'] && count($account['agents']) > 0) {
                $agent = $agentStore->findOneBy([['session_id', '=', $this->session->getId()], ['account_id', '=', $this->access->auth->account()['id']]]);
            }
        }
        if (isset($agent['email_code_sent_on'])) {
            if (time() < $agent['email_code_sent_on'] + ($this->core->core['settings']['security']['agentEmailCodeTimeout'] ?? 60)) {
                $this->addResponse(
                    'Email already sent, please wait...',
                    1,
                    [
                        'code_sent_on' => $agent['email_code_sent_on'],
                        'email_timeout' => $this->core->core['settings']['security']['agentEmailCodeTimeout'] ?? 60
                    ]
                );

                return false;
            }

            $agent['email_code_sent_on'] = time();
        } else {
            $agent['email_code_sent_on'] = time();
        }

        $emailCodeLength = 12;
        if (isset($this->core->core['settings']['security']['agentEmailCodeLength'])) {
            $emailCodeLength = $this->core->core['settings']['security']['agentEmailCodeLength'];
        }
        $code = $this->secTools->random->base62($emailCodeLength);

        $agent['verification_code'] = $this->secTools->hashPassword($code, $this->config->security->passwordWorkFactor);

        if ($this->emailVerificationCode($code)) {
            if ($this->config->databasetype === 'db') {
                $agentObj->assign($agent)->update();
            } else {
                $agentStore->update($agent);
            }

            $this->logger->log
                ->info('New verification code requested for account ' .
                       $this->access->auth->account()['email'] .
                       ' via authentication agent. New code was emailed to the account.'
                );

            $this->addResponse('Email Sent!', 0, ['email_timeout' => $this->core->core['settings']['security']['agentEmailCodeTimeout'] ?? 60]);

            return;
        }

        $this->addResponse('Please contact administrator.', 1);

        $this->packagesData->redirectUrl = $this->links->url('auth');
    }

    protected function emailVerificationCode($verificationCode)
    {
        $emailData['app_id'] = $this->apps->getAppInfo()['id'];
        $emailData['domain_id'] = $this->domains->getDomain()['id'];
        $emailData['status'] = 1;
        $emailData['priority'] = 1;
        $emailData['confidential'] = 1;
        $emailData['to_addresses'] = $this->helper->encode([$this->access->auth->account()['email']]);
        $emailData['subject'] = 'Agent verification code for ' . $this->domains->getDomain()['name'];
        $emailData['body'] = $verificationCode;

        return $this->basepackages->emailqueue->addToQueue($emailData);
    }

    public function verifyVerficationCode(array $data)
    {
        $validate = $this->validateData($data);

        if ($validate !== true) {
            $this->addResponse($validate, 1);

            return false;
        }

        if (!$this->access->auth->account()) {
            $this->access->auth->setUserFromSession();
        }

        $clientAddress = $this->request->getClientAddress();
        $userAgent = $this->request->getUserAgent();
        $sessionId = $this->session->getId();

        $this->basepackages->accounts->setFFRelations(true);
        $agentStore = $this->ff->store('basepackages_users_accounts_agents');

        $accountsObj = $this->basepackages->accounts->getFirst('id', $this->access->auth->account()['id']);

        if ($this->config->databasetype === 'db') {
            if ($accountsObj->agents) {
                $agentObj =
                    $accountsObj->agents::findFirst(
                        [
                            'conditions'    => 'session_id = :sid: AND account_id = :aid:',
                            'bind'          => [
                                'sid'       => $sessionId,
                                'aid'       => $this->access->auth->account()['id']
                            ]
                        ]
                    );
            } else {
                $this->addResponse('Please contact administrator.', 1);

                return;
            }
            $agent = $agentObj->toArray();

        } else {
            $account = $accountsObj->toArray();

            if ($account['agents'] && count($account['agents']) > 0) {
                $agent = $agentStore->findOneBy([['session_id', '=', $sessionId], ['account_id', '=', $this->access->auth->account()['id']]]);
            }
        }

        if (time() > $agent['email_code_sent_on'] + ($this->core->core['settings']['security']['agentEmailCodeTimeout'] ?? 60)) {
            $agent['email_code_sent_on'] = null;
            $agent['verification_code'] = null;

            if ($this->config->databasetype === 'db') {
                $agentObj->assign($agent)->update();
            } else {
                $agentStore->update($agent);
            }

            $this->addResponse('Code Expired! Request new code...', 1);

            return false;
        }

        if ($this->secTools->checkPassword($data['code'], $agent['verification_code'])) {
            if ($agent['client_address'] === $clientAddress &&
                $agent['user_agent'] === $userAgent &&
                $agent['session_id'] === $sessionId &&
                $agent['account_id'] === $this->access->auth->account()['id'] &&
                $agent['verified'] == '0'
            ) {
                if ($this->config->databasetype === 'db') {
                    $agentObj->assign(['verified' => '1', 'verification_code' => null])->update();
                } else {
                    $agent['verified'] = 1;
                    $agent['verification_code'] = null;
                    $agent['email_code_sent_on'] = null;

                    $agentStore->update($agent);
                }

                $this->addResponse('Authenticated. Redirecting...');

                if ($this->session->redirectUrl && $this->session->redirectUrl !== '/') {
                    $this->packagesData->redirectUrl = $this->links->url($this->session->redirectUrl, true);
                } else {
                    $this->packagesData->redirectUrl = $this->links->url('home');
                }
            } else if ($agent['client_address'] === $clientAddress &&
                $agent['user_agent'] === $userAgent &&
                $agent['account_id'] === $this->access->auth->account()['id'] &&
                $agent['session_id'] === $sessionId &&
                $agent['verified'] == '1'
            ) {
                $this->addResponse('Session Incorrect... Loggin out.', 1);

                $this->access->auth->logout();
            }
        } else {
            $this->addResponse('Incorrect verification code. Try again.', 1);
        }
    }

    protected function validateData(array $data)
    {
        $this->validation->add('code', PresenceOf::class, ["message" => "Enter valid code."]);

        $validated = $this->validation->validate($data)->jsonSerialize();

        if (count($validated) > 0) {
            $messages = 'Error: ';

            foreach ($validated as $key => $value) {
                $messages .= $value['message'] . ' ';
            }
            return $messages;
        } else {
            return true;
        }
    }
}