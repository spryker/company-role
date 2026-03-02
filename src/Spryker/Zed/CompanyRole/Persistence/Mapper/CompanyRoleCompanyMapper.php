<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Persistence\Mapper;

use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\CompanyTransfer;
use Orm\Zed\CompanyRole\Persistence\SpyCompanyRole;

class CompanyRoleCompanyMapper
{
    public function mapCompanyFromCompanyRoleEntityToCompanyRoleTransfer(
        SpyCompanyRole $spyCompanyRole,
        CompanyRoleTransfer $companyRoleTransfer
    ): CompanyRoleTransfer {
        /** @var \Orm\Zed\Company\Persistence\SpyCompany|null $companyEntity */
        $companyEntity = $spyCompanyRole->getCompany();
        if (!$companyEntity) {
            return $companyRoleTransfer;
        }

        $companyTransfer = (new CompanyTransfer())
            ->fromArray($companyEntity->toArray(), true);

        $companyRoleTransfer->setCompany($companyTransfer);

        return $companyRoleTransfer;
    }
}
