<?php

namespace App\Tests\Helpers;

use App\Helpers\ValidationHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationHelperTest extends TestCase
{

    public function testFormatErrors(): void
    {
        $badProperties = new ConstraintViolationList();
        $badProperties->add(
            new ConstraintViolation(
                'This is not a valid property of CLASS',
                'This is not a valid property of CLASS',
                array(),
                null,
                null,
                null
            )
        );
        $result = ValidationHelper::formatErrors($badProperties);
        $this->assertIsArray($result);
        $this->assertEquals(1, count($result));
        $this->assertIsArray($result[0]);
        $this->assertEquals(2, count($result[0]));
        $this->assertArrayHasKey('field', $result[0]);
        $this->assertArrayHasKey('message', $result[0]);
    }
}
