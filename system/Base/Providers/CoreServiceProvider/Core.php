<?php

namespace System\Base\Providers\CoreServiceProvider;

use Carbon\Carbon;
use Ifsnop\Mysqldump\Mysqldump;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToDeleteFile;
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

		$this->checkTmpPath();

		return $this;
	}

	public function dbbackup($data)
	{
		if (!isset($data['db'])) {
			$this->addResponse('Please provide db name', 1, []);

			return false;
		}

		if (!isset($this->core['settings']['dbs'][$data['db']])) {
			$this->addResponse('Db does not exist.', 1, []);

			return false;
		}

		$db = $this->core['settings']['dbs'][$data['db']];
		$key = $this->getDbKey($db);
		$db['password'] = $this->crypt->decryptBase64($db['password'], $this->getDbKey($db));

		try {
			$dump =
				new Mysqldump(
					'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
					$db['username'],
					$db['password'],
					['compress' => Mysqldump::GZIP, 'default-character-set' => Mysqldump::UTF8MB4]
				);

			$fileName = 'db-' . $data['db'] . '-' . Carbon::now()->format('Y-m-d-H:i:s') . '.gzip';

			$dump->start(base_path('var/tmp/' . $fileName));
		} catch (\Exception $e) {
			$this->addResponse('Backup Error: ' . $e->getMessage(), 1);
		}

		try {
			$file = $this->localContent->read('var/tmp/' . $fileName);
		} catch (FilesystemException | UnableToReadFile | \Exception $exception) {
			throw $exception;
		}

		if ($this->basepackages->storages->storeFile(
				'private',
				'core',
				$file,
				$fileName,
				filesize(base_path('var/tmp/' . $fileName)),
				'application/gzip'
			)
		) {
			try {
				$file = $this->localContent->delete('var/tmp/' . $fileName);
			} catch (FilesystemException | UnableToDeleteFile | \Exception $exception) {
				throw $exception;
			}

			$this->basepackages->storages->changeOrphanStatus($this->basepackages->storages->packagesData->storageData['uuid']);

			$this->addResponse('Generated backup ' . $fileName . '.',
							   0,
							   ['filename' => $fileName,
								'uuid' => $this->basepackages->storages->packagesData->storageData['uuid']
							   ]
			);

			return true;
		}

		return false;
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

	public function refreshKeys()
	{
		$this->createKeys();
	}

	protected function createKeys()
	{
		$keys['sigKey'] = $this->random->base58();
		$keys['sigText'] = $this->random->base58(32);
		$keys['cookiesSig'] = $this->crypt->encryptBase64($keys['sigKey'], $keys['sigText']);

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

	public function reset()
	{
		if ($this->core['settings']['dev'] == 'true') {
			$this->writeResetConfig();

			$this->addResponse('Reset Done', 0);

			return true;
		}
	}

	private function writeResetConfig()
	{
		$resetContent =
'<?php

return
	[
		"setup" 		=> true
	];';

		$this->localContent->write('/system/Configs/Base.php', $resetContent);
	}

	private function getDbKey($dbConfig)
	{
		try {
			$keys = $this->localContent->read('system/.dbkeys');

			return Json::decode($keys, true)[$dbConfig['dbname']];
		} catch (\ErrorException | FilesystemException | UnableToReadFile $exception) {
			return false;
		}
	}

	protected function checkTmpPath()
	{
		if (!is_dir(base_path('var/tmp/'))) {
			if (!mkdir(base_path('var/tmp/'), 0777, true)) {
				return false;
			}
		}

		return true;
	}
}