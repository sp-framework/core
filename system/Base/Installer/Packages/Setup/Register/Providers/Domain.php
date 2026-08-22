<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Register\Providers
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Providers;

use BlueLibraries\Dns\DnsRecords;
use BlueLibraries\Dns\Handlers\Types\TCP;
use BlueLibraries\Dns\Records\RecordTypes;
use Throwable;

/**
 * Seeds default Domain registration entry and runs DNS validation.
 */
class Domain
{
    /**
     * HTTP request service.
     *
     * @var mixed
     */
    protected mixed $request = null;

    /**
     * Registers default tenant domain record.
     *
     * @param mixed $db      PDO database connection adapter.
     * @param mixed $ff      FlatFile database manager.
     * @param mixed $request HTTP request service.
     * @param mixed $helper  Helpers service instance.
     *
     * @return void
     */
    public function register(mixed $db, mixed $ff, mixed $request, mixed $helper): void
    {
        $this->request = $request;

        if ($this->request) {
            $this->request->setStrictHostCheck(true);
        }

        $host = ($this->request && method_exists($this->request, 'getHttpHost')) ? (string) $this->request->getHttpHost() : 'localhost';

        $apps = [
            '1' => [
                'allowed'        => true,
                'view'           => 1,
                'email_service'  => null,
                'publicStorage'  => 1,
                'privateStorage' => 2
            ]
        ];

        $record = $this->validateDomain($host);

        if (count($record) > 0) {
            $isInternal = (isset($record['internal']) && $record['internal'] === true) ? '1' : '0';
            $encodedRecord = method_exists($helper, 'encode') ? $helper->encode($record) : json_encode($record);
        } else {
            $isInternal = '1';
            $encodedRecord = method_exists($helper, 'encode') ? $helper->encode([]) : '[]';
        }

        $domain = [
            'name'                     => $host,
            'description'              => '',
            'default_app_id'           => 1,
            'exclusive_to_default_app' => 0,
            'exclusive_for_api'        => 0,
            'apps'                     => method_exists($helper, 'encode') ? $helper->encode($apps) : json_encode($apps),
            'dns_record'               => $encodedRecord,
            'is_internal'              => $isInternal,
            'settings'                 => method_exists($helper, 'encode') ? $helper->encode([]) : '{}'
        ];

        if ($db) {
            $db->insertAsDict('service_provider_domains', $domain);
        }

        if ($ff) {
            $domainStore = $ff->store('service_provider_domains');

            $domainStore->updateOrInsert($domain);
        }
    }

    /**
     * Performs DNS lookup and validation for the domain name.
     *
     * @param string $domain Domain name to validate.
     *
     * @return array<string, mixed> DNS validation details.
     */
    protected function validateDomain(string $domain): array
    {
        $serverAddr = ($this->request && method_exists($this->request, 'getServer')) ? $this->request->getServer('SERVER_ADDR') : '127.0.0.1';

        $record = [
            'internal'       => false,
            'matched'        => false,
            'server_address' => $serverAddr,
        ];

        if (!class_exists(DnsRecords::class) || !class_exists(TCP::class)) {
            $record['internal'] = true;
            return $record;
        }

        try {
            $dnsHandler = (new TCP())
                ->setPort(53)
                ->setNameserver('8.8.8.8')
                ->setTimeout(3)
                ->setRetries(3);

            $dnsRecordsService = new DnsRecords($dnsHandler);

            $rawAaaa = $dnsRecordsService->get($domain, RecordTypes::AAAA);
            $aaaa = [];
            if (is_array($rawAaaa) && count($rawAaaa) > 0) {
                foreach ($rawAaaa as $aaaaRecord) {
                    $arr = method_exists($aaaaRecord, 'toArray') ? $aaaaRecord->toArray() : (array) $aaaaRecord;
                    if (isset($arr['ipv6'])) {
                        $aaaa[] = $arr['ipv6'];
                    }
                }
            }
            $record['AAAA'] = $aaaa;

            $rawA = $dnsRecordsService->get($domain, RecordTypes::A);
            $a = [];
            if (is_array($rawA) && count($rawA) > 0) {
                foreach ($rawA as $aRecord) {
                    $arr = method_exists($aRecord, 'toArray') ? $aRecord->toArray() : (array) $aRecord;
                    if (isset($arr['ip'])) {
                        $a[] = $arr['ip'];
                    }
                }
            }
            $record['A'] = $a;

            $rawCname = $dnsRecordsService->get($domain, RecordTypes::CNAME);
            $record['CNAME'] = (is_array($rawCname) && isset($rawCname[0]) && method_exists($rawCname[0], 'toArray')) ? $rawCname[0]->toArray() : [];

            $rawSoa = $dnsRecordsService->get($domain, RecordTypes::SOA);
            $record['SOA'] = (is_array($rawSoa) && isset($rawSoa[0]) && method_exists($rawSoa[0], 'toArray')) ? $rawSoa[0]->toArray() : [];

            if (count($record['AAAA']) === 0 && count($record['A']) === 0 && empty($record['CNAME'])) {
                $record['internal'] = true;
            }

            if ($record['internal'] === false) {
                if (count($record['A']) > 0 && in_array($record['server_address'], $record['A'], true)) {
                    $record['internal'] = false;
                    $record['matched'] = true;
                }
                if (count($record['AAAA']) > 0 && in_array($record['server_address'], $record['AAAA'], true)) {
                    $record['internal'] = false;
                    $record['matched'] = true;
                }
            }

            return $record;
        } catch (Throwable $e) {
            return [];
        }
    }
}