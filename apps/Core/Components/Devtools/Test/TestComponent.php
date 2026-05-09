<?php

namespace Apps\Core\Components\Devtools\Test;

// use Apps\Core\Packages\Devtools\DicExtractData\DevtoolsDicExtractData;
// use Apps\Core\Packages\Devtools\Test\DevtoolsTest;
use System\Base\BaseComponent;

class TestComponent extends BaseComponent
{
    protected $testPackage;

    public function initialize()
    {
        // $this->testPackage = new DevtoolsTest;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        // $table = new \XBase\TableReader(base_path('apps/Fintech/Packages/Mf/Tools/Extractdata/Data/BaggaCarriers/BILL/FOXUSER.DBF'));
        // trace([$table->nextRecord()->getData()]);
        // while ($record = $table->nextRecord()) {
        //     var_dump($record->get('custname'));
        // }
        // trace(['me']);
        // Formula
        // A = P * (1 + r/n)^(n*t)
        // where:
        // A = Maturity amount
        // P = Principal amount (Initial deposit)
        // r = Annual interest rate (in decimal, e.g., 6.6% = 0.066)
        // n = Number of times interest is compounded per year (i.e., if interest is compounded quarterly, n=4)
        // t = Tenure of the FD in years
        // Maturity Amount:  1132181

        // $initial = 1000000.00;
        // $interest = 6.45 / 100;
        // $startDate = \Carbon\Carbon::parse('2025-09-01');
        // $diffInYears = $startDate->diffInYears($startDate->copy()->addYears(1));
        // $diffInQuarters = $startDate->diffInQuarters($startDate->copy()->addYears(1));

        // Step 1:
        // $num = 1 + ($interest / $diffInQuarters);
        // $exponent = $diffInQuarters * $diffInYears;
        // $A = round($initial * pow($num, $exponent));
        // var_dump($diffInYears, $diffInQuarters, $num, $exponent, $A);
        //

        $rateOfInterest = 6.25/100;
        $t = 1;
        $numberOfTimesToCompound = 4;
        $amount = $principal = 1000000;
        $compoundPeriod = $t * $numberOfTimesToCompound;

        $startDate = \Carbon\Carbon::parse('2025-09-01');
        $endDate = \Carbon\Carbon::parse('2026-09-01');

        $diffInDays = $startDate->diffInDays($endDate);
        $principalReturnAsPerInterest = $principal * $rateOfInterest;
        $perDayInterest = round($principalReturnAsPerInterest / $diffInDays, 3);


        if ($startDate->day === 1) {
            $daysInMonth = $startDate->daysInMonth;
        } else {
            // $daysInMonth = $startDate->daysInMonth;
            //
        }
        $monthInterest[$startDate->copy()->endOfMonth()->toDateString()] = round($daysInMonth * $perDayInterest, 2);

        trace([$monthInterest, $daysInMonth, $perDayInterest, $principalReturnAsPerInterest, $diffInDays]);
        // trace([])
        for ($i = 0; $i < $compoundPeriod; $i++) {
            $amount += round(($amount * $rateOfInterest) / $numberOfTimesToCompound);
            var_dump($amount);
        }
        trace([$amount]);
        $compoundInterest = $amount - $principal;
        trace([$compoundInterest]);

        return;


        try {
            $files = $this->basepackages->utils->scanDir('.ff/sp/apps_fintech_mf_schemes/data/');
            sort($files['files']);

            foreach ($files['files'] as $file) {
                $schemeFile = $file;
                $dbNavFile = str_replace('apps_fintech_mf_schemes', 'apps_fintech_mf_schemes_navs', $file);
                // $rrFile = str_replace('apps_fintech_mf_schemes', 'apps_fintech_mf_schemes_navs_rolling_returns', $file);
                $scheme = $this->helper->decode($this->localContent->read($schemeFile), true);
                $dbNav = $this->helper->decode($this->localContent->read($dbNavFile), true);

                // if ($this->localContent->fileExists($rrFile)) {
                //     $rr = $this->helper->decode($this->localContent->read($rrFile), true);
                // } else {
                //     $rr = [];
                //     $rr['id'] = $scheme['id'];
                //     $rr['last_updated'] = $scheme['navs_last_updated'];
                // }

                // if (isset($dbNav['navs']) && count($dbNav['navs']) < 365) {
                //     // return true;
                //     continue;
                // }

                // foreach ($dbNav['navs'] as $date => $nav) {
                //     foreach (['year', 'two_year', 'three_year', 'five_year', 'ten_year', 'fifteen_year'] as $rrTerm) {
                //         try {
                //             $add = $startDate = \Carbon\Carbon::parse($date);

                //             if ($rrTerm === 'year') {
                //                 $add = $add->addYear()->toDateString();
                //             } else if ($rrTerm === 'two_year') {
                //                 $add = $add->addYear(2)->toDateString();
                //             } else if ($rrTerm === 'three_year') {
                //                 $add = $add->addYear(3)->toDateString();
                //             } else if ($rrTerm === 'five_year') {
                //                 $add = $add->addYear(5)->toDateString();
                //             } else if ($rrTerm === 'ten_year') {
                //                 $add = $add->addYear(10)->toDateString();
                //             } else if ($rrTerm === 'fifteen_year') {
                //                 $add = $add->addYear(15)->toDateString();
                //             }

                //             if (isset($rr[$rrTerm][$date])) {
                //                 continue;
                //             }

                //             if (isset($dbNav['navs'][$add])) {
                //                 if (!isset($rr[$rrTerm])) {
                //                     $rr[$rrTerm] = [];
                //                 }

                //                 $rr[$rrTerm][$date]['from'] = $date;
                //                 $rr[$rrTerm][$date]['to'] = $add;
                //                 $rr[$rrTerm][$date]['cagr'] =
                //                     numberFormatPrecision((pow(($dbNav['navs'][$add]['nav']/$nav['nav']),(1/1)) - 1) * 100);
                //             }
                //         } catch (\throwable $e) {
                //             trace([$e]);
                //         }
                //     }
                // }

                // try {
                //     $this->localContent->write('.ff/sp/apps_fintech_mf_schemes_navs_rolling_returns/data/' . $rr['id'] . '.json', $this->helper->encode($rr));
                // } catch (FilesystemException | UnableToWriteFile | \throwable $e) {
                //     $this->addResponse($e->getMessage(), 1);

                //     return false;
                // }
                $scheme['latest_nav'] = $this->helper->last($dbNav['navs'])['nav'];
                try {
                    $this->localContent->write('.ff/sp/apps_fintech_mf_schemes/data/' . $scheme['id'] . '.json', $this->helper->encode($scheme));
                } catch (FilesystemException | UnableToWriteFile | \throwable $e) {
                    $this->addResponse($e->getMessage(), 1);

                    return false;
                }
            }
        } catch (\throwable $e) {
            trace([$e]);
        }

        tracE(['me']);

        // $hierarhies = new \Apps\Fintech\Packages\Accounting\Tools\Accountshierarchies\AccountingToolsAccountshierarchies;
        // $hierarhies->processGnucashAccounts();

        // $countries = $this->usePackage(\System\Base\Providers\BasepackagesServiceProvider\Packages\Geo\GeoCountries::class);

        // trace([$countries->getLastInsertedId()]);
        // $countries = new \System\Base\Installer\Packages\Setup\Register\Basepackages\Geo\Countries;

        // $countries->register($this->db, $this->ff, $this->localContent, $this->helper);
        // $countries->downloadSelectedCountryStatesAndCities($this->ff, $this->localContent, $this->remoteWebContent, 'IND', $this->basepackages->progress);
        // $countries->registerSelectedCountryStatesAndCities($this->ff, $this->localContent, 'IND', false, $this->helper);
        // trace([$countries]);

        return false;
    }

