<?php

namespace App\Service;

use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\PublicKeyLoader;

class SFTPService
{
    private $sftp;

    public function __construct()
    {
        $host = $_ENV['SFTP_HOST'];
        $port = $_ENV['SFTP_PORT'];
        $username = $_ENV['SFTP_USERNAME'];
        $privateKeyPath = $_ENV['SFTP_PRIVATE_KEY'];

        // Load the private key
        $privateKey = PublicKeyLoader::load(file_get_contents($privateKeyPath));

        // Create an SFTP instance and connect
        $this->sftp = new SFTP($host, $port);

        if (!$this->sftp->login($username, $privateKey)) {
            throw new \Exception('Could not authenticate with the private key.');
        }
    }

    public function uploadFile(string $localFilePath, string $remoteFilePath): void
    {
        

        if (!$this->sftp->put($remoteFilePath, file_get_contents($localFilePath))) {
            $showerror=$this->sftp->getLastSFTPError();
            echo $showerror;    
            throw new \Exception("Could not upload the file: $localFilePath to $remoteFilePath.");
        }
        
    }
}