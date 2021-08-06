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
    public function addVendorGroup(array $data)
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
    public function updateVendorGroup(array $data)
    {
        $vendorGroup = $this->getById($data['id']);

        $vendorGroup = array_merge($vendorGroup, $data);

        if ($this->update($vendorGroup)) {
            $this->addActivityLog($data, $vendorGroup);

            $this->addResponse('Updated group ' . $vendorGroup['name']);

            $this->addToNotification('update', 'Updated group ' . $vendorGroup['name']);
        } else {
            $this->addResponse('Error updating group.', 1);
        }
    }

    /**
     * @notification(name=remove)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function removeVendorGroup(array $data)
    {
        $vendorGroup = $this->getById($data['id']);

        $modelToUse = BusinessDirectoryVendors::class;

        $searchVendors =
            $modelToUse::findFirst(
                [
                    'conditions'    => 'vendor_group_id = :gid:',
                    'bind'          => [
                        'gid'       => $vendorGroup['id']
                    ]
                ]
            );

        if ($searchVendors) {
            $this->addResponse('Vendors assigned to the group, cannot remove group.', 1);

            return;
        }

        if ($this->remove($data['id'])) {
            $this->addResponse('Removed group ' . $vendorGroup['name']);

            $this->addToNotification('remove', 'Removed group ' . $vendorGroup['name']);
        } else {
            $this->addResponse('Error removing group.', 1);
        }
    }
}