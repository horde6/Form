<?php
namespace Horde\Form\V3;
use Horde_Form_Translation;

class InvalidType extends BaseType
{
    /**
     * Initialize an Invalid Message form type
     *
     * function init($message)
     */
    public function init(...$params)
    {
        $this->message = $params[0] ?? '';
    }

    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        return false;
    }

}
