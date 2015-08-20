<?php
/**
 * Author: info@ebizmarts.com
 * Date: 8/20/15
 * Time: 1:35 PM
 * File: Config.php
 * Module: magento2-autoresponder
 */

namespace Ebizmarts\AutoResponder\Model;

class Config
{
    const ACTIVE            = 'autoresponder/general/active';
    const GENERAL_SENDER    = 'autoresponder/general/identity';


    const NEWORDER_ACTIVE           = 'autoresponder/neworder/active';
    const NEWORDER_DAYS             = 'autoresponder/neworder/days';
    const NEWORDER_CUSTOMER_GROUPS  = 'autoresponder/neworder/customer';
    const NEWORDER_TRIGGER          = 'autoresponder/neworder/trigger';
    const NEWORDER_ORDER_STATUS     = 'autoresponder/neworder/order_status';
    const NEWORDER_TEMPLATE         = 'autoresponder/neworder/template';
    const NEWORDER_MANDRILL_TAG     = 'autoresponder/neworder/mandrill_tag';
    const NEWORDER_SUBJECT          = 'autoresponder/neworder/subject';
    const NEWORDER_CRON_TIME        = 'autoresponder/neworder/cron_time';

}