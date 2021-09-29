<?php
namespace App\Tests\Functional\Database;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineSchemaTest extends KernelTestCase
{

    public function testWhenCommandDoctrineSchemaValidationIsExecutedThenShowThatTheCurrentSchemaIsSynchronized(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $command = $application->find('doctrine:schema:validate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array('command' => $command->getName()),
            array('interactive' => false) //it's important not have to answer question when you execute this command, because abort the execution
        );

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('The mapping files are correct.', $output);
        $this->assertStringContainsString('The database schema is in sync with the mapping files.', $output);
    }
}
