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
 *
 * @phpcs:disable Generic.Files.LineLength.TooLong
 */

declare(strict_types=1);

namespace Wagento\SMSNotifications\Model;

use Wagento\SMSNotifications\Api\Data\SmsSubscriptionInterface;
use Wagento\SMSNotifications\Api\SmsSubscriptionRepositoryInterface;
use Wagento\SMSNotifications\Model\SmsSubscriptionFactory as SmsSubscriptionModelFactory;
use Wagento\SMSNotifications\Model\ResourceModel\SmsSubscription as SmsSubscriptionResourceModel;
use Wagento\SMSNotifications\Model\ResourceModel\SmsSubscription\CollectionFactory as SmsSubscriptionCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * SMS Subscription Repository
 *
 * @package Wagento\SMSNotifications\Model
 * @author Joseph Leedy <joseph@wagento.com>
 */
class SmsSubscriptionRepository implements SmsSubscriptionRepositoryInterface
{
    /**
     * @var \Magento\Framework\Api\SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Wagento\SMSNotifications\Model\SmsSubscriptionFactory
     */
    private $smsSubscriptionModelFactory;
    /**
     * @var \Wagento\SMSNotifications\Model\ResourceModel\SmsSubscription\CollectionFactory
     */
    private $smsSubscriptionCollectionFactory;
    /**
     * @var \Wagento\SMSNotifications\Model\ResourceModel\SmsSubscription
     */
    private $smsSubscriptionResourceModel;
    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    public function __construct(
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SmsSubscriptionModelFactory $smsSubscriptionModelFactory,
        SmsSubscriptionCollectionFactory $smsSubscriptionCollectionFactory,
        SmsSubscriptionResourceModel $smsSubscriptionResourceModel,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->smsSubscriptionModelFactory = $smsSubscriptionModelFactory;
        $this->smsSubscriptionCollectionFactory = $smsSubscriptionCollectionFactory;
        $this->smsSubscriptionResourceModel = $smsSubscriptionResourceModel;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @param int $id
     * @return \Wagento\SMSNotifications\Api\Data\SmsSubscriptionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $id): SmsSubscriptionInterface
    {
        /** @var \Wagento\SMSNotifications\Model\SmsSubscription $smsSubscriptionModel */
        $smsSubscriptionModel = $this->smsSubscriptionModelFactory->create();

        $this->smsSubscriptionResourceModel->load($smsSubscriptionModel, $id);

        if (!$smsSubscriptionModel->getId()) {
            throw new NoSuchEntityException(__('SMS Notification Subscription with ID "%1" does not exist.', $id));
        }

        return $smsSubscriptionModel->getDataModel();
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var \Magento\Framework\Api\SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        /** @var \Wagento\SMSNotifications\Model\ResourceModel\SmsSubscription\Collection $smsSubscriptionCollection */
        $smsSubscriptionCollection = $this->smsSubscriptionCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $smsSubscriptionCollection);

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($smsSubscriptionCollection->getItems());
        $searchResults->setTotalCount($smsSubscriptionCollection->getSize());

        return $searchResults;
    }

    /**
     * @param int $customerId
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getListByCustomerId(int $customerId): SearchResultsInterface
    {
        return $this->getList($this->searchCriteriaBuilder->addFilter('customer_id', $customerId)->create());
    }

    /**
     * @param \Wagento\SMSNotifications\Api\Data\SmsSubscriptionInterface $smsSubscription
     * @return \Wagento\SMSNotifications\Api\Data\SmsSubscriptionInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(SmsSubscriptionInterface $smsSubscription): SmsSubscriptionInterface
    {
        try {
            /** @var \Wagento\SMSNotifications\Model\SmsSubscription $smsSubscriptionModel */
            $smsSubscriptionModel = $this->smsSubscriptionModelFactory->create();

            $smsSubscriptionModel->updateData($smsSubscription);

            $this->smsSubscriptionResourceModel->save($smsSubscriptionModel);

            $savedSmsSubscription = $this->get((int)$smsSubscriptionModel->getId());
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $savedSmsSubscription;
    }

    /**
     * @param \Wagento\SMSNotifications\Api\Data\SmsSubscriptionInterface $smsSubscription
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(SmsSubscriptionInterface $smsSubscription): bool
    {
        try {
            /** @var \Wagento\SMSNotifications\Model\SmsSubscription $smsSubscriptionModel */
            $smsSubscriptionModel = $this->smsSubscriptionModelFactory->create();

            $smsSubscriptionModel->updateData($smsSubscription);

            $this->smsSubscriptionResourceModel->delete($smsSubscriptionModel);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }

        return true;
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): bool
    {
        return $this->delete($this->get($id));
    }
}
