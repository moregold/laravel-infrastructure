<?php namespace Moregold\Infrastructure\Validators;

use Moregold\Infrastructure\Validators\KeysFacade as ValidationKeysFacade;

class ViewValidator implements ValidatorInterface
{
    protected $input = [];

    protected $rules = [];

    public function __construct($type, $input)
    {
        $this->input = $input;
        $this->rules = $this->rules($type);
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function getInput()
    {
        return $this->input;
    }

    private function rules($type = null)
    {
        switch ($type) {
            case ValidationKeysFacade::authLogin():
                return [
                    'email' => 'required|email',
                    'password' => 'required'
                ];
            case ValidationKeysFacade::authPasswordRemind():
                return [
                    'email' => 'required|email'
                ];
            case ValidationKeysFacade::authPasswordReset():
                return [
                    'password' => 'required|confirmed',
                    'password_confirmation' => 'required'
                ];
            case ValidationKeysFacade::userEmailAddress():
                return [
                    'email' => 'required|email'
                ];
            case ValidationKeysFacade::userSignup():
                return [
                    'email' => 'required',
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'year_in_school' => 'required',
                    'organization' => 'required',
                    'password' => 'required|confirmed',
                    'password_confirmation' => 'required'
                ];
            case ValidationKeysFacade::billingAddCreditCard():
                return [
                    'name' => 'required',
                    'address1' => 'required',
                    'locality' => 'required',
                    'region' => 'required',
                    'postal_code' => 'required',
                    'card_number' => 'required',
                    'expiration_month' => 'required',
                    'expiration_year' => 'required',
                    'cvv' => 'required'
                ];
            case ValidationKeysFacade::sellShippingLabel():
                return [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'address1' => 'required',
                    'locality' => 'required',
                    'region' => 'required',
                    'postal_code' => 'required',
                    'email' => 'required',
                    'mobile' => 'required'
                ];
            case ValidationKeysFacade::bookSearchBasic():
                return [
                    'keyword' => 'required'
                ];
            case ValidationKeysFacade::bookUpdateAuthor():
                return [
                    'author' => 'required'
                ];
            case ValidationKeysFacade::bookUpdateTitle():
                return [
                    'title' => 'required'
                ];
            case ValidationKeysFacade::bookUpdateEdition():
                return [
                    'edition' => 'required'
                ];
            case ValidationKeysFacade::bookUpdateDescriptions():
                return [
                    'short_description' => 'required|max:200',
                    'long_description' => 'required'
                ];
            case ValidationKeysFacade::userContactForm():
                return [
                    'email' => 'required|email',
                    'subject' => 'required',
                    'message' => 'required'
                ];
            default:
                return [];
        }
    }
}
