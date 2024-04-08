<?php

namespace System\Base\Providers\AppsServiceProvider;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiClients;
use System\Base\Providers\AppsServiceProvider\Exceptions\AppNotFoundException;
use System\Base\Providers\AppsServiceProvider\IpFilter;
use System\Base\Providers\AppsServiceProvider\Model\ServiceProviderApps;
use System\Base\Providers\AppsServiceProvider\Types;

class Apps extends BasePackage
{
	protected $modelToUse = ServiceProviderApps::class;

	protected $packageName = 'apps';

	protected $packageNameS = 'app';

	public $apps;

	public $types;

	public $ipFilter;

	protected $reservedRoutes;

	protected $appInfo = null;

	public function init(bool $resetCache = false)
	{
		$this->types = (new Types)->init($resetCache);

		$this->reservedRoutes = $this->getReservedRoutes();

		$this->getAll($resetCache);

		$this->app = $this->getAppInfo();

		$this->ipFilter = (new IpFilter())->init($this, $this->app);

		return $this;
	}

	public function getAppInfo($route = null)
	{
		if (PHP_SAPI === 'cli') {
			$this->appInfo = $this->getAppByRoute($route);

			$this->ipFilter = (new IpFilter())->init($this, $this->appInfo);
		}

		if (isset($this->appInfo)) {
			return $this->appInfo;
		} else {
			if ($this->checkAppRegistration($this->getAppRoute())) {
				return $this->appInfo;
			}
		}

		throw new AppNotFoundException();
	}

	protected function getAppRoute()
	{
		$uri = $this->request->getURI();

		$uri = explode('/q/', $uri);

		$domain = $this->domains->getDomain();

		if ($uri[0] === '/') {
			if ($domain) {
				return $this->getAppById($domain['default_app_id'])['route'];
			}
			return null;
		} else {
			if (isset($domain['exclusive_to_default_app']) &&
				$domain['exclusive_to_default_app'] == 1
			) {
				return $this->getAppById($domain['default_app_id'])['route'];
			}

			$uri = explode('/', $uri[0]);

			if ($uri[1] === 'api') {
				return $uri[2];
			}

			return $uri[1];
		}
	}

	protected function checkAppRegistration($route)
	{
		$app = $this->getAppByRoute($route);

		if ($app) {
			$this->appInfo = $app;

			return true;
		} else {
			return false;
		}
	}

	public function getAppById($id)
	{
		foreach($this->apps as $app) {
			if ($app['id'] == $id) {
				return $app;
			}
		}

		return false;
	}

	public function getAppByName($name)
	{
		foreach($this->apps as $app) {
			if (strtolower($app['name']) === strtolower($name)) {
				return $app;
			}
		}

		return false;
	}

	public function getAppByRoute($route)
	{
		if (!$route) {
			return false;
		}

		foreach($this->apps as $app) {
			if (strtolower($app['route']) == strtolower($route)) {
				return $app;
			}
		}

		return false;
	}

	/**
	 * @notification(name=add)
	 * notification_allowed_methods(email, sms)//Example
	 * @notification_allowed_methods(email, sms)
	 */
	public function addApp(array $data)
	{
		if (!$this->checkType($data)) {
			return;
		}

		if ($this->getAppByRoute($data['route'])) {
			$this->addResponse('App route ' . strtolower($data['route']) . ' is used by another app. Please use different route.', 1, []);

			return false;
		}

		$data['default_component'] = 0;
		$data['errors_component'] = 0;
		$data['ip_filter_default_action'] = 0;
		$data['can_login_role_ids'] = $this->helper->encode(['1']);
		$data['acceptable_usernames'] = $this->helper->encode(['email']);

		if (isset($data['default_dashboard']) && $data['default_dashboard']) {
			$data['settings']['defaultDashboard'] = $data['default_dashboard'];
		}

		if ($this->add($data)) {

			$this->addActivityLog($data);

			$this->addResponse('Added ' . $data['name'] . ' app', 0, null, true);

			$this->addToNotification('add', 'Added new app ' . $data['name']);
		} else {
			$this->addResponse('Error adding new app.', 1, []);
		}
	}

