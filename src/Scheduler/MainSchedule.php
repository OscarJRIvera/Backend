<?php
namespace App\Scheduler;

use App\Command\ExtractDataCommand;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Component\Scheduler\RecurringMessage;
USE App\Service\Transform;
use App\Service\SFTPService;
use App\Service\DBService;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class MainSchedule implements ScheduleProviderInterface
{
    private $transform;
    private $httpClient;
    private $sftpService;
    private $dbService;

    public function __construct(
        Transform $transform,
        HttpClientInterface $httpClient,
        SFTPService $sftpService,
        DBService $dbService
    ) {
        $this->transform = $transform;
        $this->httpClient = $httpClient;
        $this->sftpService = $sftpService;
        $this->dbService = $dbService;
    }

    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                RecurringMessage::every('10 seconds', new ExtractDataCommand($this->transform, $this->httpClient, $this->sftpService, $this->dbService))
            );
    }
}
