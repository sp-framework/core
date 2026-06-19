<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesTags;

class Tags extends BasePackage
{
    protected $modelToUse = BasepackagesTags::class;

    protected $packageName = 'tags';

    public $tags;

    public function addTag($data)
    {
        if (!$this->checkTag($data['name'])) {
            return false;
        }

        if (!isset($data['swatch']) ||
            (isset($data['swatch']) && $data['swatch'] === '')
        ) {
            $data['swatch'] = strtoupper(\Colors\RandomColor::one(['luminosity' => 'light']));
        }

        if (!$package = $this->modules->packages->getPackageByClass(str_replace('_', '\\', $data['package_class']))) {
            $this->addResponse('Package name provided is incorrect.', 1);

            return false;
        }

        $data['package_class'] = str_replace('\\', '_', $package['class']);

        $data['package_row_ids'] = [];
        if (isset($data['package_row_id'])) {
            $data['package_row_ids'] = [$data['package_row_id']];
        }

        if ($this->add($data)) {
            $this->addResponse('Added ' . $data['name'] . ' tag');

            return true;
        } else {
            $this->addResponse('Error adding new tag.', 1);
        }

        return false;
    }

    public function updateTag($data)
    {
        $tag = $this->getById($data['id']);

        if (!$tag) {
            $this->addResponse('Tag with ID not found.', 1);

            return false;
        }

        if (isset($data['name']) && !$this->checkTag($data['name'])) {
            return false;
        }

        if (!isset($data['swatch']) ||
            (isset($data['swatch']) && $data['swatch'] === '')
        ) {
            unset($data['swatch']);
        }

        if (!$package = $this->modules->packages->getPackageByClass(str_replace('_', '\\', $data['package_class']))) {
            $this->addResponse('Package name provided is incorrect.', 1);

            return false;
        }

        $data['package_class'] = str_replace('\\', '_', $package['class']);

        $data = array_replace($tag, $data);

        if (isset($data['package_row_id'])) {
            if (!in_array($data['package_row_id'], $data['package_row_ids'])) {
                array_push($data['package_row_ids'], $data['package_row_id']);
            }
        }

        if ($this->update($data)) {
            $this->addResponse('Updated ' . $data['name'] . ' tag');

            return true;
        } else {
            $this->addResponse('Error updating tag.', 1);
        }

        return false;
    }

    public function removeTag($data)
    {
        $tag = $this->getById($data['id']);

        if (!$tag) {
            $this->addResponse('Tag with ID not found.', 1);

            return false;
        }

        if (count($tag['package_row_ids']) > 0) {
            $this->addResponse('Tag is assigned to package entries, cannot remove.', 1);

            return false;
        }

        if ($this->remove($tag['id'])) {
            $this->addResponse('Removed ' . $tag['name'] . ' tag');

            return true;
        } else {
            $this->addResponse('Error removing tag.', 1);
        }

        return false;
    }

    public function checkTag($tagName = null, $tagId = null)
    {
        if ($tagName && !checkCtype($tagName, 'alpha', [''])) {
            $this->addResponse('Tag name cannot have special chars or numbers or spaces.', 1);

            return false;
        }

        if ($tagId && !$this->getById((int) $tagId)) {
            $this->addResponse('Tag with ID not found.', 1);

            return false;
        }

        return true;
    }

    public function getTagsByPackageClass($packageClass)
    {
        if ($this->config->databasetype === 'db') {
            $params =
                [
                    'conditions'    => 'package_class = :package_class:',
                    'bind'          =>
                        [
                            'package_class'          => $packageClass,
                        ]
                ];
        } else {
            $params = ['conditions' => [['package_class', '=', $packageClass]]];
        }

        $tagsArr = $this->getByParams($params);

        $tags = [];

        if ($tagsArr && count($tagsArr) > 0) {
            foreach ($tagsArr as $tagKey => $tag) {
                $tags[$tag['id']] = $tagsArr[$tagKey];
            }

            return $tags;
        }

        return [];
    }

    public function getTagsByPackageClassAndPackageRowId($packageClass, $packageRowId)
    {
        $tagsArr = $this->getTagsByPackageClass($packageClass);

        $tags = [];

        if (count($tagsArr) > 0) {
            foreach ($tagsArr as $tagKey => $tag) {
                if (in_array($packageRowId, $tag['package_row_ids'])) {
                    $tags[$tag['id']] = $tagsArr[$tagKey];
                }
            }
        }

        return $tags;
    }
}