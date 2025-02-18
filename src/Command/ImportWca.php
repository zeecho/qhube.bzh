<?php
namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:import-wca')]
class ImportWca extends Command
{
    // CREATE TABLE specific_results AS SELECT * FROM people_id p INNER JOIN Results r ON r.personId = p.wca_id

    protected static $defaultDescription = 'Populate the database with some default nations and cubers.';

    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to import the wca database...')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // this doesn't work
//        $this->importWCAExport($output);

        $this->createSpecificResultsTable();

        return Command::SUCCESS;
    }

    private function importWCAExport($output)
    {

        $sqlFile = '/tmp/WCA_export.sql/WCA_export.sql';

        $process = new Process([sprintf(
            'mysql -u%s -p%s %s < %s',
            $_ENV['DATABASE_USER'],
            $_ENV['DATABASE_PASSWORD'],
            $_ENV['DATABASE_NAME'],
            $sqlFile
        )]);

        return $process->run();
    }

    protected function createSpecificResultsTable()
    {
        $conn = $this->entityManager->getConnection();

        $sql = 'DROP TABLE IF EXISTS specific_results;';
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery();

        $sql = 'CREATE TABLE specific_results AS SELECT * FROM people_id p INNER JOIN Results r ON r.personId = p.wca_id;';
        $stmt = $conn->prepare($sql);
        return $stmt->executeQuery();
    }
}