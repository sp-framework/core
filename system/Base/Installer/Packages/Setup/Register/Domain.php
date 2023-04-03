<?php

namespace System\Base\Installer\Packages\Setup\Register;

use BlueLibraries\Dns\DnsRecords;
use BlueLibraries\Dns\Handlers\Types\TCP;
use BlueLibraries\Dns\Records\RecordTypes;
use Phalcon\Helper\Json;

class Domain
{
	public function register($db, $request)
	{
		$request->setStrictHostCheck(true);

		$apps =
		[
			'1' =>
			[
				'allowed'			=> true,
				'view'				=> '1',
				'email_service'		=> null,
				'storage'			=> 0,
				'publicStorage'		=> 1,
				'privateStorage'	=> 2
			]
		];

		$record = $this->validateDomain($request->getHttpHost());
		if ($record) {
			$record = Json::encode($record);
			$isInternal = 0;
		} else {
			$record = null;
			$isInternal = 1;
		}

		$db->insertAsDict(
			'domains',
			[
				'name'   							=> $request->getHttpHost(),
				'description' 						=> '',
				"default_app_id"					=> 1,
				"exclusive_to_default_app"			=> 0,
				"apps"			    				=> Json::encode($apps),
				"dns_record"						=> $record,
				"is_internal"						=> $isInternal,
				'settings'			 				=> Json::encode([])
			]
		);
	}

	protected function validateDomain($domain)
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

			return $record;
		} catch (\Exception $e) {
			return false;
		}
	}
}