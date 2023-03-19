<?php

namespace System\Base\Installer\Packages\Setup\Register;

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

		try {
			$record['AAAA'] = \dns_get_record($domain, DNS_AAAA);
			$record['A'] = \dns_get_record($domain, DNS_A);
			$record['CNAME'] = \dns_get_record($domain, DNS_CNAME);

			if (count($record['AAAA']) === 0 &&
				count($record['A']) === 0 &&
				count($record['CNAME']) === 0
			) {
				return false;
			}

			return $record;
		} catch (\Exception $e) {
			return false;
		}
	}
}