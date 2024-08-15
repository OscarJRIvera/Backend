<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\summary; // Import the SummaryFile entity

class DBService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function saveSummaryFile(string $filename)
    {
        $fecha = new \DateTime();

        // Create a new SummaryFile entity
        $summaryFile = new summary();
        $summaryFile->setRuta_Archivo($filename);
        $summaryFile->setFecha($fecha);

        // Save it to the database
        $this->entityManager->persist($summaryFile);
        $this->entityManager->flush();
    }
}
