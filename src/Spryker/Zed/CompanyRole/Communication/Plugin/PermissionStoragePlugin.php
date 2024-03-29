<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole\Communication\Plugin;

use Generated\Shared\Transfer\PermissionCollectionTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\PermissionExtension\Dependency\Plugin\PermissionStoragePluginInterface;

/**
 * @method \Spryker\Zed\CompanyRole\Business\CompanyRoleFacadeInterface getFacade()
 * @method \Spryker\Zed\CompanyRole\CompanyRoleConfig getConfig()
 */
class PermissionStoragePlugin extends AbstractPlugin implements PermissionStoragePluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string|int $identifier
     *
     * @return \Generated\Shared\Transfer\PermissionCollectionTransfer
     */
    public function getPermissionCollection($identifier): PermissionCollectionTransfer
    {
        return $this->getFacade()->findPermissionsByIdCompanyUser((int)$identifier);
    }
}
