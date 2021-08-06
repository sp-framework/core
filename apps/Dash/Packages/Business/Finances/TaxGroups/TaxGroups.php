<?php

namespace Apps\Dash\Packages\Business\Finances\TaxGroups;

use Apps\Dash\Packages\Business\Finances\TaxGroups\Model\BusinessFinancesTaxGroups;
use Apps\Dash\Packages\Business\Finances\Taxes\Model\BusinessFinancesTaxes;
use System\Base\BasePackage;

class TaxGroups extends BasePackage
{
    protected $modelToUse = BusinessFinancesTaxGroups::class;

    protected $packageName = 'taxgroups';

    public $taxgroups;

    /**
     * @notification(name=add)
     */
    public function addTaxGroup(array $data)
    {
        if ($this->add($data)) {
            $this->addActivityLog($data);

            $this->addResponse('Added new tax group ' . $data['name']);

            $this->addToNotification('add', 'Added new tax group ' . $data['name']);
        } else {
            $this->addResponse('Error adding new tax group.', 1);
        }
    }

    /**
     * @notification(name=update)
     */
    public function updateTaxGroup(array $data)
    {
        $taxGroup = $this->getById($data['id']);

        $taxGroup = array_merge($taxGroup, $data);

        if ($this->update($taxGroup)) {
            $this->addActivityLog($data, $taxGroup);

            $this->addResponse('Updated tax group ' . $taxGroup['name']);

            $this->addToNotification('update', 'Updated tax group ' . $taxGroup['name']);
        } else {
            $this->addResponse('Error updating tax group.', 1);
        }
    }

    /**
     * @notification(name=remove)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function removeTaxGroup(array $data)
    {
        $taxGroup = $this->getById($data['id']);

        $modelToUse = BusinessFinancesTaxes::class;

        $searchVendors =
            $modelToUse::findFirst(
                [
                    'conditions'    => 'tax_group_id = :gid:',
                    'bind'          => [
                        'gid'       => $taxGroup['id']
                    ]
                ]
            );

        if ($searchVendors) {
            $this->addResponse('Taxes assigned to the group, cannot remove tax group.', 1);

            return;
        }

        if ($this->remove($data['id'])) {
            $this->addResponse('Removed tax group ' . $taxGroup['name']);

            $this->addToNotification('remove', 'Removed tax group ' . $taxGroup['name']);
        } else {
            $this->addResponse('Error removing tax group.', 1);
        }
    }
}