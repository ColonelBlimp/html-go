<?php declare(strict_types=1);
namespace html_go\indexing;

use PHPUnit\Framework\TestCase;

class NewIndexManagerTest extends TestCase
{
    function testInstantiation(): void {
        $manager = new NewIndexManager(TEST_DATA_ROOT);
        $this->assertNotNull($manager);

//        return $manager;
    }


}
