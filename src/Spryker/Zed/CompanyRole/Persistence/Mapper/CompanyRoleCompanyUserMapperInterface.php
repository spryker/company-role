<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Persistence\Mapper;

use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\CompanyUserCollectionTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Orm\Zed\CompanyRole\Persistence\SpyCompanyRole;
use Orm\Zed\CompanyRole\Persistence\SpyCompanyRoleToCompanyUser;

interface CompanyRoleCompanyUserMapperInterface
{
    public function hydrateCompanyUserCollection(
        SpyCompanyRole $spyCompanyRole,
        CompanyRoleTransfer $companyRoleTransfer
    ): CompanyRoleTransfer;

    /**
     * @param iterable<\Orm\Zed\CompanyRole\Persistence\SpyCompanyRoleToCompanyUser> $companyRoleToCompanyUserEntities
     */
    public function mapCompanyRoleToCompanyUserEntitiesToCompanyUserCollectionTransfer(
        iterable $companyRoleToCompanyUserEntities,
        CompanyUserCollectionTransfer $companyUserCollectionTransfer
    ): CompanyUserCollectionTransfer;

    public function mapCompanyRoleToCompanyUserEntityToCompanyUserTransfer(
        SpyCompanyRoleToCompanyUser $companyRoleToCompanyUserEntity,
        CompanyUserTransfer $companyUserTransfer
    ): ?CompanyUserTransfer;
}
