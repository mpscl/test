<?php


namespace App\Tests\Helpers;

use App\Helpers\TextHelper;
use PHPUnit\Framework\TestCase;

class TextHelperTest extends TestCase
{

    public function testExceptItems(): void
    {
        $word = 'camelCase';
        $result = TextHelper::convertCamelCaseToSnakeCase($word);
        $this->assertIsString($result);
        $this->assertEquals(10, strlen($result));
        $this->assertEquals('camel_case', $result);

        $word = 'testing';
        $result = TextHelper::convertCamelCaseToSnakeCase($word);
        $this->assertIsString($result);
        $this->assertEquals(7, strlen($result));
        $this->assertEquals('testing', $result);
    }
}
