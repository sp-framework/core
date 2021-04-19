<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Services;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Services\XeroFilesApiBaseService;

class XeroFilesApiService extends XeroFilesApiBaseService
{
    protected static $operations =
        [
        'GetFiles' => [
          'method' => 'GET',
          'resource' => 'Files',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFilesRestResponse',
          'params' => [
            'pagesize' => [
              'valid' => [
          'integer',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'sort' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'UploadFile' => [
          'method' => 'POST',
          'resource' => 'Files',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\UploadFileRestResponse',
          'params' => [
            'FolderId' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'GetFile' => [
          'method' => 'GET',
          'resource' => 'Files/{FileId}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFileRestResponse',
          'params' => [
            'FileId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateFile' => [
          'method' => 'PUT',
          'resource' => 'Files/{FileId}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\UpdateFileRestResponse',
          'params' => [
            'FileId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'DeleteFile' => [
          'method' => 'DELETE',
          'resource' => 'Files/{FileId}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\DeleteFileRestResponse',
          'params' => [
            'FileId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetFileContent' => [
          'method' => 'GET',
          'resource' => 'Files/{FileId}/Content',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFileContentRestResponse',
          'params' => [
            'FileId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetFileAssociations' => [
          'method' => 'GET',
          'resource' => 'Files/{FileId}/Associations',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFileAssociationsRestResponse',
          'params' => [
            'FileId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateFileAssociation' => [
          'method' => 'POST',
          'resource' => 'Files/{FileId}/Associations',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\CreateFileAssociationRestResponse',
          'params' => [
            'FileId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'DeleteFileAssociation' => [
          'method' => 'DELETE',
          'resource' => 'Files/{FileId}/Associations/{ObjectId}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\DeleteFileAssociationRestResponse',
          'params' => [
            'FileId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'ObjectId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetAssociationsByObject' => [
          'method' => 'GET',
          'resource' => 'Associations/{ObjectId}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetAssociationsByObjectRestResponse',
          'params' => [
            'ObjectId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetFolders' => [
          'method' => 'GET',
          'resource' => 'Folders',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFoldersRestResponse',
          'params' => [
            'sort' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'CreateFolder' => [
          'method' => 'POST',
          'resource' => 'Folders',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\CreateFolderRestResponse',
          'params' => [
          ],
        ],
        'GetFolder' => [
          'method' => 'GET',
          'resource' => 'Folders/{FolderId}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFolderRestResponse',
          'params' => [
            'FolderId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateFolder' => [
          'method' => 'PUT',
          'resource' => 'Folders/{FolderId}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\UpdateFolderRestResponse',
          'params' => [
            'FolderId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'DeleteFolder' => [
          'method' => 'DELETE',
          'resource' => 'Folders/{FolderId}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\DeleteFolderRestResponse',
          'params' => [
            'FolderId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetInbox' => [
          'method' => 'GET',
          'resource' => 'Inbox',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetInboxRestResponse',
          'params' => [
          ],
        ],
      ];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function getFiles(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFilesRestRequest $request)
    {
        return $this->getFilesAsync($request)->wait();
    }

    public function getFilesAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFilesRestRequest $request)
    {
        return $this->callOperationAsync('GetFiles', $request);
    }

    public function uploadFile(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\UploadFileRestRequest $request)
    {
        return $this->uploadFileAsync($request)->wait();
    }

    public function uploadFileAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\UploadFileRestRequest $request)
    {
        return $this->callOperationAsync('UploadFile', $request);
    }

    public function getFile(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFileRestRequest $request)
    {
        return $this->getFileAsync($request)->wait();
    }

    public function getFileAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFileRestRequest $request)
    {
        return $this->callOperationAsync('GetFile', $request);
    }

    public function updateFile(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\UpdateFileRestRequest $request)
    {
        return $this->updateFileAsync($request)->wait();
    }

    public function updateFileAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\UpdateFileRestRequest $request)
    {
        return $this->callOperationAsync('UpdateFile', $request);
    }

    public function deleteFile(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\DeleteFileRestRequest $request)
    {
        return $this->deleteFileAsync($request)->wait();
    }

    public function deleteFileAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\DeleteFileRestRequest $request)
    {
        return $this->callOperationAsync('DeleteFile', $request);
    }

    public function getFileContent(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFileContentRestRequest $request)
    {
        return $this->getFileContentAsync($request)->wait();
    }

    public function getFileContentAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFileContentRestRequest $request)
    {
        return $this->callOperationAsync('GetFileContent', $request);
    }

    public function getFileAssociations(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFileAssociationsRestRequest $request)
    {
        return $this->getFileAssociationsAsync($request)->wait();
    }

    public function getFileAssociationsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFileAssociationsRestRequest $request)
    {
        return $this->callOperationAsync('GetFileAssociations', $request);
    }

    public function createFileAssociation(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\CreateFileAssociationRestRequest $request)
    {
        return $this->createFileAssociationAsync($request)->wait();
    }

    public function createFileAssociationAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\CreateFileAssociationRestRequest $request)
    {
        return $this->callOperationAsync('CreateFileAssociation', $request);
    }

    public function deleteFileAssociation(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\DeleteFileAssociationRestRequest $request)
    {
        return $this->deleteFileAssociationAsync($request)->wait();
    }

    public function deleteFileAssociationAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\DeleteFileAssociationRestRequest $request)
    {
        return $this->callOperationAsync('DeleteFileAssociation', $request);
    }

    public function getAssociationsByObject(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetAssociationsByObjectRestRequest $request)
    {
        return $this->getAssociationsByObjectAsync($request)->wait();
    }

    public function getAssociationsByObjectAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetAssociationsByObjectRestRequest $request)
    {
        return $this->callOperationAsync('GetAssociationsByObject', $request);
    }

    public function getFolders(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFoldersRestRequest $request)
    {
        return $this->getFoldersAsync($request)->wait();
    }

    public function getFoldersAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFoldersRestRequest $request)
    {
        return $this->callOperationAsync('GetFolders', $request);
    }

    public function createFolder(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\CreateFolderRestRequest $request)
    {
        return $this->createFolderAsync($request)->wait();
    }

    public function createFolderAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\CreateFolderRestRequest $request)
    {
        return $this->callOperationAsync('CreateFolder', $request);
    }

    public function getFolder(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFolderRestRequest $request)
    {
        return $this->getFolderAsync($request)->wait();
    }

    public function getFolderAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetFolderRestRequest $request)
    {
        return $this->callOperationAsync('GetFolder', $request);
    }

    public function updateFolder(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\UpdateFolderRestRequest $request)
    {
        return $this->updateFolderAsync($request)->wait();
    }

    public function updateFolderAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\UpdateFolderRestRequest $request)
    {
        return $this->callOperationAsync('UpdateFolder', $request);
    }

    public function deleteFolder(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\DeleteFolderRestRequest $request)
    {
        return $this->deleteFolderAsync($request)->wait();
    }

    public function deleteFolderAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\DeleteFolderRestRequest $request)
    {
        return $this->callOperationAsync('DeleteFolder', $request);
    }

    public function getInbox(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetInboxRestRequest $request)
    {
        return $this->getInboxAsync($request)->wait();
    }

    public function getInboxAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations\GetInboxRestRequest $request)
    {
        return $this->callOperationAsync('GetInbox', $request);
    }
}