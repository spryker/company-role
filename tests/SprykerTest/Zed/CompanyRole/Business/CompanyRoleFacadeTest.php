<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CompanyRole\Business;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\CompanyRoleBuilder;
use Generated\Shared\Transfer\CompanyResponseTransfer;
use Generated\Shared\Transfer\CompanyRoleCriteriaFilterTransfer;
use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Spryker\Shared\CompanyUser\Plugin\AddCompanyUserPermissionPlugin;
use Spryker\Zed\CompanyRole\Communication\Plugin\PermissionStoragePlugin;
use SprykerTest\Zed\CompanyRole\CompanyRoleBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group CompanyRole
 * @group Business
 * @group Facade
 * @group CompanyRoleFacadeTest
 * Add your own group annotations below this line
 */
class CompanyRoleFacadeTest extends Unit
{
    /**
     * @var array<string, string>
     */
    protected const array CONFIGURATION = ['testKey' => 'testValue'];

    /**
     * @var string
     */
    protected const TEST_NAME = 'Test Name';

    /**
     * @var \SprykerTest\Zed\CompanyRole\CompanyRoleBusinessTester
     */
    protected CompanyRoleBusinessTester $tester;

    public function testGetCompanyRoleByIdShouldReturnCorrectData(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $existingCompanyRole = $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
        ]);

        // Action
        $resultCompanyRoleTransfer = $this->tester->getFacade()
            ->getCompanyRoleById(
                (new CompanyRoleTransfer())
                    ->setIdCompanyRole($existingCompanyRole->getIdCompanyRole()),
            );

        // Assert
        $this->assertEquals($existingCompanyRole->getName(), $resultCompanyRoleTransfer->getName());
    }

    public function testGetCompanyUserIdsByPermissionKeyReturnsCorrectData(): void
    {
        //Assign
        $this->tester->haveCompanyUser([
            CompanyUserTransfer::CUSTOMER => $this->tester->haveCustomer(),
            CompanyUserTransfer::FK_COMPANY => $this->tester->haveCompany()->getIdCompany(),
        ]);
        $companyUserWithPermissionTransfer = $this->tester->createCompanyUserWithPermission();

        //Act
        $companyUserIds = $this->tester->getFacade()
            ->getCompanyUserIdsByPermissionKey(AddCompanyUserPermissionPlugin::KEY);

        //Assert
        $this->assertContains($companyUserWithPermissionTransfer->getIdCompanyUser(), $companyUserIds);
    }

    public function testCreateCompanyRoleShouldReturnIsSuccess(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $companyRoleTransfer = (new CompanyRoleBuilder([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
        ]))->build();

        // Action
        $companyRoleResponseTransfer = $this->tester->getFacade()
            ->create($companyRoleTransfer);

        // Assert
        $this->assertTrue($companyRoleResponseTransfer->getIsSuccessful());
    }

    public function testCreateCompanyRoleByCompanyShouldReturnIsSuccess(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $companyResponseTransfer = (new CompanyResponseTransfer())
            ->setIsSuccessful(true)
            ->setCompanyTransfer($companyTransfer);
        $this->tester->preparePermissionStorageDependency(new PermissionStoragePlugin());

        // Action
        $companyResponseTransfer = $this->tester->getFacade()
            ->createByCompany($companyResponseTransfer);

        // Assert
        $this->assertTrue($companyResponseTransfer->getIsSuccessful());
        $this->assertEmpty($companyResponseTransfer->getMessages());
    }

    public function testUpdateCompanyRoleShouldUpdateSuccessfully(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $existingCompanyRole = $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
            CompanyRoleTransfer::NAME => static::TEST_NAME,
        ]);
        $companyRoleTransfer = $this->tester->haveCompanyRole([
            CompanyRoleTransfer::ID_COMPANY_ROLE => $existingCompanyRole->getIdCompanyRole(),
        ]);

        // Action
        $this->tester->getFacade()
            ->update($existingCompanyRole);
        $resultCompanyRoleTransfer = $this->tester->getFacade()
            ->getCompanyRoleById($companyRoleTransfer);

        // Assert
        $this->assertSame(static::TEST_NAME, $resultCompanyRoleTransfer->getName());
    }

    public function testDeleteCompanyRoleShouldReturnIsSuccess(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $companyRoleTransfer = $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
        ]);

        // Action
        $companyRoleResponseTransfer = $this->tester->getFacade()
            ->delete($companyRoleTransfer);

        // Assert
        $this->assertTrue($companyRoleResponseTransfer->getIsSuccessful());
    }

    public function testFindDefaultCompanyRoleByIdCompanyReturnNullIfNonFound(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
            CompanyRoleTransfer::IS_DEFAULT => false,
        ]);

        // Action
        $resultCompanyRoleTransfer = $this->tester->getFacade()
            ->findDefaultCompanyRoleByIdCompany($companyTransfer->getIdCompany());

        // Assert
        $this->assertNull($resultCompanyRoleTransfer);
    }

    public function testFindDefaultCompanyRoleByIdCompany(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $companyRoleTransfer = $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
            CompanyRoleTransfer::IS_DEFAULT => true,
        ]);

        // Action
        $resultCompanyRoleTransfer = $this->tester->getFacade()
            ->findDefaultCompanyRoleByIdCompany($companyTransfer->getIdCompany());

        // Assert
        $this->assertNotNull($resultCompanyRoleTransfer);
        $this->assertSame($resultCompanyRoleTransfer->getIdCompanyRole(), $companyRoleTransfer->getIdCompanyRole());
    }

    public function testFindCompanyRoleByIdShouldReturnCorrectDataIfCompanyRoleExists(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $existingCompanyRole = $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
        ]);

        // Action
        $resultCompanyRoleTransfer = $this->tester->getFacade()
            ->findCompanyRoleById(
                (new CompanyRoleTransfer())
                    ->setIdCompanyRole($existingCompanyRole->getIdCompanyRole()),
            );

        // Assert
        $this->assertEquals($existingCompanyRole->getName(), $resultCompanyRoleTransfer->getName());
    }

    public function testFindCompanyRoleByIdShouldReturnNullIfCompanyRoleDoesNotExist(): void
    {
        // Prepare
        $notExistingCompanyRole = (new CompanyRoleTransfer())->setIdCompanyRole(-1);

        // Action
        $resultCompanyRole = $this->tester->getFacade()
            ->findCompanyRoleById($notExistingCompanyRole);

        // Assert
        $this->assertNull($resultCompanyRole);
    }

    public function testFindCompanyRolesShouldReturnCollection(): void
    {
        // Arrange
        $this->tester->createCompanyRoleWithPermission();

        // Act
        $companyRoleCollectionTransfer = $this->tester->getFacade()
            ->findCompanyRoles();

        // Assert
        $this->assertGreaterThan(0, $companyRoleCollectionTransfer->getRoles()->count());
    }

    public function testGetCompanyRoleCollectionShouldReturnCollectionByIdCompanyCriteria(): void
    {
        // Arrange
        $companyUserWithPermissionTransfer = $this->tester->createCompanyUserWithPermission();
        $criteriaFilterTransfer = (new CompanyRoleCriteriaFilterTransfer())
            ->setIdCompany($companyUserWithPermissionTransfer->getFkCompany());

        // Act
        $companyRoleCollectionTransfer = $this->tester->getFacade()
            ->getCompanyRoleCollection($criteriaFilterTransfer);

        // Assert
        $this->assertGreaterThan(0, $companyRoleCollectionTransfer->getRoles()->count());
    }

    public function testGetCompanyRoleCollectionShouldReturnCollectionByIdCompanyUserCriteria(): void
    {
        // Arrange
        $companyUserWithPermissionTransfer = $this->tester->createCompanyUserWithPermission();
        $criteriaFilterTransfer = (new CompanyRoleCriteriaFilterTransfer())
            ->setIdCompanyUser($companyUserWithPermissionTransfer->getIdCompanyUser());

        // Act
        $companyRoleCollectionTransfer = $this->tester->getFacade()
            ->getCompanyRoleCollection($criteriaFilterTransfer);

        // Assert
        $this->assertGreaterThan(0, $companyRoleCollectionTransfer->getRoles()->count());
    }

    public function testGetCompanyRoleCollectionShouldReturnCollectionByIdCompanyUsersCriteria(): void
    {
        // Arrange
        $companyUserWithPermissionTransfer = $this->tester->createCompanyUserWithPermission();
        $otherCompanyUserTransfer = $this->tester->createCompanyUserWithPermission();

        $criteriaFilterTransfer = (new CompanyRoleCriteriaFilterTransfer())
            ->addIdCompanyUser($companyUserWithPermissionTransfer->getIdCompanyUser());

        // Act
        $companyRoleCollectionTransfer = $this->tester->getFacade()
            ->getCompanyRoleCollection($criteriaFilterTransfer);

        // Assert
        $returnedCompanyUserIds = array_map(
            static fn ($roleTransfer) => $roleTransfer->getIdCompanyRole(),
            $companyRoleCollectionTransfer->getRoles()->getArrayCopy(),
        );

        $expectedRoleIds = array_map(
            static fn ($roleTransfer) => $roleTransfer->getIdCompanyRole(),
            $companyUserWithPermissionTransfer->getCompanyRoleCollection()->getRoles()->getArrayCopy(),
        );

        $otherRoleIds = array_map(
            static fn ($roleTransfer) => $roleTransfer->getIdCompanyRole(),
            $otherCompanyUserTransfer->getCompanyRoleCollection()->getRoles()->getArrayCopy(),
        );

        foreach ($expectedRoleIds as $expectedRoleId) {
            $this->assertContains($expectedRoleId, $returnedCompanyUserIds);
        }

        foreach ($otherRoleIds as $otherRoleId) {
            $this->assertNotContains($otherRoleId, $returnedCompanyUserIds);
        }
    }

    public function testGetCompanyRoleCollectionShouldReturnEmptyCollectionByFakeIdCompanyUsers(): void
    {
        // Arrange
        $this->tester->createCompanyUserWithPermission();

        $criteriaFilterTransfer = (new CompanyRoleCriteriaFilterTransfer())
            ->addIdCompanyUser(-1);

        // Act
        $companyRoleCollectionTransfer = $this->tester->getFacade()
            ->getCompanyRoleCollection($criteriaFilterTransfer);

        // Assert
        $this->assertCount(0, $companyRoleCollectionTransfer->getRoles());
    }

    public function testFindCompanyRolePermissionsShouldReturnCollection(): void
    {
        // Arrange
        $companyRoleTransfer = $this->tester->createCompanyRoleWithPermission();

        // Act
        $permissionCollectionTransfer = $this->tester->getFacade()
            ->findCompanyRolePermissions($companyRoleTransfer->getIdCompanyRole());

        // Assert
        $this->assertGreaterThan(0, $permissionCollectionTransfer->getPermissions()->count());
    }

    public function testFindPermissionsByIdCompanyUserShouldReturnCollection(): void
    {
        // Arrange
        $companyUserWithPermissionTransfer = $this->tester->createCompanyUserWithPermission();

        // Act
        $permissionCollectionTransfer = $this->tester->getFacade()
            ->findPermissionsByIdCompanyUser($companyUserWithPermissionTransfer->getIdCompanyUser());

        // Assert
        $this->assertGreaterThan(0, $permissionCollectionTransfer->getPermissions()->count());
    }

    public function testUpdateCompanyRolePermissionShouldPersistNewConfiguration(): void
    {
        // Arrange
        $companyRoleTransfer = $this->tester->createCompanyRoleWithPermission();
        $idPermission = $companyRoleTransfer->getPermissionCollection()->getPermissions()->offsetGet(0)->getIdPermission();
        $idCompanyRole = $companyRoleTransfer->getIdCompanyRole();
        $permissionTransfer = $this->tester->getFacade()
            ->findPermissionByIdCompanyRoleByIdPermission($idCompanyRole, $idPermission);

        // Act
        $permissionTransfer->setConfiguration(static::CONFIGURATION);
        $this->tester->getFacade()
            ->updateCompanyRolePermission($permissionTransfer);

        // Assert
        $permissionTransferUpdated = $this->tester->getFacade()
            ->findPermissionByIdCompanyRoleByIdPermission($idCompanyRole, $idPermission);
        $this->assertSame(static::CONFIGURATION, $permissionTransferUpdated->getConfiguration());
    }

    public function testHydrateCompanyUserShouldReturnHydratedCompanyUser(): void
    {
        // Arrange
        $companyUserWithPermissionTransfer = $this->tester->createCompanyUserWithPermission();
        $companyUserTransfer = (new CompanyUserTransfer())
            ->setIdCompanyUser($companyUserWithPermissionTransfer->getIdCompanyUser());

        // Act
        $companyUserTransferHydrated = $this->tester->getFacade()
            ->hydrateCompanyUser($companyUserTransfer);

        // Assert
        $this->assertNotNull($companyUserTransferHydrated->getCompanyRoleCollection());
    }
}
