<?php

namespace Framework\Baseapp\Commands;

use Illuminate\Console\Command;
use Swoolecan\Foundation\Commands\TraitCommand;
use Framework\Baseapp\Helpers\ResourceContainer;

class AbstractCommand extends Command
{
    use TraitCommand;

    protected function getResource()
    {
        return app(ResourceContainer::class);
    }

    protected function getConfig()
    {
        return config();
    }

    public function getPointArgument($param)
    {
        return $this->argument($param);
    }
}
