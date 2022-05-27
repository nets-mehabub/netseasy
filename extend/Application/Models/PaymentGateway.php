<?php 
namespace Es\NetsEasy\extend\Application\Models;

use Es\NetsEasy\Api\NetsPaymentTypes;
use Es\NetsEasy\Api\NetsLog;
class PaymentGateway extends PaymentGateway_parent
{
	protected $_NetsLog = false;

	public function executePayment($dAmount, &$oOrder)
	{
		NetsLog::log($this->_NetsLog, 'executePayment ', $this->getFunctionName);
		$this->_NetsLog = $this->getConfig()->getConfigParam('nets_blDebug_log');
		// $ox_payment_id = $this->getSession()->getInstance()->getBasket()->getPaymentId();
		$ox_payment_id = $this->getSession()->getBasket()->getPaymentId();
		$payment_type = netsPaymentTypes::getNetsPaymentType($ox_payment_id);
		NetsLog::log($this->_NetsLog, "NetsPaymentGateway executePayment: " . $payment_type);

		if (! isset($payment_type) || ! $payment_type) {
			NetsLog::log($this->_NetsLog, "NetsPaymentGateway executePayment, parent");
			return parent::executePayment($dAmount, $oOrder);
		}
		NetsLog::log($this->_NetsLog, "NetsPaymentGateway executePayment");
		$success = true;
		$this->getSession()->deleteVariable('nets_success');

		if (isset($success) && $success === true) {
			NetsLog::log($this->_NetsLog, "NetsPaymentGateway executePayment - success");
			return true;
		}
		NetsLog::log($this->_NetsLog, "NetsPaymentGateway executePayment - failure");
		return false;
	}
}
