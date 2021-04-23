<?php

declare(strict_types=1);

namespace shmolf\NotedRequestHandler\Tests;

use Opis\JsonSchema\Helper;
use Opis\JsonSchema\Resolvers\SchemaResolver;
use Opis\JsonSchema\ValidationResult;
use Opis\JsonSchema\Validator;
use PHPUnit\Framework\TestCase;
use shmolf\NotedRequestHandler\Tests\DataObjects\ClientCompatibility;

class ClientCompatibilitySchemaTest extends TestCase
{
    private const SCHEMA_URI = 'https://note-d.app/schema/client-compatibility.v1.json';
    private const SCHEMA_FILE = './src/JsonSchemas/client-compatibility.json';
    private Validator $validator;

    public function setUp(): void
    {
        $this->validator = new Validator();
        $resolver = $this->validator->resolver();

        if ($resolver instanceof SchemaResolver) {
            $resolver->registerFile(self::SCHEMA_URI, self::SCHEMA_FILE);
        }
    }

    public function testNoteSchemaHappy(): void
    {
        $validation = $this->validateSchema(ClientCompatibility::getHappy());
        /** @psalm-suppress PossiblyNullReference since 'hasError' prevents null referencing **/
        $msg = $validation->hasError() ? $validation->error()->message() : '';

        $this->assertTrue($validation->isValid(), $msg);
    }

    public function testNoteSchemaSad(): void
    {
        $validation = $this->validateSchema(ClientCompatibility::getSad());
        /** @psalm-suppress PossiblyNullReference since 'hasError' prevents null referencing **/
        $msg = $validation->hasError() ? $validation->error()->message() : '';

        $this->assertTrue($validation->isValid(), $msg);
    }

    public function testNoteSchemaBad(): void
    {
        $validation = $this->validateSchema(ClientCompatibility::getBad());

        $this->assertTrue($validation->hasError());
    }

    private function validateSchema(array $data): ValidationResult
    {
        $resolver = $this->validator->resolver();
        /** @var array */
        $preppedData = Helper::toJSON($data);

        // We'll check if the $resolver is set, otherwise, we'll manully load the file.
        return $resolver instanceof SchemaResolver
            ? $this->validator->validate($preppedData, self::SCHEMA_URI)
            : $this->validator->validate($preppedData, file_get_contents(self::SCHEMA_FILE));
    }
}