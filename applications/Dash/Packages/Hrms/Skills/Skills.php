<?php

namespace Applications\Dash\Packages\Hrms\Skills;

use Applications\Dash\Packages\Hrms\Skills\Model\HrmsSkills as HrmsSkillsModel;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Skills extends BasePackage
{
    protected $modelToUse = HrmsSkillsModel::class;

    protected $packageName = 'skills';

    public $skills;

    public function addSkill(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' skill';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new skill.';
        }
    }

    public function updateSkill(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' skill';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating skill.';
        }
    }

    public function removeSkill(array $data)
    {
        $skill = $this->getById($data['id']);

        if ($skill['skill_user_ids'] && $skill['skill_user_ids'] != '') {
            $skill['skill_user_ids'] = Json::decode($skill['skill_user_ids'], true);

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Skill is assigned to ' . count($skill['skill_user_ids']) . ' users. Error removing skill.';

            return false;
        }

        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed skill';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing skill.';
        }
    }

    public function addUserId(int $id, int $userId)
    {
        $skill = $this->getById($id);

        if ($skill['skill_user_ids'] && $skill['skill_user_ids'] != '') {

            $skill['skill_user_ids'] = Json::decode($skill['skill_user_ids'], true);

            if (!Arr::has($skill['skill_user_ids'], $userId)) {
                array_push($skill['skill_user_ids'], $userId);
            }

            $skill['skill_user_ids'] = Json::encode($skill['skill_user_ids']);
        }

        $this->update($skill);
    }

    public function removeUserId(int $id, int $userId)
    {
        $skill = $this->getById($id);

        if ($skill['skill_user_ids'] && $skill['skill_user_ids'] != '') {

            $skill['skill_user_ids'] = Json::decode($skill['skill_user_ids'], true);

            if (Arr::has($skill['skill_user_ids'], $userId)) {
                $key = array_search($userId, $skill['skill_user_ids']);
                unset($skill['skill_user_ids'][$key]);
            }

            $skill['skill_user_ids'] = Json::encode($skill['skill_user_ids']);
        }

        $this->update($skill);
    }

    public function searchSkills(string $skillQueryString)
    {
        $searchSkills =
            $this->getByParams(
                [
                    'conditions'    => 'name LIKE :sName:',
                    'bind'          => [
                        'sName'     => '%' . $skillQueryString . '%'
                    ]
                ]
            );

        if ($searchSkills) {
            $skills = [];

            foreach ($searchSkills as $skillKey => $skillValue) {
                $skills[$skillKey] = $skillValue;
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->skills = $skills;

            return true;
        }
    }
}