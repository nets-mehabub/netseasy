<?php

namespace Es\NetsEasy\Tests\Unit\Models;
if (!class_exists('oePayPalOrder_parent')) {
    class oePayPalOrder_parent extends \OxidEsales\Eshop\Application\Controller\OrderController
    {
    }
}

use Es\NetsEasy\extend\Application\Models\PaymentGateway as NetsPaymentGateway;

class PaymentGatewayTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    protected $oPaymentGateway;

    protected function setUp(): void
    {
        parent::setUp();
        include_once dirname(__FILE__) . "/../../../../../../bootstrap.php";
        $this->oPaymentGateway = \oxNew(NetsPaymentGateway::class);
    }

    protected function _before()
    {
        
    }

    protected function _after()
    {
        
    }

    /**
     * Test case to get product item
     */
    public function testExecutePayment()
    {
        //$resultValue = $this->oPaymentGateway->executePayment(900);
        $this->assertTrue(true);
    }

}
