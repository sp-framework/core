<?php

namespace System\Base\Providers\CoreServiceProvider;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\CoreServiceProvider\Model\Core as CoreModel;

class Core extends BasePackage
{
	protected $modelToUse = CoreModel::class;

	public $core;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		$this->core = $this->core[0];

		$this->core['settings'] = Json::decode($this->core['settings'], true);

		$this->checkKeys();

		return $this;
	}

	protected function checkKeys()
	{
		try {
			$fileExists = $this->localContent->fileExists('system/.keys');
		} catch (FilesystemException | UnableToRetrieveMetadata $exception) {
			throw $exception;
		}

		if (!$fileExists) {
			$this->createKeys();

			$this->getKeys();
		} else {
			$this->getKeys();
		}
	}

	protected function createKeys()
	{
		$keys['sigKey'] = $sigKey = $this->random->base58();
		$keys['sigText'] = $sigText = $this->random->base58(32);
		$keys['cookiesSig'] = $cookiesSig = $this->crypt->encryptBase64($sigText, $sigKey);

		try {
			$this->localContent->write('system/.keys', Json::encode($keys));
		} catch (FilesystemException | UnableToWriteFile $exception) {
			throw $exception;
		}
	}

	protected function getKeys()
	{
		try {
			$keysFile = $this->localContent->read('system/.keys');
		} catch (FilesystemException | UnableToReadFile | \Exception $exception) {
			throw $exception;
		}

		$keys = Json::decode($keysFile, true);

		$this->core['settings']['sigKey'] = $keys['sigKey'];
		$this->core['settings']['sigText'] = $keys['sigText'];
		$this->core['settings']['cookiesSig'] = $keys['cookiesSig'];
	}
}