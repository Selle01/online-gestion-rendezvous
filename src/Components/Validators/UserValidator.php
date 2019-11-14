<?php

namespace App\Components\Validators;


class UserValidator extends CommonValidator
{

    public function __construct(array $data, $itemDao, ?int $itemID = null, $genres, $specialties)
    {
        parent::__construct($data);
        $this->validator->rule('required', [
            'role_id', 'matricule', 'firstName', 'lastName', 'genre', 'login', 'password', 'CNI', 'email', 'tel', 'specialty_id'
        ]);
        $this->validator->rule('lengthBetween', ['firstName', 'lastName', 'login'], 3, 200);
        $this->validator->rule('length', 'CNI', 13);
        $this->validator->rule('length', 'password', 5);
        // $this->validator->rule('equals', 'password', 'confirmPassword');
        $this->validator->rule('email', 'email');
        $this->validator->rule('integer', 'tel');
        $this->validator->rule('length', 'tel', 9);
        $this->validator->rule('dateFormat', 'created_at', "Y-m-d H:i:s");
        $this->validator->rule('dateFormat', 'dateNais', "Y-m-d H:i:s");
        $this->validator->rule('subset', 'genre', $genres);
        $this->validator->rule('subset', 'specialty_id', $specialties);
        $this->validator->rule(function ($field, $value) use ($itemDao, $itemID) {
            return !($itemDao->exists($field, $value, $itemID));
        }, ['login', 'email', 'CNI', 'tel'], 'Cette valeur est déja utilisé ');
    }
}
