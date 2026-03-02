<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CompanyRole;

use Spryker\Zed\CompanyRole\Dependency\Facade\CompanyRoleToPermissionFacadeBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\CompanyRole\CompanyRoleConfig getConfig()
 */
class CompanyRoleDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_PERMISSION = 'FACADE_PERMISSION';

    /**
     * @var string
     */
    public const PLUGINS_COMPANY_ROLE_POST_SAVE = 'PLUGINS_COMPANY_ROLE_POST_SAVE';

    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addPermissionFacade($container);
        $container = $this->addCompanyRolePostSavePlugins($container);

        return $container;
    }

    protected function addPermissionFacade(Container $container): Container
    {
        $container->set(static::FACADE_PERMISSION, function (Container $container) {
            return new CompanyRoleToPermissionFacadeBridge(
                $container->getLocator()->permission()->facade(),
            );
        });

        return $container;
    }

    protected function addCompanyRolePostSavePlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_COMPANY_ROLE_POST_SAVE, function (Container $container) {
            return $this->getCompanyRolePostSavePlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Zed\CompanyRoleExtension\Dependency\Plugin\CompanyRolePostSavePluginInterface>
     */
    protected function getCompanyRolePostSavePlugins(): array
    {
        return [];
    }
}
