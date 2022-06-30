<?php

namespace Es\NetsEasy\Tests\Unit\Models;

use OxidEsales\Eshop\Application\Model\Basket;
use \Es\NetsEasy\extend\Application\Models\OrderOverview;
use Es\NetsEasy\Api\NetsLog;
use Es\NetsEasy\Core\CommonHelper;

class OrderOverviewTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    protected $oOrderOverviewObject;

    protected function setUp(): void
    {
        parent::setUp();
        include_once dirname(__FILE__) . "/../../../../../../bootstrap.php";
        $this->oOrderOverviewObject = \oxNew(OrderOverview::class);
    }

    protected function _before()
    {
        
    }

    protected function _after()
    {
        
    }

    /**
     * Test case to check the nets payment status and display in admin order list backend page
     */
    public function testGetEasyStatus()
    {
        $orderId = $this->getNetsOrderId();
        if (!empty($orderId)) {
            $result = $this->oOrderOverviewObject->getEasyStatus($orderId);
            if ($result) {
                $this->assertNotEmpty($result);
                $this->assertArrayHasKey('payStatus', $result);
            }
        } else {
            $this->assertNull($orderId);
        }
    }

    /**
     * Function to get nets order id
     * @return string
     */
    public function getNetsOrderId()
    {
        $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(true);
        $sSQL_select = "SELECT oxid FROM oxorder WHERE  OXPAYMENTTYPE= ? LIMIT 1";
        return $orderId = $oDB->getOne($sSQL_select, [
            'nets_easy'
        ]);
    }

    /*
     * Test case to get value item list for charge
     * return int
     */

    public function testGetValueItem()
    {
        $result = $this->oOrderOverviewObject->getValueItem(array("cancelledAmount" => 100, "oxbprice" => 10, "taxRate" => 2000), 100);
        //echo "<pre>";print_r($result);die;
        if ($result) {
            $this->assertNotEmpty($result);
            $this->assertArrayHasKey('cancelledAmount', $result);
        }
    }

    /*
     * Test case to get order items to pass capture, refund, cancel api
     */

    public function testGetOrderItems()
    {
        $orderId = $this->getNetsOrderId();
        if (!empty($orderId)) {
            $result = $this->oOrderOverviewObject->getOrderItems($orderId, true);
            if ($result) {
                $this->assertNotEmpty($result);
            }
        }
    }

    /*
     * Test case to get product item listing
     */

    public function testGetItemList()
    {
        $orderId = $this->getNetsOrderId();
        $blExcludeCanceled = true;
        $sSelect = "
			SELECT `oxorderarticles`.* FROM `oxorderarticles`
			WHERE `oxorderarticles`.`oxorderid` = '" . $orderId . "'" . ($blExcludeCanceled ? "
			AND `oxorderarticles`.`oxstorno` != 1 " : " ") . "
			ORDER BY `oxorderarticles`.`oxartid`, `oxorderarticles`.`oxselvariant`, `oxorderarticles`.`oxpersparam`
		";
        // order articles
        $oArticles = oxNew('oxlist');
        $oArticles->init('oxorderarticle');
        $oArticles->selectString($sSelect);
        $totalOrderAmt = 0;
        foreach ($oArticles as $listitem) {
            $items[] = $this->oOrderOverviewObject->getItemList($listitem);
        }
        $this->assertNotEmpty($items);
    }

    /*
     * Test case to get shopping cost
     */

    public function testGetShoppingCost()
    {
        $oOrderItems = $this->getOrderItems();
        foreach ($oOrderItems as $item) {
            $items[] = $this->oOrderOverviewObject->getShoppingCost($item);
        }
        $this->assertNotEmpty($items);
    }

    /*
     * Test case to get greeting card items
     */

    public function testGetGreetingCardItem()
    {
        $oOrderItems = $this->getOrderItems();
        foreach ($oOrderItems as $item) {
            $items[] = $this->oOrderOverviewObject->getGreetingCardItem($item);
        }
        $this->assertNotEmpty($items);
    }

    /*
     * Test case to get Gift Wrapping items
     */

    public function testGetGiftWrappingItem()
    {
        $oOrderItems = $this->getOrderItems();
        foreach ($oOrderItems as $item) {
            $items[] = $this->oOrderOverviewObject->getGiftWrappingItem($item);
        }
        $this->assertNotEmpty($items);
    }

    /*
     * Test case to get pay cost items
     */

    public function testGetPayCost()
    {
        $oOrderItems = $this->getOrderItems();
        foreach ($oOrderItems as $item) {
            $items[] = $this->oOrderOverviewObject->getPayCost($item);
        }
        $this->assertNotEmpty($items);
    }

    /*
     * Test case to fetch payment method type from database table oxorder
     */

    public function testGetPaymentMethod()
    {
        $orderId = $this->getNetsOrderId();
        $paymentMethod = $this->oOrderOverviewObject->getPaymentMethod($orderId);
        $this->assertNotEmpty($paymentMethod);
    }

    /*
     * Test case to fetch Charge Id from database table oxorder
     */

    public function testGetChargeId()
    {
        $orderId = $this->getNetsOrderId();
        $chargeId = $this->oOrderOverviewObject->getChargeId($orderId);
        $this->assertNotEmpty($chargeId);
    }

    /*
     * Test case to prepare items for refund
     */

    public function testGetItemForRefund()
    {
        $data = array(
            "items" => array(0 => ['reference' => 'fdaqwefffq1wd2',
                    'quantity' => 100,
                    'oxbprice' => 100,
                    'taxRate' => 100,
                    'netTotalAmount' => 100,
                    'grossTotalAmount' => 100,
                    'taxAmount' => 100,
                    'oxbprice' => 100
                ]),
            "totalAmt" => 100
        );
        //echo "<pre>";print_r($data);die;
        $items = $this->oOrderOverviewObject->getItemForRefund('fdaqwefffq1wd2', 1, $data);
        $this->assertNotEmpty($items);
    }

    /*
     * Test case to prepare amount
     */

    public function testPrepareAmount()
    {
        //$amount = $this->oOrderOverviewObject->prepareAmount(1039);
        //$this->assertNotEmpty($amount);
    }

    /*
     * Function to get order items listing
     * @return array
     */

    public function getOrderItems()
    {
        $orderId = $this->getNetsOrderId();
        $blExcludeCanceled = true;
        $sSelectOrder = "
			SELECT `oxorder`.* FROM `oxorder`
			WHERE `oxorder`.`oxid` = '" . $orderId . "'" . ($blExcludeCanceled ? "
			AND `oxorder`.`oxstorno` != 1 " : " ") . "
			ORDER BY `oxorder`.`oxordernr`";
        $oOrderItems = oxNew('oxlist');
        $oOrderItems->init('oxorder');
        $oOrderItems->selectString($sSelectOrder);
        return $oOrderItems;
    }

}
