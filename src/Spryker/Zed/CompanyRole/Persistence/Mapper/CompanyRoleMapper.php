<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Persistence\Mapper;

use Generated\Shared\Transfer\CompanyRoleTransfer;
use Orm\Zed\CompanyRole\Persistence\SpyCompanyRole;

class CompanyRoleMapper implements CompanyRoleMapperInterface
{
    public function mapEntityToCompanyRoleTransfer(
        SpyCompanyRole $spyCompanyRole,
        CompanyRoleTransfer $companyRoleTransfer
    ): CompanyRoleTransfer {
        return $companyRoleTransfer->fromArray($spyCompanyRole->toArray(), true);
    }

    public function mapCompanyRoleTransferToEntity(
        CompanyRoleTransfer $companyRoleTransfer,
        SpyCompanyRole $spyCompanyRole
    ): SpyCompanyRole {
        $spyCompanyRole->fromArray($companyRoleTransfer->modifiedToArray());
        $spyCompanyRole->setNew($companyRoleTransfer->getIdCompanyRole() === null);

        return $spyCompanyRole;
    }
}
