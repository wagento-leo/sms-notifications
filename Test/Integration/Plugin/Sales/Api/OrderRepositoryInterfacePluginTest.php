<?php
/**
 * LINK Mobility SMS Notifications
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Linkmobility\Notifications\Test\Integration\Plugin\Sales\Api
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) LINK Mobility (https://www.linkmobility.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Linkmobility\Notifications\Test\Integration\Plugin\Sales\Api;

use Linkmobility\Notifications\Model\SmsSender\OrderSender;
use Linkmobility\Notifications\Plugin\Sales\Api\OrderRepositoryInterfacePlugin;
use Linkmobility\Notifications\Test\Integration\_stubs\Model\SmsSender;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Interception\PluginList\PluginList;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Order Repository Interface Plugin Test
 *
 * @package Linkmobility\Notifications\Test\Integration\Plugin\Sales\Api
 * @author Joseph Leedy <joseph@wagento.com>
 */
class OrderRepositoryInterfacePluginTest extends TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;

    /**
     * @magentoAppArea frontend
     */
    public function testPluginIsConfigured(): void
    {
        $pluginList = $this->objectManager->create(PluginList::class);
        $plugins = $pluginList->get(OrderRepositoryInterface::class, []);

        $this->assertArrayHasKey('sms_notifications_send_order_sms', $plugins);
        $this->assertSame(
            OrderRepositoryInterfacePlugin::class,
            $plugins['sms_notifications_send_order_sms']['instance']
        );
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture orderDataFixtureProvider
     */
    public function testAfterSaveSendsOrderSms(): void
    {
        $smsSenderMock = $this->getMockBuilder(SmsSender::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();
        $orderRepository = $this->objectManager->create(OrderRepositoryInterface::class);
        $searchCriteriaBuilder = $this->objectManager->create(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('increment_id', '100000001')->create();
        $searchResults = $orderRepository->getList($searchCriteria);
        $order = current($searchResults->getItems());

        $smsSenderMock->expects($this->once())->method('send')->with($order)->willReturn(true);

        $this->objectManager->configure([OrderSender::class => ['shared' => true]]);
        $this->objectManager->addSharedInstance($smsSenderMock, OrderSender::class);

        $order->setCustomerIsGuest(true);

        $orderRepository->save($order);
    }

    public static function orderDataFixtureProvider(): void
    {
        require __DIR__ . '/../../../_files/order.php';
    }

    protected function setUp()
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();
    }
}
