<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Persistence\Mapper;

use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\CompanyUserCollectionTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Orm\Zed\CompanyRole\Persistence\SpyCompanyRole;
use Orm\Zed\CompanyRole\Persistence\SpyCompanyRoleToCompanyUser;

class CompanyRoleCompanyUserMapper implements CompanyRoleCompanyUserMapperInterface
{
    public function hydrateCompanyUserCollection(
        SpyCompanyRole $spyCompanyRole,
        CompanyRoleTransfer $companyRoleTransfer
    ): CompanyRoleTransfer {
        $companyUserCollectionTransfer = new CompanyUserCollectionTransfer();

        foreach ($spyCompanyRole->getSpyCompanyRoleToCompanyUsersJoinCompanyUser() as $spyCompanyRoleToCompanyUser) {
            /** @var \Orm\Zed\Customer\Persistence\SpyCustomer|null $customerEntity */
            $customerEntity = $spyCompanyRoleToCompanyUser->getCompanyUser()->getCustomer();

            if ($customerEntity === null) {
                continue;
            }

            $companyUserTransfer = (new CompanyUserTransfer())
                ->fromArray($spyCompanyRoleToCompanyUser->getCompanyUser()->toArray(), true);

            $customerTransfer = new CustomerTransfer();
            $customerTransfer->fromArray($customerEntity->toArray(), true);

            $companyUserTransfer->setCustomer($customerTransfer);

            $companyUserCollectionTransfer->addCompanyUser($companyUserTransfer);
        }

        $companyRoleTransfer->setCompanyUserCollection($companyUserCollectionTransfer);

        return $companyRoleTransfer;
    }

    /**
     * @param iterable<\Orm\Zed\CompanyRole\Persistence\SpyCompanyRoleToCompanyUser> $companyRoleToCompanyUserEntities
     */
    public function mapCompanyRoleToCompanyUserEntitiesToCompanyUserCollectionTransfer(
        iterable $companyRoleToCompanyUserEntities,
        CompanyUserCollectionTransfer $companyUserCollectionTransfer
    ): CompanyUserCollectionTransfer {
        foreach ($companyRoleToCompanyUserEntities as $companyRoleToCompanyUserEntity) {
            $companyUserTransfer = $this->mapCompanyRoleToCompanyUserEntityToCompanyUserTransfer(
                $companyRoleToCompanyUserEntity,
                new CompanyUserTransfer(),
            );

            if ($companyUserTransfer !== null) {
                $companyUserCollectionTransfer->addCompanyUser($companyUserTransfer);
            }
        }

        return $companyUserCollectionTransfer;
    }

    public function mapCompanyRoleToCompanyUserEntityToCompanyUserTransfer(
        SpyCompanyRoleToCompanyUser $companyRoleToCompanyUserEntity,
        CompanyUserTransfer $companyUserTransfer
    ): ?CompanyUserTransfer {
        /** @var \Orm\Zed\Customer\Persistence\SpyCustomer|null $customerEntity */
        $customerEntity = $companyRoleToCompanyUserEntity->getCompanyUser()->getCustomer();

        if ($customerEntity === null) {
            return null;
        }

        $companyUserTransfer->fromArray($companyRoleToCompanyUserEntity->getCompanyUser()->toArray(), true);

        return $companyUserTransfer->setCustomer(
            (new CustomerTransfer())->fromArray($customerEntity->toArray(), true),
        );
    }
}
