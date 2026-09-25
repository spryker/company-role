<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Persistence;

use ArrayObject;
use Generated\Shared\Transfer\CompanyRoleCollectionCriteriaTransfer;
use Generated\Shared\Transfer\CompanyRoleCollectionTransfer;
use Generated\Shared\Transfer\CompanyRoleConditionsTransfer;
use Generated\Shared\Transfer\CompanyRoleCriteriaFilterTransfer;
use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\CompanyUserCollectionTransfer;
use Generated\Shared\Transfer\FilterTransfer;
use Generated\Shared\Transfer\PaginationTransfer;
use Generated\Shared\Transfer\PermissionCollectionTransfer;
use Generated\Shared\Transfer\PermissionTransfer;
use Orm\Zed\Company\Persistence\Map\SpyCompanyTableMap;
use Orm\Zed\CompanyRole\Persistence\Map\SpyCompanyRoleTableMap;
use Orm\Zed\CompanyRole\Persistence\Map\SpyCompanyRoleToCompanyUserTableMap;
use Orm\Zed\CompanyRole\Persistence\SpyCompanyRole;
use Orm\Zed\CompanyRole\Persistence\SpyCompanyRoleQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\CompanyRole\Persistence\CompanyRolePersistenceFactory getFactory()
 */
class CompanyRoleRepository extends AbstractRepository implements CompanyRoleRepositoryInterface
{
    protected const string UUID_FILTER_METHOD = 'filterByUuid_In';

    /**
     * @see \Orm\Zed\CompanyRole\Persistence\Map\SpyCompanyRoleToCompanyUserTableMap::COL_FK_COMPANY_USER
     */
    protected const string COL_FK_COMPANY_USER = 'spy_company_role_to_company_user.fk_company_user';

    protected const string CONDITION_ROLE_NAME_LIKE = 'companyRoleNameLike';

    protected const string CONDITION_COMPANY_NAME_LIKE = 'companyNameLike';

    /**
     * @var array<string, \Generated\Shared\Transfer\CompanyRoleCollectionTransfer>
     */
    protected static array $companyRoleCollectionCache = [];

    public function getCompanyRoleById(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleTransfer
    {
        $companyRoleTransfer->requireIdCompanyRole();
        $query = $this->getFactory()
            ->createCompanyRoleQuery()
            ->filterByIdCompanyRole($companyRoleTransfer->getIdCompanyRole());

        $spyCompanyRole = $this->buildQueryFromCriteria($query)->findOne();

        return $this->prepareCompanyRoleTransfer($spyCompanyRole);
    }

    public function findPermissionsByIdCompanyUser(int $idCompanyUser): PermissionCollectionTransfer
    {
        $query = $this->getFactory()
            ->createCompanyRoleToPermissionQuery()
            ->joinWithPermission()
            ->joinCompanyRole()
            ->useCompanyRoleQuery()
                ->joinSpyCompanyRoleToCompanyUser()
                    ->useSpyCompanyRoleToCompanyUserQuery()
                        ->filterByFkCompanyUser($idCompanyUser)
                    ->endUse()
            ->endUse();

        $companyRoleToPermissionEntities = $this->buildQueryFromCriteria($query)->find();

        $permissionCollectionTransfer = new PermissionCollectionTransfer();
        foreach ($companyRoleToPermissionEntities as $companyRoleToPermissionEntity) {
            $permissionTransfer = new PermissionTransfer();

            $permissionTransfer->setKey($companyRoleToPermissionEntity->getPermission()->getKey());

            $permissionTransfer->setConfigurationSignature(
                $this->jsonDecode($companyRoleToPermissionEntity->getPermission()->getConfigurationSignature()),
            );

            $permissionTransfer->setConfiguration(
                $this->jsonDecode($companyRoleToPermissionEntity->getConfiguration()),
            );

            $permissionCollectionTransfer->addPermission($permissionTransfer);
        }

        return $permissionCollectionTransfer;
    }

    public function findPermissionsByIdCompanyRoleByIdPermission(int $idCompanyRole, int $idPermission): PermissionTransfer
    {
        $query = $this->getFactory()
            ->createCompanyRoleToPermissionQuery()
            ->filterByFkCompanyRole($idCompanyRole)
            ->filterByFkPermission($idPermission)
            ->joinWithPermission();

        $companyRoleToPermissionEntity = $this->buildQueryFromCriteria($query)->findOne();

        $permissionTransfer = new PermissionTransfer();

        if (!$companyRoleToPermissionEntity) {
            return $permissionTransfer;
        }

        $permissionTransfer->setKey($companyRoleToPermissionEntity->getPermission()->getKey());
        $permissionTransfer->setIdPermission($companyRoleToPermissionEntity->getFkPermission());
        $permissionTransfer->setIdCompanyRole($companyRoleToPermissionEntity->getFkCompanyRole());

        $permissionTransfer->setConfigurationSignature(
            $this->jsonDecode($companyRoleToPermissionEntity->getPermission()->getConfigurationSignature()),
        );

        $permissionTransfer->setConfiguration(
            $this->jsonDecode($companyRoleToPermissionEntity->getConfiguration()),
        );

        return $permissionTransfer;
    }

    /**
     * @param mixed $value
     *
     * @return array
     */
    protected function jsonDecode($value)
    {
        $decodedValue = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decodedValue;
        }

        return [];
    }

