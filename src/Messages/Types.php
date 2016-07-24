<?php namespace Moregold\Infrastructure\Messages;

use Moregold\Infrastructure\Enum;

class Types extends Enum
{
    public static $success                  = 'success';
    public static $failure                  = 'failure';

    public static $defaultError             = 'default_error';
    public static $defaultSuccess           = 'default_success';

    public static $modelAttachment          = 'model_attachment';

    public static $billingInfoUpdate        = 'billing_info_update';
    public static $billingClientMessage     = 'billing_client_message';

    public static $bookLegacyUpdate         = 'book_legacy_update';
    public static $bookNotFound             = 'book_not_found';
    public static $bookUpdateSucceeded      = 'book_update_succeeded';
    public static $bookUpdateFailed         = 'book_update_failed';

    public static $cartAddItemFailed        = 'cart_add_item_failed';
    public static $cartAddItemSucceeded     = 'cart_add_item_succeeded';
    public static $cartRemoveItemFailed     = 'cart_remove_item_failed';
    public static $cartSaveFailed           = 'cart_save_failed';
    public static $cartCheckout             = 'cart_checkout';

    public static $eventLogging             = 'event_logging';

    public static $rentalNotFound           = 'rental_not_found';
    public static $rentalPayment            = 'rental_payment';
    public static $rentalStatus             = 'rental_status';

    public static $userActivityProfile      = 'user_activity_profile';
    public static $userRental               = 'user_rental';
    public static $userSale                 = 'user_sale';
    public static $userPurchase             = 'user_purchase';
    public static $userJoin                 = 'user_join';
    public static $userLegacyUpdate         = 'user_legacy_update';
    public static $userRoleUpdate           = 'user_role_update';
}
