<?php namespace Moregold\Infrastructure\Validators;

interface RulesInterface
{
    /**
     * Retrieve default or overloaded validation rules for current model,
     * attempts to make appropriate replacements for macros inside rules
     *
     * @return array Collection of rules for current model
     */
    public function getValidationRules();
}
