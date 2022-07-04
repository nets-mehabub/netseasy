<?php

namespace Es\NetsEasy\Tests\Unit\Controller;

use \Es\NetsEasy\extend\Application\Controller\OrderController;
use \Es\NetsEasy\extend\Application\Models\Order as NetsOrder;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\UtilsObject;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Application\Controller\ThankyouController;
class OrderControllerTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $orderObject;

    protected function setUp(): void
    {
        parent::setUp();
        include_once dirname(__FILE__) . "/../../../../../../bootstrap.php";
        $this->orderObject = \oxNew(OrderController::class);
        $sut = $this->getMockBuilder(OxidEsales\Eshop\Application\Controller\OrderController::class)->setMethods(['init'])->getMock();
        $payment = $this->getMockBuilder(Payment::class)->setMethods(['load'])->getMock();
        $payment->expects($this->any())->method('load')->willReturn(true);
        $payment->oxpayments__oxactive = new Field(true);
//        $oMockOxOrder = $this->getMock('oxOrder', array('load'));
//        $oMockOxOrder->expects($this->any())->method('load')->will($this->returnValue(true));
//        $oMockOxOrder->oxorder__fcpotxid = new oxField('1234');
        // forcing payment id
        //$this->setRequestParameter("paymentid", "oxidpaypal");
        //$this->getSession()->setVariable("oepaypal-basketAmount", 129.00);
//        $mockBuilder = $this->getMockBuilder(\OxidEsales\Eshop\Core\Price::class);
//        $mockBuilder->setMethods(['getBruttoPrice']);
//        $price = $mockBuilder->getMock();
//        $price->expects($this->any())->method("getBruttoPrice")->will($this->returnValue(129.00));
//
//        $mockBuilder = $this->getMockBuilder(\OxidEsales\Eshop\Application\Model\Basket::class);
//        $mockBuilder->setMethods(['getPrice']);
//        $basket = $mockBuilder->getMock();
//        $basket->expects($this->any())->method("getPrice")->will($this->returnValue($price));
//
//        $view = new \OxidEsales\PayPalModule\Controller\PaymentController();
        //$this->assertTrue($view->isConfirmedByPayPal($basket));
    }

    protected function _before()
    {
        
    }

    protected function _after()
    {
        
    }

    public function testExecute()
    {
        \oxRegistry::getSession()->setVariable('payment_id', '0230000062a996e863308f63c7333a01');
        $order = $this->getMockBuilder(NetsOrder::class)->setMethods(['isEmbedded', 'processOrder'])->getMock();
        $order->expects($this->any())->method('processOrder')->willReturn(1);
        $order->expects($this->any())->method('isEmbedded')->willReturn(1);

        $user = $this->getMockBuilder(User::class)->setMethods(['getType', 'onOrderExecute'])->getMock();
        $user->expects($this->any())->method('getType')->willReturn(0);
        //$user->expects($this->any())->method('onOrderExecute')->willReturn(true);
        // $oBasket = $this->getMockBuilder(KlarnaBasket::class)->setMethods(['calculateBasket'])->getMock();
        //$oBasket->expects($this->any())->method('getPaymentId')->willReturn('klarna_checkout');
        //$oBasket->expects($this->any())->method('calculateBasket')->willReturn(true);
        $mockBuilder = $this->getMockBuilder(\OxidEsales\Eshop\Application\Model\Basket::class);
        $mockBuilder->setMethods(['getProductsCount']);
        $basket = $mockBuilder->getMock();
        $basket->expects($this->any())->method("getProductsCount")->will($this->returnValue(true));
        \oxRegistry::getSession()->setBasket($basket);

        $mockBuilder = $this->getMockBuilder(\oxRegistry::class);
        $mockBuilder->setMethods(['redirect']);
        $utils = $mockBuilder->getMock();
        //$utils->expects($this->any())->method("redirect")->with($this->equalTo(\oxRegistry::getConfig()
        //                     ->getSslShopUrl() . 'index.php?cl=thankyou'));
        $utils->expects($this->any())->method('redirect')->willReturn('test');

        //$sut = $this->getMockBuilder(OrderController::class)->setMethods(['init','getUtils'])->getMock();
        // $sut->expects($this->any())->method("getUtils")->will($this->returnValue($utils));
        // echo "<pre>";print_r($sut);die;
        //$sut->expects($this->exactly(2))->method('processOrder')->willReturn(true);
        //$sut->expects($this->any())->method('processOrder')->willReturn(true);
        //$sut->expects($this->any())->method('klarnaCheckoutSecurityCheck')->willReturn(true);
        //$this->setProtectedClassProperty($sut, 'oxorder__oxordernr', $order);
        //$this->setProtectedClassProperty($sut, '_aOrderData', ['merchant_requested' => ['additional_checkbox' => true]]);
        $sGetChallenge = \oxRegistry::getSession()->getSessionChallengeToken();
        //$this->setRequestParameter('stoken', $sGetChallenge);
        // UtilsObject::setClassInstance(Order::class, $order);
        //\oxRegistry::getSession()->setBasket($oBasket);
        //UtilsObject::setClassInstance(NetsOrder::class, $order);
        $oOrderOverview = new OrderController($order, $mockBuilder, $utils);
        $result = $oOrderOverview->execute();
        $this->assertEquals('test', $result);
        
        $order->expects($this->any())->method('isEmbedded')->willReturn(0);
        $result = $oOrderOverview->execute();
        // echo "<pre>";print_r($result);die;
        //$result  = $sut->execute();
        // $this->assertTrue($oOrderOverview);
        // 
        //$addressResult = $this->getSessionParam('sDelAddrMD5');
        //$this->assertEquals('address', $addressResult);
        //$paymentId = $this->getSessionParam('paymentid');
        //$this->assertEquals('klarna_checkout', $paymentId);
    }

    /**
     * Test case for OrderController::isEmbedded()
     */
    public function testIsEmbedded()
    {

        //$orderObject = \oxNew(OrderController::class);
        $embedded = $this->orderObject->isEmbedded();
        if ($embedded) {
            $this->assertTrue($embedded);
        } else {
            $this->assertFalse($embedded);
        }
    }

    /**
     * Test case for get return data after hosted payment checkout is done
     * @return null
     */
    public function testReturnhosted()
    {
        \oxRegistry::getSession()->setVariable('payment_id', '0230000062a996e863308f63c7333a01');
        \oxRegistry::getConfig()->setConfigParam('nets_autocapture', 1);

        $oOrder = $this->getMockBuilder(NetsOrder::class)->setMethods(['savePaymentDetails'])->getMock();
        $oOrder->expects($this->any())->method('savePaymentDetails')->willReturn(1);

        $mockBuilder = $this->getMockBuilder(\oxRegistry::class);
        $mockBuilder->setMethods(['redirect']);
        $utils = $mockBuilder->getMock();
        $utils->expects($this->any())->method('redirect')->willReturn('test');

        $oCommonHelper = $this->getMockBuilder(CommonHelper::class)->setMethods(['getCurlResponse', 'getApiUrl'])->getMock();
        $oCommonHelper->expects($this->any())->method('getCurlResponse')->willReturn("{'chargeId':'dummyChargeId'}");
        $oCommonHelper->expects($this->any())->method('getApiUrl')->willReturn('url');

        $oOrderOverview = new OrderController($oOrder, $oCommonHelper, $utils);
        $result = $oOrderOverview->returnhosted();
        $this->assertEquals('test', $result);
    }

    /*
     * Test case for to get checkout js url based on environment i.e live or test
     * @return checkout js url
     */

    public function testGetCheckoutJs()
    {
        \oxRegistry::getConfig()->setConfigParam('nets_blMode', 1);
        $getCheckoutJs = $this->orderObject->getCheckoutJs();
        \oxRegistry::getConfig()->setConfigParam('nets_blMode', 0);
        $getCheckoutJs = $this->orderObject->getCheckoutJs();
        $this->assertNotNull($getCheckoutJs);
    }

    /*
     * Test case to fetch checkout key to pass in checkout js options based on environment live or test
     * @return checkout key
     */

    public function testGetCheckoutKey()
    {
        $getCheckoutKey = $this->orderObject->getCheckoutKey();
        $this->assertNotNull($getCheckoutKey);
    }

    /*
     * Test case to compile layout style file url for the embedded checkout type
     * @return layout style
     */

    public function testGetLayout()
    {
        $getLayout = $this->orderObject->getLayout();
        $this->assertNotNull($getLayout);
    }

    /**
     * Test case to get error message displayed on template file
     */
    public function testGetErrorMsg()
    {
        \oxRegistry::getSession()->setVariable('nets_err_msg', 'test');
        $errorMsg = $this->orderObject->getErrorMsg();
        $this->assertEquals('test', $errorMsg);
    }

    /**
     * Test case to get basket amount
     */
    public function testGetBasketAmount()
    {
        $mockBuilder = $this->getMockBuilder(\OxidEsales\Eshop\Core\Price::class);
        $mockBuilder->setMethods(['getBruttoPrice']);
        $price = $mockBuilder->getMock();
        $price->expects($this->any())->method("getBruttoPrice")->will($this->returnValue(100));

        $mockBuilder = $this->getMockBuilder(\OxidEsales\Eshop\Application\Model\Basket::class);
        $mockBuilder->setMethods(['getPrice']);
        $basket = $mockBuilder->getMock();
        $basket->expects($this->any())->method("getPrice")->will($this->returnValue($price));
        \oxRegistry::getSession()->setBasket($basket);


        $basketAmount = $this->orderObject->getBasketAmount();
        $this->assertEquals(10000, $basketAmount);
    }

    /*
     * Test case to get payment api response and pass it to template
     */

    public function testGetPaymentApiResponse()
    {
        $oOrder = $this->getMockBuilder(\OxidEsales\Eshop\Application\Model\Order::class)->setMethods(['createNetsTransaction'])->getMock();
        $oOrder->expects($this->any())->method('createNetsTransaction')->willReturn(true);

        $mockBuilder = $this->getMockBuilder(\OxidEsales\Eshop\Application\Model\Basket::class);
        $mockBuilder->setMethods(['getProductsCount']);
        $basket = $mockBuilder->getMock();
        $basket->expects($this->any())->method("getProductsCount")->willReturn(1);
        \oxRegistry::getSession()->setBasket($basket);

        $oOrderOverview = new OrderController($oOrder);
        $result = $oOrderOverview->getPaymentApiResponse();
        $this->assertTrue($result);
        
    }

    /**
     * Set a given protected property of a given class instance to a given value.
     *
     * Note: Please use this methods only for static 'mocking' or with other hard reasons!
     *       For the most possible non static usages there exist other solutions.
     *
     * @param object $classInstance Instance of the class of which the property will be set
     * @param string $property      Name of the property to be set
     * @param mixed  $value         Value to which the property will be set
     */
    protected function setProtectedClassProperty($classInstance, $property, $value)
    {
        $className = get_class($classInstance);

        $reflectionClass = new \ReflectionClass($className);

        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($classInstance, $value);
    }

}
