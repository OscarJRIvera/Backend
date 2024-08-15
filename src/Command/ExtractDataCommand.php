<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Service\Transform;
use App\Service\SFTPService;
use App\Service\DBService;

#[AsCommand(
    name: 'ExtractDataCommand',
    description: 'Add a short description for your command',
)]
class ExtractDataCommand extends Command
{
    private $httpClient;
    private $transform;

    private $sftpService;

    private $dbService;

    public function __construct(Transform $transformcsv,HttpClientInterface $httpClient, SFTPService $sftpService, DBService $dbService)
    {
        $this->httpClient = $httpClient;
        $this->transform = $transformcsv;
        $this->sftpService = $sftpService;
        $this->dbService = $dbService;  
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('date', InputArgument::OPTIONAL, 'The date for the data to be extracted');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        
        $response = $this->httpClient->request('GET', 'https://dummyjson.com/users');
        $data = json_decode($response->getContent(), true)['users'];

        $date = $input->getArgument('date') ?: (new \DateTime())->format('Ymd');

        // creates folder
        if (!is_dir("Archivos")) {
            mkdir("Archivos", 0777, true);
        }
        

        $JsonF = "Archivos/data_{$date}.json";
        $ETLF = "Archivos/etl_{$date}.csv";
        $SummaryF = "Archivos/summary_{$date}.csv";

        file_put_contents($JsonF, json_encode($data));

        $this->transform->transformCsv($ETLF, $data);

        $summary = $this->generateSummary($data);

        $this->transform->TransformSummaryCsv($SummaryF, $summary);

        $output->writeln("Data extracted and transformed");


        $this->sftpService->uploadFile($JsonF, "data_{$date}.json");
        
        $this->sftpService->uploadFile($ETLF, "etl_{$date}.csv");

        $this->sftpService->uploadFile($SummaryF, "summary_{$date}.csv");
        
        $rutaAbsoluta = realpath($SummaryF);
        $this->dbService->saveSummaryFile($rutaAbsoluta);

        
        


        return Command::SUCCESS;
    }
    private function generateSummary(array $data): array
    {
        $summary = [
            'register' => 0,
            'gender' => ['male' => 0, 'female' => 0, 'other' => 0],
            'age' => [
                '00-10' => ['male' => 0, 'female' => 0, 'other' => 0],
                '11-20' => ['male' => 0, 'female' => 0, 'other' => 0],
                '21-30' => ['male' => 0, 'female' => 0, 'other' => 0],
                '31-40' => ['male' => 0, 'female' => 0, 'other' => 0],
                '41-50' => ['male' => 0, 'female' => 0, 'other' => 0],
                '51-60' => ['male' => 0, 'female' => 0, 'other' => 0],
                '61-70' => ['male' => 0, 'female' => 0, 'other' => 0],
                '71-80' => ['male' => 0, 'female' => 0, 'other' => 0],
                '81-90' => ['male' => 0, 'female' => 0, 'other' => 0],
                '91+' => ['male' => 0, 'female' => 0, 'other' => 0],
            ],
            'city' => [],
            'os' => ['Windows' => 0, 'Mac OS' => 0, 'Linux' => 0, 'Unknown' => 0],
        ];

        foreach ($data as $user) {
            $summary['register']++; 
            $gender = strtolower($user['gender']);
            $age = $user['age'];
            $city = $user['address']['city'];
            $os = $this->getOsFromUserAgent($user['userAgent']);

            // Increment gender count
            if (isset($summary['gender'][$gender])) {
                $summary['gender'][$gender]++;
            }

            // Increment age group and gender count
            foreach ($summary['age'] as $ageGroup => &$count) {
                [$min, $max] = array_map('intval', explode('-', $ageGroup . '-' . $ageGroup));
                if ($age >= $min && $age <= $max) {
                    $count[$gender]++;
                    break;
                }
            }

            // Increment city count
            if (!isset($summary['city'][$city])) {
                $summary['city'][$city] = ['male' => 0, 'female' => 0, 'other' => 0];
            }
            $summary['city'][$city][$gender]++;

            // Increment OS count
            if (isset($summary['os'][$os])) {
                $summary['os'][$os]++;
            }
        }

        return $summary;
    }

    private function getOsFromUserAgent(string $userAgent): string
    {
        $osArray = [
            'Windows' => 'Windows',
            'Mac OS' => 'Mac',
            'Linux' => 'Linux',
        ];

        foreach ($osArray as $os => $pattern) {
            if (preg_match("/$pattern/i", $userAgent)) {
                return $os;
            }
        }

        return 'Unknown';
    }

}
