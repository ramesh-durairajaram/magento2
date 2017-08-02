<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Store\Model\Plugin;

use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\StoreIsInactiveException;
use Magento\Framework\Exception\NoSuchEntityException;
use \InvalidArgumentException;
use Magento\Store\Api\StoreResolverInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Class StoreCookie
 * @since 2.0.0
 */
class StoreCookie
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     * @since 2.0.0
     */
    protected $storeManager;

    /**
     * @var StoreCookieManagerInterface
     * @since 2.0.0
     */
    protected $storeCookieManager;

    /**
     * @var StoreRepositoryInterface
     * @since 2.0.0
     */
    protected $storeRepository;

    /**
     * @var StoreResolverInterface
     * @since 2.2.0
     */
    private $storeResolver;

    /**
     * @param StoreManagerInterface $storeManager
     * @param StoreCookieManagerInterface $storeCookieManager
     * @param StoreRepositoryInterface $storeRepository
     * @param StoreResolverInterface $storeResolver
     * @since 2.0.0
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        StoreCookieManagerInterface $storeCookieManager,
        StoreRepositoryInterface $storeRepository,
        StoreResolverInterface $storeResolver = null
    ) {
        $this->storeManager = $storeManager;
        $this->storeCookieManager = $storeCookieManager;
        $this->storeRepository = $storeRepository;
        $this->storeResolver = $storeResolver ?: ObjectManager::getInstance()->get(StoreResolverInterface::class);
    }

    /**
     * Delete cookie "store" if the store (a value in the cookie) does not exist or is inactive
     *
     * @param \Magento\Framework\App\FrontController $subject
     * @param \Magento\Framework\App\RequestInterface $request
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @since 2.1.0
     */
    public function beforeDispatch(
        \Magento\Framework\App\FrontController $subject,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $storeCodeFromCookie = $this->storeCookieManager->getStoreCodeFromCookie();
        if ($storeCodeFromCookie) {
            try {
                $this->storeRepository->getActiveStoreByCode($storeCodeFromCookie);
            } catch (StoreIsInactiveException $e) {
                $this->storeCookieManager->deleteStoreCookie($this->storeManager->getDefaultStoreView());
            } catch (NoSuchEntityException $e) {
                $this->storeCookieManager->deleteStoreCookie($this->storeManager->getDefaultStoreView());
            } catch (InvalidArgumentException $e) {
                $this->storeCookieManager->deleteStoreCookie($this->storeManager->getDefaultStoreView());
            }
        }
        if ($this->storeCookieManager->getStoreCodeFromCookie() === null
            || $request->getParam(StoreResolverInterface::PARAM_NAME) !== null
        ) {
            $storeId = $this->storeResolver->getCurrentStoreId();
            $store = $this->storeRepository->getActiveStoreById($storeId);
            $this->storeCookieManager->setStoreCookie($store);
        }
    }
}
