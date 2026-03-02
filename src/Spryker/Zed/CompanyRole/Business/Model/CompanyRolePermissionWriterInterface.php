<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Business\Model;

use Generated\Shared\Transfer\CompanyRoleTransfer;

interface CompanyRolePermissionWriterInterface
{
    public function saveCompanyRolePermissions(CompanyRoleTransfer $companyRoleTransfer): void;
}
