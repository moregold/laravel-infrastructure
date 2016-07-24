<?php namespace Moregold\Infrastructure\Validators;

use Moregold\Infrastructure\Enum;

class Keys extends Enum
{
    public static $htmlName = '_validator-key';

    public static function isValidValidationKey($key = null)
    {
        $class = new \ReflectionClass(get_class());
        $staticProperties = $class->getStaticProperties();
        return array_search($key, $staticProperties) !== false;
    }

    public static $authLogin                   = 'auth.login';
    public static $authPasswordRemind          = 'auth.password.remind';
    public static $authPasswordReset           = 'auth.password.reset';

    public static $userContactForm             = 'user.contact';
    public static $userEmailAddress            = 'user.email';
    public static $userSignup                  = 'user.signup';

    public static $billingAddCreditCard        = 'billing.add.credit-card';

    public static $sellShippingLabel           = 'sell.shipping-label';

    public static $bookSearchBasic             = 'book.search.basic';

    public static $bookUpdateAuthor            = 'book.update.author';
    public static $bookUpdateTitle             = 'book.update.title';
    public static $bookUpdateEdition           = 'book.update.edition';
    public static $bookUpdateDescriptions      = 'book.update.descriptions';
}
