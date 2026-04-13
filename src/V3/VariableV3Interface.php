<?php

/**
 * Copyright 2001-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Robert E. Coyle <robertecoyle@hotmail.com>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL
 * @package  Form
 */

namespace Horde\Form\V3;

/**
 * V3-native interface for Variable methods introduced in the V3 implementation.
 *
 * These methods are new to V3 and don't exist in lib/ (Horde_Form_Variable).
 * They support the modernized PHP 8+ architecture and provide new functionality.
 *
 * @category Horde
 * @package  Form
 */
interface VariableV3Interface
{
    /**
     * Returns the validation error message.
     *
     * @return string  The validation error message
     */
    public function getMessage();

    /**
     * Gets internal property value by name.
     *
     * @param string $property  Property name (without underscore prefix)
     *
     * @return mixed  Property value, or null if not set
     */
    public function getProperty($property);

    /**
     * Sets internal property value by name.
     *
     * @param string $property  Property name (without underscore prefix)
     * @param mixed $value      Value to set
     */
    public function setProperty($property, $value);

    /**
     * Initialize variable with type-specific parameters.
     *
     * @param mixed ...$params  Variable arguments specific to variable type
     */
    public function init(...$params);

    /**
     * Mark variable as invalid with error message.
     *
     * @param string $message  The error message
     *
     * @return bool  Always returns false
     */
    public function invalid(string $message): bool;

    /**
     * Return info about field type (metadata).
     *
     * @return array  Type metadata including name and parameters
     */
    public function about(): array;
}
