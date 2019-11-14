<?php

namespace App\Components\Validators;

use App\Dao\SpecialtyDao;

class SpecialtyValidator extends CommonValidator
{

    public function __construct(array $data, SpecialtyDao $specialtyDao, ?int $specialtyID = null, array $services)
    {
        parent::__construct($data);
        $this->validator->rule('required', 'name');
        $this->validator->rule('required', 'created_at');
        $this->validator->rule('lengthBetween', 'name', 3, 200);
        $this->validator->rule('dateFormat', 'created_at', "Y-m-d H:i:s");
        $this->validator->rule('subset', 'service_id', array_keys($services));
        $this->validator->rule(function ($field, $value) use ($specialtyDao, $specialtyID) {
            return !($specialtyDao->exists($field, $value, $specialtyID));
        }, ['name'], 'Cette valeur est déja utilisé ');
    }
}
