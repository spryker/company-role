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
            $this->cleanupCompanyDefaultRoles($spyCompanyRole, $companyRoleTransfer->getFkCompany());
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

    protected function cleanupCompanyDefaultRoles(SpyCompanyRole $spyCompanyRole, ?int $idCompany = null): void
    {
        $idCompany = $idCompany
            ?: $spyCompanyRole->getFkCompany()
            ?: $this->findIdCompanyByIdCompanyRole($spyCompanyRole->getIdCompanyRole());

        if (!$idCompany) {
            return;
        }

        $updateQuery = $this->getFactory()
            ->createCompanyRoleQuery()
            ->filterByFkCompany($idCompany);

        $idCompanyRole = $spyCompanyRole->getIdCompanyRole();

        if ($idCompanyRole) {
            $updateQuery->filterByIdCompanyRole($idCompanyRole, Criteria::NOT_EQUAL);
        }

        $updateQuery->update(['IsDefault' => false]);
    }

    protected function findIdCompanyByIdCompanyRole(?int $idCompanyRole): ?int
    {
        if (!$idCompanyRole) {
            return null;
        }

        return $this->getFactory()
            ->createCompanyRoleQuery()
            ->filterByIdCompanyRole($idCompanyRole)
            ->findOne()
            ?->getFkCompany();
    }
}
