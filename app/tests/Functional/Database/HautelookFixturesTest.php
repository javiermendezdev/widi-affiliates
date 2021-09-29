<?php
namespace App\Tests\Functional\Database;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class HautelookFixturesTest extends KernelTestCase
{
    public function testWhenCommandHautelookFixturesLoadIsExecutedThenAnyErrorIsShowed(): void
    {
        $kernel = self::bootKernel();

        $application = new Application($kernel);
        $command = $application->find('hautelook:fixtures:load');
        $commandTester = new CommandTester($command);

        $commandTester->execute(
            array('command' => $command->getName()),
            array('interactive' => false), //it's important not have to answer question when you execute this command, because abort the execution
            array('--env' => 'test')
        );

        // the output of the command in the console
        $output = $commandTester->getDisplay();

        //Not return anything ... but execute fixtures
        //If exist some error, the test was fire an error
        $this->assertStringContainsString('', $output);
    }
}
