<?php


namespace App\Tests\Helpers;

use App\Helpers\ArrayHelper;
use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase
{

    public function testExceptItems(): void
    {
        $array = ['1', null, 3, 4, 5];
        $exceptKeys = [1, 2];
        $result = ArrayHelper::exceptItems($array, $exceptKeys);
        $this->assertIsArray($result);
        $this->assertEquals(3, count($result));
        $this->assertEqualsCanonicalizing(['1', 4, 5], $result);
    }

    public function testOnlyItems(): void
    {
        $array = [1, '2', null, 4, 5];
        $exceptKeys = [1, 2];
        $result = ArrayHelper::onlyItems($array, $exceptKeys);
        $this->assertIsArray($result);
        $this->assertEquals(2, count($result));
        $this->assertEqualsCanonicalizing([2, null], $result);
    }
}
