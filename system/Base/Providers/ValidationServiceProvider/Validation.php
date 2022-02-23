<?php

namespace System\Base\Providers\ValidationServiceProvider;

use Phalcon\Validation as PhalconValidation;

class Validation
{
    protected $validator;

    public function __construct()
    {
        $this->validator = new PhalconValidation();
    }

    public function init()
    {
        return $this;
    }

    public function getValidator()
    {
        return $this->validator;
    }

    public function add($inputs, $ruleClass, $ruleParams)
    {
        $this->validator->add(
            $inputs,
            new $ruleClass(
                $ruleParams
            )
        );
    }

    public function validate($data)
    {
        return $this->validator->validate($data);
    }
}