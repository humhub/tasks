<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\tasks;

use DateTime;

/**
 * Description of CalendarUtils
 *
 * @author luke
 */
class CalendarUtils
{

    const DATE_FORMAT = 'Y-m-d';

    /**
     *
     * @param DateTime $date1
     * @param DateTime $date2
     * @param type $endDateMomentAfter
     * @return boolean
     */
    public static function isFullDaySpan(DateTime $date1, DateTime $date2, $endDateMomentAfter = false)
    {
        $dateInterval = $date1->diff($date2, true);

        if ($endDateMomentAfter) {
            if ($dateInterval->days > 0 && $dateInterval->h == 0 && $dateInterval->i == 0 && $dateInterval->s == 0) {
                return true;
            }
        } else {
            if ($dateInterval->h == 23 && $dateInterval->i == 59) {
                return true;
            }
        }


        return false;
    }

    /**
     * Helper function to get the start_datetime query filter.
     * @param string $date
     * @param $field
     * @param string $eq
     * @return array
     */
    public static function getStartCriteria($date, $field, $eq = '>=')
    {
        return [$eq, $field, date(static::DATE_FORMAT, strtotime($date))];
    }

    /**
     * Helper function to get the end_datetime query filter.
     * @param string $date
     * @param $field
     * @param string $eq
     * @return array
     */
    public static function getEndCriteria($date, $field, $eq = '<=')
    {
        return [$eq, $field, date(static::DATE_FORMAT, strtotime($date))];
    }

}
