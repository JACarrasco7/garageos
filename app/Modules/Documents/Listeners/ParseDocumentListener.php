<?php

namespace App\Modules\Documents\Listeners;

use App\Modules\Documents\Events\DocumentUploaded;
use App\Modules\Documents\Jobs\ParseDocumentJob;

class ParseDocumentListener
{
    public function handle(DocumentUploaded $event): void
    {
        ParseDocumentJob::dispatch($event->document);
    }
}