    public function findCompanyRole(): CompanyRoleCollectionTransfer
    {
        $query = $this->getFactory()
            ->createCompanyRoleQuery()
            ->joinWithSpyCompanyRoleToPermission()
                ->useSpyCompanyRoleToPermissionQuery()
                ->joinWithPermission()
            ->endUse();

        $companyRoleEntityTransfers = $this->buildQueryFromCriteria($query)->find();

        $companyRoleCollectionTransfer = new CompanyRoleCollectionTransfer();

        foreach ($companyRoleEntityTransfers as $spyCompanyRole) {
            $companyRoleTransfer = $this->prepareCompanyRoleTransfer($spyCompanyRole);
            $companyRoleCollectionTransfer->addRole($companyRoleTransfer);
        }

        return $companyRoleCollectionTransfer;
    }

    public function findCompanyRolePermissions(int $idCompanyRole): PermissionCollectionTransfer
    {
        $query = $this->getFactory()
            ->createCompanyRoleToPermissionQuery()
            ->filterByFkCompanyRole($idCompanyRole)
            ->joinWithPermission();

        $companyRoleToPermissionEntities = $this->buildQueryFromCriteria($query)->find();

        return $this->getFactory()
            ->createCompanyRolePermissionMapper()
            ->mapCompanyRoleToPermissionEntitiesToPermissionCollectionTransfer(
                $companyRoleToPermissionEntities,
                new PermissionCollectionTransfer(),
            );
    }

    /**
     * @module Permission
     *
     * @param string $permissionKey
     * @param int|null $idCompany
     *
     * @return array<int>
     */
    public function getCompanyUserIdsByPermissionKey(string $permissionKey, ?int $idCompany = null): array
    {
        $companyRoleQuery = $this->getFactory()->createCompanyRoleQuery();

        if ($idCompany) {
            $companyRoleQuery->filterByFkCompany($idCompany);
        }

        /** @var \Propel\Runtime\Collection\ArrayCollection $companyUserIds */
        $companyUserIds = $companyRoleQuery->joinSpyCompanyRoleToCompanyUser()
            ->useSpyCompanyRoleToPermissionQuery()
                ->usePermissionQuery()
                    ->filterByKey($permissionKey)
                ->endUse()
            ->endUse()
            ->select([SpyCompanyRoleToCompanyUserTableMap::COL_FK_COMPANY_USER])
            ->find();

         return $companyUserIds->toArray();
    }

