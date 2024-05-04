<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use League\Flysystem\StorageAttributes;
use Seld\JsonLint\JsonParser;
use System\Base\BasePackage;
use System\Base\Providers\SecurityServiceProvider\PasswordGenerator\Generator\RequirementPasswordGenerator;
use ZxcvbnPhp\Zxcvbn;

class Utils extends BasePackage
{
    protected $microtime = 0;

    protected $microTimers = [];

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

        if (isset($params['passwordpolicylengthmin']) && $params['passwordpolicylengthmin'] < 4) {
            $params['passwordpolicylengthmin'] = 4;
        }
        if (!isset($params['passwordpolicylengthmin'])) {
            $params['passwordpolicylengthmin'] = 8;
        }

        if (isset($params['passwordpolicylengthmax']) && $params['passwordpolicylengthmax'] > 20) {
            $params['passwordpolicylengthmax'] = 20;
        }
        if (!isset($params['passwordpolicylengthmax'])) {
            $params['passwordpolicylengthmax'] = 12;
        }

        if (!isset($params['passwordpolicycomplexity'])) {
            $params['passwordpolicycomplexity'] = 'simple';
        }

        if ($params['passwordpolicycomplexity'] === 'simple') {
            $password = $this->secTools->random->base62($params['passwordpolicylengthmin']);

            $this->addResponse('Simple complexity password generate successfully', 0, ['password' => $password]);

            return ['password' => $password];
        } else if ($params['passwordpolicycomplexity'] === 'complex') {
            if ((!isset($params['passwordpolicyuppercase']) &&
                 !isset($params['passwordpolicylowercase']) &&
                 !isset($params['passwordpolicynumbers']) &&
                 !isset($params['passwordpolicysymbols']) &&
                 !isset($params['passwordpolicyavoidsimilar'])) ||
                ((isset($params['passwordpolicyuppercase']) && filter_var($params['passwordpolicyuppercase'], FILTER_VALIDATE_BOOLEAN) == false) &&
                 (isset($params['passwordpolicylowercase']) && filter_var($params['passwordpolicylowercase'], FILTER_VALIDATE_BOOLEAN) == false) &&
                 (isset($params['passwordpolicynumbers']) && filter_var($params['passwordpolicynumbers'], FILTER_VALIDATE_BOOLEAN) == false) &&
                 (isset($params['passwordpolicysymbols']) && filter_var($params['passwordpolicysymbols'], FILTER_VALIDATE_BOOLEAN) == false) &&
                 (isset($params['passwordpolicyavoidsimilar']) && filter_var($params['passwordpolicyavoidsimilar'], FILTER_VALIDATE_BOOLEAN) == false)
                )
            ) {
                $password = $this->secTools->random->base62($params['passwordpolicylengthmin']);

                $this->addResponse('Password generated using default generator as no option was enabled. Select complex complexity options or use simple complexity.', 2, ['password' => $password]);

                return ['password' => $password];
            } else {
                try {
                    $generator = $this->initPasswordGenerator($params);

                    if (!$generator) {
                        return false;
                    }

                    $password = $generator->generatePassword();

                    $this->addResponse('Complex complexity password generate successfully', 0, ['password' => $password]);
                } catch (\Exception $e) {//Generate password using default generator if something fails.
                    $this->logger->log->debug($e->getMessage());

                    $password = $this->secTools->random->base62($params['passwordpolicylengthmin']);

                    $this->addResponse('Password generated using default generator due to an error. Contact developer for more help.', 1, ['password' => $password]);
                }

                return ['password' => $password];
            }
        }