	/**
	 * @notification(name=update)
	 * notification_allowed_methods(email, sms)//Example
	 * @notification_allowed_methods(email, sms)
	 */
	public function updateApp(array $data)
	{
		if (isset($data['domains'])) {//Coming via app wizard.
			$domains = $this->helper->decode($data['domains'], true);

			if (isset($domains['data'])) {
				$domains = $domains['data'];
			}

			foreach ($domains as $domain) {
				$domain = $this->domains->getDomainById($domain);

				if (is_string($domain['apps'])) {
					$domain['apps'] = $this->helper->decode($domain['apps'], true);
				}

				$domain['apps'][$data['id']]['allowed'] = true;
				$domain['apps'][$data['id']]['view'] = $data['view'];
				$domain['apps'][$data['id']]['email_service'] = $data['email'];
				$domain['apps'][$data['id']]['publicStorage'] = $data['public'];
				$domain['apps'][$data['id']]['privateStorage'] = $data['private'];

				$this->domains->updateDomain($domain);

				//add new viewsettings
				$viewSettingsData = [];
				$viewSettingsData['view_id'] = $data['view'];
				$viewSettingsData['domain_id'] = $domain['id'];
				$viewSettingsData['app_id'] = $data['id'];
				$viewSettingsData['via_app'] = true;

				$this->modules->viewsSettings->addViewsSettings($viewSettingsData);
			}

			return true;
		}

		$app = $this->getById($data['id']);

		if (isset($data['default_dashboard']) && $data['default_dashboard']) {
			$data['settings']['defaultDashboard'] = $data['default_dashboard'];
		}

		$app = array_merge($app, $data);

		if (isset($app['reset_structure']) && $app['reset_structure'] == '1') {
			$app['menu_structure'] = null;
		}

		if (isset($app['can_login_role_ids'])) {
			if (is_string($app['can_login_role_ids'])) {
				$app['can_login_role_ids'] = $this->helper->decode($app['can_login_role_ids'], true);
			}

			if (isset($app['can_login_role_ids']['data'])) {
				$app['can_login_role_ids'] = $this->helper->encode($app['can_login_role_ids']['data']);
			} else {
				$app['can_login_role_ids'] = $this->helper->encode($app['can_login_role_ids']);
			}
		}

		if (isset($app['views']) && $app['views'] !== '') {
			$views = $this->helper->decode($app['views'], true);

			foreach ($views as $viewId => $view) {
				if ($view === false) {
					$viewInfo = $this->modules->views->getViewById($viewId);

					$domainCheckAppsSettings = $this->domains->checkAppsSettings($app['id'], 'view', $viewId);

					if ($domainCheckAppsSettings) {
						$this->addResponse('View ' . $viewInfo['display_name'] . ' is being used by Domain ' . $domainCheckAppsSettings['name'], 1);

						return false;
					}
				}
			}

			$this->modules->views->updateViews($app);
		}

		if (isset($app['components'])) {
			$this->modules->components->updateComponents($app);
		}

		if (isset($app['menus'])) {
			$this->basepackages->menus->updateMenus($app);
		}

		if (isset($app['middlewares'])) {
			$this->modules->middlewares->updateMiddlewares($app);
		}

		if ($app['acceptable_usernames']) {
			if (is_string($app['acceptable_usernames'])) {
				$app['acceptable_usernames'] = $this->helper->decode($app['acceptable_usernames'], true);
			}

			if (isset($app['acceptable_usernames']['data'])) {
				$app['acceptable_usernames'] = $this->helper->encode($app['acceptable_usernames']['data']);
			} else {
				$app['acceptable_usernames'] = $this->helper->encode($app['acceptable_usernames']);
			}
		} else {
			$app['acceptable_usernames'] = $this->helper->encode(['email']);
		}

		if ($this->update($app)) {
			$this->addActivityLog($data, $app);

			$this->addToNotification('update', 'Updated app ' . $app['name']);

			$this->addResponse('Updated ' . $app['name'] . ' app');
		} else {
			$this->addResponse('Error updating app.', 1);
		}
	}

	protected function checkType($data)
	{
		$typesArr = $this->types->types;

		foreach ($typesArr as $key => $type) {
			if (strtolower($data['route']) === $type['app_type'] ||
				in_array(strtolower($data['route']), $this->reservedRoutes)
			) {
				$this->addResponse('App route ' . strtolower($data['route']) . ' is reserved. Please use different route.', 1, []);

				return false;
			}
		}

		return true;
	}

