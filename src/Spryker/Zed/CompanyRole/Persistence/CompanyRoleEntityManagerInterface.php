<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Persistence;

use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\PermissionTransfer;

interface CompanyRoleEntityManagerInterface
{
    public function saveCompanyRole(
        CompanyRoleTransfer $companyRoleTransfer
    ): CompanyRoleTransfer;

    public function deleteCompanyRoleById(int $idCompanyRole): void;

    public function saveCompanyUser(CompanyUserTransfer $companyUserTransfer): void;

    /**
     * @param array<\Generated\Shared\Transfer\PermissionTransfer> $permissions
     * @param int $idCompanyRole
     *
     * @return void
     */
    public function addPermissions(array $permissions, int $idCompanyRole): void;

    public function removePermissions(array $idPermissions, int $idCompanyRole): void;

    public function updateCompanyRolePermission(PermissionTransfer $permissionTransfer): void;
}
