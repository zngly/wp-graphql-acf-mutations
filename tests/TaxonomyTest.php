<?php

declare(strict_types=1);

require __DIR__ . '/graphql.php';

use PHPUnit\Framework\TestCase;
use Zngly\ACFM\Tests\Graphql;

final class TaxonomyTest extends TestCase
{

    /**
     * create a parent class which has a setup function 
     * that logs in and resets the database to a clean state
     */

    public $gql;

    protected function setUp(): void
    {
        $gql = Graphql::getInstance();
        $this->gql = $gql;
    }

    public function testTaxonomy()
    {
        $this->assertTrue(true);
    }

    public function testQuery(): void
    {
        // $this->gql->query('query {
        //     generalSettings {
        //         url
        //     }
        // }');
        $this->assertTrue(true);
    }
}