    /**
     * @api_acl(name=view)
     */
    public function apiViewAction()
    {
        $this->addResponse('Test', 0, ['connection' => $this->connection->getId(), 'session' => $this->session->getId()]);
    }

    public function addAction()
    {
        //
    }

    /**
     * @api_acl(name=add)
     */
    public function apiAddAction()
    {
        $this->addResponse('Test', 0, ['add' => true]);
    }

    public function updateAction()
    {
        //
    }

    /**
     * @api_acl(name=update)
     */
    public function apiUpdateAction()
    {
        $this->addResponse('Test', 0, ['update' => true]);
    }

    public function removeAction()
    {
        //
    }

    /**
     * @api_acl(name=remove)
     */
    public function apiRemoveAction()
    {
        $this->addResponse('Test', 0, ['remove' => true]);
    }

    public function testAction()
    {
        if ($this->basepackages->progress->checkProgressFile()) {
            $this->basepackages->progress->deleteProgressFile();
        }

        $this->basepackages->progress->registerMethods(
            [
                [
                    'method'    => 'testTest',
                    'text'      => 'Test',
                ],
                [
                    'method'    => 'testTest',
                    'text'      => 'Test',
                ],
                [
                    'method'    => 'testTest',
                    'text'      => 'Test',
                ],
                [
                    'method'    => 'testDownload',
                    'text'      => 'Download Data...',
                    'remoteWeb' => true
                ],
                // [
                //     'method'    => 'testProcess',
                //     'text'      => 'Process Data...',
                //     'steps'     => true
                // ]
            ]
        );

        $this->testPackage->testTest();
        $this->testPackage->testTest();
        $this->testPackage->testTest();
        $this->testPackage->testDownload();

        $this->addResponse(
            $this->testPackage->packagesData->responseMessage,
            $this->testPackage->packagesData->responseCode
        );
    }
}