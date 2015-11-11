<?php
/**
 * Ebizmarts_Autoresponder Magento JS component
 *
 * @category    Ebizmarts
 * @package     Ebizmarts_Autoresponder
 * @author      Ebizmarts Team <info@ebizmarts.com>
 * @copyright   Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


namespace Ebizmarts\AutoResponder\Model;

class Config
{
    const ACTIVE            = 'autoresponder/general/active';
    const GENERAL_SENDER    = 'autoresponder/general/identity';

    const COUPON_AUTOMATIC  = 2;
    const COUPON_MANUAL     = 1;
    const COUPON_GENERAL    = 2;
    const COUPON_PER_ORDER  = 1;
    const TYPE_EACH         = 1;
    const TYPE_ONCE         = 2;
    const TYPE_SPECIFIC     = 3;


    const NEWORDER_ACTIVE           = 'autoresponder/neworder/active';
    const NEWORDER_DAYS             = 'autoresponder/neworder/days';
    const NEWORDER_CUSTOMER_GROUPS  = 'autoresponder/neworder/customer';
    const NEWORDER_TRIGGER          = 'autoresponder/neworder/trigger';
    const NEWORDER_ORDER_STATUS     = 'autoresponder/neworder/order_status';
    const NEWORDER_TEMPLATE         = 'autoresponder/neworder/template';
    const NEWORDER_MANDRILL_TAG     = 'autoresponder/neworder/mandrill_tag';
    const NEWORDER_SUBJECT          = 'autoresponder/neworder/subject';
    const NEWORDER_CRON_TIME        = 'autoresponder/neworder/cron_time';

    const BIRTHDAY_ACTIVE           = 'autoresponder/birthday/active';
    const BIRTHDAY_DAYS             = 'autoresponder/birthday/days';
    const BIRTHDAY_CUSTOMER_GROUPS  = 'autoresponder/birthday/customer';
    const BIRTHDAY_TEMPLATE         = 'autoresponder/birthday/template';
    const BIRTHDAY_SUBJECT          = 'autoresponder/birthday/subject';
    const BIRTHDAY_MANDRILL_TAG     = 'autoresponder/birthday/mandrill_tag';
    const BIRTHDAY_COUPON           = 'autoresponder/birthday/coupon';
    const BIRTHDAY_CUSTOMER_COUPON  = 'autoresponder/birthday/customer_coupon';
    const BIRTHDAY_AUTOMATIC        = 'autoresponder/birthday/automatic';
    const BIRTHDAY_COUPON_CODE      = 'autoresponder/birthday/coupon_code';
    const BIRTHDAY_EXPIRE           = 'autoresponder/birthday/expire';
    const BIRTHDAY_LENGTH           = 'autoresponder/birthday/length';
    const BIRTHDAY_DISCOUNT_TYPE    = 'autoresponder/birthday/discounttype';
    const BIRTHDAY_DISCOUNT         = 'autoresponder/birthday/discount';
    const BIRTHDAY_COUPON_LABEL     = 'autoresponder/birthday/couponlabel';
    const BIRTHDAY_CRON_TIME        = 'autoresponder/birthday/cron_time';

}