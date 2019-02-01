<?php

namespace Planck\Helper;

class Date
{

    public static function formatDatetime($datetime, $format = 'd/m/Y H:i')
    {
        return date(
            $format,
            strtotime($datetime)
        );
    }


}