    public function getCompanyRoleCollection(
        CompanyRoleCriteriaFilterTransfer $companyRoleCriteriaFilterTransfer
    ): CompanyRoleCollectionTransfer {
        $hash = md5($this->recursiveImplode($companyRoleCriteriaFilterTransfer->toArray(), ','));
        if (isset(static::$companyRoleCollectionCache[$hash])) {
            return static::$companyRoleCollectionCache[$hash];
        }

        $query = $this->getFactory()
            ->createCompanyRoleQuery();

        if ($companyRoleCriteriaFilterTransfer->getIdCompany() !== null) {
            $query->filterByFkCompany($companyRoleCriteriaFilterTransfer->getIdCompany());
        }

        if ($companyRoleCriteriaFilterTransfer->getIdCompanyUser() !== null) {
            $query->useSpyCompanyRoleToCompanyUserQuery()
                ->filterByFkCompanyUser($companyRoleCriteriaFilterTransfer->getIdCompanyUser())
                ->endUse();
        }

        $idCompanyUsers = $companyRoleCriteriaFilterTransfer->getIdCompanyUsers();
        if ($idCompanyUsers !== []) {
            $query->useSpyCompanyRoleToCompanyUserQuery()
                    ->filterByFkCompanyUser_In($idCompanyUsers)
                ->endUse()
                ->distinct();
        }

        $collection = $this->buildQueryFromCriteria($query, $companyRoleCriteriaFilterTransfer->getFilter());
        /** @var array<\Orm\Zed\CompanyRole\Persistence\SpyCompanyRole> $spyCompanyRoleCollection */
        $spyCompanyRoleCollection = $this->getPaginatedCollection($collection, $companyRoleCriteriaFilterTransfer->getPagination());

        $collectionTransfer = new CompanyRoleCollectionTransfer();
        foreach ($spyCompanyRoleCollection as $spyCompanyRole) {
            $companyRoleTransfer = $this->prepareCompanyRoleTransfer($spyCompanyRole);
            $collectionTransfer->addRole($companyRoleTransfer);
        }

        $collectionTransfer->setPagination($companyRoleCriteriaFilterTransfer->getPagination());

        static::$companyRoleCollectionCache[$hash] = $collectionTransfer;

        return $collectionTransfer;
    }

    public function getCompanyRoleCollectionByCollectionCriteria(
        CompanyRoleCollectionCriteriaTransfer $companyRoleCollectionCriteriaTransfer
    ): CompanyRoleCollectionTransfer {
        $paginationTransfer = $companyRoleCollectionCriteriaTransfer->getPagination();

        $query = $this->buildCompanyRoleQueryByConditions($companyRoleCollectionCriteriaTransfer->getCompanyRoleConditions());
        $query = $this->applyCompanyRoleSortToQuery($query, $companyRoleCollectionCriteriaTransfer->getSortCollection());
        $query = $this->applyCompanyRolePagination($query, $paginationTransfer);

        $companyRoleCollectionTransfer = new CompanyRoleCollectionTransfer();

        $companyRoleEntities = $query->find();
        $companyRoleIds = $this->extractCompanyRoleIds($companyRoleEntities);
        $permissionCollectionTransfersByIdCompanyRole = $this->getPermissionCollectionsByCompanyRoleIds($companyRoleIds);
        $companyUserCollectionTransfersByIdCompanyRole = [];

        if ($companyRoleCollectionCriteriaTransfer->getCompanyRoleConditions()?->getWithCompanyUsers()) {
            $companyUserCollectionTransfersByIdCompanyRole = $this->getCompanyUserCollectionsByCompanyRoleIds($companyRoleIds);
        }

        foreach ($companyRoleEntities as $companyRoleEntity) {
            $companyRoleTransfer = $this->getFactory()
                ->createCompanyRoleMapper()
                ->mapEntityToCompanyRoleTransfer($companyRoleEntity, new CompanyRoleTransfer());

            $companyRoleTransfer = $this->getFactory()
                ->createCompanyRoleCompanyMapper()
                ->mapCompanyFromCompanyRoleEntityToCompanyRoleTransfer($companyRoleEntity, $companyRoleTransfer);

            $companyRoleTransfer->setPermissionCollection(
                $permissionCollectionTransfersByIdCompanyRole[$companyRoleEntity->getIdCompanyRole()]
                    ?? new PermissionCollectionTransfer(),
            );

            if ($companyUserCollectionTransfersByIdCompanyRole !== []) {
                $companyRoleTransfer->setCompanyUserCollection(
                    $companyUserCollectionTransfersByIdCompanyRole[$companyRoleEntity->getIdCompanyRole()]
                        ?? new CompanyUserCollectionTransfer(),
                );
            }

            $companyRoleCollectionTransfer->addRole($companyRoleTransfer);
        }

        return $companyRoleCollectionTransfer->setPagination($paginationTransfer);
    }

