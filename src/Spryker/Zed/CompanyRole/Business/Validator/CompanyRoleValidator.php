<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Zed\CompanyRole\Business\Validator;

use Generated\Shared\Transfer\CompanyRoleResponseTransfer;
use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\PermissionTransfer;
use Generated\Shared\Transfer\ResponseMessageTransfer;
use Spryker\Zed\CompanyRole\Dependency\Facade\CompanyRoleToPermissionFacadeInterface;
use Spryker\Zed\CompanyRole\Persistence\CompanyRoleRepositoryInterface;

class CompanyRoleValidator implements CompanyRoleValidatorInterface
{
    protected const string ERROR_NAME_REQUIRED = 'A company role name is required.';

    protected const string ERROR_NAME_TOO_LONG = 'The company role name must not exceed 255 characters.';

    protected const string ERROR_NAME_NOT_UNIQUE = 'A company role with this name already exists in this company.';

    protected const string ERROR_COMPANY_REQUIRED = 'A company is required for a company role.';

    protected const string ERROR_UNKNOWN_PERMISSION = 'At least one of the given permissions does not exist.';

    protected const string ERROR_NOT_FOUND = 'The company role was not found.';

    protected const string ERROR_COMPANY_IMMUTABLE = 'The company of an existing company role cannot be changed.';

    protected const string ERROR_DEFAULT_CANNOT_BE_CLEARED = 'The default flag cannot be cleared. Make another company role the default instead.';

    protected const string ERROR_DELETE_IS_DEFAULT = 'The default company role cannot be deleted.';

    protected const string ERROR_DELETE_HAS_USERS = 'company.company_role.delete.error.has_users';

    /**
     * @see \Orm\Zed\CompanyRole\Persistence\Map\SpyCompanyRoleTableMap::COL_NAME
     */
    protected const int NAME_MAX_LENGTH = 255;

    protected const string INDEX_KEY = 'key';

    protected const string INDEX_ID = 'id';

    public function __construct(
        protected CompanyRoleRepositoryInterface $companyRoleRepository,
        protected CompanyRoleToPermissionFacadeInterface $permissionFacade
    ) {
    }

    public function validateCreate(CompanyRoleTransfer $companyRoleTransfer): ?CompanyRoleResponseTransfer
    {
        $errorMessageKeys = array_merge(
            $this->validateName($companyRoleTransfer),
            $this->validateCompany($companyRoleTransfer),
            $this->validatePermissions($companyRoleTransfer),
        );

        return $this->createResponse($companyRoleTransfer, $errorMessageKeys);
    }

    public function validateUpdate(CompanyRoleTransfer $companyRoleTransfer): ?CompanyRoleResponseTransfer
    {
        $persistedCompanyRoleTransfer = $this->findPersistedCompanyRole($companyRoleTransfer);

        if ($persistedCompanyRoleTransfer === null) {
            return $this->createResponse($companyRoleTransfer, [static::ERROR_NOT_FOUND]);
        }

        $errorMessageKeys = array_merge(
            $this->validateName($companyRoleTransfer, $persistedCompanyRoleTransfer),
            $this->validateCompanyIsUnchanged($companyRoleTransfer, $persistedCompanyRoleTransfer),
            $this->validateDefaultIsNotCleared($companyRoleTransfer, $persistedCompanyRoleTransfer),
            $this->validatePermissions($companyRoleTransfer),
        );

        return $this->createResponse($companyRoleTransfer, $errorMessageKeys);
    }

    public function validateDelete(CompanyRoleTransfer $companyRoleTransfer): ?CompanyRoleResponseTransfer
    {
        $persistedCompanyRoleTransfer = $this->findPersistedCompanyRole($companyRoleTransfer);

        if ($persistedCompanyRoleTransfer === null) {
            return $this->createResponse($companyRoleTransfer, [static::ERROR_NOT_FOUND]);
        }

        if ($persistedCompanyRoleTransfer->getIsDefault() === true) {
            return $this->createResponse($companyRoleTransfer, [static::ERROR_DELETE_IS_DEFAULT]);
        }

        if ($this->companyRoleRepository->hasUsers($persistedCompanyRoleTransfer->getIdCompanyRoleOrFail())) {
            return $this->createResponse($companyRoleTransfer, [static::ERROR_DELETE_HAS_USERS]);
        }

        return null;
    }

