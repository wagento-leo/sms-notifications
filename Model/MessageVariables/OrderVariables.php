<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Model\MessageVariables
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Model\MessageVariables;

use Wagento\SMSNotifications\Api\MessageVariablesInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface as UrlBuilder;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;

/**
 * Order Message Variables
 *
 * @package Wagento\SMSNotifications\Model\MessageVariables
 * @author Joseph Leedy <joseph@wagento.com>
 */
class OrderVariables implements MessageVariablesInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

    public function __construct(
        UrlBuilder $urlBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    public function getVariables(): array
    {
        if ($this->order === null) {
            return [];
        }

        $orderUrl = $this->urlBuilder->setScope($this->order->getStoreId())
            ->getUrl(
                'sales/order/view',
                [
                    'order_id' => $this->order->getEntityId(),
                    '_scope_to_url' => true,
                    '_nosid' => true
                ]
            );
        $orderUrl = preg_replace('/\?___store=.+$/', '', $orderUrl);

        return [
            'order_id' => $this->order->getIncrementId(),
            'order_url' => $orderUrl,
            'customer_name' => $this->order->getCustomerFirstname() . ' ' . $this->order->getCustomerLastname(),
            'customer_first_name' => $this->order->getCustomerFirstname(),
            'customer_last_name' => $this->order->getCustomerLastname(),
            'store_name' => $this->getStoreNameById((int)$this->order->getStoreId(), $this->order->getStoreName()),
        ];
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    private function getStoreNameById(int $storeId, string $default): string
    {
        try {
            $storeName = $this->scopeConfig->getValue(
                'general/store_information/name',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        } catch (\Exception $e) {
            $storeName = null;
        }

        if ($storeName === null) {
            if (strpos($default, "\n") !== false) {
                $default = explode("\n", $default)[1];
            }

            $storeName = $default;
        }

        return $storeName;
    }
}
