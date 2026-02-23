<?php

namespace tests;

use Core\Paginator;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    public function testPaginationWorks()
    {
        $items = range(1, 10);

        $result = Paginator::paginate($items, 2, 3);

        $this->assertEquals([4, 5, 6], $result);
    }
}