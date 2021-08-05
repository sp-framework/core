<?php

namespace Apps\Dash\Packages\Business\Directory\VendorGroups;

use Apps\Dash\Packages\Business\Directory\VendorGroups\Model\BusinessDirectoryVendorGroups;
use Apps\Dash\Packages\Business\Directory\Vendors\Model\BusinessDirectoryVendors;
use System\Base\BasePackage;

class VendorGroups extends BasePackage
{
    protected $modelToUse = BusinessDirectoryVendorGroups::class;

    protected $packageName = 'vendorgroups';

    public $vendorgroups;

    /**
     * @notification(name=add)
     */
    public function addGroup(array $data)
    {
        if ($this->add($data)) {
            $this->addActivityLog($data);

            $this->addResponse('Added new group ' . $data['name']);

            $this->addToNotification('add', 'Added new group ' . $data['name']);
        } else {
            $this->addResponse('Error adding new group.', 1);
        }
    }

    /**
     * @notification(name=update)
     */
    public function updateGroup(array $data)
    {
        $group = $this->getById($data['id']);

        $group = array_merge($group, $data);

        if ($this->update($group)) {
            $this->addActivityLog($data, $group);

            $this->addResponse('Updated group ' . $group['name']);

            $this->addToNotification('update', 'Updated group ' . $group['name']);
        } else {
            $this->addResponse('Error updating group.', 1);
        }
    }

    /**
     * @notification(name=remove)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function removeGroup(array $data)
    {
        $group = $this->getById($data['id']);

        $modelToUse = BusinessDirectoryVendors::class;

        $searchVendors =
            $modelToUse::findFirst(
                [
                    'conditions'    => 'group_id = :gid:',
                    'bind'          => [
                        'gid'       => $group['id']
                    ]
                ]
            );

        if ($searchVendors) {
            $this->addResponse('Vendors assigned to the group, cannot remove group.', 1);

            return;
        }

        if ($this->remove($data['id'])) {
            $this->addResponse('Removed group ' . $group['name']);

            $this->addToNotification('remove', 'Removed group ' . $group['name']);
        } else {
            $this->addResponse('Error removing group.', 1);
        }
    }
}