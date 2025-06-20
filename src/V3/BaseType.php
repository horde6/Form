<?php
/**
 * Copyright 2001-2025 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Robert E. Coyle <robertecoyle@hotmail.com>
 */

namespace Horde\Form\V3;
use Horde_Variables;
class BaseType implements Type
{
    /**
     * Messages from isValid() method.
     */
    protected string $message = '';

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getProperty($property)
    {
        $prop = '_' . $property;
        return $this->$prop ?? null;
    }

    /**
     * Not part of the interface, implementation detail
     */
    public function __get($property)
    {
        return $this->getProperty($property);
    }

    public function setProperty($property, $value)
    {
        $prop = '_' . $property;
        $this->$prop = $value;
    }

    /**
     * Not part of the interface, implementation detail
     */
    public function __set($property, $value)
    {
        return $this->setProperty($property, $value);
    }

    /**
     * Initialize (kind of constructor) - Parameter list may vary on overloading
     */
    public function init(...$params) {}

    public function onSubmit(...$params) {}

    /**
     * Use $this->getMessage() to retrieve error messages.
     */
    public function isValid($var, Horde_Variables|array $vars, $value): bool
    {
        $this->message = '<strong>Error:</strong> Horde_Form_Type::isValid() called - should be overridden<br />';
        return false;
    }

    /**
     * Override with a simple return 'literal' string in your own types.
     */
    public function getTypeName(): string
    {
        return mb_strtolower(str_replace('Horde\Form\V3\\', '', substr($this::class, 0, -4)));
    }

    public function getValues(...$params)
    {
        return null;
    }

    public function getInfo($vars, $var): array|object
    {
        return $var->getValue($vars);
    }

}