<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventoryMassSourceAssign\Model\Assign\Validator;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryMassSourceAssignApi\Model\MassAssignValidatorInterface;

/**
 * Check if sources exist
 * Do not perform any source/product cross check
 */
class SourcesValidator implements MassAssignValidatorInterface
{
    /**
     * @var ValidationResultFactory
     */
    private $validationResultFactory;

    /**
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;

    /**
     * @param ValidationResultFactory $validationResultFactory
     * @param SourceRepositoryInterface $sourceRepository
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        ValidationResultFactory $validationResultFactory,
        SourceRepositoryInterface $sourceRepository
    ) {
        $this->validationResultFactory = $validationResultFactory;
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $skus, array $sourceCodes): ValidationResult
    {
        $errors = [];
        foreach ($sourceCodes as $sourceCode) {
            try {
                $this->sourceRepository->get($sourceCode);
            } catch (NoSuchEntityException $e) {
                $errors[] = __('Source %sourceCode does not exist', ['sourceCode' => $sourceCode]);
            }
        }

        return $this->validationResultFactory->create(['errors' => $errors]);
    }
}
