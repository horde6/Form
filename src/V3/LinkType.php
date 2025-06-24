<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class LinkType extends BaseType
{
   /**
     * List of hashes containing link parameters. Possible keys: 'url', 'text',
     * 'target', 'onclick', 'title', 'accesskey', 'class'.
     *
     * @var array
     */
    public $values;

    /**
     * Init a Link field
     *
     * function init($values)
     */
    public function init(...$params)
    {
        $this->values = $params[0] ?? null;
    }

    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        return true;
    }

    /**
     * Return info about field type.
     */
    public function about()
    {
        return [
            'name' => Horde_Form_Translation::t("Link"),
            'params' => [
                'url' => [
                    'label' => Horde_Form_Translation::t("Link URL"),
                    'type' => 'text'
                ],
                'text' => [
                    'label' => Horde_Form_Translation::t("Link text"),
                    'type' => 'text'
                ],
                'target' => [
                    'label' => Horde_Form_Translation::t("Link target"),
                    'type' => 'text'
                ],
                'onclick' => [
                    'label' => Horde_Form_Translation::t("Onclick event"),
                    'type' => 'text'
                ],
                'title' => [
                    'label' => Horde_Form_Translation::t("Link title attribute"),
                    'type' => 'text'
                ],
                'accesskey' => [
                    'label' => Horde_Form_Translation::t("Link access key"),
                    'type' => 'text'
                ],
                'class' => [
                    'label' => Horde_Form_Translation::t("Link CSS class"),
                    'type' => 'text'
                ],
            ],
        ];
    }

}
