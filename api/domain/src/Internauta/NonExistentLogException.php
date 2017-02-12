<?php

namespace Muchacuba\Internauta;

class NonExistentLogException extends \Exception
{
    public function __construct($id)
    {
        parent::__construct(sprintf("Log with id \"%s\" was not found", $id));
    }
}
