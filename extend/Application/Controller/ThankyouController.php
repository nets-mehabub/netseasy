<?php

namespace Es\NetsEasy\extend\Application\Controller;

/**
 * Class Extending thank you controller for adding payment id in front end
 */
class ThankyouController extends ThankyouController_parent {

    /**
     * Get payment id from database to display in thank you page.
     *
     * @return $paymentId
     */
    public function getPaymentId() {
        $oOrder = $this->getOrder();
        $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(true);
        $sSQL_select = "SELECT transaction_id FROM oxnets WHERE oxorder_id = ? LIMIT 1";
        $paymentId = $oDB->getOne($sSQL_select, [
            $oOrder->oxorder__oxid->value
        ]);
        return $paymentId;
    }

}
