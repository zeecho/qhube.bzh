<?php
namespace App\Command;

use App\Entity\Nation;
use App\Entity\PeopleId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:populate-database')]
class PopulateDatabase extends Command
{
    protected static $defaultDescription = 'Populate the database with some default nations and cubers.';

    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to populate the database...')
            ->addArgument('file', InputArgument::REQUIRED, 'CSV file to import');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileName = $input->getArgument('file');
        if (!file_exists($fileName)) {
            throw new \InvalidArgumentException('File not found: ' . $fileName);
        }

        $csv = array_map(function($line) {
            return str_getcsv($line, "\t");
        }, file($fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

        $progressBar = new ProgressBar($output, count($csv));
        $progressBar->start();

        $this->clearAllPeople();
        foreach ($csv as $key => $row) {
            $wcaId = $row[0];
            $countries = $row[1];
            if (preg_match('/^\d{4}[A-Z]{4}\d{2}$/', $wcaId)) {
                $output->writeln("");
                $output->writeln("Importing " . $wcaId);
            } else {
                throw new \Exception("On line " . $key+1 . ", " . $wcaId . " does not seem to be a valid WCA ID", 1);
            }

            foreach (explode(",", $countries) as $country) {
                $peopleId = new PeopleId();
                $peopleId->setCountryCode($country);
                $peopleId->setWcaId($wcaId);
                $this->entityManager->persist($peopleId);
            }

            $progressBar->advance();
        }

        $this->entityManager->flush();
        $progressBar->finish();
        $output->writeln("");
        $output->writeln('<info>Import completed successfully!</info>');


        $this->clearAllNations();
        $this->createNations();
//        $this->createPeople();
        return Command::SUCCESS;
    }

    // this is useless but kept if needed in the future
    private function createPeople()
    {
        $countriesPeople = [
            'bzh' => []
        ];
        foreach ($countriesPeople as $country => $people) {
            foreach ($people as $person) {
                $this->createPerson($person, $country);
            }
        }
    }

    private function createPerson($wcaId, $country)
    {
        $peopleId = new PeopleId();
        $peopleId->setCountryCode($country);
        $peopleId->setWcaId($wcaId);

        $this->entityManager->persist($peopleId);
        $this->entityManager->flush();
    }

    private function createNations()
    {
        $this->createNation('Breizh / Bertègn', 'bzh', 'bzh.svg');
        $this->createNation('Normaundie', 'nor', 'nor.svg');
        $this->createNation('Elsàss', 'els', 'els.svg');
        $this->createNation('Arpitania', 'arp', 'arp.svg');
        $this->createNation('Savouè', 'sav', 'sav.svg');
//        $this->createNation('Forêz', 'for', 'for.svg');
        $this->createNation('Occitània', 'occ', 'occ.svg');
        $this->createNation('Provença', 'pro', 'pro.svg');
        $this->createNation('Auvèrnhe', 'auv', 'auv.svg');
        $this->createNation('Catalunya', 'cat', 'cat.svg');
        $this->createNation('Corsica', 'cor', 'cor.svg');
        $this->createNation('Bregogne', 'bre', 'bre.svg');
        $this->createNation('Kurdistan', 'kur', 'kur.svg');
        $this->createNation('Euskal Herria', 'euh', 'euh.svg');
        $this->createNation('Galicia', 'gal', 'gal.svg');
        $this->createNation('Asturies', 'ast', 'ast.svg');
        $this->createNation('Poetou', 'poe', 'poe.svg');
        $this->createNation('Fraintche-Comtè / Franche-Comtât', 'frc', 'frc.svg');
        $this->createNation('Engoumaes / Engolmés', 'eng', 'eng.svg');
        $this->createNation('Anjou', 'anj', 'anj.svg');
        $this->createNation('Champaigne', 'cha', 'cha.svg');
        $this->createNation('Louréne / Lottrìnge', 'lou', 'lou.svg');
        $this->createNation('Dârfénât / Daufinat', 'dau', 'dau.svg');
        $this->createNation('Cymru', 'cym', 'cym.svg');
        $this->createNation('Alba / Scotland', 'sco', 'sco.svg');
        $this->createNation('Kernow', 'ker', 'ker.svg');
        $this->createNation('Tuaisceart Éireann / Norlin Airlann', 'tué', 'tué.svg');
    }

    private function createNation($name, $short, $img)
    {
        $nation = new Nation();
        $nation->setName($name);
        $nation->setShort($short);
        $nation->setImg($img);

        $this->entityManager->persist($nation);
        $this->entityManager->flush();
    }

    private function clearAllNations()
    {
        $conn = $this->entityManager->getConnection();

        $sql = 'TRUNCATE nation;';
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery();
    }

    private function clearAllPeople()
    {
        $conn = $this->entityManager->getConnection();

        $sql = 'TRUNCATE people_id;';
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery();
    }
}