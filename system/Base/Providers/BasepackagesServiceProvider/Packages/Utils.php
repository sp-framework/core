<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use League\Flysystem\StorageAttributes;
use Seld\JsonLint\JsonParser;
use System\Base\BasePackage;
use System\Base\Providers\SecurityServiceProvider\PasswordGenerator\Generator\RequirementPasswordGenerator;
use ZxcvbnPhp\Zxcvbn;

class Utils extends BasePackage
{
    public function init($container = null)
    {
        if ($container) {
            $this->container = $container;
        }

        return $this;
    }

    public function scanDir($directory, $sub = true, $exclude = [])
    {
        $files = [];
        $files['dirs'] = [];
        $files['files'] = [];

        if ($directory) {
            $files['files'] =
                $this->localContent->listContents($directory, $sub)
                ->filter(fn (StorageAttributes $attributes) => $attributes->isFile())
                ->map(fn (StorageAttributes $attributes) => $attributes->path())
                ->toArray();

            $files['dirs'] =
                $this->localContent->listContents($directory, $sub)
                ->filter(fn (StorageAttributes $attributes) => $attributes->isDir())
                ->map(fn (StorageAttributes $attributes) => $attributes->path())
                ->toArray();

            if (count($exclude) > 0) {
                foreach ($exclude as $excluded) {
                    foreach ($files['files'] as $key => $file) {
                        if (strpos($file, $excluded)) {
                            unset($files['files'][$key]);
                        }
                    }
                    foreach ($files['dirs'] as $key => $dir) {
                        if (strpos($dir, $excluded)) {
                            unset($files['dirs'][$key]);
                        }
                    }
                }
            }

            return $files;
        } else {
            return null;
        }
    }

    public function checkPwStrength(string $pass)
    {
        $checkingTool = new Zxcvbn();

        $result = $checkingTool->passwordStrength($pass);

        if ($result && is_array($result) && isset($result['score'])) {
            if ($result['score'] === 0) {
                $result['score'] = 1;
            }

            $this->addResponse('Checking Password Strength Success', 0, ['result' => $result['score']]);

            return $result['score'];
        }

        $this->addResponse('Error Checking Password Strength', 1);

        return false;
    }

    public function generateNewPassword($params = [])
    {
        $password = null;

        if (isset($params['length_min']) && $params['length_min'] < 4) {
            $params['length_min'] = 4;
        }
        if (!isset($params['length_min'])) {
            $params['length_min'] = 8;
        }

        if (isset($params['length_max']) && $params['length_max'] > 20) {
            $params['length_max'] = 20;
        }
        if (!isset($params['length_max'])) {
            $params['length_max'] = 12;
        }

        if (!isset($params['complexity'])) {
            $params['complexity'] = 'simple';
        }

        if ($params['complexity'] === 'simple') {
            $password = $this->secTools->random->base62($params['length_min']);
        } else if ($params['complexity'] === 'complex') {
            if ((!isset($params['lowercase']) &&
                 !isset($params['uppercase']) &&
                 !isset($params['numbers']) &&
                 !isset($params['symbols']) &&
                 !isset($params['avoid_similar'])) ||
                ((isset($params['lowercase']) && $params['lowercase'] == false) &&
                 (isset($params['uppercase']) && $params['uppercase'] == false) &&
                 (isset($params['numbers']) && $params['numbers'] == false) &&
                 (isset($params['symbols']) && $params['symbols'] == false) &&
                 (isset($params['avoid_similar']) && $params['avoid_similar'] == false)
                )
            ) {
                $password = $this->secTools->random->base62($params['length_min']);

                $this->addResponse('Password generated using simple generator as no option was enabled.', 1, ['password' => $password]);

                return;
            } else {
                try {
                    $generator = $this->initPasswordGenerator($params);

                    if (!$generator) {
                        return false;
                    }

                    $password = $generator->generatePassword();
                } catch (\Exception $e) {//Generate Simple password if something fails.
                    $this->logger->log->debug($e->getMessage());

                    $password = $this->secTools->random->base62($params['length_min']);

                    $this->addResponse('Password generated using simple generator. Contact developer for more help.', 1, ['password' => $password]);
                }
            }
        }

        if (!$password) {
            $this->addResponse('Error Generating Password', 1);

            return;
        }

        $this->addResponse('Password Generate Successfully', 0, ['password' => $password]);
    }

