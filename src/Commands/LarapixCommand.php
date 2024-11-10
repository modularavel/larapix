<?php

namespace Modularavel\Larapix\Commands;

use Illuminate\Console\Command;

class LarapixCommand extends Command
{
    public $signature = 'larapix';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
