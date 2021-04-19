<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Services;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Services\XeroPayrollAuApiBaseService;

class XeroPayrollAuApiService extends XeroPayrollAuApiBaseService
{
    protected static $operations =
        [
        'GetEmployees' => [
          'method' => 'GET',
          'resource' => 'Employees',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetEmployeesRestResponse',
          'params' => [
            'If-Modified-Since' => [
              'valid' => [
              ],
            ],
            'where' => [
              'valid' => [
                'string',
              ],
            ],
            'order' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'CreateEmployee' => [
          'method' => 'POST',
          'resource' => 'Employees',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreateEmployeeRestResponse',
          'params' => [
          ],
        ],
        'GetEmployee' => [
          'method' => 'GET',
          'resource' => 'Employees/{EmployeeID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetEmployeeRestResponse',
          'params' => [
            'EmployeeID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateEmployee' => [
          'method' => 'POST',
          'resource' => 'Employees/{EmployeeID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdateEmployeeRestResponse',
          'params' => [
            'EmployeeID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetLeaveApplications' => [
          'method' => 'GET',
          'resource' => 'LeaveApplications',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetLeaveApplicationsRestResponse',
          'params' => [
            'If-Modified-Since' => [
              'valid' => [
              ],
            ],
            'where' => [
              'valid' => [
                'string',
              ],
            ],
            'order' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'CreateLeaveApplication' => [
          'method' => 'POST',
          'resource' => 'LeaveApplications',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreateLeaveApplicationRestResponse',
          'params' => [
          ],
        ],
        'GetLeaveApplication' => [
          'method' => 'GET',
          'resource' => 'LeaveApplications/{LeaveApplicationID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetLeaveApplicationRestResponse',
          'params' => [
            'LeaveApplicationID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateLeaveApplication' => [
          'method' => 'POST',
          'resource' => 'LeaveApplications/{LeaveApplicationID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdateLeaveApplicationRestResponse',
          'params' => [
            'LeaveApplicationID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetPayItems' => [
          'method' => 'GET',
          'resource' => 'PayItems',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayItemsRestResponse',
          'params' => [
            'If-Modified-Since' => [
              'valid' => [
              ],
            ],
            'where' => [
              'valid' => [
                'string',
              ],
            ],
            'order' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'CreatePayItem' => [
          'method' => 'POST',
          'resource' => 'PayItems',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreatePayItemRestResponse',
          'params' => [
          ],
        ],
        'GetPayrollCalendars' => [
          'method' => 'GET',
          'resource' => 'PayrollCalendars',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayrollCalendarsRestResponse',
          'params' => [
            'If-Modified-Since' => [
              'valid' => [
              ],
            ],
            'where' => [
              'valid' => [
                'string',
              ],
            ],
            'order' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'CreatePayrollCalendar' => [
          'method' => 'POST',
          'resource' => 'PayrollCalendars',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreatePayrollCalendarRestResponse',
          'params' => [
          ],
        ],
        'GetPayrollCalendar' => [
          'method' => 'GET',
          'resource' => 'PayrollCalendars/{PayrollCalendarID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayrollCalendarRestResponse',
          'params' => [
            'PayrollCalendarID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetPayRuns' => [
          'method' => 'GET',
          'resource' => 'PayRuns',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayRunsRestResponse',
          'params' => [
            'If-Modified-Since' => [
              'valid' => [
              ],
            ],
            'where' => [
              'valid' => [
                'string',
              ],
            ],
            'order' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'CreatePayRun' => [
          'method' => 'POST',
          'resource' => 'PayRuns',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreatePayRunRestResponse',
          'params' => [
          ],
        ],
        'GetPayRun' => [
          'method' => 'GET',
          'resource' => 'PayRuns/{PayRunID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayRunRestResponse',
          'params' => [
            'PayRunID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdatePayRun' => [
          'method' => 'POST',
          'resource' => 'PayRuns/{PayRunID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdatePayRunRestResponse',
          'params' => [
            'PayRunID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetPayslip' => [
          'method' => 'GET',
          'resource' => 'Payslip/{PayslipID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayslipRestResponse',
          'params' => [
            'PayslipID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdatePayslip' => [
          'method' => 'POST',
          'resource' => 'Payslip/{PayslipID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdatePayslipRestResponse',
          'params' => [
            'PayslipID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetSettings' => [
          'method' => 'GET',
          'resource' => 'Settings',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetSettingsRestResponse',
          'params' => [
          ],
        ],
        'GetSuperfunds' => [
          'method' => 'GET',
          'resource' => 'Superfunds',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetSuperfundsRestResponse',
          'params' => [
            'If-Modified-Since' => [
              'valid' => [
              ],
            ],
            'where' => [
              'valid' => [
                'string',
              ],
            ],
            'order' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'CreateSuperfund' => [
          'method' => 'POST',
          'resource' => 'Superfunds',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreateSuperfundRestResponse',
          'params' => [
          ],
        ],
        'GetSuperfund' => [
          'method' => 'GET',
          'resource' => 'Superfunds/{SuperFundID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetSuperfundRestResponse',
          'params' => [
            'SuperFundID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateSuperfund' => [
          'method' => 'POST',
          'resource' => 'Superfunds/{SuperFundID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdateSuperfundRestResponse',
          'params' => [
            'SuperFundID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetSuperfundProducts' => [
          'method' => 'GET',
          'resource' => 'SuperfundProducts',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetSuperfundProductsRestResponse',
          'params' => [
            'ABN' => [
              'valid' => [
                'string',
              ],
            ],
            'USI' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'GetTimesheets' => [
          'method' => 'GET',
          'resource' => 'Timesheets',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetTimesheetsRestResponse',
          'params' => [
            'If-Modified-Since' => [
              'valid' => [
              ],
            ],
            'where' => [
              'valid' => [
                'string',
              ],
            ],
            'order' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'CreateTimesheet' => [
          'method' => 'POST',
          'resource' => 'Timesheets',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreateTimesheetRestResponse',
          'params' => [
          ],
        ],
        'GetTimesheet' => [
          'method' => 'GET',
          'resource' => 'Timesheets/{TimesheetID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetTimesheetRestResponse',
          'params' => [
            'TimesheetID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateTimesheet' => [
          'method' => 'POST',
          'resource' => 'Timesheets/{TimesheetID}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdateTimesheetRestResponse',
          'params' => [
            'TimesheetID' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
      ];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function getEmployees(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetEmployeesRestRequest $request)
    {
        return $this->getEmployeesAsync($request)->wait();
    }

    public function getEmployeesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetEmployeesRestRequest $request)
    {
        return $this->callOperationAsync('GetEmployees', $request);
    }

    public function createEmployee(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreateEmployeeRestRequest $request)
    {
        return $this->createEmployeeAsync($request)->wait();
    }

    public function createEmployeeAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreateEmployeeRestRequest $request)
    {
        return $this->callOperationAsync('CreateEmployee', $request);
    }

    public function getEmployee(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetEmployeeRestRequest $request)
    {
        return $this->getEmployeeAsync($request)->wait();
    }

    public function getEmployeeAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetEmployeeRestRequest $request)
    {
        return $this->callOperationAsync('GetEmployee', $request);
    }

    public function updateEmployee(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdateEmployeeRestRequest $request)
    {
        return $this->updateEmployeeAsync($request)->wait();
    }

    public function updateEmployeeAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdateEmployeeRestRequest $request)
    {
        return $this->callOperationAsync('UpdateEmployee', $request);
    }

    public function getLeaveApplications(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetLeaveApplicationsRestRequest $request)
    {
        return $this->getLeaveApplicationsAsync($request)->wait();
    }

    public function getLeaveApplicationsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetLeaveApplicationsRestRequest $request)
    {
        return $this->callOperationAsync('GetLeaveApplications', $request);
    }

    public function createLeaveApplication(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreateLeaveApplicationRestRequest $request)
    {
        return $this->createLeaveApplicationAsync($request)->wait();
    }

    public function createLeaveApplicationAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreateLeaveApplicationRestRequest $request)
    {
        return $this->callOperationAsync('CreateLeaveApplication', $request);
    }

    public function getLeaveApplication(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetLeaveApplicationRestRequest $request)
    {
        return $this->getLeaveApplicationAsync($request)->wait();
    }

    public function getLeaveApplicationAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetLeaveApplicationRestRequest $request)
    {
        return $this->callOperationAsync('GetLeaveApplication', $request);
    }

    public function updateLeaveApplication(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdateLeaveApplicationRestRequest $request)
    {
        return $this->updateLeaveApplicationAsync($request)->wait();
    }

    public function updateLeaveApplicationAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdateLeaveApplicationRestRequest $request)
    {
        return $this->callOperationAsync('UpdateLeaveApplication', $request);
    }

    public function getPayItems(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayItemsRestRequest $request)
    {
        return $this->getPayItemsAsync($request)->wait();
    }

    public function getPayItemsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayItemsRestRequest $request)
    {
        return $this->callOperationAsync('GetPayItems', $request);
    }

    public function createPayItem(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreatePayItemRestRequest $request)
    {
        return $this->createPayItemAsync($request)->wait();
    }

    public function createPayItemAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreatePayItemRestRequest $request)
    {
        return $this->callOperationAsync('CreatePayItem', $request);
    }

    public function getPayrollCalendars(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayrollCalendarsRestRequest $request)
    {
        return $this->getPayrollCalendarsAsync($request)->wait();
    }

    public function getPayrollCalendarsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayrollCalendarsRestRequest $request)
    {
        return $this->callOperationAsync('GetPayrollCalendars', $request);
    }

    public function createPayrollCalendar(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreatePayrollCalendarRestRequest $request)
    {
        return $this->createPayrollCalendarAsync($request)->wait();
    }

    public function createPayrollCalendarAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreatePayrollCalendarRestRequest $request)
    {
        return $this->callOperationAsync('CreatePayrollCalendar', $request);
    }

    public function getPayrollCalendar(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayrollCalendarRestRequest $request)
    {
        return $this->getPayrollCalendarAsync($request)->wait();
    }

    public function getPayrollCalendarAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayrollCalendarRestRequest $request)
    {
        return $this->callOperationAsync('GetPayrollCalendar', $request);
    }

    public function getPayRuns(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayRunsRestRequest $request)
    {
        return $this->getPayRunsAsync($request)->wait();
    }

    public function getPayRunsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayRunsRestRequest $request)
    {
        return $this->callOperationAsync('GetPayRuns', $request);
    }

    public function createPayRun(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreatePayRunRestRequest $request)
    {
        return $this->createPayRunAsync($request)->wait();
    }

    public function createPayRunAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreatePayRunRestRequest $request)
    {
        return $this->callOperationAsync('CreatePayRun', $request);
    }

    public function getPayRun(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayRunRestRequest $request)
    {
        return $this->getPayRunAsync($request)->wait();
    }

    public function getPayRunAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayRunRestRequest $request)
    {
        return $this->callOperationAsync('GetPayRun', $request);
    }

    public function updatePayRun(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdatePayRunRestRequest $request)
    {
        return $this->updatePayRunAsync($request)->wait();
    }

    public function updatePayRunAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdatePayRunRestRequest $request)
    {
        return $this->callOperationAsync('UpdatePayRun', $request);
    }

    public function getPayslip(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayslipRestRequest $request)
    {
        return $this->getPayslipAsync($request)->wait();
    }

    public function getPayslipAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetPayslipRestRequest $request)
    {
        return $this->callOperationAsync('GetPayslip', $request);
    }

    public function updatePayslip(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdatePayslipRestRequest $request)
    {
        return $this->updatePayslipAsync($request)->wait();
    }

    public function updatePayslipAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdatePayslipRestRequest $request)
    {
        return $this->callOperationAsync('UpdatePayslip', $request);
    }

    public function getSettings(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetSettingsRestRequest $request)
    {
        return $this->getSettingsAsync($request)->wait();
    }

    public function getSettingsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetSettingsRestRequest $request)
    {
        return $this->callOperationAsync('GetSettings', $request);
    }

    public function getSuperfunds(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetSuperfundsRestRequest $request)
    {
        return $this->getSuperfundsAsync($request)->wait();
    }

    public function getSuperfundsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetSuperfundsRestRequest $request)
    {
        return $this->callOperationAsync('GetSuperfunds', $request);
    }

    public function createSuperfund(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreateSuperfundRestRequest $request)
    {
        return $this->createSuperfundAsync($request)->wait();
    }

    public function createSuperfundAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreateSuperfundRestRequest $request)
    {
        return $this->callOperationAsync('CreateSuperfund', $request);
    }

    public function getSuperfund(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetSuperfundRestRequest $request)
    {
        return $this->getSuperfundAsync($request)->wait();
    }

    public function getSuperfundAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetSuperfundRestRequest $request)
    {
        return $this->callOperationAsync('GetSuperfund', $request);
    }

    public function updateSuperfund(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdateSuperfundRestRequest $request)
    {
        return $this->updateSuperfundAsync($request)->wait();
    }

    public function updateSuperfundAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdateSuperfundRestRequest $request)
    {
        return $this->callOperationAsync('UpdateSuperfund', $request);
    }

    public function getSuperfundProducts(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetSuperfundProductsRestRequest $request)
    {
        return $this->getSuperfundProductsAsync($request)->wait();
    }

    public function getSuperfundProductsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetSuperfundProductsRestRequest $request)
    {
        return $this->callOperationAsync('GetSuperfundProducts', $request);
    }

    public function getTimesheets(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetTimesheetsRestRequest $request)
    {
        return $this->getTimesheetsAsync($request)->wait();
    }

    public function getTimesheetsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetTimesheetsRestRequest $request)
    {
        return $this->callOperationAsync('GetTimesheets', $request);
    }

    public function createTimesheet(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreateTimesheetRestRequest $request)
    {
        return $this->createTimesheetAsync($request)->wait();
    }

    public function createTimesheetAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\CreateTimesheetRestRequest $request)
    {
        return $this->callOperationAsync('CreateTimesheet', $request);
    }

    public function getTimesheet(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetTimesheetRestRequest $request)
    {
        return $this->getTimesheetAsync($request)->wait();
    }

    public function getTimesheetAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\GetTimesheetRestRequest $request)
    {
        return $this->callOperationAsync('GetTimesheet', $request);
    }

    public function updateTimesheet(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdateTimesheetRestRequest $request)
    {
        return $this->updateTimesheetAsync($request)->wait();
    }

    public function updateTimesheetAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Operations\UpdateTimesheetRestRequest $request)
    {
        return $this->callOperationAsync('UpdateTimesheet', $request);
    }
}