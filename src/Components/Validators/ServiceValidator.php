<?php

namespace App\Components\Validators;

use App\Dao\ServiceDao;

class ServiceValidator extends CommonValidator
{

    public function __construct(array $data, ServiceDao $serviceDao, ?int $serviceID = null)
    {
        parent::__construct($data);
        $this->validator->rule('required', 'name');
        $this->validator->rule('required', 'created_at');
        $this->validator->rule('dateFormat', 'created_at', "Y-m-d H:i:s");
        $this->validator->rule('lengthBetween', 'name', 3, 200);
        $this->validator->rule(function ($field, $value) use ($serviceDao, $serviceID) {
            return !($serviceDao->exists($field, $value, $serviceID));
        }, ['name'], 'Cette valeur est déja utilisé ');
    }
}
