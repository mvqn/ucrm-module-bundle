<?php
declare(strict_types=1);

namespace Tests\MVQN\Common\Examples\Helpers;

trait CountryHelper
{

    public function getBoth()
    {
        return "{$this->name} ({$this->code})";
    }
}
