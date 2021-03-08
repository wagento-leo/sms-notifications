<?php
/**
 * Wagento SMS Notifications powered by LINK Mobility
 *
 * Sends transactional SMS notifications through the LINK Mobility messaging
 * service.
 *
 * @package Wagento\SMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 * @author Yair García Torres <yair.garcia@wagento.com>
 * @copyright Copyright (c) Wagento (https://wagento.com/)
 * @license https://opensource.org/licenses/OSL-3.0.php Open Software License 3.0
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Model;

use Wagento\SMSNotifications\Api\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Configuration Model
 *
 * @package Wagento\SMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 *
 * @phpcs:disable Generic.Files.LineLength.TooLong
 */
final class Config implements ConfigInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(?int $websiteId = null): bool
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, $scope, $websiteId);
    }

    public function isOptinRequired(?int $websiteId = null): bool
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->isSetFlag(self::XML_PATH_REQUIRE_OPTIN, $scope, $websiteId);
    }

    public function getTermsAndConditions(?int $websiteId = null): ?string
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->getValue(self::XML_PATH_TERMS_AND_CONDITIONS, $scope, $websiteId);
    }

    public function isTermsAndConditionsShownAfterOptin(?int $websiteId = null): bool
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->isSetFlag(self::XML_PATH_SHOW_TERMS_AFTER_OPTIN, $scope, $websiteId);
    }

    public function sendWelcomeMessage(?int $websiteId = null): bool
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->isSetFlag(self::XML_PATH_SEND_WELCOME_MESSAGE, $scope, $websiteId);
    }

    public function getApiUser(?int $websiteId = null): ?string
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->getValue(self::XML_PATH_API_USER, $scope, $websiteId);
    }

    public function getApiPassword(?int $websiteId = null): ?string
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->getValue(self::XML_PATH_API_PASSWORD, $scope, $websiteId);
    }

    public function getPlatformId(?int $websiteId = null): ?string
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->getValue(self::XML_PATH_PLATFORM_ID, $scope, $websiteId);
    }

    public function getPlatformPartnerId(?int $websiteId = null): ?string
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->getValue(self::XML_PATH_PLATFORM_PARTNER_ID, $scope, $websiteId);
    }

    public function getGateId(?int $websiteId = null): ?string
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->getValue(self::XML_PATH_GATE_ID, $scope, $websiteId);
    }

    public function getSourceType(?int $websiteId = null): ?string
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->getValue(self::XML_PATH_SOURCE_TYPE, $scope, $websiteId);
    }

    public function getSource(?int $websiteId = null): ?string
    {
        $scope = $websiteId === null ? ScopeConfigInterface::SCOPE_TYPE_DEFAULT : ScopeInterface::SCOPE_WEBSITE;

        return $this->scopeConfig->getValue(self::XML_PATH_SOURCE, $scope, $websiteId);
    }

    public function isLoggingEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_LOGGING);
    }

    public function getWelcomeMessageTemplate(?int $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string
    {
        if ($scopeId === null) {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE_WELCOME, $scopeType, $scopeId);
    }

    public function getOrderPlacedTemplate(?int $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string
    {
        if ($scopeId === null) {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE_ORDER_PLACED, $scopeType, $scopeId);
    }

    public function getOrderInvoicedTemplate(?int $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string
    {
        if ($scopeId === null) {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE_ORDER_INVOICED, $scopeType, $scopeId);
    }

    public function getOrderShippedTemplate(?int $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string
    {
        if ($scopeId === null) {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE_ORDER_SHIPPED, $scopeType, $scopeId);
    }

    public function getOrderRefundedTemplate(?int $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string
    {
        if ($scopeId === null) {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE_ORDER_REFUNDED, $scopeType, $scopeId);
    }

    public function getOrderCanceledTemplate(?int $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string
    {
        if ($scopeId === null) {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE_ORDER_CANCELED, $scopeType, $scopeId);
    }

    public function getOrderHeldTemplate(?int $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string
    {
        if ($scopeId === null) {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE_ORDER_HELD, $scopeType, $scopeId);
    }

    public function getOrderReleasedTemplate(?int $scopeId = null, string $scopeType = ScopeInterface::SCOPE_STORE): ?string
    {
        if ($scopeId === null) {
            $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue(self::XML_PATH_TEMPLATE_ORDER_RELEASED, $scopeType, $scopeId);
    }
}
