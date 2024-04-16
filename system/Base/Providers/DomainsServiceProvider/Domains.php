<?php

namespace System\Base\Providers\DomainsServiceProvider;

use BlueLibraries\Dns\DnsRecords;
use BlueLibraries\Dns\Handlers\Types\TCP;
use BlueLibraries\Dns\Records\RecordTypes;
use Phalcon\Validation\Validator\Url;
use System\Base\BasePackage;
use System\Base\Providers\DomainsServiceProvider\Model\ServiceProviderDomains;

class Domains extends BasePackage
{
	protected $modelToUse = ServiceProviderDomains::class;

	public $domains;

	public $domain;

	protected $appDefaults;

	protected $defaults = null;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		$this->getDomain();

		return $this;
	}

	public function getDomain()
	{
		if (!$this->domain) {
			$this->setDomain();

			return $this->domain;
		}
		return $this->domain;
	}

	protected function setDomain()
	{
		$this->domain = $this->getDomainByName($this->request->getHttpHost());

		if ($this->domain) {
			if (is_string($this->domain['apps']) && $this->domain['apps'] !== '') {
				$this->domain['apps'] = $this->helper->decode($this->domain['apps'], true);
			}
		}
	}

	/**
	 * @notification(name=add)
	 * notification_allowed_methods(email, sms)//Example
	 * @notification_allowed_methods(email, sms)
	 */
	public function addDomain(array $data)
	{
		if (!isset($data['dns_record']) ||
			(isset($data['dns_record']) && $data['dns_record'] === '')
		) {
			$data['dns_record'] = $this->validateDomain($data['name']);
		}

		if (isset($data['exclusive_to_default_app']) && $data['exclusive_to_default_app'] == '1') {
			$data = $this->checkAppsData($data);
		} else {
			$data = $this->checkAppsData($data, false);
		}

		if (!$data) {
			return false;
		}

		try {
			$add = $this->add($data);
		} catch (\Exception $e) {
			if ($e->getCode() == '23000') {
				$this->addResponse('Domain name already in use.', 1);

				return;
			}

			throw $e;
		}

		if ($add) {
			if ($data['apps'] && count($data['apps']) >= 1) {
				foreach ($data['apps'] as $appId => $appSettings) {
					if ($appSettings['allowed'] == true) {
						//add new viewsettings
						$viewSettingsData = [];
						$viewSettingsData['view_id'] = (int) $appSettings['view'];
						$viewSettingsData['domain_id'] = $this->packagesData->last['id'];
						$viewSettingsData['app_id'] = $appId;
						$viewSettingsData['via_domain'] = true;

						$this->modules->viewsSettings->addViewsSettings($viewSettingsData);
					}
				}
			}

			$this->addActivityLog($data);

			$this->addResponse('Added ' . $data['name'] . ' domain', 0, null, true);

			$this->addToNotification('add', 'Added new domain ' . $data['name'], null, $this->modules->packages->getPackageByName('Domains'));
		} else {
			$this->addResponse('Error adding new domain.', 1, []);
		}
	}

	/**
	 * @notification(name=update)
	 * notification_allowed_methods(email, sms)//Example
	 * @notification_allowed_methods(email, sms)
	 */
	public function updateDomain(array $data)
	{
		if (!isset($data['dns_record']) ||
			(isset($data['dns_record']) && $data['dns_record'] === '')
		) {
			$data['dns_record'] = $this->validateDomain($data['name']);
		}

		if (isset($data['exclusive_to_default_app']) && $data['exclusive_to_default_app'] == '1') {
			$data = $this->checkAppsData($data);
		} else {
			$data = $this->checkAppsData($data, false);
		}

		if (!$data) {
			return false;
		}

		$domain = $this->getById($data['id']);

		$domain = array_merge($domain, $data);

		try {
			$update = $this->update($domain);
		} catch (\Exception $e) {
			if ($e->getCode() == '23000') {
				$this->addResponse('Domain name ' . $data['name'] . ' already in use.', 1);

				return;
			}
		}

		if ($update) {
			$this->addActivityLog($data, $domain);

			$this->addResponse('Updated domain ' . $data['name']);

			$this->addToNotification('update', 'Updated domain ' . $data['name'], null, $this->modules->packages->getPackageByName('Domains'));
		} else {
			$this->addResponse('Error adding new domain.', 1);
		}
	}

	protected function checkAppsData($data, $exclusive = true)
	{
		if (!is_array($data['apps'])) {
			$data['apps'] = $this->helper->decode($data['apps'], true);
		}

		if (count($data['apps']) >= 1) {
			foreach ($data['apps'] as $appId => &$appSettings) {
				if ($exclusive === true) {
					if ($data['default_app_id'] == $appId) {
						$appSettings['allowed'] = true;

						if (!isset($appSettings['view']) ||
							!isset($appSettings['publicStorage']) ||
							!isset($appSettings['privateStorage'])
						) {
							$this->addResponse('Please provide complete app settings for app Id: ' . $appId, 1);

							return false;
						}
					} else {
						$appSettings['allowed'] = false;
						$appSettings['view'] = '';
						$appSettings['email_service'] = '';
						$appSettings['publicStorage'] = '';
						$appSettings['privateStorage'] = '';
					}
				} else {
					if ($appSettings['allowed'] === true &&
						(!isset($appSettings['view']) ||
						 !isset($appSettings['publicStorage']) ||
						 !isset($appSettings['privateStorage']))
					) {
						$this->addResponse('Please provide complete app settings for app Id: ' . $appId, 1);

						return false;
					}
				}
			}
		} else {
			$this->addResponse('Please provide app settings for this domain. Can not add placeholder domains.', 1);
		}

		return $data;
	}

	/**
	 * @notification(name=remove)
	 * notification_allowed_methods(email, sms)//Example
	 * @notification_allowed_methods(email, sms)
	 */
	public function removeDomain(array $data)
	{
		$domain = $this->getById($data['id']);

		$count = 1;

		if ($this->config->databasetype === 'db') {
			$count = (int) $this->useModel()->count();
		} else {
			$count = (int) $this->ffStore->count(true);
		}

		if ($domain['name'] === $this->request->getHttpHost() ||
			$count === 1
		) {
			$this->addResponse('App is being accessed from this domain. Cannot remove.', 1);

			return false;
		}

		if ($this->remove($domain['id'])) {
			$this->addResponse('Removed domain ' . $domain['name']);

			$this->addToNotification('remove', 'Removed domain ' . $domain['name'], null, $this->modules->packages->getPackageByName('Domains'));
		} else {
			$this->addResponse('Error removing domain.', 1);
		}
	}

	public function getDomainByName($name)
	{
		if (!$this->domains) {
			$this->init();
		}

		foreach($this->domains as $domain) {
			if ($domain['name'] === $name) {
				return $domain;
			}
		}

		return false;
	}

	public function getDomainById($id)
	{
		if (!$this->domains) {
			$this->init();
		}

		foreach($this->domains as $domain) {
			if ($domain['id'] == $id) {
				return $domain;
			}
		}

		return false;
	}

	public function generateViewData(int $did = null)
	{
		$appsArr = $this->apps->apps;
		$apps = [];

		foreach ($appsArr as $key => $value) {
			$apps[$value['id']] = $value;
			$apps[$value['id']]['views'] =
				$this->modules->views->getViewsForAppId($value['id']);
		}

		$this->packagesData->apps = $apps;

		$this->packagesData->emailservices = $this->basepackages->emailservices->init()->emailServices;

		$this->packagesData->storages = $this->basepackages->storages->storages;

		if ($did) {
			$domain = $this->getById($did);

			if (!$domain) {
				return false;
			}

			if (is_string($domain['apps'])) {
				$domain['apps'] = $this->helper->decode($domain['apps'], true);
			}
			if ($domain['settings']) {
				if (is_string($domain['settings'])) {
					$domain['settings'] = $this->helper->decode($domain['settings'], true);
				}
			} else {
				$domain['settings'] = [];
			}

			$this->packagesData->domain = $domain;

			return true;
		}
		return false;
	}

	public function removeAppFromApps(int $id)
	{
		foreach ($this->domains as $domainkey => $domain) {
			if (!is_array($domain['apps'])) {
				$domain['apps'] = $this->helper->decode($domain['apps'], true);
			}

			if (isset($domain['apps'][$id])) {
				unset($domain['apps'][$id]);
			}

			$domain['apps'] = $this->helper->encode($domain['apps']);

			$this->update($domain);
		}
	}

	public function validateDomain($domain)
	{
		$record = [];
		$record['internal'] = false;
		$record['matched'] = false;
		$record['server_address'] = $this->request->getServer('SERVER_ADDR');

		try {
			$dnsHandler = (new TCP())
				->setPort(53)
				->setNameserver('8.8.8.8')
				->setTimeout(3) // limit execution to 3 seconds
				->setRetries(3); // allows 3 retries if response fails

			$dnsRecordsService = new DnsRecords($dnsHandler);

			$record['AAAA'] = $dnsRecordsService->get($domain, RecordTypes::AAAA);
			$aaaa = [];
			if (count($record['AAAA']) > 0) {
				if (count($record['AAAA']) === 1) {
					$aaaaRecord = $record['AAAA'][0]->toArray();
					array_push($aaaa, $aaaaRecord['ipv6']);
				} else {
					foreach ($record['AAAA'] as $aaaaRecord) {
						$aaaaRecord = $aaaaRecord->toArray();

						if (isset($aaaaRecord['ipv6'])) {
							array_push($aaaa, $aaaaRecord['ipv6']);
						}
					}
				}
			}
			$record['AAAA'] = $aaaa;

			$record['A'] = $dnsRecordsService->get($domain, RecordTypes::A);
			$a = [];
			if (count($record['A']) > 0) {
				if (count($record['A']) === 1) {
					$aRecord = $record['A'][0]->toArray();
					array_push($a, $aRecord['ip']);
				} else {
					foreach ($record['A'] as $aRecord) {
						$aRecord = $aRecord->toArray();

						if (isset($aRecord['ip'])) {
							array_push($a, $aRecord['ip']);
						}
					}
				}
			}
			$record['A'] = $a;
			$record['CNAME'] = $dnsRecordsService->get($domain, RecordTypes::CNAME);
			if (count($record['CNAME']) > 0) {
				$record['CNAME'] = $record['CNAME'][0]->toArray();
			}

			$record['SOA'] = $dnsRecordsService->get($domain, RecordTypes::SOA);
			if (count($record['SOA']) > 0) {
				$record['SOA'] = $record['SOA'][0]->toArray();
			}

			if (count($record['AAAA']) === 0 &&
				count($record['A']) === 0 &&
				count($record['CNAME']) === 0
			) {
				$record['internal'] = true;
			}

			if ($record['internal'] === false) {
				if (count($record['A']) > 0 && in_array($record['server_address'], $record['A'])) {
					$record['internal'] = false;
					$record['matched'] = true;
				}
				if (count($record['AAAA']) > 0 && in_array($record['server_address'], $record['AAAA'])) {
					$record['internal'] = false;
					$record['matched'] = true;
				}
			}

			$this->packagesData->domainDetails = $record;

			return $record;
		} catch (\Exception $e) {
			$this->logger->log->debug('DNS resolution for domain failed: ' . $e->getMessage());

			return [];
		}
	}

	public function checkAppsSettings($appId, $settingKey, $settingValue)
	{
		foreach ($this->domains as $domain) {
			if (is_string($domain['apps'])) {
				$domain['apps'] = $this->helper->decode($domain['apps'], true);
			}

			if (isset($domain['apps'][$appId])) {
				if (isset($domain['apps'][$appId][$settingKey]) &&
					$domain['apps'][$appId][$settingKey] == $settingValue
				) {
					return $domain;
				}
			}
		}

		return false;
	}
}