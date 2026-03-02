<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Business\Reader;

use Generated\Shared\Transfer\CompanyRoleResponseTransfer;
use Generated\Shared\Transfer\CompanyRoleTransfer;

interface CompanyRoleReaderInterface
{
    public function findCompanyRoleByUuid(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleResponseTransfer;
}
