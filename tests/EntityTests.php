<?php

use Ramsey\Uuid\Uuid;
use SimpleORM\Entity;

require_once('assets/Book.php');

class EntityTests extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function can_create_an_instance()
    {
        static::assertTrue(new Book() instanceof Entity);
    }

    /**
     * @test
     */
    public function can_create_a_mocked_entity_by_definition()
    {
        static::assertArrayHasKey('id', Book::factory());
    }

    /**
     * @test
     */
    public function can_create_a_mocked_entity_by_function()
    {
        static::assertArrayHasKey('id', Book::factory(function($faker){
            return [
                'id' => Uuid::uuid4(),
                'title' => $faker->sentence(5, true),
            ];
        }));
    }

    /**
     * @test
     */
    public function created_entity_is_new()
    {
        static::assertTrue((new Book)->isNew());
    }
}