    protected function initPasswordGenerator($params = [])
    {
        $lowercase = $uppercase = $numbers = $symbols = false;
        $lowercaseMinCount = $uppercaseMinCount = $numbersMinCount = $symbolsMinCount = null;
        $lowercaseMaxCount = $uppercaseMaxCount = $numbersMaxCount = $symbolsMaxCount = null;
        $maxCountTotal = 0;

        if (isset($params['uppercase'])) {
            $uppercase = $params['uppercase'] == true ? true : false ;
        }
        if (isset($params['lowercase'])) {
            $lowercase = $params['lowercase'] == true ? true : false ;
        }
        if (isset($params['numbers'])) {
            $numbers = $params['numbers'] == true ? true : false ;
        }
        if (isset($params['symbols'])) {
            $symbols = $params['symbols'] == true ? true : false ;
        }
        if (isset($params['avoid_similar'])) {
            $avoid_similar = $params['avoid_similar'] == 'true' ? true : false ;
        }
        if (isset($params['uppercase_min_count']) && $params['uppercase_min_count'] !== '') {
            $uppercaseMinCount = (int) abs($params['uppercase_min_count']);
        }
        if (isset($params['uppercase_max_count']) && $params['uppercase_max_count'] !== '') {
            $uppercaseMaxCount = (int) abs($params['uppercase_max_count']);
            $maxCountTotal = $maxCountTotal + $uppercaseMaxCount;
        }
        if (isset($params['lowercase_min_count']) && $params['lowercase_min_count'] !== '') {
            $lowercaseMinCount = (int) abs($params['lowercase_min_count']);
        }
        if (isset($params['lowercase_max_count']) && $params['lowercase_max_count'] !== '') {
            $lowercaseMaxCount = (int) abs($params['lowercase_max_count']);
            $maxCountTotal = $maxCountTotal + $lowercaseMaxCount;
        }
        if (isset($params['numbers_min_count']) && $params['numbers_min_count'] !== '') {
            $numbersMinCount = (int) abs($params['numbers_min_count']);
        }
        if (isset($params['numbers_max_count']) && $params['numbers_max_count'] !== '') {
            $numbersMaxCount = (int) abs($params['numbers_max_count']);
            $maxCountTotal = $maxCountTotal + $numbersMaxCount;
        }
        if (isset($params['symbols_min_count']) && $params['symbols_min_count'] !== '') {
            $symbolsMinCount = (int) abs($params['symbols_min_count']);
        }
        if (isset($params['symbols_max_count']) && $params['symbols_max_count'] !== '') {
            $symbolsMaxCount = (int) abs($params['symbols_max_count']);
            $maxCountTotal = $maxCountTotal + $symbolsMaxCount;
        }

        if ($uppercaseMinCount > $uppercaseMaxCount ||
            $lowercaseMinCount > $lowercaseMaxCount ||
            $numbersMinCount > $numbersMaxCount ||
            $symbolsMinCount > $symbolsMaxCount
        ) {
            $this->addResponse('Max value has to be greater than min value.', 1);

            return;
        }

        if ($maxCountTotal < (int) $params['length_min']) {
            $this->addResponse('Max value total has to be greater than minimum length.', 1);

            return;
        }

        if ($maxCountTotal > (int) $params['length_max']) {
            $this->addResponse('Max value total has to be smaller than maximum length.', 1);

            return;
        }

        $generator = new RequirementPasswordGenerator();

        $generator
            ->setLength((int) $params['length_min'], 'minimum')
            ->setLength((int) $params['length_max'], 'maximum')
            ->setOptionValue(RequirementPasswordGenerator::OPTION_UPPER_CASE, $uppercase)
            ->setOptionValue(RequirementPasswordGenerator::OPTION_LOWER_CASE, $lowercase)
            ->setOptionValue(RequirementPasswordGenerator::OPTION_NUMBERS, $numbers)
            ->setOptionValue(RequirementPasswordGenerator::OPTION_SYMBOLS, $symbols)
            ->setOptionValue(RequirementPasswordGenerator::OPTION_AVOID_SIMILAR, $avoid_similar)
            ->setMinimumCount(RequirementPasswordGenerator::OPTION_UPPER_CASE, $uppercaseMinCount)
            ->setMaximumCount(RequirementPasswordGenerator::OPTION_UPPER_CASE, $uppercaseMaxCount)
            ->setMinimumCount(RequirementPasswordGenerator::OPTION_LOWER_CASE, $lowercaseMinCount)
            ->setMaximumCount(RequirementPasswordGenerator::OPTION_LOWER_CASE, $lowercaseMaxCount)
            ->setMinimumCount(RequirementPasswordGenerator::OPTION_NUMBERS, $numbersMinCount)
            ->setMaximumCount(RequirementPasswordGenerator::OPTION_NUMBERS, $numbersMaxCount)
            ->setMinimumCount(RequirementPasswordGenerator::OPTION_SYMBOLS, $symbolsMinCount)
            ->setMaximumCount(RequirementPasswordGenerator::OPTION_SYMBOLS, $symbolsMaxCount)
        ;

        if ($uppercase === true && isset($params['uppercase_include']) && $params['uppercase_include'] !== '') {
            $generator
                ->setParameter(RequirementPasswordGenerator::PARAMETER_UPPER_CASE, $params['uppercase_include'])
            ;
        }
        if ($lowercase === true && isset($params['lowercase_include']) && $params['lowercase_include'] !== '') {
            $generator
                ->setParameter(RequirementPasswordGenerator::PARAMETER_LOWER_CASE, $params['lowercase_include'])
            ;
        }
        if ($numbers === true && isset($params['numbers_include']) && $params['numbers_include'] !== '') {
            $generator
                ->setParameter(RequirementPasswordGenerator::PARAMETER_NUMBERS, $params['numbers_include'])
            ;
        }
        if ($symbols === true && isset($params['symbols_include']) && $params['symbols_include'] !== '') {
            $generator
                ->setParameter(RequirementPasswordGenerator::PARAMETER_SYMBOLS, $params['symbols_include'])
            ;
        }
        if ($avoid_similar === true && isset($params['avoid_similar_characters']) && $params['avoid_similar_characters'] !== '') {
            $generator
                ->setParameter(RequirementPasswordGenerator::PARAMETER_SIMILAR, $params['avoid_similar_characters'])
            ;
        }

        if (!$generator->validLimits()) {
            $this->addResponse('Complex fields count not set correctly. total of min & max count should be greater than password length.', 1);

            return;
        }

        return $generator;
    }

