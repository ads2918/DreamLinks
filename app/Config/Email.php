<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = '';
    public string $fromName   = '';
    public string $recipients = '';
    public string $userAgent  = 'CodeIgniter';
    public string $protocol   = 'smtp';
    public string $mailPath   = '/usr/sbin/sendmail';
    public string $SMTPHost   = '';
    public string $SMTPUser   = '';
    public string $SMTPPass   = '';
    public int $SMTPPort      = 465;
    public int $SMTPTimeout   = 5;
    public bool $wordWrap     = true;
    public int $wrapChars     = 76;
    public string $mailType   = 'html';
    public string $charset    = 'UTF-8';
    public bool $validate     = false;
    public int $priority      = 3;
    public string $CRLF       = "\r\n";
    public string $newline    = "\r\n";
    public bool $BCCBatchMode = false;
    public int $BCCBatchSize  = 200;
    public bool $DSN          = false;
    public string $SMTPCrypto = 'ssl'; // Set to 'ssl' for port 465

    public function __construct()
    {
        parent::__construct();

        // This pulls from .env without hardcoding values here
        $this->SMTPHost = env('mail.SMTPHost');
        $this->SMTPUser = env('mail.SMTPUser');
        $this->SMTPPass = env('mail.SMTPPass');
        $this->SMTPPort = (int) env('mail.SMTPPort');
        $this->mailPath = env('mail.mailPath');
    }
}
