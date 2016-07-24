<?php namespace Moregold\Infrastructure\Validators;

interface ValidatorInterface
{
    public function getRules();
    public function getInput();
}
