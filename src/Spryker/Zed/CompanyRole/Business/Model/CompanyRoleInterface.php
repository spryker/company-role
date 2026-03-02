<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Business\Model;

use Generated\Shared\Transfer\CompanyResponseTransfer;
use Generated\Shared\Transfer\CompanyRoleResponseTransfer;
use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;

interface CompanyRoleInterface
{
    public function create(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleResponseTransfer;

    public function createByCompany(CompanyResponseTransfer $companyResponseTransfer): CompanyResponseTransfer;

    public function update(CompanyRoleTransfer $companyRoleTransfer): void;

    public function delete(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleResponseTransfer;

    public function saveCompanyUser(CompanyUserTransfer $companyUserTransfer): void;

    public function hydrateCompanyUser(CompanyUserTransfer $companyUserTransfer): CompanyUserTransfer;
}
