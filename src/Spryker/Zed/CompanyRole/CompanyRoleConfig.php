<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole;

use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\PermissionCollectionTransfer;
use Generated\Shared\Transfer\PermissionTransfer;
use Orm\Zed\Company\Persistence\Map\SpyCompanyTableMap;
use Orm\Zed\CompanyRole\Persistence\Map\SpyCompanyRoleTableMap;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class CompanyRoleConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    protected const DEFAULT_ADMIN_ROLE_NAME = 'Administrator';

    protected const string SORT_FIELD_NAME = 'name';

    protected const string SORT_FIELD_COMPANY_NAME = 'companyName';

    protected const string SORT_FIELD_IS_DEFAULT = 'isDefault';

    /**
     * @api
     *
     * @return string
     */
    public function getDefaultAdminRoleName(): string
    {
        return static::DEFAULT_ADMIN_ROLE_NAME;
    }

    /**
     * @api
     *
     * @return array<string>
     */
    public function getAdminRolePermissionKeys(): array
    {
        return [];
    }

    /**
     * @api
     *
     * @return array<\Generated\Shared\Transfer\CompanyRoleTransfer>
     */
    public function getPredefinedCompanyRoles(): array
    {
        return [
            $this->getAdminRole(),
        ];
    }

    protected function getAdminRole(): CompanyRoleTransfer
    {
        return (new CompanyRoleTransfer())
            ->setName(static::DEFAULT_ADMIN_ROLE_NAME)
            ->setIsDefault(true)
            ->setPermissionCollection($this->createPermissionCollectionFromPermissionKeys(
                $this->getAdminRolePermissionKeys(),
            ));
    }

    /**
     * @param array<string> $permissionKeys
     *
     * @return \Generated\Shared\Transfer\PermissionCollectionTransfer
     */
    protected function createPermissionCollectionFromPermissionKeys(array $permissionKeys): PermissionCollectionTransfer
    {
        $permissions = new PermissionCollectionTransfer();

        foreach ($permissionKeys as $permissionKey) {
            $permission = (new PermissionTransfer())
                ->setKey($permissionKey);

            $permissions->addPermission($permission);
        }

        return $permissions;
    }

    /**
     * Specification:
     * - Returns the map of sortable company role collection field names to their database columns.
     * - The keys are the field names a consumer may sort by; the values reach an SQL `ORDER BY`
     *   clause, so a field outside this map is never passed through.
     * - Mirrors the columns the Back Office company role table allows sorting by, minus the role
     *   id, which is internal and not exposed outside the Back Office.
     *
     * @api
     *
     * @return array<string, string>
     */
    public function getCompanyRoleCollectionSortableFieldMap(): array
    {
        return [
            static::SORT_FIELD_NAME => SpyCompanyRoleTableMap::COL_NAME,
            static::SORT_FIELD_COMPANY_NAME => SpyCompanyTableMap::COL_NAME,
            static::SORT_FIELD_IS_DEFAULT => SpyCompanyRoleTableMap::COL_IS_DEFAULT,
        ];
    }
}
