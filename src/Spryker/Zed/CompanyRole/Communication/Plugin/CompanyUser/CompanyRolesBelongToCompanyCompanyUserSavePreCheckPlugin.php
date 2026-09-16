<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Communication\Plugin\CompanyUser;

use Generated\Shared\Transfer\CompanyUserResponseTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Spryker\Zed\CompanyUserExtension\Dependency\Plugin\CompanyUserSavePreCheckPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\CompanyRole\Business\CompanyRoleBusinessFactory getBusinessFactory()
 * @method \Spryker\Zed\CompanyRole\CompanyRoleConfig getConfig()
 */
class CompanyRolesBelongToCompanyCompanyUserSavePreCheckPlugin extends AbstractPlugin implements CompanyUserSavePreCheckPluginInterface
{
    /**
     * {@inheritDoc}
     * - Checks that every company role in `CompanyUserTransfer.companyRoleCollection` belongs to
     *   `CompanyUserTransfer.fkCompany`.
     * - Returns an unsuccessful response when a role belongs to another company or does not exist.
     * - Returns a successful response when `CompanyUserTransfer.companyRoleCollection` or
     *   `CompanyUserTransfer.fkCompany` is not set.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CompanyUserTransfer $companyUserTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyUserResponseTransfer
     */
    public function check(CompanyUserTransfer $companyUserTransfer): CompanyUserResponseTransfer
    {
        return $this->getBusinessFactory()
            ->createCompanyUserRoleValidator()
            ->validateRolesBelongToCompany($companyUserTransfer);
    }
}
