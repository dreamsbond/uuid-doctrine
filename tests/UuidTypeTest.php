<?php

namespace Ramsey\Uuid\Doctrine;

use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @package App
 */
class UuidTypeTest extends TestCase
{
    /**
     * @var
     */
    private $platform;
    /**
     * @var
     */
    private $type;

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function setUpBeforeClass()
    {
        if (class_exists('Doctrine\\DBAL\\Types\\Type')) {
            Type::addType('uuid', 'Dreamsbond\Uuid\Doctrine\UuidType');
        }
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function setUp()
    {
        $this->platform = $this->getPlatformMock();
        $this->platform->expects($this->any())
            ->method('getGuidTypeDeclarationSQL')
            ->will($this->returnValue('DUMMYVARCHAR()'));

        $this->type = Type::getType('uuid');
    }

    /**
     * @covers Dreamsbond\Uuid\Doctrine\UuidType::convertToDatabaseValue
     */
    public function testUuidConvertsToDatabaseValue()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $expected = $uuid->toString();
        $actual = $this->type->convertToDatabaseValue($uuid, $this->platform);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Dreamsbond\Uuid\Doctrine\UuidType::convertToDatabaseValue
     */
    public function testUuidInterfaceConvertsToDatabaseValue()
    {
        $uuid = \Mockery::mock('Ramsey\\Uuid\\UuidInterface,Ramsey\\Uuid\\Doctrine\\StringableInterface');

        $uuid
            ->shouldReceive('__toString')
            ->once()
            ->andReturn('foo');

        $actual = $this->type->convertToDatabaseValue($uuid, $this->platform);

        $this->assertEquals('foo', $actual);
    }

    /**
     * @covers Dreamsbond\Uuid\Doctrine\UuidType::convertToDatabaseValue
     */
    public function testUuidStringConvertsToDatabaseValue()
    {
        $uuid = 'ff6f8cb0-c57d-11e1-9b21-0800200c9a66';

        $actual = $this->type->convertToDatabaseValue($uuid, $this->platform);

        $this->assertEquals($uuid, $actual);
    }

    /**
     * @expectedException Doctrine\DBAL\Types\ConversionException
     * @covers Dreamsbond\Uuid\Doctrine\UuidType::convertToDatabaseValue
     */
    public function testInvalidUuidConversionForDatabaseValue()
    {
        $this->type->convertToDatabaseValue('abcdefg', $this->platform);
    }

    /**
     * @covers Dreamsbond\Uuid\Doctrine\UuidType::convertToDatabaseValue
     */
    public function testNullConversionForDatabaseValue()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    /**
     * @covers Dreamsbond\Uuid\Doctrine\UuidType::convertToPHPValue
     */
    public function testUuidInterfaceConvertsToPHPValue()
    {
        $uuid = \Mockery::mock('Ramsey\\Uuid\\UuidInterface');

        $actual = $this->type->convertToPHPValue($uuid, $this->platform);

        $this->assertSame($uuid, $actual);
    }

    /**
     * @covers Dreamsbond\Uuid\Doctrine\UuidType::convertToPHPValue
     */
    public function testUuidConvertsToPHPValue()
    {
        $uuid = $this->type->convertToPHPValue('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $this->platform);
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $uuid);
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }

    /**
     * @expectedException Doctrine\DBAL\Types\ConversionException
     * @covers Dreamsbond\Uuid\Doctrine\UuidType::convertToPHPValue
     */
    public function testInvalidUuidConversionForPHPValue()
    {
        $this->type->convertToPHPValue('abcdefg', $this->platform);
    }

    /**
     * @covers Dreamsbond\Uuid\Doctrine\UuidType::convertToPHPValue
     */
    public function testNullConversionForPHPValue()
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    /**
     * @covers Dreamsbond\Uuid\Doctrine\UuidType::convertToPHPValue
     */
    public function testReturnValueIfUuidForPHPValue()
    {
        $uuid = Uuid::uuid4();
        $this->assertSame($uuid, $this->type->convertToPHPValue($uuid, $this->platform));
    }

    /**
     * @covers Dreamsbond\Uuid\Doctrine\UuidType::getName
     */
    public function testGetName()
    {
        $this->assertEquals('limoncelloUuid', $this->type->getName());
    }

    /**
     * @covers Dreamsbond\Uuid\Doctrine\UuidType::getSqlDeclaration
     */
    public function testGetGuidTypeDeclarationSQL()
    {
        $this->assertEquals('DUMMYVARCHAR()', $this->type->getSqlDeclaration(['length' => 36], $this->platform));
    }

    /**
     * @covers Dreamsbond\Uuid\Doctrine\UuidType::requiresSQLCommentHint
     */
    public function testRequiresSQLCommentHint()
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getPlatformMock()
    {
        return $this->getMockBuilder('Doctrine\DBAL\Platforms\AbstractPlatform')
            ->setMethods(['getGuidTypeDeclarationSQL'])
            ->getMockForAbstractClass();
    }
}
