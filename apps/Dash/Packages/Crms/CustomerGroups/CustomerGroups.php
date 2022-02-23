<?php

namespace Apps\Dash\Packages\Crms\CustomerGroups;

use Apps\Dash\Packages\Crms\CustomerGroups\Model\CrmsCustomerGroups;
use Apps\Dash\Packages\Crms\Customers\Model\CrmsCustomers;
use System\Base\BasePackage;

class CustomerGroups extends BasePackage
{
    protected $modelToUse = CrmsCustomerGroups::class;

    protected $packageName = 'customergroups';

    public $customergroups;

    /**
     * @notification(name=add)
     */
    public function addCustomerGroup(array $data)
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
    public function updateCustomerGroup(array $data)
    {
        $customerGroup = $this->getById($data['id']);

        $customerGroup = array_merge($customerGroup, $data);

        if ($this->update($customerGroup)) {
            $this->addActivityLog($data, $customerGroup);

            $this->addResponse('Updated group ' . $customerGroup['name']);

            $this->addToNotification('update', 'Updated group ' . $customerGroup['name']);
        } else {
            $this->addResponse('Error updating group.', 1);
        }
    }

    /**
     * @notification(name=remove)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function removeCustomerGroup(array $data)
    {
        $customerGroup = $this->getById($data['id']);

        $modelToUse = CrmsCustomers::class;

        $searchCustomers =
            $modelToUse::count(
                [
                    'conditions'    => 'customer_group_id = :gid:',
                    'bind'          => [
                        'gid'       => $customerGroup['id']
                    ]
                ]
            );

        if ($searchCustomers) {
            $this->addResponse($searchCustomers . ' customers assigned to the group, cannot remove group.', 1);

            return;
        }

        if ($this->remove($data['id'])) {
            $this->addResponse('Removed group ' . $customerGroup['name']);

            $this->addToNotification('remove', 'Removed group ' . $customerGroup['name']);
        } else {
            $this->addResponse('Error removing group.', 1);
        }
    }
}