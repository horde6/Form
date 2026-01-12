<?php
namespace Horde\Form\V3;

use Horde_Variables;
use Horde_Form_Translation;

/**
 * InvalidVariable type for fields that should always be invalid with a custom message.
 *
 * @property string $message The error message to display
 */
class InvalidVariable extends BaseVariable
{
    /**
     * Initialize an invalid message field.
     *
     * @param array $params Variable arguments:
     *                      - $params[0]: string $message - The error message to display (default: '')
     */
    public function init(...$params)
    {
        $this->message = $params[0] ?? '';
    }

    public function isValid(Horde_Variables $vars, $value): bool
    {
        return false;
    }

    /**
     * Return info about field type.
     */
    public function about(): array
    {
        return [
            'name' => Horde_Form_Translation::t("Invalid"),
            'params' => [
                'message' => [
                    'label' => Horde_Form_Translation::t("Text"),
                    'type'  => 'text'
                ]
            ]
        ];
    }
}