    /**
     * @param iterable<\Orm\Zed\CompanyRole\Persistence\SpyCompanyRole> $companyRoleEntities
     *
     * @return list<int>
     */
    protected function extractCompanyRoleIds(iterable $companyRoleEntities): array
    {
        $companyRoleIds = [];

        foreach ($companyRoleEntities as $companyRoleEntity) {
            $companyRoleIds[] = $companyRoleEntity->getIdCompanyRole();
        }

        return $companyRoleIds;
    }

    /**
     * @module Permission
     *
     * @param list<int> $companyRoleIds
     *
     * @return array<int, \Generated\Shared\Transfer\PermissionCollectionTransfer>
     */
    protected function getPermissionCollectionsByCompanyRoleIds(array $companyRoleIds): array
    {
        if ($companyRoleIds === []) {
            return [];
        }

        $companyRoleToPermissionEntities = $this->getFactory()
            ->createCompanyRoleToPermissionQuery()
            ->filterByFkCompanyRole_In($companyRoleIds)
            ->joinWithPermission()
            ->find();

        $permissionCollectionTransfersByIdCompanyRole = [];

        foreach ($companyRoleToPermissionEntities as $companyRoleToPermissionEntity) {
            $idCompanyRole = $companyRoleToPermissionEntity->getFkCompanyRole();

            if (!isset($permissionCollectionTransfersByIdCompanyRole[$idCompanyRole])) {
                $permissionCollectionTransfersByIdCompanyRole[$idCompanyRole] = new PermissionCollectionTransfer();
            }

            $permissionCollectionTransfersByIdCompanyRole[$idCompanyRole]->addPermission(
                $this->getFactory()
                    ->createCompanyRolePermissionMapper()
                    ->mapCompanyRoleToPermissionEntityToPermissionTransfer(
                        $companyRoleToPermissionEntity,
                        new PermissionTransfer(),
                    ),
            );
        }

        return $permissionCollectionTransfersByIdCompanyRole;
    }

    /**
     * @module CompanyUser
     * @module Customer
     *
     * @param list<int> $companyRoleIds
     *
     * @return array<int, \Generated\Shared\Transfer\CompanyUserCollectionTransfer>
     */
    protected function getCompanyUserCollectionsByCompanyRoleIds(array $companyRoleIds): array
    {
        if ($companyRoleIds === []) {
            return [];
        }

        $companyRoleToCompanyUserEntities = $this->getFactory()
            ->createCompanyRoleToCompanyUserQuery()
            ->filterByFkCompanyRole_In($companyRoleIds)
            ->joinWithCompanyUser()
            ->useCompanyUserQuery()
                ->joinWithCustomer()
            ->endUse()
            ->find();

        $companyRoleToCompanyUserEntitiesByIdCompanyRole = [];

        foreach ($companyRoleToCompanyUserEntities as $companyRoleToCompanyUserEntity) {
            $companyRoleToCompanyUserEntitiesByIdCompanyRole[$companyRoleToCompanyUserEntity->getFkCompanyRole()][] = $companyRoleToCompanyUserEntity;
        }

        $companyUserCollectionTransfersByIdCompanyRole = [];

        foreach ($companyRoleToCompanyUserEntitiesByIdCompanyRole as $idCompanyRole => $entities) {
            $companyUserCollectionTransfersByIdCompanyRole[$idCompanyRole] = $this->getFactory()
                ->createCompanyRoleCompanyUserMapper()
                ->mapCompanyRoleToCompanyUserEntitiesToCompanyUserCollectionTransfer(
                    $entities,
                    new CompanyUserCollectionTransfer(),
                );
        }

        return $companyUserCollectionTransfersByIdCompanyRole;
    }

