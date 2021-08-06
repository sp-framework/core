<?php

namespace Apps\Dash\Packages\Business\Finances\Taxes;

use Apps\Dash\Packages\Business\Finances\Taxes\Model\BusinessFinancesTaxes;
use System\Base\BasePackage;

class Taxes extends BasePackage
{
    protected $modelToUse = BusinessFinancesTaxes::class;

    protected $packageName = 'taxes';

    public $taxes;

    /**
     * @notification(name=add)
     */
    public function addTax(array $data)
    {
        if ($this->add($data)) {
            $this->addActivityLog($data);

            $this->addResponse('Added new tax ' . $data['name']);

            $this->addToNotification('add', 'Added new tax ' . $data['name']);
        } else {
            $this->addResponse('Error adding new tax.', 1);
        }
    }

    /**
     * @notification(name=update)
     */
    public function updateTax(array $data)
    {
        $tax = $this->getById($data['id']);

        $tax = array_merge($tax, $data);

        if ($this->update($tax)) {
            $this->addActivityLog($data, $tax);

            $this->addResponse('Updated tax ' . $tax['name']);

            $this->addToNotification('update', 'Updated tax ' . $tax['name']);
        } else {
            $this->addResponse('Error updating tax.', 1);
        }
    }

    /**
     * @notification(name=remove)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function removeTax(array $data)
    {
        $tax = $this->getById($data['id']);

        if ($this->remove($tax['id'])) {
            $this->addResponse('Removed tax ' . $tax['name']);

            $this->addToNotification('remove', 'Removed tax ' . $tax['name']);
        } else {
            $this->addResponse('Error removing tax.', 1);
        }
    }
}