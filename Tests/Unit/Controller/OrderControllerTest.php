<?php

namespace Es\NetsEasy\Tests\Unit\Controller;

use \Es\NetsEasy\extend\Application\Controller\OrderController;
use \Es\NetsEasy\extend\Application\Models\Order;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Core\Field;
 
class OrderControllerTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $orderObject;

    protected function setUp(): void {
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
//        $price->expects($this->once())->method("getBruttoPrice")->will($this->returnValue(129.00));
//
//        $mockBuilder = $this->getMockBuilder(\OxidEsales\Eshop\Application\Model\Basket::class);
//        $mockBuilder->setMethods(['getPrice']);
//        $basket = $mockBuilder->getMock();
//        $basket->expects($this->once())->method("getPrice")->will($this->returnValue($price));
//
//        $view = new \OxidEsales\PayPalModule\Controller\PaymentController();
        //$this->assertTrue($view->isConfirmedByPayPal($basket));
    }

    protected function _before() {
        
    }

    protected function _after() {
        
    }

    /**
     * Test case for OrderController::isEmbedded()
     */
    public function testIsEmbedded() {

        //$orderObject = \oxNew(OrderController::class);
        $embedded = $this->orderObject->isEmbedded();
        $this->assertTrue($embedded);
    }

    /**
     * Test case for get return data after hosted payment checkout is done
     * @return null
     */
    public function testReturnhosted() {
        //$returnhosted = $this->orderObject->returnhosted();
        $paymentId = "edd22ddadf13rvv124ffsda";
        $sUrl = \oxRegistry::getConfig()
                        ->getSslShopUrl() . 'index.php?cl=thankyou&paymentid=' . $paymentId;
        $response = \oxRegistry::getUtilsUrl()->processUrl($sUrl, $blFinalUrl = true, $aParams = null, $iLang = null
        );
        //$this->assertEquals(\oxRegistry::getConfig()->getShopSecureHomeUrl() . 'cl=basket', oxUtilsHelper::$sRedirectUrl);
        //$response->assertResponseStatus(302);
        $this->assertNotNull($response);
        //$this->verifyRedirect(\oxRegistry::getUtils()->redirect(\oxRegistry::getConfig()
        //->getSslShopUrl() . 'index.php?cl=thankyou&paymentid=' . $paymentId));
        // $this->assertRedirectContains('thankyou');
    }

    /*
     * Test case for to get checkout js url based on environment i.e live or test
     * @return checkout js url
     */

    public function testGetCheckoutJs() {
        $getCheckoutJs = $this->orderObject->getCheckoutJs();
        $this->assertNotNull($getCheckoutJs);
    }

    /*
     * Test case to fetch checkout key to pass in checkout js options based on environment live or test
     * @return checkout key
     */

    public function testGetCheckoutKey() {
        $getCheckoutKey = $this->orderObject->getCheckoutKey();
        $this->assertNotNull($getCheckoutKey);
    }

    /*
     * Test case to compile layout style file url for the embedded checkout type
     * @return layout style
     */

    public function testGetLayout() {
        $getLayout = $this->orderObject->getLayout();
        $this->assertNotNull($getLayout);
    }

}
