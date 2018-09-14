<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Dependency\Facade;

use Generated\Shared\Transfer\PermissionCollectionTransfer;

interface CompanyRoleToPermissionFacadeInterface
{
    /**
     * @return \Generated\Shared\Transfer\PermissionCollectionTransfer
     */
    public function getRegisteredNonInfrastructuralPermissions(): PermissionCollectionTransfer;
}
