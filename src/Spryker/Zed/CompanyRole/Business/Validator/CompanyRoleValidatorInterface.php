<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Zed\CompanyRole\Business\Validator;

use Generated\Shared\Transfer\CompanyRoleResponseTransfer;
use Generated\Shared\Transfer\CompanyRoleTransfer;

interface CompanyRoleValidatorInterface
{
    public function validateCreate(CompanyRoleTransfer $companyRoleTransfer): ?CompanyRoleResponseTransfer;

    public function validateUpdate(CompanyRoleTransfer $companyRoleTransfer): ?CompanyRoleResponseTransfer;

    public function validateDelete(CompanyRoleTransfer $companyRoleTransfer): ?CompanyRoleResponseTransfer;
}
