<?php

namespace Apps\Dash\Packages\Devtools\Api\Enums;

use Apps\Dash\Packages\Devtools\Api\Contracts\Contracts;
use Apps\Dash\Packages\Devtools\Api\Enums\Model\DevtoolsApiEnums;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use Phalcon\Helper\Str;
use System\Base\BasePackage;

class Enums extends BasePackage
{
    protected $modelToUse = DevtoolsApiEnums::class;

    protected $packageName = 'enums';

    protected $ebayServicesClass = 'Apps\Dash\Packages\System\Api\Apis\Ebay';

    protected $ebayServicesDirectory = 'apps/Dash/Packages/System/Api/Apis/Ebay/';

    public $enums;

    protected $enum;

    public function addEnum(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' api contract.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding api contract.';
        }
    }

    public function updateEnum(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' api contract.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating api contract.';
        }
    }

    public function removeEnum(array $data)
    {
        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed api contract.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing api contract.';
        }
    }

    public function extractEnums(array $data)
    {
        if ($data['link'] === '' || $data['class'] === '') {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Link/Class missing';

            return;
        }

        include('vendor/Simplehtmldom.php');

        $html = file_get_html($data['link']);

        $enums = $html->find('div.' . $data['class']);

        if (count($enums) === 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'No Data Found. Check Tag Class';

            return;
        }

        $enumsArr = [];

        foreach ($enums as $key => $enum) {
            $enumsArr[$key] = $enum->innertext;
        }

        $this->packagesData->responseData = implode(',', $enumsArr);
    }

    public function generateEnum($id)
    {
        $enum = $this->getById($id);

        if ($enum['enums'] !== '') {
            $enum['enums'] = explode(',', $enum['enums']);
        }

        $enum['contract_name'] =
            $this->usePackage(Contracts::class)->getById($enum['contract_id'])['name'];

        $this->enum = $enum;

        $this->writeEnumFile($this->generateEnumFile());
    }

    protected function generateEnumFile()
    {
        $file = '<?php

namespace ' . $this->ebayServicesClass . '\\' . $this->enum['contract_name'] . '\\Enums;

class ' . $this->enum['contract_name'] . 'Enum
{';

        foreach ($this->enum['enums'] as $enumKey => $enum) {
            $file .= '
    const ' . strtoupper($this->enum['constant_prefix']) . strtoupper($enum) . ' = \'' . strtoupper($enum) . '\';';
        }

        $file .= '
}';

        return $file;
    }

    protected function writeEnumFile($file)
    {
        $this->localContent->put(
            $this->ebayServicesDirectory .
            $this->enum['contract_name'] .
            '/Enums/' .
            $this->enum['contract_name'] . 'Enum' .
            '.php',
            $file
        );
    }
}