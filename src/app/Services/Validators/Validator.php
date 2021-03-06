<?php

namespace LaravelEnso\DataImport\app\Services\Validators;

use LaravelEnso\Helpers\app\Classes\Obj;

abstract class Validator
{
    private $errors;
    private $params;

    public function __construct()
    {
        $this->emptyErrors();
    }

    abstract public function run(Obj $row);

    public function fails()
    {
        return $this->errors->isNotEmpty();
    }

    public function message()
    {
        return $this->errors->implode(' | ');
    }

    public function addError(string $error)
    {
        $this->errors->push($error);
    }

    public function emptyErrors()
    {
        $this->errors = collect();
    }

    public function params()
    {
        return $this->params;
    }

    public function setParams(?Obj $params)
    {
        $this->params = $params;

        return $this;
    }
}
