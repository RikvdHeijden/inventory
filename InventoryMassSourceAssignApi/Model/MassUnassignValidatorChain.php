<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventoryMassSourceAssignApi\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;

/**
 * Chain of validators. Extension point for new validators via di configuration
 *
 * @api
 */
class MassUnassignValidatorChain implements MassUnassignValidatorInterface
{
    /**
     * @var ValidationResultFactory
     */
    private $validationResultFactory;

    /**
     * @var MassUnassignValidatorInterface[]
     */
    private $validators;

    /**
     * @param ValidationResultFactory $validationResultFactory
     * @param MassUnassignValidatorInterface[] $validators
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        ValidationResultFactory $validationResultFactory,
        array $validators = []
    ) {
        $this->validationResultFactory = $validationResultFactory;

        foreach ($validators as $validator) {
            if (!$validator instanceof MassUnassignValidatorInterface) {
                throw new LocalizedException(
                    __('Source Validator must implement MassUnassignValidatorInterface.')
                );
            }
        }
        $this->validators = $validators;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $skus, array $sourceCodes): ValidationResult
    {
        $errors = [];
        foreach ($this->validators as $validator) {
            $validationResult = $validator->validate($skus, $sourceCodes);

            if (!$validationResult->isValid()) {
                $errors = array_merge($errors, $validationResult->getErrors());
            }
        }
        return $this->validationResultFactory->create(['errors' => $errors]);
    }
}