	/**
	 * @notification(name=remove)
	 * notification_allowed_methods(email, sms)//Example
	 * @notification_allowed_methods(email, sms)
	 */
	public function removeApp(array $data)
	{
		if ($data['id'] == 1) {
			$this->addResponse('Cannot remove Admin App. Error removing app.', 1);

			return false;
		}

		$app = $this->getById($data['id']);

		//Check relations before removing.
		if ($this->remove($data['id'])) {

			$this->domains->removeAppFromApps($data['id']);

			$this->addToNotification('remove', 'Removed app ' . $app['name']);

			$this->addResponse('Removed App ' . $app['name']);
		} else {
			$this->addResponse('Error removing app.', 1);
		}
	}

	protected function getReservedRoutes()
	{
		return
			[
				'core', 'dash', 'api', 'pusher', 'messenger'
			];
	}

	public function getAcceptableUsernamesForAppId()
	{
		return
			[
				'email'   =>
					[
						'type'      	=> 'email',
						'name'          => 'Email'
					],
				'username'    =>
					[
						'type'      	=> 'username',
						'name'          => 'Username',
					]
			];
	}

	public function getAvailableAPIGrantTypes()
	{
		return
			[
				'password'    =>
					[
						'id'        	=> 'password',
						'name'          => 'Password Grant',
					],
				'client_credentials'   =>
					[
						'id'        	=> 'client_credentials',
						'name'          => 'Client Credential Grant'
					],
				// 'dcg'   =>//Implemented in OAuth ver 9.x
				// 	[
				// 		'id'        	=> 'dcg',
				// 		'name'          => 'Device Code Grant'
				// 	],
				'code'    =>
					[
						'id'        	=> 'code',
						'name'          => 'Authorization Code Grant',
					]
			];
	}

	public function getOpensslAlgorithms()
	{
		$algos = [];

		foreach (openssl_get_md_methods() as $algo) {
			$algos[$algo]['id'] = $algo;
			$algos[$algo]['name'] = strtoupper($algo);
		}

		return $algos;
	}

	public function getOpensslKeyBits()
	{
		$bits = ['2048', '4096'];

		$keyBits = [];

		foreach ($bits as $bit) {
			$keyBits[$bit]['id'] = $bit;
			$keyBits[$bit]['name'] = $bit;
		}

		return $keyBits;
	}

	public function getAPIKeysParams()
	{
		$params = '1024|sha256|8';

		try {
			$params = $this->localContent->read('system/.api/' . $this->app['id'] . '/.params');
		} catch (FilesystemException | UnableToReadFile $exception) {
			//Do nothing.
		}

		$params = explode('|', $params);

		return $params;
	}

	public function generatePKIKeys($data = [])
	{
		if (!$this->checkAPIPath()) {
			$this->addResponse('Not able to create api directory, contact administrator.', 1);

			return false;
		}

		if (!extension_loaded('openssl')) {
			$this->addResponse('Extension openssl not loaded.', 1);

			return false;
		}

		if ($this->app['api_private_key'] == true &&
			!isset($data['force_regenerate'])
		) {
			$this->addResponse('Key already exists, set argument regenerate to force regenerate key.', 1);

			return false;
		}

		try {
			$key = '';
			$privateKey = '';
			$passphrase = $this->random->base58(32);
			$encryptionKeySize = isset($data['encryption_key_size']) ? (int) $data['encryption_key_size'] : 32;
			$encryptionKey = $this->random->base58();

			$config = [
				"private_key_bits" => isset($data['pki_key_size']) ? (int) $data['pki_key_size'] : 2048,
				"digest_alg" => isset($data['pki_algorithm']) ? $data['pki_algorithm'] : 'sha256'
			];

			$pki = openssl_pkey_new($config);
			openssl_pkey_export($pki, $privateKey, $passphrase);
			$publicKey = openssl_pkey_get_details($pki)["key"];

			$key = trim($privateKey . $publicKey);

			try {
				$this->localContent->write(
					'system/.api/' . $this->app['id'] . '/.params',
					$config['private_key_bits'] . '|' . $config['digest_alg'] . '|' . $encryptionKeySize,
					['visibility' => 'private']);
				$this->localContent->write('system/.api/' . $this->app['id'] . '/.pki', $key, ['visibility' => 'private']);
				$this->localContent->write('system/.api/' . $this->app['id'] . '/.private', $privateKey, ['visibility' => 'private']);
				$this->localContent->write('system/.api/' . $this->app['id'] . '/.public', $publicKey, ['visibility' => 'private']);
				$this->localContent->write('system/.api/' . $this->app['id'] . '/.enc', $this->secTools->encryptBase64($encryptionKey), ['visibility' => 'private']);
			} catch (FilesystemException | UnableToWriteFile $exception) {
				throw $exception;
			}

			$this->app['api_private_key_passphrase'] = $this->secTools->encryptBase64($passphrase);
			$this->app['api_private_key'] = '1';
			$this->app['api_private_key_location'] = base_path('system/.api/' . $this->app['id'] . '/.pki');

			$this->updateApp($this->app);

			$this->addResponse('Generated keys!');
		} catch (\Exception $e) {
			$this->addResponse($e->getMessage(), 1);
		}
	}

