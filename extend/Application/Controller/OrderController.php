<?php

namespace Es\NetsEasy\extend\Application\Controller;

use Es\NetsEasy\Api\NetsLog;
use Es\NetsEasy\Api\NetsPaymentTypes;
use Es\NetsEasy\extend\Application\Models\Order as NetsOrder;
use Es\NetsEasy\Core\CommonHelper;

/**
 * Class controls nets payment process
 * It also shows the nets embedded checkout window
 */
class OrderController extends OrderController_parent
{

    const ENDPOINT_TEST = 'https://test.api.dibspayment.eu/v1/payments/';
    const ENDPOINT_LIVE = 'https://api.dibspayment.eu/v1/payments/';
    const JS_ENDPOINT_TEST = "https://test.checkout.dibspayment.eu/v1/checkout.js";
    const JS_ENDPOINT_LIVE = "https://checkout.dibspayment.eu/v1/checkout.js";
    const MODULE_NAME = "nets_easy";

    protected $_NetsLog = false;
    protected $oCommonHelper = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_NetsLog = $this->getConfig()->getConfigParam('nets_blDebug_log');
        NetsLog::log($this->_NetsLog, "NetsOrderController, constructor");
        $this->oCommonHelper = \oxNew(CommonHelper::class);
    }

    /**
     * Function that returns next step in payment process, calls parent function
     * @return string iSuccess
     */
    protected function _getNextStep($iSuccess)
    {
        NetsLog::log($this->_NetsLog, "NetsOrderController, _getNextStep");
        $nextStep = parent::_getNextStep($iSuccess);
        return $nextStep;
    }

    /**
     * Function that executes the payment
     * @return null
     */
    public function execute()
    {
        NetsLog::log($this->_NetsLog, "NetsOrderController, execute");
        $oBasket = $this->getSession()->getBasket();
        $oUser = $this->getUser();
        if (!$oUser) {
            return 'user';
        }
        if ($oBasket->getProductsCount()) {
            try {
                $netsOrder = \oxNew(NetsOrder::class);
                if ($netsOrder->isEmbedded()) {
                    //finalizing ordering process (validating, storing order into DB, executing payment, setting status 
                    $netsOrder->processOrder($oUser);
                    \oxRegistry::getUtils()->redirect($this->getConfig()
                                    ->getSslShopUrl() . 'index.php?cl=thankyou');
                } else {
                    $this->getPaymentApiResponse();
                }
            } catch (\OxidEsales\Eshop\Core\Exception\OutOfStockException $oEx) {
                $oEx->setDestination('basket');
                Registry::getUtilsView()->addErrorToDisplay($oEx, false, true, 'basket');
            } catch (\OxidEsales\Eshop\Core\Exception\NoArticleException $oEx) {
                Registry::getUtilsView()->addErrorToDisplay($oEx);
            } catch (\OxidEsales\Eshop\Core\Exception\ArticleInputException $oEx) {
                Registry::getUtilsView()->addErrorToDisplay($oEx);
            }
        }
    }

    /**
     * Function to get error message displayed on template file
     * @return string
     */
    public function getErrorMsg()
    {
        return $this->getSession()->getVariable('nets_err_msg');
    }

    /**
     * Function to get basket amount
     * @return amount
     */
    protected function getBasketAmount()
    {
        $mySession = $this->getSession();
        $oBasket = $mySession->getBasket();
        return intval(strval(($oBasket->getPrice()->getBruttoPrice() * 100)));
    }

    /**
     * Function to get return data after hosted payment checkout is done
     * @return null
     */
    public function returnhosted()
    {
        //$paymentId = \oxRegistry::getConfig()->getRequestParameter('paymentid');
        $paymentId = \oxRegistry::getSession()->getVariable('payment_id');
        if ($this->getConfig()->getConfigParam('nets_autocapture')) {
            $chargeResponse = $this->oCommonHelper->getCurlResponse($this->oCommonHelper->getApiUrl() . $paymentId, 'GET');
            $api_ret = json_decode($chargeResponse, true);
            $netsOrder = \oxNew(NetsOrder::class);
            $netsOrder->savePaymentDetails($api_ret);
        }
        \oxRegistry::getUtils()->redirect($this->getConfig()
                        ->getSslShopUrl() . 'index.php?cl=thankyou&paymentid=' . $paymentId);
    }

    /*
     * Function to get checkout js url based on environment i.e live or test
     * @return checkout js url
     */

    public function getCheckoutJs()
    {
        if ($this->getConfig()->getConfigParam('nets_blMode') == 0) {
            return self::JS_ENDPOINT_TEST;
        }
        return self::JS_ENDPOINT_LIVE;
    }

    /*
     * Function to get payment api response and pass it to template
     * @return payment id
     */

    public function getPaymentApiResponse()
    {
        // additional user check
        $oUser = $this->getUser();
        if (!$oUser) {
            return 'user';
        }
        $oBasket = $this->getSession()->getBasket();
        if ($oBasket->getProductsCount()) {
            $oOrder = \oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
            // finalizing ordering process (validating, storing order into DB, executing payment, setting status ...)
            $iSuccess = $oOrder->finalizeOrder($oBasket, $oUser);
            // performing special actions after user finishes order (assignment to special user groups)
            $oUser->onOrderExecute($oBasket, $iSuccess);
            $netsOrder = \oxNew(NetsOrder::class);
            return $netsOrder->createNetsTransaction($oOrder);
        }
    }

    /**
     * Function to check if it embedded checkout
     * @return bool
     */
    public function isEmbedded()
    {
        $netsOrder = \oxNew(NetsOrder::class);
        return $netsOrder->isEmbedded();
    }

    /*
     * Function to fetch checkout key to pass in checkout js options based on environment live or test
     * @return checkout key
     */

    public function getCheckoutKey()
    {
        return $this->oCommonHelper->getCheckoutKey();
    }

    /*
     * Function to compile layout style file url for the embedded checkout type
     * @return layout style
     */

    public function getLayout()
    {
        return $this->oCommonHelper->getLayout();
    }

}