        if (!$password) {
            $this->addResponse('Error generating password', 1);

            return false;
        }
    }

    protected function initPasswordGenerator($params = [])
    {
        $uppercase = $lowercase = $numbers = $symbols = false;
        $uppercaseMinCount = $lowercaseMinCount = $numbersMinCount = $symbolsMinCount = null;
        $uppercaseMaxCount = $lowercaseMaxCount = $numbersMaxCount = $symbolsMaxCount = null;
        $maxCountTotal = 0;

        if (isset($params['passwordpolicyuppercase'])) {
            $uppercase = $params['passwordpolicyuppercase'] == true ? true : false ;
        }
        if (isset($params['passwordpolicylowercase'])) {
            $lowercase = $params['passwordpolicylowercase'] == true ? true : false ;
        }
        if (isset($params['passwordpolicynumbers'])) {
            $numbers = $params['passwordpolicynumbers'] == true ? true : false ;
        }
        if (isset($params['passwordpolicysymbols'])) {
            $symbols = $params['passwordpolicysymbols'] == true ? true : false ;
        }
        if (isset($params['passwordpolicyavoidsimilar'])) {
            $avoid_similar = $params['passwordpolicyavoidsimilar'] == 'true' ? true : false ;
        }
        if (isset($params['passwordpolicyuppercasemincount']) && $params['passwordpolicyuppercasemincount'] !== '') {
            $uppercaseMinCount = (int) abs($params['passwordpolicyuppercasemincount']);
        }
        if (isset($params['passwordpolicyuppercasemaxcount']) && $params['passwordpolicyuppercasemaxcount'] !== '') {
            $uppercaseMaxCount = (int) abs($params['passwordpolicyuppercasemaxcount']);
            $maxCountTotal = $maxCountTotal + $uppercaseMaxCount;
        }
        if (isset($params['passwordpolicylowercasemincount']) && $params['passwordpolicylowercasemincount'] !== '') {
            $lowercaseMinCount = (int) abs($params['passwordpolicylowercasemincount']);
        }
        if (isset($params['passwordpolicylowercasemaxcount']) && $params['passwordpolicylowercasemaxcount'] !== '') {
            $lowercaseMaxCount = (int) abs($params['passwordpolicylowercasemaxcount']);
            $maxCountTotal = $maxCountTotal + $lowercaseMaxCount;
        }
        if (isset($params['passwordpolicynumbersmincount']) && $params['passwordpolicynumbersmincount'] !== '') {
            $numbersMinCount = (int) abs($params['passwordpolicynumbersmincount']);
        }
        if (isset($params['passwordpolicynumbersmaxcount']) && $params['passwordpolicynumbersmaxcount'] !== '') {
            $numbersMaxCount = (int) abs($params['passwordpolicynumbersmaxcount']);
            $maxCountTotal = $maxCountTotal + $numbersMaxCount;
        }
        if (isset($params['passwordpolicysymbolsmincount']) && $params['passwordpolicysymbolsmincount'] !== '') {
            $symbolsMinCount = (int) abs($params['passwordpolicysymbolsmincount']);
        }
        if (isset($params['passwordpolicysymbolsmaxcount']) && $params['passwordpolicysymbolsmaxcount'] !== '') {
            $symbolsMaxCount = (int) abs($params['passwordpolicysymbolsmaxcount']);
            $maxCountTotal = $maxCountTotal + $symbolsMaxCount;
        }

        if ($maxCountTotal !== 0 &&
            ($uppercaseMaxCount && $uppercaseMinCount > $uppercaseMaxCount)
        ) {
            $this->addResponse('Max uppercase value has to be greater than min value.', 1);

            return;
        }
        if ($maxCountTotal !== 0 &&
            ($lowercaseMaxCount && $lowercaseMinCount > $lowercaseMaxCount)
        ) {
            $this->addResponse('Max lowercase value has to be greater than min value.', 1);

            return;
        }
        if ($maxCountTotal !== 0 &&
            ($numbersMaxCount && $numbersMinCount > $numbersMaxCount)
        ) {
            $this->addResponse('Max numbers value has to be greater than min value.', 1);

            return;
        }
        if ($maxCountTotal !== 0 &&
            ($symbolsMaxCount && $symbolsMinCount > $symbolsMaxCount)
        ) {
            $this->addResponse('Max symbols value has to be greater than min value.', 1);

            return;
        }

        if ($maxCountTotal !== 0 &&
            $maxCountTotal < (int) $params['passwordpolicylengthmin'] &&
            $uppercaseMaxCount && $lowercaseMaxCount && $numbersMaxCount && $symbolsMaxCount
        ) {
            $this->addResponse('Max value total has to be greater than minimum length.', 1);

            return;
        }

        if ($maxCountTotal !== 0 &&
            $maxCountTotal > (int) $params['passwordpolicylengthmax'] &&
            $uppercaseMaxCount && $lowercaseMaxCount && $numbersMaxCount && $symbolsMaxCount
        ) {
            $this->addResponse('Max value total has to be smaller than maximum length.', 1);

            return;
        }

        $generator = new RequirementPasswordGenerator();

        $generator
            ->setLength((int) $params['passwordpolicylengthmin'], 'minimum')
            ->setLength((int) $params['passwordpolicylengthmax'], 'maximum')
            ->setOptionValue(RequirementPasswordGenerator::OPTION_UPPER_CASE, $uppercase)
            ->setMinimumCount(RequirementPasswordGenerator::OPTION_UPPER_CASE, $uppercaseMinCount)
            ->setMaximumCount(RequirementPasswordGenerator::OPTION_UPPER_CASE, $uppercaseMaxCount)
            ->setOptionValue(RequirementPasswordGenerator::OPTION_LOWER_CASE, $lowercase)
            ->setMinimumCount(RequirementPasswordGenerator::OPTION_LOWER_CASE, $lowercaseMinCount)
            ->setMaximumCount(RequirementPasswordGenerator::OPTION_LOWER_CASE, $lowercaseMaxCount)
            ->setOptionValue(RequirementPasswordGenerator::OPTION_NUMBERS, $numbers)
            ->setMinimumCount(RequirementPasswordGenerator::OPTION_NUMBERS, $numbersMinCount)
            ->setMaximumCount(RequirementPasswordGenerator::OPTION_NUMBERS, $numbersMaxCount)
            ->setOptionValue(RequirementPasswordGenerator::OPTION_SYMBOLS, $symbols)
            ->setMinimumCount(RequirementPasswordGenerator::OPTION_SYMBOLS, $symbolsMinCount)
            ->setMaximumCount(RequirementPasswordGenerator::OPTION_SYMBOLS, $symbolsMaxCount)
            ->setOptionValue(RequirementPasswordGenerator::OPTION_AVOID_SIMILAR, $avoid_similar)
        ;

        if ($uppercase === true && isset($params['passwordpolicyuppercaseinclude']) && $params['passwordpolicyuppercaseinclude'] !== '') {
            $generator
                ->setParameter(RequirementPasswordGenerator::PARAMETER_UPPER_CASE, $params['passwordpolicyuppercaseinclude'])
            ;
        }
        if ($lowercase === true && isset($params['passwordpolicylowercaseinclude']) && $params['passwordpolicylowercaseinclude'] !== '') {
            $generator
                ->setParameter(RequirementPasswordGenerator::PARAMETER_LOWER_CASE, $params['passwordpolicylowercaseinclude'])
            ;
        }
        if ($numbers === true && isset($params['passwordpolicynumbersinclude']) && $params['passwordpolicynumbersinclude'] !== '') {
            $generator
                ->setParameter(RequirementPasswordGenerator::PARAMETER_NUMBERS, $params['passwordpolicynumbersinclude'])
            ;
        }
        if ($symbols === true && isset($params['passwordpolicysymbolsinclude']) && $params['passwordpolicysymbolsinclude'] !== '') {
            $generator
                ->setParameter(RequirementPasswordGenerator::PARAMETER_SYMBOLS, $params['passwordpolicysymbolsinclude'])
            ;
        }
        if ($avoid_similar === true && isset($params['passwordpolicyavoidsimilarcharacters']) && $params['passwordpolicyavoidsimilarcharacters'] !== '') {
            $generator
                ->setParameter(RequirementPasswordGenerator::PARAMETER_SIMILAR, $params['passwordpolicyavoidsimilarcharacters'])
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
                        if ($i !== 0) {
                            $prefix = $return;
                        }
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

    public function setMicroTimer($reference)
    {
        $microtime['reference'] = $reference;

        if ($this->microtime === 0) {
            $microtime['difference'] = '-';
            $this->microtime = microtime(true);
        } else {
            $now = microtime(true);
            $microtime['difference'] = $now - $this->microtime;
            $this->microtime = $now;
        }

        array_push($this->microTimers, $microtime);
    }

    public function getMicroTimer()
    {
        return $this->microTimers;
    }
}