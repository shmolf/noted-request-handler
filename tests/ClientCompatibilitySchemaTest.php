<?php

declare(strict_types=1);

namespace shmolf\NotedHydrator\Tests;

use PHPUnit\Framework\TestCase;
use shmolf\NotedHydrator\JsonSchema\Library;
use shmolf\NotedHydrator\Tests\DataObjects\ClientCompatibility;
use Swaggest\JsonSchema\Exception\ArrayException;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\SchemaContract;

class ClientCompatibilitySchemaTest extends TestCase
{
    private SchemaContract $schemaValidator;
    private array $schemas;

    public function setUp(): void
    {
        $this->schemas = Library::getCurrent();
        $this->schemaValidator = Schema::import(
            json_decode(
                file_get_contents($this->schemas['client-compatibility']['file'])
            )
        );
    }

    public function testNoteSchemaHappy(): void
    {
        $isValid = $this->validateSchema(json_encode(ClientCompatibility::getHappy()));

        $this->assertTrue($isValid);
    }

    public function testNoteSchemaSad(): void
    {
        $isValid = $this->validateSchema(json_encode(ClientCompatibility::getSad()));

        $this->assertTrue($isValid);
    }

    public function testNoteSchemaBad(): void
    {
        $this->expectException(ArrayException::class);
        $this->validateSchema(json_encode(ClientCompatibility::getBad()));
    }

    private function validateSchema(string $json): bool
    {
        $this->schemaValidator->in(json_decode($json));
        return true;
    }
}
