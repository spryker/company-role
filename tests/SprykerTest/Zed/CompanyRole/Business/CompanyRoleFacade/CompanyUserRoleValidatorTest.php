<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\CompanyRole\Business\CompanyRoleFacade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CompanyRoleCollectionTransfer;
use Generated\Shared\Transfer\CompanyRoleTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Spryker\Zed\CompanyRole\Communication\Plugin\CompanyUser\CompanyRolesBelongToCompanyCompanyUserSavePreCheckPlugin;
use SprykerTest\Zed\CompanyRole\CompanyRoleBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group CompanyRole
 * @group Business
 * @group CompanyRoleFacade
 * @group CompanyUserRoleValidatorTest
 * Add your own group annotations below this line
 */
class CompanyUserRoleValidatorTest extends Unit
{
    protected CompanyRoleBusinessTester $tester;

    public function testAcceptsRolesWhenTheIdsArriveAsNumericStrings(): void
    {
        // Arrange
        $companyTransfer = $this->tester->haveCompany();
        $companyRoleTransfer = $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
        ]);

        $companyUserTransfer = (new CompanyUserTransfer())
            ->setFkCompany((string)$companyTransfer->getIdCompany())
            ->setCompanyRoleCollection(
                (new CompanyRoleCollectionTransfer())->addRole(
                    (new CompanyRoleTransfer())->setIdCompanyRole((string)$companyRoleTransfer->getIdCompanyRole()),
                ),
            );

        // Act
        $companyUserResponseTransfer = (new CompanyRolesBelongToCompanyCompanyUserSavePreCheckPlugin())
            ->check($companyUserTransfer);

        // Assert
        $this->assertTrue(
            $companyUserResponseTransfer->getIsSuccessful(),
            'A numeric-string role id must not make the company own role look like another company\'s.',
        );
    }

    public function testAcceptsRolesBelongingToTheSameCompany(): void
    {
        // Arrange
        $companyTransfer = $this->tester->haveCompany();
        $companyRoleTransfer = $this->tester->haveCompanyRole([
            'fkCompany' => $companyTransfer->getIdCompany(),
        ]);

        $companyUserTransfer = (new CompanyUserTransfer())
            ->setFkCompany($companyTransfer->getIdCompany())
            ->setCompanyRoleCollection(
                (new CompanyRoleCollectionTransfer())->addRole($companyRoleTransfer),
            );

        // Act
        $companyUserResponseTransfer = (new CompanyRolesBelongToCompanyCompanyUserSavePreCheckPlugin())
            ->check($companyUserTransfer);

        // Assert
        $this->assertTrue($companyUserResponseTransfer->getIsSuccessful());
    }

    public function testAcceptsARoleCreatedAfterAnEarlierRoleOfTheSameCompanyWasValidated(): void
    {
        // Arrange
        $companyTransfer = $this->tester->haveCompany();
        $firstCompanyRoleTransfer = $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
        ]);

        (new CompanyRolesBelongToCompanyCompanyUserSavePreCheckPlugin())->check(
            $this->createCompanyUserTransfer($companyTransfer->getIdCompany(), $firstCompanyRoleTransfer),
        );

        $secondCompanyRoleTransfer = $this->tester->haveCompanyRole([
            CompanyRoleTransfer::FK_COMPANY => $companyTransfer->getIdCompany(),
        ]);

        // Act
        $companyUserResponseTransfer = (new CompanyRolesBelongToCompanyCompanyUserSavePreCheckPlugin())->check(
            $this->createCompanyUserTransfer($companyTransfer->getIdCompany(), $secondCompanyRoleTransfer),
        );

        // Assert
        $this->assertTrue(
            $companyUserResponseTransfer->getIsSuccessful(),
            'A company role created after an earlier role of the same company was validated must not be rejected.',
        );
    }

    public function testRejectsARoleBelongingToAnotherCompany(): void
    {
        // Arrange
        $companyTransfer = $this->tester->haveCompany();
        $otherCompanyTransfer = $this->tester->haveCompany();
        $foreignCompanyRoleTransfer = $this->tester->haveCompanyRole([
            'fkCompany' => $otherCompanyTransfer->getIdCompany(),
        ]);

        $companyUserTransfer = (new CompanyUserTransfer())
            ->setFkCompany($companyTransfer->getIdCompany())
            ->setCompanyRoleCollection(
                (new CompanyRoleCollectionTransfer())->addRole($foreignCompanyRoleTransfer),
            );

        // Act
        $companyUserResponseTransfer = (new CompanyRolesBelongToCompanyCompanyUserSavePreCheckPlugin())
            ->check($companyUserTransfer);

        // Assert
        $this->assertFalse($companyUserResponseTransfer->getIsSuccessful());
        $this->assertNotEmpty($companyUserResponseTransfer->getMessages());
    }

    public function testAcceptsACompanyUserWithoutRolesSoTheDefaultRolePluginCanAssignOne(): void
    {
        // Arrange
        $companyTransfer = $this->tester->haveCompany();

        $companyUserTransfer = (new CompanyUserTransfer())
            ->setFkCompany($companyTransfer->getIdCompany());

        // Act
        $companyUserResponseTransfer = (new CompanyRolesBelongToCompanyCompanyUserSavePreCheckPlugin())
            ->check($companyUserTransfer);

        // Assert
        $this->assertTrue($companyUserResponseTransfer->getIsSuccessful());
    }

    protected function createCompanyUserTransfer(int $idCompany, CompanyRoleTransfer $companyRoleTransfer): CompanyUserTransfer
    {
        return (new CompanyUserTransfer())
            ->setFkCompany($idCompany)
            ->setCompanyRoleCollection(
                (new CompanyRoleCollectionTransfer())->addRole($companyRoleTransfer),
            );
    }
}
