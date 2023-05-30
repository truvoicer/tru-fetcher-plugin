<?php
namespace TruFetcher\Includes\Traits;

trait Tru_Fetcher_Traits_Errors
{
    public array $errors = [];

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    public function addError(\WP_Error $error): void
    {
        $this->errors[] = $error;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function clearErrors() {
        $this->setErrors([]);
    }
}
