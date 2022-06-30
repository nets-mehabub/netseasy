<?php
/**
 * Copyright © OXID eSales Nets. All rights reserved.
 * See LICENSE file for license details.
 */
namespace Es\NetsEasy\Tests\Unit;
use \Es\NetsEasy\extend\Application\Controller\OrderController;
use \Es\NetsEasy\extend\Application\Models\Order;
use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\Eshop\Core\Field;
abstract class BaseUnitTestCase extends \Codeception\Test\Unit
{
     /** @var string */
    protected $moduleName;

    /** @var DatabaseHandler */
    protected $dbHandler;

    /** @var TestConfig  */
    protected $testConfig;
    protected $orderObject;
    protected function setUp(): void
    {
        parent::setUp();
        include_once dirname(__FILE__) . "/../../../../../bootstrap.php";
        $this->$orderObject =  \oxNew(OrderController::class);
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

        $mockBuilder = $this->getMockBuilder(\OxidEsales\Eshop\Core\Price::class);
        $mockBuilder->setMethods(['getBruttoPrice']);
        $price = $mockBuilder->getMock();
        $price->expects($this->once())->method("getBruttoPrice")->will($this->returnValue(129.00));

        $mockBuilder = $this->getMockBuilder(\OxidEsales\Eshop\Application\Model\Basket::class);
        $mockBuilder->setMethods(['getPrice']);
        $basket = $mockBuilder->getMock();
        $basket->expects($this->once())->method("getPrice")->will($this->returnValue($price));

        $view = new \OxidEsales\PayPalModule\Controller\PaymentController();

        //$this->assertTrue($view->isConfirmedByPayPal($basket));
    
    }
   
     
} 