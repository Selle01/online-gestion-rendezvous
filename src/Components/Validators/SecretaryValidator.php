<?php

namespace App\Components\Validators;

use App\Dao\SecretaryDao;
use App\Dao\UserDao;

class SecretaryValidator extends CommonValidator
{

    public function __construct(array $data, UserDao $itemDao, ?int $itemID = null, $genres, $services)
    {
        parent::__construct($data);
        $this->validator->rule('required', [
            'role_id', 'matricule', 'firstName', 'lastName', 'genre', 'login',
            // 'password', 
            'CNI', 'email', 'tel', 'service_id'
        ]);
        $this->validator->rule('lengthBetween', ['firstName', 'lastName', 'login'], 2, 200);
        // $this->validator->rule('length', 'CNI', 13);
        // $this->validator->rule('length', 'password', 5);
        $this->validator->rule('email', 'email');
        $this->validator->rule('integer', 'tel');
        $this->validator->rule('length', 'tel', 9);
        $this->validator->rule('dateFormat', 'created_at', "Y-m-d H:i:s");
        $this->validator->rule('dateFormat', 'dateNais', "Y-m-d H:i:s");
        $this->validator->rule('subset', 'genre', $genres);
        $this->validator->rule('subset', 'service_id', $services);
        $this->validator->rule(function ($field, $value) use ($itemDao, $itemID) {
            return !($itemDao->exists($field, $value, $itemID));
        }, ['login', 'email', 'CNI', 'tel'], 'Cette valeur est déja utilisé ');
    }
}
