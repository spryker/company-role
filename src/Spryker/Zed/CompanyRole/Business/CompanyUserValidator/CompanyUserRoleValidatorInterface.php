<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Business\CompanyUserValidator;

use Generated\Shared\Transfer\CompanyUserResponseTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;

interface CompanyUserRoleValidatorInterface
{
    public function validateRolesBelongToCompany(CompanyUserTransfer $companyUserTransfer): CompanyUserResponseTransfer;
}
