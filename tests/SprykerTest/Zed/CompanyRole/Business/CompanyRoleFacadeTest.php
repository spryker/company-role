<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CompanyRole\Business;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\CompanyRoleBuilder;
use Generated\Shared\Transfer\CompanyResponseTransfer;
use Generated\Shared\Transfer\CompanyRoleCollectionCriteriaTransfer;
use Generated\Shared\Transfer\CompanyRoleConditionsTransfer;
use Generated\Shared\Transfer\CompanyRoleCriteriaFilterTransfer;
use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\PaginationTransfer;
use Generated\Shared\Transfer\SortTransfer;
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

    public function testGetCompanyRoleCollectionByCollectionCriteriaFiltersByName(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $nameToken = uniqid('role', false);
        $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
            CompanyRoleTransfer::NAME => $nameToken . ' Approver',
        ]);
        $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
            CompanyRoleTransfer::NAME => $nameToken . ' Buyer',
        ]);

        // Action
        $companyRoleCollectionTransfer = $this->tester->getFacade()
            ->getCompanyRoleCollectionByCollectionCriteria(
                $this->createCollectionCriteria(
                    (new CompanyRoleConditionsTransfer())->setName($nameToken . ' Approv'),
                ),
            );

        // Assert
        $this->assertCount(1, $companyRoleCollectionTransfer->getRoles());
        $this->assertSame($nameToken . ' Approver', $companyRoleCollectionTransfer->getRoles()[0]->getName());
    }

    public function testGetCompanyRoleCollectionByCollectionCriteriaFiltersByCompanyIds(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $otherCompanyTransfer = $this->tester->haveCompany();
        $nameToken = uniqid('role', false);
        $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
            CompanyRoleTransfer::NAME => $nameToken . ' wanted',
        ]);
        $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $otherCompanyTransfer->getIdCompany(),
            CompanyRoleTransfer::NAME => $nameToken . ' other',
        ]);

        // Action
        $companyRoleCollectionTransfer = $this->tester->getFacade()
            ->getCompanyRoleCollectionByCollectionCriteria(
                $this->createCollectionCriteria(
                    (new CompanyRoleConditionsTransfer())
                        ->setSearchTerm($nameToken)
                        ->addCompanyId($companyTransfer->getIdCompany()),
                ),
            );

        // Assert
        $this->assertCount(1, $companyRoleCollectionTransfer->getRoles());
        $this->assertSame($nameToken . ' wanted', $companyRoleCollectionTransfer->getRoles()[0]->getName());
    }

    public function testGetCompanyRoleCollectionByCollectionCriteriaFiltersByCompanyUserIds(): void
    {
        // Prepare
        $companyUserTransfer = $this->tester->createCompanyUserWithPermission();

        // Action
        $companyRoleCollectionTransfer = $this->tester->getFacade()
            ->getCompanyRoleCollectionByCollectionCriteria(
                $this->createCollectionCriteria(
                    (new CompanyRoleConditionsTransfer())
                        ->addCompanyUserId($companyUserTransfer->getIdCompanyUser()),
                ),
            );

        // Assert
        $this->assertCount(1, $companyRoleCollectionTransfer->getRoles());
    }

    public function testGetCompanyRoleCollectionByCollectionCriteriaDoesNotHydrateCompanyUsersWithoutTheOptIn(): void
    {
        // Prepare
        $companyUserTransfer = $this->tester->createCompanyUserWithPermission();

        // Action
        $companyRoleCollectionTransfer = $this->tester->getFacade()
            ->getCompanyRoleCollectionByCollectionCriteria(
                $this->createCollectionCriteria(
                    (new CompanyRoleConditionsTransfer())
                        ->addCompanyUserId($companyUserTransfer->getIdCompanyUser()),
                ),
            );

        // Assert
        $this->assertCount(1, $companyRoleCollectionTransfer->getRoles());
        $this->assertNull($companyRoleCollectionTransfer->getRoles()->offsetGet(0)->getCompanyUserCollection());
    }

    public function testGetCompanyRoleCollectionByCollectionCriteriaHydratesCompanyUsersWhenOptedIn(): void
    {
        // Prepare
        $companyUserTransfer = $this->tester->createCompanyUserWithPermission();

        // Action
        $companyRoleCollectionTransfer = $this->tester->getFacade()
            ->getCompanyRoleCollectionByCollectionCriteria(
                $this->createCollectionCriteria(
                    (new CompanyRoleConditionsTransfer())
                        ->addCompanyUserId($companyUserTransfer->getIdCompanyUser())
                        ->setWithCompanyUsers(true),
                ),
            );

        // Assert
        $this->assertCount(1, $companyRoleCollectionTransfer->getRoles());

        $companyUserCollectionTransfer = $companyRoleCollectionTransfer->getRoles()->offsetGet(0)->getCompanyUserCollection();

        $this->assertNotNull($companyUserCollectionTransfer);
        $this->assertCount(1, $companyUserCollectionTransfer->getCompanyUsers());

        $hydratedCompanyUserTransfer = $companyUserCollectionTransfer->getCompanyUsers()->offsetGet(0);

        $this->assertSame($companyUserTransfer->getIdCompanyUser(), $hydratedCompanyUserTransfer->getIdCompanyUser());
        $this->assertSame(
            $companyUserTransfer->getCustomer()->getIdCustomer(),
            $hydratedCompanyUserTransfer->getCustomer()?->getIdCustomer(),
        );
    }

    public function testGetCompanyRoleCollectionByCollectionCriteriaAppliesTheOffsetAndReportsTheUnpagedTotal(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();

        $nameToken = uniqid('role', false);

        foreach (['A', 'B', 'C'] as $suffix) {
            $this->tester->haveCompanyRole([
                CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
                CompanyRoleTransfer::NAME => $nameToken . ' ' . $suffix,
            ]);
        }

        // Action
        $companyRoleCollectionTransfer = $this->tester->getFacade()
            ->getCompanyRoleCollectionByCollectionCriteria(
                $this->createCollectionCriteria(
                    (new CompanyRoleConditionsTransfer())->setSearchTerm($nameToken),
                    (new PaginationTransfer())->setOffset(1)->setLimit(1),
                    (new SortTransfer())->setField('name')->setIsAscending(true),
                ),
            );

        // Assert
        $this->assertCount(1, $companyRoleCollectionTransfer->getRoles());
        $this->assertSame($nameToken . ' B', $companyRoleCollectionTransfer->getRoles()[0]->getName());
        $this->assertSame(3, $companyRoleCollectionTransfer->getPagination()->getNbResults());
    }

    /**
     * Permissions are batch-loaded for the whole page, so the risk is that they leak onto the wrong
     * role. A role that has one and a role that has none are asserted together.
     */
    public function testGetCompanyRoleCollectionByCollectionCriteriaHydratesEachRoleWithItsOwnPermissions(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $nameToken = uniqid('role', false);
        $this->tester->createCompanyRoleWithPermission([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
            CompanyRoleTransfer::NAME => $nameToken . ' with',
        ]);
        $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
            CompanyRoleTransfer::NAME => $nameToken . ' without',
        ]);

        // Action
        $companyRoleCollectionTransfer = $this->tester->getFacade()
            ->getCompanyRoleCollectionByCollectionCriteria(
                $this->createCollectionCriteria((new CompanyRoleConditionsTransfer())->setSearchTerm($nameToken)),
            );

        // Assert
        $permissionCountsByRoleName = [];

        foreach ($companyRoleCollectionTransfer->getRoles() as $companyRoleTransfer) {
            $permissionCountsByRoleName[$companyRoleTransfer->getName()]
                = $companyRoleTransfer->getPermissionCollection()->getPermissions()->count();
        }

        $this->assertSame(1, $permissionCountsByRoleName[$nameToken . ' with']);
        $this->assertSame(0, $permissionCountsByRoleName[$nameToken . ' without']);
    }

    public function testCreateStillPersistsInputThatCreateCompanyRoleRejects(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $companyRoleTransfer = (new CompanyRoleTransfer())
            ->setFkCompany($companyTransfer->getIdCompany())
            ->setName('');

        // Action
        $companyRoleResponseTransfer = $this->tester->getFacade()->create($companyRoleTransfer);

        // Assert
        $this->assertTrue($companyRoleResponseTransfer->getIsSuccessful());
    }

    public function testCreateCompanyRoleRejectsABlankName(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $companyRoleTransfer = (new CompanyRoleTransfer())
            ->setFkCompany($companyTransfer->getIdCompany())
            ->setName('');

        // Action
        $companyRoleResponseTransfer = $this->tester->getFacade()->createCompanyRole($companyRoleTransfer);

        // Assert
        $this->assertFalse($companyRoleResponseTransfer->getIsSuccessful());
        $this->assertSame(
            'A company role name is required.',
            $companyRoleResponseTransfer->getMessages()[0]->getText(),
        );
    }

    public function testCreateCompanyRoleRejectsANameAlreadyUsedInTheCompany(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
            CompanyRoleTransfer::NAME => static::TEST_NAME,
        ]);

        // Action
        $companyRoleResponseTransfer = $this->tester->getFacade()->createCompanyRole(
            (new CompanyRoleTransfer())
                ->setFkCompany($companyTransfer->getIdCompany())
                ->setName(static::TEST_NAME),
        );

        // Assert
        $this->assertFalse($companyRoleResponseTransfer->getIsSuccessful());
        $this->assertSame(
            'A company role with this name already exists in this company.',
            $companyRoleResponseTransfer->getMessages()[0]->getText(),
        );
    }

    public function testUpdateCompanyRoleRejectsClearingTheDefaultFlag(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $companyRoleTransfer = $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
            CompanyRoleTransfer::IS_DEFAULT => true,
        ]);

        // Action
        $companyRoleResponseTransfer = $this->tester->getFacade()
            ->updateCompanyRole($companyRoleTransfer->setIsDefault(false));

        // Assert
        $this->assertFalse($companyRoleResponseTransfer->getIsSuccessful());
        $this->assertSame(
            'The default flag cannot be cleared. Make another company role the default instead.',
            $companyRoleResponseTransfer->getMessages()[0]->getText(),
        );
    }

    public function testDeleteCompanyRoleRejectsTheCompanyDefaultRole(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $companyRoleTransfer = $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
            CompanyRoleTransfer::IS_DEFAULT => true,
        ]);

        // Action
        $companyRoleResponseTransfer = $this->tester->getFacade()->delete($companyRoleTransfer);

        // Assert
        $this->assertFalse($companyRoleResponseTransfer->getIsSuccessful());
        $this->assertSame(
            'The default company role cannot be deleted.',
            $companyRoleResponseTransfer->getMessages()[0]->getText(),
        );
    }

    public function testDeleteCompanyRoleRejectsARoleThatDoesNotExist(): void
    {
        // Prepare
        $companyTransfer = $this->tester->haveCompany();
        $companyRoleTransfer = $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
        ]);
        $this->tester->getFacade()->delete($companyRoleTransfer);

        // Action
        $companyRoleResponseTransfer = $this->tester->getFacade()->delete($companyRoleTransfer);

        // Assert
        $this->assertFalse($companyRoleResponseTransfer->getIsSuccessful());
        $this->assertSame(
            'The company role was not found.',
            $companyRoleResponseTransfer->getMessages()[0]->getText(),
        );
    }

    protected function createCollectionCriteria(
        CompanyRoleConditionsTransfer $companyRoleConditionsTransfer,
        ?PaginationTransfer $paginationTransfer = null,
        ?SortTransfer $sortTransfer = null
    ): CompanyRoleCollectionCriteriaTransfer {
        $companyRoleCollectionCriteriaTransfer = (new CompanyRoleCollectionCriteriaTransfer())
            ->setCompanyRoleConditions($companyRoleConditionsTransfer)
            ->setPagination($paginationTransfer ?? (new PaginationTransfer())->setOffset(0)->setLimit(100));

        if ($sortTransfer !== null) {
            $companyRoleCollectionCriteriaTransfer->addSort($sortTransfer);
        }

        return $companyRoleCollectionCriteriaTransfer;
    }
}
