<?php

namespace App\Components\Validators;

use App\Libraries\Validator;

abstract class CommonValidator
{
    protected $data;
    protected $validator;

    public function __construct(array $data)
    {
        $this->data = $data;
        Validator::lang('fr');
        $this->validator  = new Validator($_POST);
    }
    public function validate(): bool
    {
        return $this->validator->validate();
    }
    public function errors(): array
    {
        return $this->validator->errors();
    }
}