    /**
     * @return list<string>
     */
    protected function validateName(
        CompanyRoleTransfer $companyRoleTransfer,
        ?CompanyRoleTransfer $persistedCompanyRoleTransfer = null
    ): array {
        $name = $companyRoleTransfer->getName();

        if ($name === null && $persistedCompanyRoleTransfer !== null) {
            return [];
        }

        if ($name === null || trim($name) === '') {
            return [static::ERROR_NAME_REQUIRED];
        }

        if (mb_strlen($name) > static::NAME_MAX_LENGTH) {
            return [static::ERROR_NAME_TOO_LONG];
        }

        $idCompany = $companyRoleTransfer->getFkCompany() ?? $persistedCompanyRoleTransfer?->getFkCompany();

        if ($idCompany === null) {
            return [];
        }

        $isTaken = $this->companyRoleRepository->existsCompanyRoleByNameAndIdCompany(
            $name,
            $idCompany,
            $persistedCompanyRoleTransfer?->getIdCompanyRole(),
        );

        return $isTaken ? [static::ERROR_NAME_NOT_UNIQUE] : [];
    }

    /**
     * @return list<string>
     */
    protected function validateCompany(CompanyRoleTransfer $companyRoleTransfer): array
    {
        $idCompany = $companyRoleTransfer->getFkCompany();

        return $idCompany === null || $idCompany < 1 ? [static::ERROR_COMPANY_REQUIRED] : [];
    }

    /**
     * @return list<string>
     */
    protected function validateCompanyIsUnchanged(
        CompanyRoleTransfer $companyRoleTransfer,
        CompanyRoleTransfer $persistedCompanyRoleTransfer
    ): array {
        $idCompany = $companyRoleTransfer->getFkCompany();

        if ($idCompany === null || $idCompany === $persistedCompanyRoleTransfer->getFkCompany()) {
            return [];
        }

        return [static::ERROR_COMPANY_IMMUTABLE];
    }

    /**
     * @return list<string>
     */
    protected function validateDefaultIsNotCleared(
        CompanyRoleTransfer $companyRoleTransfer,
        CompanyRoleTransfer $persistedCompanyRoleTransfer
    ): array {
        if ($companyRoleTransfer->getIsDefault() !== false) {
            return [];
        }

        return $persistedCompanyRoleTransfer->getIsDefault() === true
            ? [static::ERROR_DEFAULT_CANNOT_BE_CLEARED]
            : [];
    }

    /**
     * @return list<string>
     */
    protected function validatePermissions(CompanyRoleTransfer $companyRoleTransfer): array
    {
        $permissionCollectionTransfer = $companyRoleTransfer->getPermissionCollection();

        if ($permissionCollectionTransfer === null || count($permissionCollectionTransfer->getPermissions()) === 0) {
            return [];
        }

        $permissionIndexes = $this->indexKnownPermissions();

        foreach ($permissionCollectionTransfer->getPermissions() as $permissionTransfer) {
            if (!$this->isPermissionKnown($permissionTransfer, $permissionIndexes)) {
                return [static::ERROR_UNKNOWN_PERMISSION];
            }
        }

        return [];
    }

    /**
     * @return array<string, array<int|string, true>>
     */
    protected function indexKnownPermissions(): array
    {
        $indexes = [static::INDEX_KEY => [], static::INDEX_ID => []];

        foreach ($this->permissionFacade->findMergedRegisteredNonInfrastructuralPermissions()->getPermissions() as $permissionTransfer) {
            $key = $permissionTransfer->getKey();
            $idPermission = $permissionTransfer->getIdPermission();

            if ($key !== null) {
                $indexes[static::INDEX_KEY][$key] = true;
            }

            if ($idPermission !== null) {
                $indexes[static::INDEX_ID][$idPermission] = true;
            }
        }

        return $indexes;
    }

    /**
     * @param array<string, array<int|string, true>> $permissionIndexes
     */
    protected function isPermissionKnown(PermissionTransfer $permissionTransfer, array $permissionIndexes): bool
    {
        $key = $permissionTransfer->getKey();

        if ($key !== null) {
            return isset($permissionIndexes[static::INDEX_KEY][$key]);
        }

        $idPermission = $permissionTransfer->getIdPermission();

        return $idPermission !== null && isset($permissionIndexes[static::INDEX_ID][$idPermission]);
    }

    protected function findPersistedCompanyRole(CompanyRoleTransfer $companyRoleTransfer): ?CompanyRoleTransfer
    {
        if ($companyRoleTransfer->getIdCompanyRole() === null) {
            return null;
        }

        return $this->companyRoleRepository->findCompanyRoleById($companyRoleTransfer);
    }

    /**
     * @param list<string> $errorMessageKeys
     */
    protected function createResponse(
        CompanyRoleTransfer $companyRoleTransfer,
        array $errorMessageKeys
    ): ?CompanyRoleResponseTransfer {
        if ($errorMessageKeys === []) {
            return null;
        }

        $companyRoleResponseTransfer = (new CompanyRoleResponseTransfer())
            ->setIsSuccessful(false)
            ->setCompanyRoleTransfer($companyRoleTransfer);

        foreach ($errorMessageKeys as $errorMessageKey) {
            $companyRoleResponseTransfer->addMessage(
                (new ResponseMessageTransfer())->setText($errorMessageKey),
            );
        }

        return $companyRoleResponseTransfer;
    }
}
