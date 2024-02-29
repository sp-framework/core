<?php

namespace System\Base\Installer\Packages\Setup\Register\Providers;

use BlueLibraries\Dns\DnsRecords;
use BlueLibraries\Dns\Handlers\Types\TCP;
use BlueLibraries\Dns\Records\RecordTypes;

class Domain
{
	protected $request;

	public function register($db, $ff, $request)
	{
		$this->request = $request;

		$this->request->setStrictHostCheck(true);

		$apps =
		[
			'1' =>
			[
				'allowed'			=> true,
				'view'				=> 1,
				'email_service'		=> null,
				'publicStorage'		=> 1,
				'privateStorage'	=> 2
			]
		];

		$record = $this->validateDomain($this->request->getHttpHost());

		if (count($record) > 0) {
			$isInternal = isset($record['internal']) ? $record['internal'] : '1';
			$record = $this->helper->encode($record);
		} else {
			$isInternal = '1';
			$record = [];
		}

		$domain =
			[
				'name'   							=> $this->request->getHttpHost(),
				'description' 						=> '',
				"default_app_id"					=> 1,
				"exclusive_to_default_app"			=> 0,
				"apps"			    				=> $this->helper->encode($apps),
				"dns_record"						=> $record,
				"is_internal"						=> $isInternal,
				'settings'			 				=> $this->helper->encode([])
			];

		if ($db) {
			$db->insertAsDict('service_provider_domains', $domain);
		}

		if ($ff) {
			$domainStore = $ff->store('service_provider_domains');

			$domainStore->updateOrInsert($domain);
		}
	}

	protected function validateDomain($domain)
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

			return $record;
		} catch (\Exception $e) {
			return [];
		}
	}
}