<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class DateType extends BaseType
{
    public $_format;

    /**
     * Initialize a Set form type
     *
     * function init($format = '%a %d %B')
     */
    public function init(...$params)
    {
        $this->_format = $params[0] ?? '%a %d %B';
    }

    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        if ($var->isRequired() && strlen(trim($value)) == 0) {
            $message = sprintf(Horde_Form_Translation::t("%s is required"), $var->getHumanName());
            $this->message = $message;
            return false;
        }

        return true;
    }

    /**
     * @static
     *
     * @param mixed $date  The date to calculate the difference from. Can be
     *                     either a timestamp integer value, or an array
     *                     with date parts: 'day', 'month', 'year'.
     *
     * @return string
     */
    public function getAgo($date)
    {
        if ($date === null) {
            return '';
        }

        try {
            $today = new Horde_Date(time());
            $date = new Horde_Date($date);
            $ago = $date->toDays() - $today->toDays();
        } catch (Horde_Date_Exception $e) {
            return '';
        }

        if ($ago < -1) {
            return sprintf(Horde_Form_Translation::t(" (%s days ago)"), abs($ago));
        } elseif ($ago == -1) {
            return Horde_Form_Translation::t(" (yesterday)");
        } elseif ($ago == 0) {
            return Horde_Form_Translation::t(" (today)");
        } elseif ($ago == 1) {
            return Horde_Form_Translation::t(" (tomorrow)");
        } else {
            return sprintf(Horde_Form_Translation::t(" (in %s days)"), $ago);
        }
    }

    public function getFormattedTime($timestamp, $format = null, $showago = true)
    {
        if (empty($format)) {
            $format = $this->_format;
        }
        if (!empty($timestamp)) {
            return strftime($format, $timestamp) . ($showago ? Horde_Form_Type_date::getAgo($timestamp) : '');
        } else {
            return '';
        }
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [ 'name' => Horde_Form_Translation::t("Date") ];
    }

}
