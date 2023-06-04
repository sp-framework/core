<?php

namespace System\Base\Providers\DatabaseServiceProvider\Ff\Classes;

use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\IOException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Exceptions\InvalidArgumentException;
use System\Base\Providers\DatabaseServiceProvider\Ff\Query;
use System\Base\Providers\DatabaseServiceProvider\Ff\Store;

class DocumentUpdater
{
    protected $storePath;

    protected $primaryKey;

    public function __construct(string $storePath, string $primaryKey)
    {
        $this->storePath = $storePath;

        $this->primaryKey = $primaryKey;
    }

    public function updateResults(array $results, array $updatable, bool $returnUpdatedDocuments)
    {
        if (count($results) === 0) {
            return false;
        }

        foreach ($results as $key => $data) {
            $primaryKeyValue = IoHelper::secureStringForFileAccess($data[$this->primaryKey]);

            $data[$this->primaryKey] = (int) $primaryKeyValue;

            $results[$key] = $data;

            $filePath = $this->getDataPath() . $primaryKeyValue . '.json';

            if (!file_exists($filePath)) {
                return false;
            }
        }

        foreach ($results as $key => $data) {
            $filePath = $this->getDataPath() . $data[$this->primaryKey] . '.json';

            foreach ($updatable as $fieldName => $value) {
                if ($fieldName !== $this->primaryKey) {
                    NestedHelper::updateNestedValue($fieldName, $data, $value);
                }
            }

            IoHelper::writeContentToFile($filePath, json_encode($data));

            $results[$key] = $data;
        }

        return ($returnUpdatedDocuments === true) ? $results : true;
    }

    public function deleteResults(array $results, int $returnOption)
    {
        switch ($returnOption) {
            case Query::DELETE_RETURN_BOOL:
                $returnValue = !empty($results);
                break;
            case Query::DELETE_RETURN_COUNT:
                $returnValue = count($results);
                break;
            case Query::DELETE_RETURN_RESULTS:
                $returnValue = $results;
                break;
            default:
                throw new InvalidArgumentException("Return option \"$returnOption\" is not supported");
        }

        if (empty($results)) {
            return $returnValue;
        }

        foreach ($results as $key => $data) {
            $primaryKeyValue = IoHelper::secureStringForFileAccess($data[$this->primaryKey]);

            $filePath = $this->getDataPath() . $primaryKeyValue . '.json';

            if (false === IoHelper::deleteFile($filePath)) {
                throw new IOException(
                    'Unable to delete document!
                    Already deleted documents: '.$key.'.
                    Location: "' . $filePath .'"'
                );
            }
        }

        return $returnValue;
    }

    public function removeFields(array &$results, array $fieldsToRemove)
    {
        foreach ($results as $key => $data) {
            $primaryKeyValue = IoHelper::secureStringForFileAccess($data[$this->primaryKey]);

            $data[$this->primaryKey] = $primaryKeyValue;

            $results[$key] = $data;

            $filePath = $this->getDataPath() . $primaryKeyValue . '.json';

            if (!file_exists($filePath)) {
                return false;
            }
        }

        foreach ($results as &$document) {
            foreach ($fieldsToRemove as $fieldToRemove) {
                if ($fieldToRemove !== $this->primaryKey) {
                    NestedHelper::removeNestedField($document, $fieldToRemove);
                }
            }

            $filePath = $this->getDataPath() . $document[$this->primaryKey] . '.json';

            IoHelper::writeContentToFile($filePath, json_encode($document));
        }

        return $results;
    }

    protected function getDataPath(): string
    {
        return $this->storePath . Store::dataDirectory;
    }
}