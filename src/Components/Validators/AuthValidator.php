<?php

namespace App\Components\Validators;

use App\Dao\ServiceDao;

class AuthValidator extends CommonValidator
{
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->validator->rule('required', ['login', 'password']);
        $this->validator->rule('requiredWith', 'password', 'login');
    }
}
