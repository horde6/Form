<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class TablesetVariable extends BaseVariable
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

    public function isValid(Horde_Variables $vars, $value): bool
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

    public function getValues(...$params): ?array
    {
        return $this->_values;
    }

    /**
     * Return info about field type.
     */
    public function about(): array
    {
        return [
            'name' => Horde_Form_Translation::t("Table Set"),
            'params' => [
                'values' => [
                    'label' => Horde_Form_Translation::t("Values"),
                    'type'  => 'stringlist'
                ],
                'header' => [
                    'label' => Horde_Form_Translation::t("Headers"),
                    'type'  => 'stringlist'
                ]
            ],
        ];
    }
}
