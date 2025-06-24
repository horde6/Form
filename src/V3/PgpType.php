<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class PgpType extends LongtextType
{
    /**
     * Path to the GnuPG binary.
     *
     * @var string
     */
    public $_gpg;

    /**
     * A temporary directory.
     *
     * @var string
     */
    public $_temp;

    /**
     * Init a PGP field
     *
     * function init($gpg, $temp_dir = null, $rows = null, $cols = null)
     */
    public function init(...$params)
    {
        $gpg = $params[0] ?? null;
        $temp_dir = $params[1] ?? null;
        $rows = $params[2] ?? null;
        $cols = $params[3] ?? null;

        $this->_gpg = $gpg;
        $this->_temp = $temp_dir;
        parent::init($rows, $cols);
    }

    /**
     * Returns a parameter hash for the Horde_Crypt_pgp constructor.
     *
     * @return array  A parameter hash.
     */
    public function getPGPParams()
    {
        return [ 'program' => $this->_gpg, 'temp' => $this->_temp ];
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [
            'name' => Horde_Form_Translation::t("PGP Key"),
            'params' => [
                'gpg'      => [
                    'label' => Horde_Form_Translation::t("Path to the GnuPG binary"),
                    'type'  => 'string'
                ],
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
