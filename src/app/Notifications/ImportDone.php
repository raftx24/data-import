<?php

namespace LaravelEnso\DataImport\app\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use LaravelEnso\DataImport\app\Enums\ImportTypes;
use LaravelEnso\DataImport\app\Models\DataImport;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ImportDone extends Notification implements ShouldQueue
{
    use Queueable;

    public $dataImport;

    public function __construct(DataImport $dataImport)
    {
        $this->dataImport = $dataImport;
    }

    public function via()
    {
        return array_merge(['mail'], config('enso.imports.notifications'));
    }

    public function toBroadcast()
    {
        return (new BroadcastMessage([
            'level' => 'success',
            'title' => $this->broadcastTitle(),
            'body' => $this->filename(),
            'icon' => 'file-excel',
        ]))->onQueue($this->queue);
    }

    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject($this->mailSubject())
            ->markdown('laravel-enso/data-import::emails.import', [
                'name' => $notifiable->person->appellative
                    ?: $notifiable->person->name,
                'filename' => $this->filename(),
                'type' => $this->type(),
                'successful' => $this->dataImport->successful,
                'failed' => $this->dataImport->failed,
                'entries' => $this->dataImport->entries(),
            ]);
    }

    public function toArray()
    {
        return [
            'body' => $this->notificationBody(),
            'icon' => 'file-excel',
        ];
    }

    private function notificationBody()
    {
        return $this->broadcastTitle()
            .': '
            .$this->filename();
    }

    private function broadcastTitle()
    {
        return __(':type import done', ['type' => $this->type()]);
    }

    private function mailSubject()
    {
        return __(config('app.name'))
            .': '
            .__(':type import done', ['type' => $this->type()]);
    }

    private function filename()
    {
        return $this->dataImport->file->original_name;
    }

    private function type()
    {
        return ImportTypes::get($this->dataImport->type);
    }
}
