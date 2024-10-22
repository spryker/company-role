<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\CompanyRole\Plugin\Permission;

use Spryker\Shared\PermissionExtension\Dependency\Plugin\PermissionPluginInterface;

class EditCompanyRolesPermissionPlugin implements PermissionPluginInterface
{
    /**
     * @var string
     */
    protected const KEY = 'EditCompanyRolesPermissionPlugin';

    /**
     * {@inheritDoc}
     * - Returns plugin name as a key to permission to edit company roles.
     *
     * @api
     *
     * @return string
     */
    public function getKey(): string
    {
        return static::KEY;
    }
}
