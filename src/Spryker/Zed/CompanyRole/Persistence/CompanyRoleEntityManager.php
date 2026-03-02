<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Persistence;

use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\PermissionTransfer;
use Orm\Zed\CompanyRole\Persistence\SpyCompanyRole;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \Spryker\Zed\CompanyRole\Persistence\CompanyRolePersistenceFactory getFactory()
 */
class CompanyRoleEntityManager extends AbstractEntityManager implements CompanyRoleEntityManagerInterface
{
    public function saveCompanyRole(
        CompanyRoleTransfer $companyRoleTransfer
    ): CompanyRoleTransfer {
        $spyCompanyRole = $this->getFactory()
            ->createCompanyRoleMapper()
            ->mapCompanyRoleTransferToEntity($companyRoleTransfer, new SpyCompanyRole());

        if ($spyCompanyRole->getIsDefault()) {
            $this->cleanupCompanyDefaultRoles($spyCompanyRole);
        }

        $spyCompanyRole->save();

        return $this->getFactory()
            ->createCompanyRoleMapper()
            ->mapEntityToCompanyRoleTransfer($spyCompanyRole, $companyRoleTransfer);
    }

    public function deleteCompanyRoleById(int $idCompanyRole): void
    {
        $this->getFactory()
            ->createCompanyRoleQuery()
            ->filterByIdCompanyRole($idCompanyRole)
            ->delete();
    }

    public function saveCompanyUser(CompanyUserTransfer $companyUserTransfer): void
    {
        $companyRoles = [];

        if ($companyUserTransfer->getCompanyRoleCollection()) {
            $companyRoles = $companyUserTransfer->getCompanyRoleCollection()->getRoles();
        }

        $assignedIdCompanyRoles = [];

        foreach ($companyRoles as $companyRoleTransfer) {
            $this->getFactory()
                ->createCompanyRoleToCompanyUserQuery()
                ->filterByFkCompanyUser($companyUserTransfer->getIdCompanyUser())
                ->filterByFkCompanyRole($companyRoleTransfer->getIdCompanyRole())
                ->findOneOrCreate()
                ->save();

            $assignedIdCompanyRoles[] = $companyRoleTransfer->getIdCompanyRole();
        }

        $this->getFactory()
            ->createCompanyRoleToCompanyUserQuery()
            ->filterByFkCompanyUser($companyUserTransfer->getIdCompanyUser())
            ->filterByFkCompanyRole($assignedIdCompanyRoles, Criteria::NOT_IN)
            ->delete();
    }

    /**
     * @param array<\Generated\Shared\Transfer\PermissionTransfer> $permissions
     * @param int $idCompanyRole
     *
     * @return void
     */
    public function addPermissions(array $permissions, int $idCompanyRole): void
    {
        foreach ($permissions as $permission) {
            $this->saveCompanyRolePermission($idCompanyRole, $permission);
        }
    }

    public function removePermissions(array $idPermissions, int $idCompanyRole): void
    {
        if (count($idPermissions) === 0) {
            return;
        }

        $this->getFactory()
            ->createCompanyRoleToPermissionQuery()
            ->filterByFkCompanyRole($idCompanyRole)
            ->filterByFkPermission_In($idPermissions)
            ->delete();
    }

    public function updateCompanyRolePermission(PermissionTransfer $permissionTransfer): void
    {
        $spyCompanyRoleToPermission = $this->getFactory()
            ->createCompanyRoleToPermissionQuery()
            ->filterByFkCompanyRole($permissionTransfer->getIdCompanyRole())
            ->filterByFkPermission($permissionTransfer->getIdPermission())
            ->findOne();

        if ($spyCompanyRoleToPermission !== null) {
            $spyCompanyRoleToPermission->setConfiguration(json_encode($permissionTransfer->getConfiguration()) ?: null);
            $spyCompanyRoleToPermission->save();
        }
    }

    protected function saveCompanyRolePermission(int $idCompanyRole, PermissionTransfer $permissionTransfer): void
    {
        $spyCompanyRoleToPermission = $this->getFactory()
            ->createCompanyRoleToPermissionQuery()
            ->filterByFkCompanyRole($idCompanyRole)
            ->filterByFkPermission($permissionTransfer->getIdPermission())
            ->findOneOrCreate();

        $spyCompanyRoleToPermission->setConfiguration(json_encode($permissionTransfer->getConfiguration()) ?: null);
        $spyCompanyRoleToPermission->save();
    }

    protected function cleanupCompanyDefaultRoles(SpyCompanyRole $spyCompanyRole): void
    {
        $updateQuery = $this->getFactory()
            ->createCompanyRoleQuery()
            ->filterByFkCompany($spyCompanyRole->getFkCompany());

        if ($spyCompanyRole->getIdCompanyRole() !== null) {
            $updateQuery->filterByIdCompanyRole($spyCompanyRole->getIdCompanyRole(), Criteria::NOT_EQUAL);
        }

        $updateQuery->update(['IsDefault' => false]);
    }
}
