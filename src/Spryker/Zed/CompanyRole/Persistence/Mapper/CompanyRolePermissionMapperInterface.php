<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Persistence\Mapper;

use Generated\Shared\Transfer\CompanyRoleTransfer;
use Orm\Zed\CompanyRole\Persistence\SpyCompanyRole;

interface CompanyRolePermissionMapperInterface
{
    public function hydratePermissionCollection(
        SpyCompanyRole $spyCompanyRole,
        CompanyRoleTransfer $companyRoleTransfer
    ): CompanyRoleTransfer;
}
