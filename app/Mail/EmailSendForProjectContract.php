<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\ProjectContract;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailSendForProjectContract extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($event,$envelope,$subject,ProjectContract $projectContract)
    {
        $this->event = $event;
        $this->projectContract = $projectContract;
        $this->subject = $subject;
        $this->envelope = $envelope;
    }

    public function envelope()
    {
        $sendCCs = array();
        $sendTos = array();
        $sendReplys = array();

        $sendFrom = User::where("email",$this->envelope["from"])->firstOrFail();

        foreach(User::where("email",$this->envelope["to"])->get() as $perTo){
            array_push($sendTos,new Address($perTo->email,$perTo->name));
        }

        foreach(User::where("email",$this->envelope["cc"])->get() as $perCC){
            array_push($sendCCs,new Address($perCC->email,$perCC->name));
        }

        foreach(User::where("email",$this->envelope["reply"])->get() as $perReply){
            array_push($sendReplys,new Address($perReply->email,$perReply->name));
        }

        $sendFrom =  new Address($sendFrom->email,$sendFrom->name);

        return new Envelope(
            to: $sendTos,
            cc: $sendCCs,
            from: $sendFrom,
            replyTo: $sendReplys,
            subject: $this->subject,
        );
    }
    public function content()
    {
        return new Content(
            view: 'internal user.project contract.project contract.email',
            with: [
                'projectContract' => $this->projectContract,
                'event' => $this->event,
            ],
        );
    }

    public function attachments()
    {
        return [];
    }
}
