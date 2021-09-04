<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Attachments;

use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Attachments\Model\SystemApiXeroAttachments;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFilesRestRequest;
use System\Base\BasePackage;

class Attachments extends BasePackage
{
    protected $apiPackage;

    protected $api;

    protected $xeroApi;

    protected $request;

    public function sync($apiId = null, $xeroPackage = null, $xeroPackageRowId = null, array $attachments = null)
    {
        if ($attachments) {
            $this->addUpdateXeroAttachments($apiId, $xeroPackage, $xeroPackageRowId, $attachments);

            return;
        }

        $this->apiPackage = new Api;

        $this->request = new GetFilesRestRequest;

        $xeroApis = $this->apiPackage->getApiByType('xero', true);

        if (!$apiId) {
            foreach ($xeroApis as $key => $xeroApi) {
                $this->syncWithXero($xeroApi['api_id']);
            }
        } else {
            $this->syncWithXero($apiId);
        }
    }

    protected function syncWithXero($apiId)
    {
        $this->api = $this->apiPackage->useApi(['api_id' => $apiId]);

        $this->xeroApi = $this->api->useService('XeroFilesApi');

        // $this->api->refreshXeroCallStats($response->getHeaders());
        //
    }

    public function addUpdateXeroAttachments($apiId = null, $xeroPackage = null, $xeroPackageRowId = null, array $attachments)
    {
        if (count($attachments) > 0) {
            foreach ($attachments as $attachmentKey => $attachment) {
                $model = SystemApiXeroAttachments::class;

                $xeroAttachment = $model::findFirst(
                    [
                        'conditions'    => 'AttachmentID = :aid:',
                        'bind'          =>
                            [
                                'aid'   => $attachment['AttachmentID']
                            ]
                    ]
                );

                $attachment['api_id'] = $apiId;
                $attachment['xero_package'] = $xeroPackage;
                $attachment['xero_package_row_id'] = $xeroPackageRowId;

                if (!$xeroAttachment) {
                    $modelToUse = new $model();

                    $modelToUse->assign($attachment);

                    $modelToUse->create();
                } else {
                    $xeroAttachment->assign($attachment);

                    $xeroAttachment->update();
                }
            }
        }
    }

    public function reSync()
    {
        //
    }

    public function syncWithLocal()
    {
        //
    }
}