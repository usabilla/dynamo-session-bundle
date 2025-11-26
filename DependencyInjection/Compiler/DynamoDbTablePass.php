<?php
/**
 * Copyright (c) 2013 Gijs Kunze
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GWK\DynamoSessionBundle\DependencyInjection\Compiler;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Exception\ResourceNotFoundException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\LogicException;

class DynamoDbTablePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if(!$container->hasDefinition("dynamo_session_client") && !$container->hasAlias("dynamo_session_client")) {
            return;
        }

        if($container->getAlias('session.handler') != "dynamo_session_handler") {
            return;
        }

        /** @var DynamoDbClient $client */
        $client = $container->get("dynamo_session_client");

        $tableName = $container->getParameter("dynamo_session_table");

        try {
            $client->describeTable(['TableName' => $tableName]);
        } catch(DynamoDbException|ResourceNotFoundException $e) {
            echo "ResourceNotFoundException - Table does not exist. Creating table...\n";

            $read_capacity = $container->getParameter("dynamo_session_read_capacity");
            $write_capacity = $container->getParameter("dynamo_session_write_capacity");

            // Create the DynamoDB table using the client's createTable method
            $client->createTable([
                'TableName' => $tableName,
                'AttributeDefinitions' => [
                    [
                        'AttributeName' => 'id',
                        'AttributeType' => 'S'
                    ]
                ],
                'KeySchema' => [
                    [
                        'AttributeName' => 'id',
                        'KeyType' => 'HASH'
                    ]
                ],
                'ProvisionedThroughput' => [
                    'ReadCapacityUnits' => $read_capacity,
                    'WriteCapacityUnits' => $write_capacity
                ]
            ]);

            echo "Table '$tableName' created successfully!\n";
        }
    }
}
