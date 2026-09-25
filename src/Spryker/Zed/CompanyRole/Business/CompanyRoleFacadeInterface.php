<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Business;

use Generated\Shared\Transfer\CompanyResponseTransfer;
use Generated\Shared\Transfer\CompanyRoleCollectionCriteriaTransfer;
use Generated\Shared\Transfer\CompanyRoleCollectionTransfer;
use Generated\Shared\Transfer\CompanyRoleCriteriaFilterTransfer;
use Generated\Shared\Transfer\CompanyRoleResponseTransfer;
use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\PermissionCollectionTransfer;
use Generated\Shared\Transfer\PermissionTransfer;

interface CompanyRoleFacadeInterface
{
    /**
     * Specification:
     * - Finds a company role by CompanyRoleTransfer::idCompanyRole in the transfer
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CompanyRoleTransfer $companyRoleTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyRoleTransfer
     */
    public function getCompanyRoleById(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleTransfer;

    /**
     * Specification:
     * - Creates a company role
     * - Creates company role permission relations
     * - Demotes the company's previous default role when `CompanyRoleTransfer.isDefault` is set
     * - Persists the company role without validating it; use {@link createCompanyRole()} to have the name, the company and the requested permissions checked before the write
     *
     * @api
     *
     * @deprecated Use {@link createCompanyRole()} instead, which validates the company role before persisting it.
     *
     * @param \Generated\Shared\Transfer\CompanyRoleTransfer $companyRoleTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyRoleResponseTransfer
     */
    public function create(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleResponseTransfer;

    /**
     * Specification:
     * - Creates a company role
     * - Creates company role permission relations
     * - Returns an unsuccessful response when the name is missing, longer than the column allows or
     *   already used by another role of the same company, when the company is missing, or when a
     *   requested permission is not known to this installation
     * - Demotes the company's previous default role when `CompanyRoleTransfer.isDefault` is set
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CompanyRoleTransfer $companyRoleTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyRoleResponseTransfer
     */
    public function createCompanyRole(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleResponseTransfer;

    /**
     * Specification:
     * - Creates a company role by for a company
     * - Fills default name from a module configuration
     * - Creates company role permission relations
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CompanyResponseTransfer $companyResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyResponseTransfer
     */
    public function createByCompany(CompanyResponseTransfer $companyResponseTransfer): CompanyResponseTransfer;

    /**
     * Specification:
     * - Finds a company role by CompanyRoleTransfer::idCompanyRole in the transfer
     * - Updates fields in a company role entity
     * - Finds/creates/updates permissions according CompanyRoleTransfer::permissionCollection and updates
     * configuration in them
     * - Silently does nothing when the update is rejected; use {@link updateCompanyRole()} to read
     * the validation result
     *
     * @api
     *
     * @deprecated Use {@link updateCompanyRole()} instead, which reports whether the update succeeded.
     *
     * @param \Generated\Shared\Transfer\CompanyRoleTransfer $companyRoleTransfer
     *
     * @return void
     */
    public function update(CompanyRoleTransfer $companyRoleTransfer): void;

    /**
     * Specification:
     * - Finds a company role by CompanyRoleTransfer::idCompanyRole
     * - Deletes the company role
     * - Returns an unsuccessful response when the company role does not exist, when it is the
     *   company's default role, or when company users are still assigned to it
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CompanyRoleTransfer $companyRoleTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyRoleResponseTransfer
     */
    public function delete(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleResponseTransfer;

    /**
     * Specification:
     * - Finds company roles
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\CompanyRoleCollectionTransfer
     */
    public function findCompanyRoles(): CompanyRoleCollectionTransfer;

    /**
     * Specification:
     * - Finds company role permissions
     *
     * @api
     *
     * @param int $idCompanyRole
     *
     * @return \Generated\Shared\Transfer\PermissionCollectionTransfer
     */
    public function findCompanyRolePermissions(int $idCompanyRole): PermissionCollectionTransfer;

    /**
     * Specification:
     * - Removes related to the company user roles
     * - Creates relations roles to the company user according CompanyUserTransfer::companyRoleCollection
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CompanyUserTransfer $companyUserTransfer
     *
     * @return void
     */
    public function saveCompanyUser(CompanyUserTransfer $companyUserTransfer): void;

    /**
     * Specification:
     * - Hydrates a list of assigned to a company user permissions
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CompanyUserTransfer $companyUserTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyUserTransfer
     */
    public function hydrateCompanyUser(CompanyUserTransfer $companyUserTransfer): CompanyUserTransfer;

    /**
     * Specification:
     * - Collects related to a company user permissions from all assigned roles
     *
     * @api
     *
     * @param int $idCompanyUser
     *
     * @return \Generated\Shared\Transfer\PermissionCollectionTransfer
     */
    public function findPermissionsByIdCompanyUser(int $idCompanyUser): PermissionCollectionTransfer;

    /**
     * Specification:
     * - Finds a permission for a role
     * - Hydrates permission
     * - Returns an empty permission transfer if a desired combination does not exist
     *
     * @api
     *
     * @param int $idCompanyRole
     * @param int $idPermission
     *
     * @return \Generated\Shared\Transfer\PermissionTransfer
     */
    public function findPermissionByIdCompanyRoleByIdPermission(int $idCompanyRole, int $idPermission): PermissionTransfer;

    /**
     * Specification:
     * - Filters company users by assigned permission key.
     * - Filters company users by idCompany if provided.
     * - Returns company users ids.
     *
     * @api
     *
     * @param string $permissionKey
     * @param int|null $idCompany
     *
     * @return array<int>
     */
    public function getCompanyUserIdsByPermissionKey(string $permissionKey, ?int $idCompany = null): array;

    /**
     * Specification:
     * - Finds company roles according CompanyRoleCriteriaFilterTransfer
     *
     * @api
     *
     * @deprecated Use {@link getCompanyRoleCollectionByCollectionCriteria()} instead, which takes a
     * `CompanyRoleCollectionCriteriaTransfer`, memoizes nothing and loads the permissions of a whole
     * page in one query.
     *
     * @param \Generated\Shared\Transfer\CompanyRoleCriteriaFilterTransfer $criteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyRoleCollectionTransfer
     */
    public function getCompanyRoleCollection(
        CompanyRoleCriteriaFilterTransfer $criteriaFilterTransfer
    ): CompanyRoleCollectionTransfer;

    /**
     * Specification:
     * - Updates company role permission configuration
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PermissionTransfer $permissionTransfer
     *
     * @return void
     */
    public function updateCompanyRolePermission(PermissionTransfer $permissionTransfer): void;

    /**
     * Specification:
     * - Retrieves default company role.
     *
     * @api
     *
     * @deprecated Use {@link findDefaultCompanyRoleByIdCompany()} instead.
     *
     * @return \Generated\Shared\Transfer\CompanyRoleTransfer
     */
    public function getDefaultCompanyRole(): CompanyRoleTransfer;

    /**
     * Specification:
     * - Finds default company role for a given company by company id.
     *
     * @api
     *
     * @param int $idCompany
     *
     * @return \Generated\Shared\Transfer\CompanyRoleTransfer|null
     */
    public function findDefaultCompanyRoleByIdCompany(int $idCompany): ?CompanyRoleTransfer;

    /**
     * Specification:
     * - Finds company role by CompanyRoleTransfer::idCompanyRole.
     * - Returns null if company role does not exist.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CompanyRoleTransfer $companyRoleTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyRoleTransfer|null
     */
    public function findCompanyRoleById(CompanyRoleTransfer $companyRoleTransfer): ?CompanyRoleTransfer;

    /**
     * Specification:
     * - Finds a company role by uuid.
     * - Requires uuid field to be set in CompanyRoleTransfer taken as parameter.
     *
     * @api
     *
     * {@internal will work if UUID field is provided.}
     *
     * @param \Generated\Shared\Transfer\CompanyRoleTransfer $companyRoleTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyRoleResponseTransfer
     */
    public function findCompanyRoleByUuid(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleResponseTransfer;

    /**
     * Specification:
     * - Retrieves company role names grouped by company user IDs.
     * - Fetches data in bulk for performance optimization.
     *
     * @api
     *
     * @param list<int> $companyUserIds
     *
     * @return array<int, list<string>>
     */
    public function getCompanyRoleNamesGroupedByCompanyUserIds(array $companyUserIds): array;

    /**
     * Specification:
     * - Retrieves a company role collection filtered by `CompanyRoleConditionsTransfer`.
     * - Filters by company role uuids when `CompanyRoleConditionsTransfer.companyRoleUuids` is set.
     * - Filters by company uuids when `CompanyRoleConditionsTransfer.companyUuids` is set.
     * - Filters by company ids when `CompanyRoleConditionsTransfer.companyIds` is set.
     * - Filters by the company users a role is assigned to when
     *   `CompanyRoleConditionsTransfer.companyUserIds` is set.
     * - Filters by the default flag when `CompanyRoleConditionsTransfer.isDefault` is set.
     * - Matches the role name partially when `CompanyRoleConditionsTransfer.name` is set.
     * - Matches the company name partially when `CompanyRoleConditionsTransfer.companyName` is set.
     * - Matches the role name or the company name partially, OR-combined, when
     *   `CompanyRoleConditionsTransfer.searchTerm` is set.
     * - Sorts by the fields in `CompanyRoleCollectionCriteriaTransfer.sortCollection`, each resolved
     *   through `CompanyRoleConfig::getCompanyRoleCollectionSortableFieldMap()`; an unmapped field is
     *   ignored. The company role id always breaks a tie, so the order is total.
     * - Applies `CompanyRoleCollectionCriteriaTransfer.pagination` as a limit/offset window and
     *   returns the total number of results on `CompanyRoleCollectionTransfer.pagination.nbResults`.
     * - Hydrates every role with its permissions and its company.
     * - Hydrates every role with its company users and their customers when
     *   `CompanyRoleConditionsTransfer.withCompanyUsers` is set. Hydration is opt-in because it is
     *   not part of every consumer's contract; when requested, the whole page is loaded in one
     *   query rather than one query per role.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CompanyRoleCollectionCriteriaTransfer $companyRoleCollectionCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyRoleCollectionTransfer
     */
    public function getCompanyRoleCollectionByCollectionCriteria(
        CompanyRoleCollectionCriteriaTransfer $companyRoleCollectionCriteriaTransfer
    ): CompanyRoleCollectionTransfer;

    /**
     * Specification:
     * - Updates the company role identified by `CompanyRoleTransfer.idCompanyRole`.
     * - Returns an unsuccessful response when the role does not exist, its name is missing, longer
     *   than the column allows or already used by another role of the same company, its company
     *   differs from the stored one, the default flag is being cleared while the role holds it, or a
     *   requested permission is not known to this installation.
     * - Updates only the fields the transfer carries.
     * - Replaces the role's permissions with `CompanyRoleTransfer.permissionCollection`; a transfer
     *   carrying no permission collection therefore detaches every permission.
     * - Executes `CompanyRolePostSavePluginInterface` plugins on success.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CompanyRoleTransfer $companyRoleTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyRoleResponseTransfer
     */
    public function updateCompanyRole(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleResponseTransfer;
}
