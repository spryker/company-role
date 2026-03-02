<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Persistence;

use Generated\Shared\Transfer\CompanyRoleCollectionTransfer;
use Generated\Shared\Transfer\CompanyRoleCriteriaFilterTransfer;
use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\PermissionCollectionTransfer;
use Generated\Shared\Transfer\PermissionTransfer;

interface CompanyRoleRepositoryInterface
{
    public function getCompanyRoleById(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleTransfer;

    public function findPermissionsByIdCompanyUser(int $idCompanyUser): PermissionCollectionTransfer;

    public function findPermissionsByIdCompanyRoleByIdPermission(int $idCompanyRole, int $idPermission): PermissionTransfer;

    public function findCompanyRole(): CompanyRoleCollectionTransfer;

    public function findCompanyRolePermissions(int $idCompanyRole): PermissionCollectionTransfer;

    /**
     * @param string $permissionKey
     * @param int|null $idCompany
     *
     * @return array<int>
     */
    public function getCompanyUserIdsByPermissionKey(string $permissionKey, ?int $idCompany = null): array;

    public function getCompanyRoleCollection(
        CompanyRoleCriteriaFilterTransfer $companyRoleCriteriaFilterTransfer
    ): CompanyRoleCollectionTransfer;

    /**
     * @deprecated Use {@link findDefaultCompanyRoleByIdCompany()} instead.
     *
     * @return \Generated\Shared\Transfer\CompanyRoleTransfer
     */
    public function getDefaultCompanyRole(): CompanyRoleTransfer;

    public function findDefaultCompanyRoleByIdCompany(int $idCompany): ?CompanyRoleTransfer;

    public function hasUsers(int $idCompanyRole): bool;

    public function findCompanyRoleById(CompanyRoleTransfer $companyRoleTransfer): ?CompanyRoleTransfer;

    public function findCompanyRoleByUuid(string $companyRoleUuid): ?CompanyRoleTransfer;

    /**
     * @param list<int> $companyUserIds
     *
     * @return array<int, list<string>>
     */
    public function getCompanyRoleNamesGroupedByCompanyUserIds(array $companyUserIds): array;
}
