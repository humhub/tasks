<?php


namespace humhub\modules\tasks\comp;

use humhub\libs\DbDateValidator;

/**
 * Patches https://github.com/humhub/humhub-modules-meetings/issues/6
 * The date validation failed since the date format in swedish is in DB date format alread (but not DateTimeFormat).
 *
 * This will be fixed in HumHub v1.4
 *
 * Class DbDateValidatorPatched
 * @package humhub\modules\meeting\comp
 */
class DbDateValidatorPatched extends DbDateValidator
{
    protected function isInDbFormat($value)
    {
        return preg_match(self::REGEX_DBFORMAT_DATETIME, $value);
    }
}