    public function formatJson($data)
    {
        if (!isset($data['json'])) {
            $this->addResponse('Json data not provided.', 1);

            return;
        }

        if (is_array($data['json'])) {
            $data['json'] = $this->helper->encode($data['json'], JSON_UNESCAPED_SLASHES);
        }

        $return = "\n";
        $indent = "\t";
        $formatted_json = '';
        $quotes = false;
        $arrayLevel = 0;

        for ($i = 0; $i < strlen($data['json']); $i++) {
            $prefix = '';
            $suffix = '';

            switch ($data['json'][$i]) {
                case '"':
                    $quotes = !$quotes;
                    if ($data['json'][$i - 1] === '[') {
                        $prefix = $return;
                        $prefix .= str_repeat($indent, $arrayLevel);
                    }
                    break;

                case '[':
                    if ($data['json'][$i + 1] === '{' || $data['json'][$i + 1] === '"') {
                        $prefix = $return;
                        $prefix .= str_repeat($indent, $arrayLevel);
                    }
                    $arrayLevel++;

                    break;

                case ']':
                    $arrayLevel--;
                    if ($data['json'][$i -1] !== '[') {
                        $prefix = $return;
                        $prefix .= str_repeat($indent, $arrayLevel);
                    }
                    break;

                case '{':
                    if ($data['json'][$i - 1] === '[') {
                        $prefix = $return;
                        $prefix .= str_repeat($indent, $arrayLevel);
                    }
                    if ($data['json'][$i + 1] === '"') {
                        $prefix = $return;
                        $prefix .= str_repeat($indent, $arrayLevel);
                    }
                    $arrayLevel++;
                    if ($data['json'][$i + 1] !== '}') {
                        $suffix = $return;
                        $suffix .= str_repeat($indent, $arrayLevel);
                    }

                    break;

                case ':':
                    if ($data['json'][$i - 1] === '"' &&
                        ($data['json'][$i + 1] === '"' ||
                         $data['json'][$i + 1] === '[' ||
                         $data['json'][$i + 1] === '{' ||
                         $data['json'][$i + 1] === 't' ||
                         $data['json'][$i + 1] === 'T' ||
                         $data['json'][$i + 1] === 'f' ||
                         $data['json'][$i + 1] === 'F') ||
                         is_numeric($data['json'][$i + 1])
                    ) {
                        $prefix = ' ';
                        $suffix = ' ';
                    }
                    break;

                case ',':
                    if (!$quotes) {
                        $suffix = $return;
                        $suffix .= str_repeat($indent, $arrayLevel);
                    }
                    break;

                case '}':
                    $arrayLevel--;

                case ']':
                    $prefix = $return;
                    $prefix .= str_repeat($indent, $arrayLevel);
                    break;
            }

            $formatted_json .= $prefix.$data['json'][$i].$suffix;
        }

        $this->addResponse('Success', 0, ['formatted_json' => $formatted_json]);

        return $formatted_json;
    }

    public function jsonDecodeData(array $data)
    {
        return $this->jsonData($data, true);
    }

    public function validateJson($data)
    {
        if (!isset($data['json'])) {
            $this->addResponse('Json data not provided.', 1);

            return;
        }

        $result = null;

        try {
            $parser = new JsonParser();

            $result = $parser->lint($data['json']);

            $parser->parse($data['json'], JsonParser::DETECT_KEY_CONFLICTS);
        } catch (ParsingException | \throwable $e) {
            if ($result === null) {
                if (defined('JSON_ERROR_UTF8') && JSON_ERROR_UTF8 === json_last_error()) {
                    $this->addResponse('Json is not UTF-8, could not parse json data.', 1);

                    return;
                }
            }

            $this->addResponse($e->getMessage(), 1);

            return false;
            // throw $e;
        }

        if (isset($data['returnJson']) && $data['returnJson'] === 'array') {
            $data['json'] = $this->helper->decode($data['json'], true);
        } else if (isset($data['returnJson']) && $data['returnJson'] === 'formatted') {
            $data['json'] = $this->basepackages->utils->formatJson($data);
        }

        $this->addResponse('Success', 0, ['json' => $data['json']]);

        return $data['json'];
    }
}