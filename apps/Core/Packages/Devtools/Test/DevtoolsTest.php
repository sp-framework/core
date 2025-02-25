<?php

namespace Apps\Core\Packages\Devtools\Test;

use Apps\Core\Packages\Devtools\Test\Model\DevtoolsTest as DevtoolsTestModel;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToRetrieveMetadata;
use System\Base\BasePackage;
use System\Base\Providers\DatabaseServiceProvider\Sqlite;

class DevtoolsTest extends BasePackage
{
    protected $modelToUse = DevtoolsTestModel::class;

    protected $packageName = 'devtoolstest';

    public $devtoolstest;

    protected $sourceDir = 'apps/Core/Packages/Devtools/Test/Data/';

    protected $sourceLink = 'https://github.com/captn3m0/historical-mf-data/releases/latest/download/funds.db.zst';

    protected $trackCounter = 0;

    public $method;

    protected $now;

    public function onConstruct()
    {
        if (!is_dir(base_path($this->sourceDir))) {
            if (!mkdir(base_path($this->sourceDir), 0777, true)) {
                return false;
            }
        }

        //Increase Exectimeout to 3 hours as this process takes time to extract and merge data.
        if ((int) ini_get('max_execution_time') < 10800) {
            set_time_limit(10800);
        }

        //Increase memory_limit to 2G as the process takes a bit of memory to process the array.
        if ((int) ini_get('memory_limit') < 1024) {
            ini_set('memory_limit', '1024M');
        }

        $this->now = \Carbon\Carbon::now();

        parent::onConstruct();
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this, $method)) {
            $this->basepackages->progress->updateProgress($method, null, false);

            $call = call_user_func_array([$this, $method], $arguments);

            $callResult = $call;

            if ($call !== false) {
                $call = true;
            }

            $this->basepackages->progress->updateProgress($method, $call, false);

            return $callResult;
        }
    }

    protected function testTest()
    {
        sleep(1);

        return true;
    }

    protected function testDownload()
    {
        $today = $this->now->toDateString();

        try {
            //File is already extracted
            if ($this->localContent->fileExists($this->sourceDir . $today . '-funds.db')) {
                return true;
            }

            //File is already downloaded
            if ($this->localContent->fileExists($this->sourceDir . $today . '-funds.db.zst')) {
                return true;
            }
        } catch (FilesystemException | UnableToCheckExistence | UnableToRetrieveMetadata | \throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        $this->method = 'testDownload';

        return $this->downloadData($this->sourceLink, base_path($this->sourceDir) . $today . '-funds.db.zst');
    }

    protected function downloadData($url, $sink)
    {
        $download = $this->remoteWebContent->request(
            'GET',
            $url,
            [
                'progress' => function(
                    $downloadTotal,
                    $downloadedBytes,
                    $uploadTotal,
                    $uploadedBytes
                ) {
                    $counters =
                            [
                                'downloadTotal'     => $downloadTotal,
                                'downloadedBytes'   => $downloadedBytes,
                                'uploadTotal'       => $uploadTotal,
                                'uploadedBytes'     => $uploadedBytes
                            ];

                    if ($downloadedBytes === 0) {
                        return;
                    }

                    //Trackcounter is needed as guzzelhttp runs this in a while loop causing too many updates with same download count.
                    //So this way, we only update progress when there is actually an update.
                    if ($downloadedBytes === $this->trackCounter) {
                        return;
                    }

                    $this->trackCounter = $downloadedBytes;

                    if ($downloadedBytes === $downloadTotal) {
                        $this->basepackages->progress->updateProgress($this->method, true, false, null, $counters);
                    } else {
                        $this->basepackages->progress->updateProgress($this->method, null, false, null, $counters);
                    }
                },
                'verify'            => false,
                'connect_timeout'   => 5,
                'sink'              => $sink
            ]
        );

        $this->trackCounter = 0;

        if ($download->getStatusCode() === 200) {
            return true;
        }

        $this->addResponse('Download resulted in : ' . $download->getStatusCode(), 1);

        return false;
    }

    protected function testProcess()
    {
        $this->method = 'testProcess';

        $today = $this->now->toDateString();

        try {
            //File is already extracted
            if (!$this->localContent->fileExists($this->sourceDir . $today . '-funds.db')) {
                $this->addResponse('File not downloaded and extracted correctly!', 1);

                return false;
            }
        } catch (FilesystemException | UnableToCheckExistence | \throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        try {
            $sqlite = (new Sqlite())->init(base_path($this->sourceDir . $today . '-funds.db'));
        } catch (\throwable $e) {
            $this->addResponse('Unable to open database file', 1);

            return false;
        }

        //Test
        // try {
        //     $this->basepackages->utils->setMicroTimer('DBStart', true);

        //     $isinNavs =
        //         $sqlite->query(
        //             "SELECT * from nav N
        //             JOIN securities S ON N.scheme_code = S.scheme_code
        //             WHERE S.isin = 'INF760K01FV4'
        //             AND N.date >= '2000-01-01'
        //             ORDER BY N.date DESC"
        //         )->fetchAll(Enum::FETCH_ASSOC);

        //     $this->basepackages->utils->setMicroTimer('DBStop', true);
        //     trace(varsToDump : [$this->basepackages->utils->getMicroTimer()], exit: false, dumpTraces: false);

        //     $this->basepackages->utils->resetMicroTimer();

        //     $this->basepackages->utils->setMicroTimer('FFReadStart', true);
        //     $this->navsPackage = new MfNavs;
        //     $new = $this->navsPackage->getMfNavsByIsin('INF760K01FV4');
        //     $this->basepackages->utils->setMicroTimer('FFReadStop', true);
        //     trace(varsToDump : [$this->basepackages->utils->getMicroTimer()], exit: false, dumpTraces: false);

        //     $this->basepackages->utils->resetMicroTimer();

        //     $this->basepackages->utils->setMicroTimer('FFReadStart2', true);
        //     $this->navsPackage = new MfNavs;
        //     $new = $this->navsPackage->getById(1);
        //     $this->basepackages->utils->setMicroTimer('FFReadStop2', true);
        //     trace(varsToDump : [$this->basepackages->utils->getMicroTimer()], exit: false, dumpTraces: false);
        //     die();
        // } catch (\throwable $e) {
        //     trace([$e]);
        // }
        //Test

        try {
            $this->navsPackage = new MfNavs;

            $isins = $sqlite->query("SELECT * from securities")->fetchAll(Enum::FETCH_ASSOC);

            if ($isins && count($isins) > 0) {
                $isinsTotal = 1000;
                foreach ($isins as $key => $isin) {
                    $this->basepackages->utils->setMicroTimer('Start');
                    $dbIsin = $this->navsPackage->getMfNavsByIsin($isin['isin']);

                    // $lastUpdated = $this->now->subDay(1)->toDateString();
                    $lastUpdated = '2000-01-01';

                    if (!$dbIsin) {
                        $dbIsin = [];
                        $dbIsin['type'] = $isin['type'];
                        $dbIsin['scheme_code'] = $isin['scheme_code'];
                        $dbIsin['isin'] = $isin['isin'];
                        $dbIsin['navs'] = [];
                    } else {
                        $lastUpdated = $dbIsin['last_updated'];
                    }

                    $isin = $isin['isin'];
                    // $this->basepackages->utils->setMicroTimer('DBStart', true);
                    $isinNavs =
                        $sqlite->query(
                            "SELECT * from nav N
                            JOIN securities S ON N.scheme_code = S.scheme_code
                            WHERE S.isin = '$isin'
                            AND N.date >= '$lastUpdated'
                            ORDER BY N.date ASC"
                        )->fetchAll(Enum::FETCH_ASSOC);

                    // $this->basepackages->utils->setMicroTimer('DBStop', true);
                    // trace(varsToDump : [$this->basepackages->utils->getMicroTimer()], exit: false, dumpTraces: false);
                    if ($isinNavs && count($isinNavs) > 0) {
                        $dbIsin['last_updated'] = $this->helper->last($isinNavs)['date'];
                        $dbIsin['latest_nav'] = $this->helper->last($isinNavs)['nav'];
                        foreach ($isinNavs as $isinNav) {
                            $dbIsin['navs'][$isinNav['date']] = $isinNav['nav'];
                        }
                    }

                    if (isset($dbIsin['id'])) {
                        $this->navsPackage->update($dbIsin);
                    } else {
                        $this->navsPackage->addMfNavs($dbIsin);
                    }
                    // $this->basepackages->utils->setMicroTimer('FFStop', true);
                    // trace(varsToDump : [$this->basepackages->utils->getMicroTimer()], exit: false, dumpTraces: false);

                    // $this->basepackages->utils->setMicroTimer('FFReadStart', true);
                    // $new = $this->navsPackage->getMfNavsByIsin($isin);
                    $this->basepackages->utils->setMicroTimer('End');

                    $time = $this->basepackages->utils->getMicroTimer();

                    if ($time && isset($time[1]['difference']) && $time[1]['difference'] !== 0) {
                        $totalTime = date("H:i:s", floor($time[1]['difference'] * ($isinsTotal - $key)));
                        // $this->basepackages->utils->formatMicrotime($time[1]['difference'] * $isinsTotal);
                    }

                    $this->basepackages->utils->resetMicroTimer();
                    // trace(varsToDump : [$this->basepackages->utils->getMicroTimer()], exit: true, dumpTraces: false);
                    $this->basepackages->progress->updateProgress(
                        method: $this->method,
                        counters: ['stepsTotal' => $isinsTotal, 'stepsCurrent' => ($key + 1)],
                        text: 'Time remaining : ' . $totalTime . '...'
                    );
                    if ($key === 1000) {
                        return true;
                    }
                }
            }
        } catch (\throwable $e) {
            trace([$e]);
        }

        return true;
        // $statement = $sqlite->prepare('SELECT * from nav LIMIT 0,1');
        // trace([$sqlite->query('SELECT * from securities LIMIT 0,10')->fetchAll(Enum::FETCH_ASSOC)]);
        // trace([$sqlite->query("SELECT * from nav N JOIN securities S ON N.scheme_code = S.scheme_code WHERE S.isin = 'INF209KB1ZL4' LIMIT 0,10")->fetchAll(Enum::FETCH_ASSOC)]);
        // trace([$sqlite->query("SELECT * from nav N JOIN securities S ON N.scheme_code = S.scheme_code WHERE S.isin = 'INF209KB1ZL4'")->fetchAll(Enum::FETCH_ASSOC)]);
    }

    // public function getDevtoolsTestById($id)
    // {
    //     $devtoolstest = $this->getById($id);

    //     if ($devtoolstest) {
    //         //
    //         $this->addResponse('Success');

    //         return;
    //     }

    //     $this->addResponse('Error', 1);
    // }

    // public function addDevtoolsTest($data)
    // {
    //     //
    // }

    // public function updateDevtoolsTest($data)
    // {
    //     $devtoolstest = $this->getById($id);

    //     if ($devtoolstest) {
    //         //
    //         $this->addResponse('Success');

    //         return;
    //     }

    //     $this->addResponse('Error', 1);
    // }

    // public function removeDevtoolsTest($data)
    // {
    //     $devtoolstest = $this->getById($id);

    //     if ($devtoolstest) {
    //         //
    //         $this->addResponse('Success');

    //         return;
    //     }

    //     $this->addResponse('Error', 1);
    // }
}