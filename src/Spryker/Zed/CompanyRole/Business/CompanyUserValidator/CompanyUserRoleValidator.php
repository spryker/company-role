<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Business\CompanyUserValidator;

use Generated\Shared\Transfer\CompanyUserResponseTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\ResponseMessageTransfer;
use Spryker\Zed\CompanyRole\Persistence\CompanyRoleRepositoryInterface;

class CompanyUserRoleValidator implements CompanyUserRoleValidatorInterface
{
    protected const string GLOSSARY_KEY_ERROR_ROLE_NOT_IN_COMPANY = 'message.company_user.validation.role_not_in_company';

    public function __construct(
        protected CompanyRoleRepositoryInterface $companyRoleRepository
    ) {
    }

    public function validateRolesBelongToCompany(CompanyUserTransfer $companyUserTransfer): CompanyUserResponseTransfer
    {
        $companyUserResponseTransfer = (new CompanyUserResponseTransfer())
            ->setCompanyUser($companyUserTransfer)
            ->setIsSuccessful(true);

        $companyRoleCollectionTransfer = $companyUserTransfer->getCompanyRoleCollection();
        $idCompany = $companyUserTransfer->getFkCompany();

        if ($companyRoleCollectionTransfer === null || $idCompany === null) {
            return $companyUserResponseTransfer;
        }

        $requestedCompanyRoleIds = [];

        foreach ($companyRoleCollectionTransfer->getRoles() as $companyRoleTransfer) {
            $requestedCompanyRoleIds[] = (int)$companyRoleTransfer->getIdCompanyRole();
        }

        $ownedCompanyRoleIds = $this->companyRoleRepository->getCompanyRoleIdsBelongingToCompany(
            $requestedCompanyRoleIds,
            $idCompany,
        );

        foreach ($requestedCompanyRoleIds as $idCompanyRole) {
            if (isset($ownedCompanyRoleIds[$idCompanyRole])) {
                continue;
            }

            return $companyUserResponseTransfer
                ->setIsSuccessful(false)
                ->addMessage(
                    (new ResponseMessageTransfer())->setText(static::GLOSSARY_KEY_ERROR_ROLE_NOT_IN_COMPANY),
                );
        }

        return $companyUserResponseTransfer;
    }
}
