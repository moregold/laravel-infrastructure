<?php namespace Moregold\Infrastructure\Validators;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;
use Symfony\Component\Translation\TranslatorInterface;
use Moregold\Domains\Users\Facades\TypesFacade as UserTypes,
    LucaDegasperi\OAuth2Server\Authorizer;

class CustomRules extends Validator
{
    public function __construct(
        TranslatorInterface $translator,
        $data,
        $rules,
        $messages = array(),
        Authorizer $auth
    )
    {
        parent::__construct($translator, $data, $rules, $messages);
        $this->auth = $auth;
    }

    /**
     * Laravel custom validator to check if rental exists for current user with a status
     * of added/ready/active
     *
     * @param $attribute
     * @param $value
     * @param null $parameters
     * @return bool
     */
    public function validateUniqueRental($attribute, $value, $parameters = null)
    {
        $auth_user_id = $this->auth->getResourceOwnerId();

        $rental_exists = [
            UserTypes::rentalStatusAdded(),
            UserTypes::rentalStatusReady(),
            UserTypes::rentalStatusActive()
        ];

        $user_rentals = DB::table('rentals')
            ->where('user_id', $auth_user_id)
            ->where('isbn13', $value)
            ->whereIn('status', $rental_exists)
            ->get();

        // This will be an array of ISBNs that are ready/active/added; empty array if nothing found
        if (!empty($user_rentals)) {
            return false;
        }

        return true;
    }
}
