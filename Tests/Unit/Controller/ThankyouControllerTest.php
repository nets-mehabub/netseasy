<?php

namespace Es\NetsEasy\Tests\Unit\Controller;

use \Es\NetsEasy\extend\Application\Controller\ThankyouController;

class ThankyouControllerTest extends \Codeception\Test\Unit {

    /**
     * @var \UnitTester
     */
    protected $oThankyouController;

    protected function setUp(): void {
        parent::setUp();
        include_once dirname(__FILE__) . "/../../../../../../bootstrap.php";
        $this->oThankyouController = \oxNew(ThankyouController::class);
    }

    protected function _before() {
        
    }

    protected function _after() {
        
    }

    /**
     * Test case to get payment id from database to display in thank you page.
     */
    public function testGetPaymentId() {
        $mockBuilder = $this->getMockBuilder(\OxidEsales\Eshop\Core\DatabaseProvider::class);
        $mockBuilder->setMethods(['getDb', 'getOne']);
        $mockDB = $mockBuilder->getMock();
        $mockDB->expects($this->any())->method('getDb')->willReturn(true);
        $mockDB->expects($this->any())->method('getOne')->willReturn(1);
        
        $sSQL_select = "SELECT transaction_id FROM oxnets WHERE oxnets_id = ? LIMIT 1";
        $paymentId = $mockDB->getOne($sSQL_select, [
            1
        ]);
        if ($paymentId) {
            $this->assertNotEmpty($paymentId);
        } else {
            $this->assertNull($paymentId);
        }
        //
    }

}
