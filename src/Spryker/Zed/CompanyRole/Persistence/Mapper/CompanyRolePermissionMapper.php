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

class CompanyRolePermissionMapper implements CompanyRolePermissionMapperInterface
{
    public function hydratePermissionCollection(
        SpyCompanyRole $spyCompanyRole,
        CompanyRoleTransfer $companyRoleTransfer
    ): CompanyRoleTransfer {
        return $companyRoleTransfer->setPermissionCollection(
            $this->mapCompanyRoleToPermissionEntitiesToPermissionCollectionTransfer(
                $spyCompanyRole->getSpyCompanyRoleToPermissionsJoinPermission(),
                new PermissionCollectionTransfer(),
            ),
        );
    }

    /**
     * @param iterable<\Orm\Zed\CompanyRole\Persistence\SpyCompanyRoleToPermission> $companyRoleToPermissionEntities
     */
    public function mapCompanyRoleToPermissionEntitiesToPermissionCollectionTransfer(
        iterable $companyRoleToPermissionEntities,
        PermissionCollectionTransfer $permissionCollectionTransfer
    ): PermissionCollectionTransfer {
        foreach ($companyRoleToPermissionEntities as $companyRoleToPermissionEntity) {
            $permissionCollectionTransfer->addPermission(
                $this->mapCompanyRoleToPermissionEntityToPermissionTransfer(
                    $companyRoleToPermissionEntity,
                    new PermissionTransfer(),
                ),
            );
        }

        return $permissionCollectionTransfer;
    }

    public function mapCompanyRoleToPermissionEntityToPermissionTransfer(
        SpyCompanyRoleToPermission $companyRoleToPermissionEntity,
        PermissionTransfer $permissionTransfer
    ): PermissionTransfer {
        $permissionEntity = $companyRoleToPermissionEntity->getPermission();

        $permissionTransfer
        ->setIdPermission($companyRoleToPermissionEntity->getFkPermission())
        ->setConfiguration($this->decodeJson($companyRoleToPermissionEntity->getConfiguration()))
        ->setConfigurationSignature($this->decodeJson($permissionEntity->getConfigurationSignature()))
        ->setKey($permissionEntity->getKey());

        return $permissionTransfer;
    }

    /**
     * @return array<mixed>|null
     */
    protected function decodeJson(?string $value): ?array
    {
        if ($value === null) {
            return null;
        }

        $decodedValue = json_decode($value, true);

        return is_array($decodedValue) ? $decodedValue : null;
    }
}
