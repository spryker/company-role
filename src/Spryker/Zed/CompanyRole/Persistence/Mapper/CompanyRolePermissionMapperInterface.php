<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Persistence\Mapper;

use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\PermissionCollectionTransfer;
use Generated\Shared\Transfer\PermissionTransfer;
use Orm\Zed\CompanyRole\Persistence\SpyCompanyRole;
use Orm\Zed\CompanyRole\Persistence\SpyCompanyRoleToPermission;

interface CompanyRolePermissionMapperInterface
{
    public function hydratePermissionCollection(
        SpyCompanyRole $spyCompanyRole,
        CompanyRoleTransfer $companyRoleTransfer
    ): CompanyRoleTransfer;

    /**
     * @param iterable<\Orm\Zed\CompanyRole\Persistence\SpyCompanyRoleToPermission> $companyRoleToPermissionEntities
     */
    public function mapCompanyRoleToPermissionEntitiesToPermissionCollectionTransfer(
        iterable $companyRoleToPermissionEntities,
        PermissionCollectionTransfer $permissionCollectionTransfer
    ): PermissionCollectionTransfer;

    public function mapCompanyRoleToPermissionEntityToPermissionTransfer(
        SpyCompanyRoleToPermission $companyRoleToPermissionEntity,
        PermissionTransfer $permissionTransfer
    ): PermissionTransfer;
}