    /**
     * @module Company
     */
    protected function buildCompanyRoleQueryByConditions(
        ?CompanyRoleConditionsTransfer $companyRoleConditionsTransfer
    ): SpyCompanyRoleQuery {
        /** @var \Orm\Zed\CompanyRole\Persistence\SpyCompanyRoleQuery $query */
        $query = $this->getFactory()
            ->createCompanyRoleQuery()
            ->leftJoinWithCompany();

        if ($companyRoleConditionsTransfer === null) {
            return $query;
        }

        if ($companyRoleConditionsTransfer->getCompanyRoleUuids() && method_exists($query, static::UUID_FILTER_METHOD)) {
            $query->filterByUuid_In($companyRoleConditionsTransfer->getCompanyRoleUuids());
        }

        if ($companyRoleConditionsTransfer->getCompanyUuids() && defined(SpyCompanyTableMap::class . '::COL_UUID')) {
            $query->addUsingAlias(
                SpyCompanyTableMap::COL_UUID,
                $companyRoleConditionsTransfer->getCompanyUuids(),
                Criteria::IN,
            );
        }

        if ($companyRoleConditionsTransfer->getCompanyIds()) {
            $query->filterByFkCompany_In($companyRoleConditionsTransfer->getCompanyIds());
        }

        if ($companyRoleConditionsTransfer->getCompanyUserIds()) {
            $query->useSpyCompanyRoleToCompanyUserQuery()
                    ->filterByFkCompanyUser_In($companyRoleConditionsTransfer->getCompanyUserIds())
                ->endUse()
                ->distinct();
        }

        if ($companyRoleConditionsTransfer->getIsDefault() !== null) {
            $query->filterByIsDefault($companyRoleConditionsTransfer->getIsDefault());
        }

        if ($companyRoleConditionsTransfer->getName()) {
            $query->filterByName(sprintf('%%%s%%', $companyRoleConditionsTransfer->getName()), Criteria::LIKE);
        }

        if ($companyRoleConditionsTransfer->getCompanyName()) {
            $query->addUsingAlias(
                SpyCompanyTableMap::COL_NAME,
                sprintf('%%%s%%', $companyRoleConditionsTransfer->getCompanyName()),
                Criteria::LIKE,
            );
        }

        if ($companyRoleConditionsTransfer->getSearchTerm()) {
            $query = $this->applySearchTermToCompanyRoleQuery(
                $query,
                (string)$companyRoleConditionsTransfer->getSearchTerm(),
            );
        }

        return $query;
    }

