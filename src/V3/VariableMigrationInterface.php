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

use Horde_Variables;

/**
 * Migration interface for Variable methods that existed in lib/ (Horde_Form_Variable).
 *
 * These methods provide backward compatibility with the lib/ implementation.
 * Code migrating from lib/ to V3 can rely on these methods having similar
 * signatures and behavior.
 *
 * @category Horde
 * @package  Form
 */
interface VariableMigrationInterface
{
    /**
     * Assign this variable to a form.
     *
     * @param \Horde\Form\Form $form  The form instance
     */
    public function setFormOb($form);

    /**
     * Sets a default value for this variable.
     *
     * @param mixed $value  A variable value
     */
    public function setDefault($value);

    /**
     * Returns this variable's default value.
     *
     * @return mixed  This variable's default value
     */
    public function getDefault();

    /**
     * Assigns an action to this variable.
     *
     * @param Action $action  An Action instance
     */
    public function setAction($action);

    /**
     * Returns whether this variable has an attached action.
     *
     * @return bool  True if this variable has an attached action
     */
    public function hasAction();

    /**
     * Makes this a hidden variable.
     */
    public function hide();

    /**
     * Returns whether this is a hidden variable.
     *
     * @return bool  True if this a hidden variable
     */
    public function isHidden();

    /**
     * Disables this variable.
     */
    public function disable();

    /**
     * Returns whether this variable is disabled.
     *
     * @return bool  True if this variable is disabled
     */
    public function isDisabled();

    /**
     * Return the short description of this variable.
     *
     * @return string  A short description
     */
    public function getHumanName();

    /**
     * Returns the internally used variable name.
     *
     * @return string  This variable's internal name
     */
    public function getVarName();

    /**
     * Returns the name of this variable's type.
     *
     * @return string  This variable's type name
     */
    public function getTypeName(): string;

    /**
     * Returns whether this variable has a long description.
     *
     * @return bool  True if this variable has a long description
     */
    public function hasDescription(): bool;

    /**
     * Returns this variable's long description.
     *
     * @return string  This variable's long description
     */
    public function getDescription();

    /**
     * Returns whether this is an array variable.
     *
     * @return bool  True if this an array variable
     */
    public function isArrayVal();

    /**
     * Returns whether this variable is to upload a file.
     *
     * @return bool  True if variable is to upload a file
     */
    public function isUpload();

    /**
     * Assigns a help text to this variable.
     *
     * @param string $help  The variable help text
     */
    public function setHelp($help);

    /**
     * Returns whether this variable has some help text assigned.
     *
     * @return bool  True if this variable has a help text
     */
    public function hasHelp();

    /**
     * Returns the help text of this variable.
     *
     * @return string  This variable's help text
     */
    public function getHelp();

    /**
     * Sets a variable option.
     *
     * @param string $option  The option name
     * @param mixed $val      The option's value
     */
    public function setOption($option, $val);

    /**
     * Returns a variable option's value.
     *
     * @param string $option  The option name
     *
     * @return mixed  The option's value
     */
    public function getOption($option);

    /**
     * Processes the submitted value of this variable.
     *
     * @param Horde_Variables $vars  The submitted form variables
     * @param mixed ...$args         Additional arguments (deprecated)
     *
     * @return mixed  Processed value of the variable
     */
    public function getInfo($vars, ...$args);

    /**
     * Returns whether this variable has been changed.
     *
     * @param Horde_Variables $vars  The submitted form variables
     *
     * @return ?bool  Null if trackchange not set, boolean otherwise
     */
    public function wasChanged($vars);

    /**
     * Validates this variable.
     *
     * @param Horde_Variables $vars  The submitted form variables
     * @param mixed ...$args         Additional arguments (deprecated)
     *
     * @return bool  True if the variable validated
     */
    public function validate($vars, ...$args);

    /**
     * Returns the submitted or default value of this variable.
     *
     * @param Horde_Variables $vars  The submitted form variables
     * @param int|null $index        Array element index if array variable
     *
     * @return mixed  The variable or element value
     */
    public function getValue($vars, $index = null);

    /**
     * Returns the possible values of this variable.
     *
     * @param mixed ...$params  Type-specific parameters
     *
     * @return array|null  The possible values or null
     */
    public function getValues(...$params);

    /**
     * Hook called on form submission.
     *
     * @param Horde_Variables $vars  Submitted form variables
     */
    public function onSubmit($vars);
}
