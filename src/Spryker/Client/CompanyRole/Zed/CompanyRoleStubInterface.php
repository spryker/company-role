<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\CompanyRole\Zed;

use Generated\Shared\Transfer\CompanyRoleCollectionTransfer;
use Generated\Shared\Transfer\CompanyRoleCriteriaFilterTransfer;
use Generated\Shared\Transfer\CompanyRolePermissionResponseTransfer;
use Generated\Shared\Transfer\CompanyRoleResponseTransfer;
use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\PermissionCollectionTransfer;
use Generated\Shared\Transfer\PermissionTransfer;

interface CompanyRoleStubInterface
{
    public function createCompanyRole(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleResponseTransfer;

    public function getCompanyRoleCollection(
        CompanyRoleCriteriaFilterTransfer $criteriaFilterTransfer
    ): CompanyRoleCollectionTransfer;

    public function getCompanyRoleById(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleTransfer;

    public function updateCompanyRole(CompanyRoleTransfer $companyRoleTransfer): void;

    public function deleteCompanyRole(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleResponseTransfer;

    public function findCompanyRolePermissions(CompanyRoleTransfer $companyRoleTransfer): PermissionCollectionTransfer;

    public function saveCompanyUser(CompanyUserTransfer $companyUserTransfer): void;

    public function findPermissionByIdCompanyRoleByIdPermission(PermissionTransfer $permissionTransfer): PermissionTransfer;

    public function updateCompanyRolePermission(PermissionTransfer $permissionTransfer): CompanyRolePermissionResponseTransfer;

    public function findCompanyRoleByUuid(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleResponseTransfer;
}
