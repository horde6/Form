<?php

/**
 * Copyright 2001-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL
 * @package  Form
 */

/**
 *
 *
 * @author
 * @category  Horde
 * @copyright 2001-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL
 * @package   Form
 */
class Horde_Form_Type_tableset extends Horde_Form_Type
{
    public $_values;
    public $_header;

    /**
     *     function init($values, $header)
     */
    public function init(...$params)
    {
        $this->_values = $params[0];
        $this->_header = $params[1];
    }

    public function isValid($var, $vars, $value, $message)
    {
        if (count($this->_values) == 0 || count($value) == 0) {
            return true;
        }
        foreach ($value as $item) {
            if (!isset($this->_values[$item])) {
                $error = true;
                break;
            }
        }
        if (!isset($error)) {
            return true;
        }

        return $this->invalid('Invalid data submitted.');
    }

    public function getHeader()
    {
        return $this->_header;
    }

    public function getValues()
    {
        return $this->_values;
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [
            'name' => Horde_Form_Translation::t("Table Set"),
            'params' => [
                'values' => ['label' => Horde_Form_Translation::t("Values"),
                    'type'  => 'stringlist'],
                'header' => ['label' => Horde_Form_Translation::t("Headers"),
                    'type'  => 'stringlist']],
        ];
    }

}
