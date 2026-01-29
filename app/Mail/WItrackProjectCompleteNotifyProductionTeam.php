<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WItrackProjectCompleteNotifyProductionTeam extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;
    public $attachmentPath;

    public function __construct($emailData, $attachmentPath){

        $this->emailData = $emailData;
        $this->attachmentPath = $attachmentPath;
    }

    public function build(){

        return $this->view('emails.project_complete')
                    ->subject('WITrack Project Completed - ' . $this->emailData['project_no'] . ' - ' . $this->emailData['project_name'])
                    ->attach($this->attachmentPath);                

    }

}