<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\DataHubBundle\Tests\GraphQL\Traits;

use Codeception\Test\Unit;
use Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ElementIdentificationTrait;

class TestTrait
{
    use ElementIdentificationTrait;

    const BY_ID = 'ById';

    const BY_PATH = 'ByPath';

    /**
     * @param string $elementType
     *
     * @return string
     */
    protected function getElementById($elementType)
    {
        return $elementType . self::BY_ID;
    }

    /**
     * @param string $elementType
     *
     * @return string
     */
    protected function getElementByPath($elementType)
    {
        return $elementType . self::BY_PATH;
    }
}

class ElementIdentificationTraitTest extends Unit
{
    const TRAIT_TO_TEST = '\Pimcore\Bundle\DataHubBundle\GraphQL\Traits\ElementIdentificationTrait';

    const TEST_TYPE = 'object';

    public function testThrowingClientSafeExceptionIfTypeIsMissing()
    {
        // Arrange
        $this->expectExceptionMessageMatches('/type expected/');
        $newValueItemValue = [];
        // System under Test
        $sut = $this->getMockForTrait(self::TRAIT_TO_TEST);
        // Act + Assert
        $sut->getElementByTypeAndIdOrPath($newValueItemValue);
    }

    public function testThrowingClientSafeExceptionIfTypeIsNotSupported()
    {
        // Arrange
        $this->expectExceptionMessageMatches('/The type .* is not supported/');
        $newValueItemValue = ['type' => 'wrong'];
        // System under Test
        $sut = $this->getMockForTrait(self::TRAIT_TO_TEST);
        // Act + Assert
        $sut->getElementByTypeAndIdOrPath($newValueItemValue);
    }

    public function testThrowingClientSafeExceptionIfBothIdAndFullpathAreMissing()
    {
        // Arrange
        $this->expectExceptionMessageMatches('/either .* or .* expected/');
        $newValueItemValue = ['type' => self::TEST_TYPE];
        // System under Test
        $sut = $this->getMockForTrait(self::TRAIT_TO_TEST);
        // Act + Assert
        $sut->getElementByTypeAndIdOrPath($newValueItemValue);
    }

    public function testThrowingClientSafeExceptionIfBothIdAndFullpathArePassed()
    {
        // Arrange
        $this->expectExceptionMessage('either id or fullpath expected but not both');
        $newValueItemValue = [
            'type' => self::TEST_TYPE,
            'id' => 4,
            'fullpath' => '/some/path/withKey',
        ];
        // System under Test
        $sut = new TestTrait();
        // Act & Assert
        $sut->getElementByTypeAndIdOrPath($newValueItemValue);
    }

    public function testElementIdentificationGetElementByFullPath()
    {
        // Arrange
        $newValueItemValue = [
            'type' => self::TEST_TYPE,
            'fullpath' => '/some/path/withKey',
        ];
        // System under Test
        $sut = new TestTrait();
        // Act
        $result = $sut->getElementByTypeAndIdOrPath($newValueItemValue);
        // Assert
        $this->assertEquals(self::TEST_TYPE . TestTrait::BY_PATH, $result);
    }

    public function testElementIdentificationIfTypeCanBePassedAsSeparateArgument()
    {
        // Arrange
        $newValueItemValue = [
            'fullpath' => '/some/path/withKey',
        ];
        // System under Test
        $sut = new TestTrait();
        // Act
        $result = $sut->getElementByTypeAndIdOrPath($newValueItemValue, self::TEST_TYPE);
        // Assert
        $this->assertEquals(self::TEST_TYPE . TestTrait::BY_PATH, $result);
    }
}