	protected function checkAPIPath()
	{
		if (!is_dir(base_path('system/.api/' . $this->app['id'] . '/'))) {
			if (!mkdir(base_path('system/.api/' . $this->app['id'] . '/'), 0777, true)) {
				return false;
			}
		}

		return true;
	}

	public function getAPIKeys()
	{
		$keys = [];

		try {
			$keys['enc'] = $this->secTools->decryptBase64($this->localContent->read('system/.api/' . $this->app['id'] . '/.enc'));
			$keys['public'] = $this->localContent->read('system/.api/' . $this->app['id'] . '/.public');
			$keys['public_location'] = base_path('system/.api/' . $this->app['id'] . '/.public');
			$keys['private'] = $this->localContent->read('system/.api/' . $this->app['id'] . '/.private');
			$keys['private_location'] = base_path('system/.api/' . $this->app['id'] . '/.private');
			$keys['pki'] = $this->localContent->read('system/.api/' . $this->app['id'] . '/.pki');
			$keys['pki_location'] = base_path('system/.api/' . $this->app['id'] . '/.pki');
			$keys['pki_passphrase'] = $this->secTools->decryptBase64($this->app['api_private_key_passphrase']);
		} catch (FilesystemException | UnableToReadFile $exception) {
			throw $exception;
		}

		return $keys;
	}

	public function generateClientKeys()
	{
		$newClient['app_id'] = $this->app['id'];
		$newClient['domain_id'] = $this->domains->domain['id'];
		$newClient['account_id'] = $this->auth->account()['id'];
		$newClient['name'] = $newClient['app_id'] . '_' . $newClient['domain_id'] . '_' . $newClient['account_id'];
		$newClient['client_id'] = $this->random->base58(isset($this->app['api_client_id_length']) ? $this->app['api_client_id_length'] : 8);
		$client_secret = $this->random->base58(isset($this->app['api_client_secret_length']) ? $this->app['api_client_secret_length'] : 32);
		$newClient['client_secret'] = $this->secTools->hashPassword($client_secret);
		$newClient['redirect_uri'] = 'https://';
		$newClient['grant_types'] = '';
		$newClient['scope'] = '*';
		// $newClient['created_at'] = time();
		// $newClient['updated_at'] = time();

		try {
			$clientsObject = new ServiceProviderApiClients;
			$clientsStore = $this->ff->store($clientsObject->getSource());

			if ($this->config->databasetype === 'db') {
				$oldClientsObj = $clientsObject->findFirstByName($newClient['name']);

				if ($oldClientsObj) {
					$oldClient = $oldClientsObj->toArray();
				}
			} else {
				$oldClient = $clientsStore->findOneBy(['name', '=', $newClient['name']]);
			}

			if (isset($oldClient)) {
				$newClient = array_merge($oldClient, $newClient);

				if ($this->config->databasetype === 'db') {
					$oldClientsObj->assign($newClient);

					$oldClientsObj->update();
				} else {
					$clientsStore->update($newClient);
				}

				$this->addResponse('Keys regenerated successfully.', 0, ['client_id' => $newClient['client_id'], 'client_secret' => $client_secret]);
			} else {
				if ($this->config->databasetype === 'db') {
					$clientsObject->assign($newClient);

					$clientsObject->create();
				} else {
					$clientsStore->insert($newClient);
				}

				$this->addResponse('Keys generated successfully.', 0, ['client_id' => $newClient['client_id'], 'client_secret' => $client_secret]);
			}
		} catch (\Exception $e) {
			$this->addResponse('Error generating/updating keys. Please contact administrator.', 1);
		}
	}
}