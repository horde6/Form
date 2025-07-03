<?php
namespace Horde\Form\V3;
use Horde_Variables;
use Horde_Form_Translation;

class SmimeVariable extends LongtextVariable
{
    /**
     * A temporary directory.
     *
     * @var string
     */
    public $_temp;

    /**
     * Init a S/MIME field
     *
     * function init($temp_dir = null, $rows = null, $cols = null)
     */
    public function init(...$params)
    {
        $temp_dir = $params[0] ?? null;
        $rows = $params[1] ?? null;
        $cols = $params[2] ?? null;

        $this->_temp = $temp_dir;
        parent::init($rows, $cols);
    }

    /**
     * Returns a parameter hash for the Horde_Crypt_smime constructor.
     *
     * @return array  A parameter hash.
     */
    public function getSMIMEParams()
    {
        return [ 'temp' => $this->_temp ];
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [
            'name' => Horde_Form_Translation::t("S/MIME Key"),
            'params' => [
                'temp_dir' => [
                    'label' => Horde_Form_Translation::t("A temporary directory"),
                    'type'  => 'string'
                ],
                'rows'     => [
                    'label' => Horde_Form_Translation::t("Number of rows"),
                    'type'  => 'int'
                ],
                'cols'     => [
                    'label' => Horde_Form_Translation::t("Number of columns"),
                    'type'  => 'int'
                ]
            ]
        ];
    }

}