    /**
     * @module Company
     */
    protected function applySearchTermToCompanyRoleQuery(
        SpyCompanyRoleQuery $query,
        string $searchTerm
    ): SpyCompanyRoleQuery {
        $pattern = sprintf('%%%s%%', $searchTerm);

        $query
            ->condition(static::CONDITION_ROLE_NAME_LIKE, sprintf('%s LIKE ?', SpyCompanyRoleTableMap::COL_NAME), $pattern)
            ->condition(static::CONDITION_COMPANY_NAME_LIKE, sprintf('%s LIKE ?', SpyCompanyTableMap::COL_NAME), $pattern)
            ->combine(
                [static::CONDITION_ROLE_NAME_LIKE, static::CONDITION_COMPANY_NAME_LIKE],
                Criteria::LOGICAL_OR,
            );

        return $query;
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\SortTransfer> $sortCollection
     */
    protected function applyCompanyRoleSortToQuery(SpyCompanyRoleQuery $query, ArrayObject $sortCollection): SpyCompanyRoleQuery
    {
        $sortableFieldMap = $this->getFactory()->getConfig()->getCompanyRoleCollectionSortableFieldMap();
        $lastDirection = Criteria::ASC;

        foreach ($sortCollection as $sortTransfer) {
            $column = $sortableFieldMap[$sortTransfer->getField()] ?? null;

            if ($column === null) {
                continue;
            }

            $lastDirection = $sortTransfer->getIsAscending() === false ? Criteria::DESC : Criteria::ASC;
            $query->orderBy($column, $lastDirection);
        }

        return $query->orderBy(SpyCompanyRoleTableMap::COL_ID_COMPANY_ROLE, $lastDirection);
    }

    protected function applyCompanyRolePagination(
        SpyCompanyRoleQuery $query,
        ?PaginationTransfer $paginationTransfer = null
    ): SpyCompanyRoleQuery {
        if ($paginationTransfer === null) {
            return $query;
        }

        $paginationTransfer->setNbResults($query->count());

        $limit = $paginationTransfer->getLimit();
        $offset = $paginationTransfer->getOffset();

        if ($limit !== null) {
            $query->limit($limit);
        }

        if ($offset !== null) {
            $query->offset($offset);
        }

        return $query;
    }

    public function existsCompanyRoleByNameAndIdCompany(
        string $name,
        int $idCompany,
        ?int $excludedIdCompanyRole = null
    ): bool {
        $query = $this->getFactory()
            ->createCompanyRoleQuery()
            ->filterByFkCompany($idCompany)
            ->filterByName($name);

        if ($excludedIdCompanyRole !== null) {
            $query->filterByIdCompanyRole($excludedIdCompanyRole, Criteria::NOT_EQUAL);
        }

        return $query->exists();
    }

    public function buildQueryFromCriteria(ModelCriteria $modelCriteria, ?FilterTransfer $filterTransfer = null): ModelCriteria
    {
        $modelCriteria = parent::buildQueryFromCriteria($modelCriteria, $filterTransfer);

        $modelCriteria->setFormatter(ModelCriteria::FORMAT_OBJECT);

        return $modelCriteria;
    }

    /**
     * @return \Propel\Runtime\Collection\Collection|\Propel\Runtime\Collection\ObjectCollection|array<\Propel\Runtime\ActiveRecord\ActiveRecordInterface>
     */
    protected function getPaginatedCollection(ModelCriteria $query, ?PaginationTransfer $paginationTransfer = null)
    {
        if ($paginationTransfer !== null) {
            $page = $paginationTransfer
                ->requirePage()
                ->getPage();

            $maxPerPage = $paginationTransfer
                ->requireMaxPerPage()
                ->getMaxPerPage();

            $paginationModel = $query->paginate($page, $maxPerPage);

            $paginationTransfer->setNbResults($paginationModel->getNbResults());
            $paginationTransfer->setFirstIndex($paginationModel->getFirstIndex());
            $paginationTransfer->setLastIndex($paginationModel->getLastIndex());
            $paginationTransfer->setFirstPage($paginationModel->getFirstPage());
            $paginationTransfer->setLastPage($paginationModel->getLastPage());
            $paginationTransfer->setNextPage($paginationModel->getNextPage());
            $paginationTransfer->setPreviousPage($paginationModel->getPreviousPage());

            return $paginationModel->getResults();
        }

        return $query->find();
    }

    protected function prepareCompanyRoleTransfer(SpyCompanyRole $spyCompanyRole): CompanyRoleTransfer
    {
        $companyRoleTransfer = $this->getFactory()
            ->createCompanyRoleMapper()
            ->mapEntityToCompanyRoleTransfer(
                $spyCompanyRole,
                new CompanyRoleTransfer(),
            );

        $companyRoleTransfer = $this->getFactory()
            ->createCompanyRolePermissionMapper()
            ->hydratePermissionCollection(
                $spyCompanyRole,
                $companyRoleTransfer,
            );

        $companyRoleTransfer = $this->getFactory()
            ->createCompanyRoleCompanyUserMapper()
            ->hydrateCompanyUserCollection(
                $spyCompanyRole,
                $companyRoleTransfer,
            );

        $companyRoleTransfer = $this->getFactory()
            ->createCompanyRoleCompanyMapper()
            ->mapCompanyFromCompanyRoleEntityToCompanyRoleTransfer(
                $spyCompanyRole,
                $companyRoleTransfer,
            );

        return $companyRoleTransfer;
    }

    /**
     * @deprecated Use {@link findDefaultCompanyRoleByIdCompany()} instead.
     *
     * @return \Generated\Shared\Transfer\CompanyRoleTransfer
     */
    public function getDefaultCompanyRole(): CompanyRoleTransfer
    {
        $query = $this->getFactory()
            ->createCompanyRoleQuery()
            ->filterByIsDefault(true);

        $spyCompanyRole = $this->buildQueryFromCriteria($query)->findOne();

        return $this->prepareCompanyRoleTransfer($spyCompanyRole);
    }

    public function findDefaultCompanyRoleByIdCompany(int $idCompany): ?CompanyRoleTransfer
    {
        $companyRoleEntity = $this->getFactory()
            ->createCompanyRoleQuery()
            ->filterByFkCompany($idCompany)
            ->filterByIsDefault(true)
            ->findOne();

        if (!$companyRoleEntity) {
            return null;
        }

        return $this->prepareCompanyRoleTransfer($companyRoleEntity);
    }

    public function hasUsers(int $idCompanyRole): bool
    {
        $spyCompanyRoleToCompanyUser = $this->getFactory()
            ->createCompanyRoleToCompanyUserQuery()
            ->filterByFkCompanyRole($idCompanyRole)
            ->findOne();

        return ($spyCompanyRoleToCompanyUser !== null);
    }

    public function findCompanyRoleById(CompanyRoleTransfer $companyRoleTransfer): ?CompanyRoleTransfer
    {
        $companyRoleTransfer->requireIdCompanyRole();

        $companyRoleEntity = $this->getFactory()
            ->createCompanyRoleQuery()
            ->filterByIdCompanyRole($companyRoleTransfer->getIdCompanyRole())
            ->findOne();

        if (!$companyRoleEntity) {
            return null;
        }

        return $this->prepareCompanyRoleTransfer($companyRoleEntity);
    }

    /**
     * @module Company
     *
     * @param string $companyRoleUuid
     *
     * @return \Generated\Shared\Transfer\CompanyRoleTransfer|null
     */
    public function findCompanyRoleByUuid(string $companyRoleUuid): ?CompanyRoleTransfer
    {
        $companyRoleEntity = $this->getFactory()
            ->createCompanyRoleQuery()
            ->joinCompany()
            ->filterByUuid($companyRoleUuid)
            ->findOne();

        if (!$companyRoleEntity) {
            return null;
        }

        return $this->prepareCompanyRoleTransfer($companyRoleEntity);
    }

    /**
     * @param list<int> $companyUserIds
     *
     * @return array<int, list<string>>
     */
    public function getCompanyRoleNamesGroupedByCompanyUserIds(array $companyUserIds): array
    {
        if ($companyUserIds === []) {
            return [];
        }

        $query = $this->getFactory()->createCompanyRoleQuery();

        $results = $query
            ->joinSpyCompanyRoleToCompanyUser()
            ->useSpyCompanyRoleToCompanyUserQuery()
                ->filterByFkCompanyUser_In($companyUserIds)
            ->endUse()
            ->select([
                SpyCompanyRoleTableMap::COL_NAME,
                static::COL_FK_COMPANY_USER,
            ])
            ->find();

        $groupedRoles = [];

        foreach ($results as $result) {
            $groupedRoles[$result[static::COL_FK_COMPANY_USER]] ??= [];
            $groupedRoles[$result[static::COL_FK_COMPANY_USER]][] = $result[SpyCompanyRoleTableMap::COL_NAME];
        }

        return $groupedRoles;
    }

    /**
     * @param list<int> $companyRoleIds
     *
     * @return array<int, int>
     */
    public function getCompanyRoleIdsBelongingToCompany(array $companyRoleIds, int $idCompany): array
    {
        if ($companyRoleIds === []) {
            return [];
        }

        /** @var \Propel\Runtime\Collection\ArrayCollection $matchedCompanyRoles */
        $matchedCompanyRoles = $this->getFactory()
            ->createCompanyRoleQuery()
            ->filterByIdCompanyRole_In($companyRoleIds)
            ->filterByFkCompany($idCompany)
            ->select([
                SpyCompanyRoleTableMap::COL_ID_COMPANY_ROLE,
                SpyCompanyRoleTableMap::COL_FK_COMPANY,
            ])
            ->find();

        return $matchedCompanyRoles->toKeyValue(
            SpyCompanyRoleTableMap::COL_ID_COMPANY_ROLE,
            SpyCompanyRoleTableMap::COL_ID_COMPANY_ROLE,
        );
    }

    protected function recursiveImplode(array $array, string $glue = ','): string
    {
        $result = [];

        foreach ($array as $value) {
            if (is_array($value)) {
                $result[] = $this->recursiveImplode($value, $glue);
            } else {
                $result[] = (string)$value;
            }
        }

        return implode($glue, $result);
    }
}
