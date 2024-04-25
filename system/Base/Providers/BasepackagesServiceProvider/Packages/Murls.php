<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use League\Flysystem\UnableToReadFile;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use Phalcon\Filter\Validation\Validator\Url;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesMurls;

class Murls extends BasePackage
{
    protected $modelToUse = BasepackagesMurls::class;

    protected $packageName = 'murls';

    public $murls;

    public function addMurl($data)
    {
        //
    }

    public function updateMurl($data)
    {
        //
    }

    public function removeMurl($data)
    {
        //
    }

    public function generateMurl($data)
    {
        $this->validation->init()->add('generator_type', PresenceOf::class, ["message" => "Please provider generator type."]);

        if (!$this->doValidation($data)) {
            return false;
        }

        if ($data['generator_type'] === 'random') {
            if (!isset($data['random_characters_length'])) {
                $data['random_characters_length'] = 4;
            }

            $murl = $this->random->base58($data['random_characters_length']);
        } else if ($data['generator_type'] === 'dictionary') {
            if (!isset($data['dictionary_word_separator'])) {
                $data['dictionary_word_separator'] = '-';
            }

            if (isset($data['dictionary_word_separator'])) {
                if ($data['dictionary_word_separator'] === 'dash') {
                    $data['dictionary_word_separator'] = '-';
                } else if ($data['dictionary_word_separator'] === 'underscore') {
                    $data['dictionary_word_separator'] = '_';
                }
            }

            if (!isset($data['dictionary_words_amount'])) {
                $data['dictionary_words_amount'] = 1;
            }
            if (!isset($data['dictionary_word_length'])) {
                $data['dictionary_word_length'] = 3;
            }

            $wordsArr = [];
            $charactersArr = range('a','z');

            for ($i=0; $i < $data['dictionary_words_amount']; $i++) {
                $randomKey = array_rand($charactersArr);

                try {
                    $file = $this->localContent->read('system/Base/Providers/BasepackagesServiceProvider/Packages/Murls/Dictionary/' . $data['dictionary_word_length'] . '/' . $charactersArr[$randomKey] . '.json');
                } catch (\throwable | UnableToReadFile $e) {
                    if ($e->getCode() === 2) {
                        continue;
                    }

                    throw $e;
                }

                $fileArr = $this->helper->decode($file, true);

                array_push($wordsArr, $fileArr[array_rand($fileArr)]);
            }

            $murl = implode($data['dictionary_word_separator'], $wordsArr);
        }

        $this->addResponse('Generated mURL', 0, ['murl' => $murl]);

        return true;
    }

    protected function doValidation($data)
    {
        // $this->validation->init()->add('app_id', PresenceOf::class, ["message" => "Please provide app information."]);
        // $this->validation->add('domain_id', PresenceOf::class, ["message" => "Please provide domain information."]);
        // $this->validation->add('url', PresenceOf::class, ["message" => "Please provide URL."]);

        // if (!$this->doValidation($data)) {
        //     return false;
        // }

        // $this->validation->init()->add('url', Url::class, ["message" => "VALID."]);//We dont want valid URL

        // if ($this->doValidation($data)) {//URL is valid which is incorrect as we dont want URL with domain or http
        //     $this->addResponse('Please provide URL without http scheme or domain.', 1);

        //     return false;
        // }

        $validated = $this->validation->validate($data)->jsonSerialize();

        if (count($validated) > 0) {
            $messages = 'Error: ';

            foreach ($validated as $key => $value) {
                $messages .= $value['message'] . ' ';
            }

            $this->addResponse($messages, 1);

            return false;
        }

        return true;
    }
}