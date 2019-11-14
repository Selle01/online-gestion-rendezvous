<?php

namespace App\Dao\Exception;


class NotFoundException extends \Exception
{
    function __construct(string $table, int $id)
    {
        $this->message = "Aucun enregistrement ne correspond a l'id #$id dans la table '$table'";
    }
}
