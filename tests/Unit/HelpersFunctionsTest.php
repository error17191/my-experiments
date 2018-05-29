<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HelpersFunctionsTest extends TestCase
{
    public function test_is_numeric_array_only_accepts_arrays()
    {
        $this->expectException(\TypeError::class);
        is_numeric_array('String');
    }

    public function test_empty_array_is_considered_numeric()
    {
        $this->assertTrue(is_numeric_array([]));
    }

    public function test_empty_array_is_not_considered_associative()
    {
        $this->assertFalse(is_assoc_array([]));
    }

    public function test_numeric_arrays_can_be_associative()
    {
        $array = [
            5 => 'five',
            '3' => 'three'
        ];
        $this->assertTrue(is_assoc_array($array));
    }
}
