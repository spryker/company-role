<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Communication\Controller;

use Generated\Shared\Transfer\CompanyRoleCollectionTransfer;
use Generated\Shared\Transfer\CompanyRoleCriteriaFilterTransfer;
use Generated\Shared\Transfer\CompanyRolePermissionResponseTransfer;
use Generated\Shared\Transfer\CompanyRoleResponseTransfer;
use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\PermissionCollectionTransfer;
use Generated\Shared\Transfer\PermissionTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \Spryker\Zed\CompanyRole\Business\CompanyRoleFacadeInterface getFacade()
 * @method \Spryker\Zed\CompanyRole\Persistence\CompanyRoleRepositoryInterface getRepository()
 */
class GatewayController extends AbstractGatewayController
{
    public function createAction(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleResponseTransfer
    {
        return $this->getFacade()->create($companyRoleTransfer);
    }

    public function getCompanyRoleCollectionAction(
        CompanyRoleCriteriaFilterTransfer $companyRoleCriteriaFilterTransfer
    ): CompanyRoleCollectionTransfer {
        return $this->getFacade()->getCompanyRoleCollection($companyRoleCriteriaFilterTransfer);
    }

    public function getCompanyRoleByIdAction(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleTransfer
    {
        return $this->getFacade()->getCompanyRoleById($companyRoleTransfer);
    }

    public function updateCompanyRoleAction(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleResponseTransfer
    {
        $this->getFacade()->update($companyRoleTransfer);

        $response = new CompanyRoleResponseTransfer();
        $response->setIsSuccessful(true);

        return $response;
    }

    public function findCompanyRolePermissionsAction(
        CompanyRoleTransfer $companyRoleTransfer
    ): PermissionCollectionTransfer {
        $companyRoleTransfer->requireIdCompanyRole();

        return $this->getFacade()->findCompanyRolePermissions($companyRoleTransfer->getIdCompanyRole());
    }

    public function deleteCompanyRoleAction(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleResponseTransfer
    {
        return $this->getFacade()->delete($companyRoleTransfer);
    }

    public function saveCompanyUserAction(CompanyUserTransfer $companyUserTransfer): CompanyRoleResponseTransfer
    {
        $this->getFacade()->saveCompanyUser($companyUserTransfer);

        $response = new CompanyRoleResponseTransfer();
        $response->setIsSuccessful(true);

        return $response;
    }

    /**
     * @param \Generated\Shared\Transfer\PermissionTransfer $permissionTransfer
     *
     * @return \Generated\Shared\Transfer\PermissionTransfer
     */
    public function findPermissionByIdCompanyRoleByIdPermissionAction(PermissionTransfer $permissionTransfer)
    {
        return $this->getFacade()->findPermissionByIdCompanyRoleByIdPermission(
            $permissionTransfer->getIdCompanyRole(),
            $permissionTransfer->getIdPermission(),
        );
    }

    public function updateCompanyRolePermissionAction(PermissionTransfer $permissionTransfer): CompanyRolePermissionResponseTransfer
    {
        $this->getFacade()->updateCompanyRolePermission($permissionTransfer);

        return (new CompanyRolePermissionResponseTransfer())
            ->setPermission($permissionTransfer)
            ->setIsSuccessful(true);
    }

    public function findCompanyRoleByUuidAction(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleResponseTransfer
    {
        return $this->getFacade()->findCompanyRoleByUuid($companyRoleTransfer);
    }
}
