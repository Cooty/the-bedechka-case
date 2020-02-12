<?php

namespace App\Enum\Admin;

abstract class FlashTypes
{
    // need to correspond to Bootstrap CSS framework's alert-types
    // see.: https://getbootstrap.com/docs/4.0/components/alerts/
    const OK = 'success';
    const ERROR = 'danger';
    const INFO = 'primary';
}