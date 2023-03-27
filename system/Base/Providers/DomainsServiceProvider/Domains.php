<?php

namespace System\Base\Providers\DomainsServiceProvider;

use BlueLibraries\Dns\DnsRecords;
use BlueLibraries\Dns\Handlers\Types\TCP;
use BlueLibraries\Dns\Records\RecordTypes;
use Phalcon\Helper\Json;
use Phalcon\Validation\Validator\Url;
use System\Base\BasePackage;
use System\Base\Providers\DomainsServiceProvider\Model\Domains as DomainsModel;

class Domains extends BasePackage
{
	protected $modelToUse = DomainsModel::class;

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
		$this->domain = $this->getNamedDomain($this->request->getHttpHost());

		if ($this->domain) {
			if ($this->domain['apps'] !== '') {
				$this->domain['apps'] = Json::decode($this->domain['apps'], true);
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
		$data['dns_record'] = $this->validateDomain($data['name']);

		try {
			$add = $this->add($data);
		} catch (\Exception $e) {
			if ($e->getCode() == '23000') {
				$this->addResponse('Domain name already in use.', 1);

				return;
			}
		}

		if ($add) {
			$this->addActivityLog($data);

			$this->addResponse('Added ' . $data['name'] . ' domain', 0, null, true);

			$this->addToNotification('add', 'Added new domain ' . $data['name'], null, $this->modules->packages->getNamePackage('Domains'));
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
		$data['dns_record'] = $this->validateDomain($data['name']);

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

			$this->addToNotification('update', 'Updated domain ' . $data['name'], null, $this->modules->packages->getNamePackage('Domains'));
		} else {
			$this->addResponse('Error adding new domain.', 1);
		}
	}

	/**
	 * @notification(name=remove)
	 * notification_allowed_methods(email, sms)//Example
	 * @notification_allowed_methods(email, sms)
	 */
	public function removeDomain(array $data)
	{
		$domain = $this->getById($data['id']);

		if ($this->remove($domain['id'])) {
			$this->addResponse('Removed domain ' . $domain['name']);

			$this->addToNotification('remove', 'Removed domain ' . $domain['name'], null, $this->modules->packages->getNamePackage('Domains'));
		} else {
			$this->addResponse('Error removing domain.', 1);
		}
	}

	public function getNamedDomain($name)
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

	public function getIdDomain($id)
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
				$this->modules->views->getViewsForApp($value['id']);
		}

		$this->packagesData->apps = $apps;

		$this->packagesData->emailservices = $this->basepackages->emailservices->init()->emailServices;

		$this->packagesData->storages = $this->basepackages->storages->storages;

		if ($did) {
			$domain = $this->getById($did);

			if (!$domain) {
				return false;
			}

			$domain['apps'] = Json::decode($domain['apps'], true);
			if ($domain['settings']) {
				$domain['settings'] = Json::decode($domain['settings'], true);
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
				$domain['apps'] = Json::decode($domain['apps'], true);
			}

			if (isset($domain['apps'][$id])) {
				unset($domain['apps'][$id]);
			}

			$domain['apps'] = Json::encode($domain['apps']);

			$this->update($domain);
		}
	}

	public function validateDomain($domain)
	{
		$record = [];
		$record['internal'] = false;

		try {
			$dnsHandler = (new TCP())
				->setPort(53)
				->setNameserver('8.8.8.8')
				->setTimeout(3) // limit execution to 3 seconds
				->setRetries(3); // allows 5 retries if response fails

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

			$keys = [
				'HTTP_CLIENT_IP',
				'HTTP_X_FORWARDED_FOR',
				'HTTP_X_FORWARDED',
				'HTTP_X_CLUSTER_CLIENT_IP',
				'HTTP_FORWARDED_FOR',
				'HTTP_FORWARDED',
				'REMOTE_ADDR'
			];

			$record['server_address'] = $_SERVER['REMOTE_ADDR'];

			foreach ($keys as $key) {
				if (array_key_exists($key, $_SERVER) === true) {
					foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
						if ($record['internal']) {
							$flags = FILTER_FLAG_NO_RES_RANGE;
						} else {
							$flags = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
						}

						if (filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false) {
							$record['server_address'] = $ip;
						}
					}
				}
			}

			$this->packagesData->domainDetails = $record;

			return $record;
		} catch (\Exception $e) {
			return false;
		}
	}
}