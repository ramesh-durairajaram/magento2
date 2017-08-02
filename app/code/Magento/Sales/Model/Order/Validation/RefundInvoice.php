<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Model\Order\Validation;

use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Invoice\InvoiceValidatorInterface;
use Magento\Sales\Model\Order\Creditmemo\CreditmemoValidatorInterface;
use Magento\Sales\Model\Order\Creditmemo\Item\Validation\CreationQuantityValidator;
use Magento\Sales\Model\Order\Creditmemo\ItemCreationValidatorInterface;
use Magento\Sales\Model\Order\Creditmemo\Validation\QuantityValidator;
use Magento\Sales\Model\Order\Creditmemo\Validation\TotalsValidator;
use Magento\Sales\Model\Order\OrderValidatorInterface;
use Magento\Sales\Model\ValidatorResultMerger;

/**
 * Class RefundInvoice
 * @since 2.2.0
 */
class RefundInvoice implements RefundInvoiceInterface
{
    /**
     * @var OrderValidatorInterface
     * @since 2.2.0
     */
    private $orderValidator;

    /**
     * @var CreditmemoValidatorInterface
     * @since 2.2.0
     */
    private $creditmemoValidator;

    /**
     * @var ItemCreationValidatorInterface
     * @since 2.2.0
     */
    private $itemCreationValidator;

    /**
     * @var InvoiceValidatorInterface
     * @since 2.2.0
     */
    private $invoiceValidator;

    /**
     * @var ValidatorResultMerger
     * @since 2.2.0
     */
    private $validatorResultMerger;

    /**
     * RefundArguments constructor.
     * @param OrderValidatorInterface $orderValidator
     * @param CreditmemoValidatorInterface $creditmemoValidator
     * @param ItemCreationValidatorInterface $itemCreationValidator
     * @param InvoiceValidatorInterface $invoiceValidator
     * @param ValidatorResultMerger $validatorResultMerger
     * @since 2.2.0
     */
    public function __construct(
        OrderValidatorInterface $orderValidator,
        CreditmemoValidatorInterface $creditmemoValidator,
        ItemCreationValidatorInterface $itemCreationValidator,
        InvoiceValidatorInterface $invoiceValidator,
        ValidatorResultMerger $validatorResultMerger
    ) {
        $this->orderValidator = $orderValidator;
        $this->creditmemoValidator = $creditmemoValidator;
        $this->itemCreationValidator = $itemCreationValidator;
        $this->invoiceValidator = $invoiceValidator;
        $this->validatorResultMerger = $validatorResultMerger;
    }

    /**
     * @inheritdoc
     * @since 2.2.0
     */
    public function validate(
        InvoiceInterface $invoice,
        OrderInterface $order,
        CreditmemoInterface $creditmemo,
        array $items = [],
        $isOnline = false,
        $notify = false,
        $appendComment = false,
        \Magento\Sales\Api\Data\CreditmemoCommentCreationInterface $comment = null,
        \Magento\Sales\Api\Data\CreditmemoCreationArgumentsInterface $arguments = null
    ) {
        $orderValidationResult = $this->orderValidator->validate(
            $order,
            [
                CanRefund::class
            ]
        );
        $creditmemoValidationResult = $this->creditmemoValidator->validate(
            $creditmemo,
            [
                QuantityValidator::class,
                TotalsValidator::class
            ]
        );

        $itemsValidation = [];
        foreach ($items as $item) {
            $itemsValidation[] = $this->itemCreationValidator->validate(
                $item,
                [CreationQuantityValidator::class],
                $order
            )->getMessages();
        }

        $invoiceValidationResult = $this->invoiceValidator->validate(
            $invoice,
            [
                \Magento\Sales\Model\Order\Invoice\Validation\CanRefund::class
            ]
        );

        return $this->validatorResultMerger->merge(
            $orderValidationResult,
            $creditmemoValidationResult,
            $invoiceValidationResult->getMessages(),
            ...$itemsValidation
        );
    }
}
