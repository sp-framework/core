<?php

namespace Apps\Core\Packages\Devtools\DicExtractData;

use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use System\Base\BasePackage;

class DevtoolsDicExtractData extends BasePackage
{
    //Download dic data from https://raw.githubusercontent.com/dwyl/english-words/master/words_alpha.txt and store in data folder
    //call processDicData from test component.
    protected $sourceDir = 'apps/Core/Packages/Devtools/DicExtractData/Data/';
    protected $sourceFile = 'apps/Core/Packages/Devtools/DicExtractData/Data/words_alpha.txt';

    public function processDicData()
    {
        try {
            $words = $this->localContent->readStream($this->sourceFile);
        } catch (\throwable | UnableToReadFile $e) {
            throw $e;
        }

        $alphas = range('a','z');

        $wordsArr = [];

        while(!feof($words)) {
            $word = stream_get_line($words, 0, "\n");

            foreach ($alphas as $alpha) {
                if (str_starts_with($word, $alpha)) {
                    $stringLength = strlen($word) - 1;

                    if (!isset($wordsArr[$stringLength][$alpha])) {
                        $wordsArr[$stringLength][$alpha] = [];
                    }
                    array_push($wordsArr[$stringLength][$alpha], trim($word));
                }
            }
        }

        foreach ($wordsArr as $length => $chars) {
            foreach ($chars as $charKey => $charValue) {
                try {
                    $this->localContent->write($this->sourceDir . $length . '/' . $charKey . '.json', $this->helper->encode($chars[$charKey]));
                } catch (\throwable | UnableToWriteFile $e) {
                    throw $e;
                }
            }
        }

        fclose($words);
    }
}