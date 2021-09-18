<?php

namespace System\Base\Providers\CoreServiceProvider;

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

		if (isset($this->core['settings']['sigKey']) &&
			isset($this->core['settings']['sigText']) &&
			isset($this->core['settings']['cookiesSig'])
		) {
			$sigKey = $this->core['settings']['sigKey'];
			$sigText = $this->core['settings']['sigText'];
			$cookiesSig = $this->core['settings']['cookiesSig'];
		} else {
			$this->core['settings']['sigKey'] = $sigKey = $this->random->base58();
			$this->core['settings']['sigText'] = $sigText = $this->random->base58(32);
			$this->core['settings']['cookiesSig'] = $cookiesSig = $this->crypt->encryptBase64($sigText, $sigKey);
			$this->core['settings'] = Json::encode($this->core['settings']);

			$this->update($this->core);
		}

		return $this;
	}
}