<?php
namespace App\Tests\Functional\Database;

use App\Kernel;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineMigrationTest extends KernelTestCase
{

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        try {
            $kernel = self::bootKernel();

            $em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

            /** @var Connection $connection */
            $connection = $em->getConnection();
            $driver = $connection->getDriver();
            if (!$driver instanceof Driver) {
                $this->markTestSkipped('This test requires MySQL.');
            }

            $databaseName = getenv('MYSQL_DB_NAME_TEST');
            if (in_array($databaseName, $connection->getSchemaManager()->listDatabases())) {
                $schemaTool = new SchemaTool($em);
                // Drop all tables, so we can test on a clean DB
                $schemaTool->dropDatabase();
            }
        } catch (\Exception $e) {
            $this->fail('Could not cleanup test database for migration test: ' . $e->getMessage());
        }
    }

    public function testWhenCommandDoctrineMigrationsMigrateIsExecutedThenAllDatabaseStructureIsCreatedOk(): void
    {

        $application = new Application(static::$kernel);
        //$application->add(new MigrationsMigrateDoctrineCommand());
        $command = $application->find('doctrine:migrations:migrate');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            array('command' => $command->getName()),
            array('interactive' => false) //it's important not have to answer question when you execute this command, because abort the execution
        );

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        // // Test if all migrations run through
        $this->assertMatchesRegularExpression('/\d+ sql queries\n$/', $output);
    }
}
