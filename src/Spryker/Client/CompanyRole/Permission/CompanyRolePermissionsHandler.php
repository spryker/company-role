<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\CompanyRole\Permission;

use ArrayObject;
use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\PermissionCollectionTransfer;
use Generated\Shared\Transfer\PermissionTransfer;
use Spryker\Client\CompanyRole\Dependency\Client\CompanyRoleToPermissionClientInterface;
use Spryker\Client\CompanyRole\Zed\CompanyRoleStubInterface;

class CompanyRolePermissionsHandler implements CompanyRolePermissionsHandlerInterface
{
    /**
     * @var string
     */
    protected const PERMISSION_KEY_GLOSSARY_PREFIX = 'permission.name.';

    /**
     * @var \Spryker\Client\CompanyRole\Dependency\Client\CompanyRoleToPermissionClientInterface
     */
    protected $permissionClient;

    /**
     * @var \Spryker\Client\CompanyRole\Zed\CompanyRoleStubInterface
     */
    protected $companyRoleStub;

    public function __construct(
        CompanyRoleToPermissionClientInterface $permissionClient,
        CompanyRoleStubInterface $companyRoleStub
    ) {
        $this->permissionClient = $permissionClient;
        $this->companyRoleStub = $companyRoleStub;
    }

    public function findNonInfrastructuralCompanyRolePermissionsByIdCompanyRole(
        CompanyRoleTransfer $companyRoleTransfer
    ): PermissionCollectionTransfer {
        $availablePermissions = $this->permissionClient->findMergedRegisteredNonInfrastructuralPermissions()->getPermissions();
        $companyRolePermissions = $this->companyRoleStub->findCompanyRolePermissions($companyRoleTransfer)
            ->getPermissions();

        $availableCompanyRolePermissions = $this->getAvailableCompanyRolePermissions(
            $availablePermissions,
            $companyRolePermissions,
            $companyRoleTransfer,
        );

        $permissions = new ArrayObject();
        foreach ($availableCompanyRolePermissions as $permissionTransfer) {
            $permissionData = $this->transformPermissionTransferToArray(
                $availableCompanyRolePermissions[$permissionTransfer->getKey()],
            );

            $permissions->append($permissionData);
        }

        return (new PermissionCollectionTransfer())
            ->setPermissions($permissions);
    }

    protected function transformPermissionTransferToArray(
        PermissionTransfer $permissionTransfer
    ): array {
        $permissionData = $permissionTransfer->toArray(false, true);

        $permissionGlossaryKeyName = static::PERMISSION_KEY_GLOSSARY_PREFIX . $permissionData[PermissionTransfer::KEY];
        $permissionData[PermissionTransfer::KEY] = $permissionGlossaryKeyName;

        $idCompanyRole = $permissionTransfer->getIdCompanyRole();
        if ($permissionTransfer->getIdCompanyRole()) {
            $permissionData[CompanyRoleTransfer::ID_COMPANY_ROLE] = $idCompanyRole;
        }

        return $permissionData;
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\PermissionTransfer> $availablePermissions
     * @param \ArrayObject<int, \Generated\Shared\Transfer\PermissionTransfer> $companyRolePermissions
     * @param \Generated\Shared\Transfer\CompanyRoleTransfer $companyRoleTransfer
     *
     * @return array<\Generated\Shared\Transfer\PermissionTransfer> Keys are permission keys
     */
    protected function getAvailableCompanyRolePermissions(
        ArrayObject $availablePermissions,
        ArrayObject $companyRolePermissions,
        CompanyRoleTransfer $companyRoleTransfer
    ): array {
        $availableCompanyRolePermissions = [];

        $companyRoleTransfer->requireIdCompanyRole();

        foreach ($availablePermissions as $availablePermission) {
            $assignedCompanyRolePermission = $this->findAssignedCompanyRolePermission(
                $availablePermission,
                $companyRolePermissions,
            );

            if ($assignedCompanyRolePermission) {
                $availablePermission->setIdCompanyRole($companyRoleTransfer->getIdCompanyRole());
            }

            $availableCompanyRolePermissions[$availablePermission->getKey()] = $availablePermission;
        }

        return $availableCompanyRolePermissions;
    }

    /**
     * @param \Generated\Shared\Transfer\PermissionTransfer $availablePermission
     * @param \ArrayObject<int, \Generated\Shared\Transfer\PermissionTransfer> $companyRolePermissions
     *
     * @return \Generated\Shared\Transfer\PermissionTransfer|null
     */
    protected function findAssignedCompanyRolePermission(
        PermissionTransfer $availablePermission,
        ArrayObject $companyRolePermissions
    ): ?PermissionTransfer {
        foreach ($companyRolePermissions as $companyRolePermission) {
            if ($companyRolePermission->getKey() === $availablePermission->getKey()) {
                return $companyRolePermission;
            }
        }

        return null;
    }
}
