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
 * Combined interface for form variables.
 *
 * Extends both VariableMigrationInterface (lib/ compatibility methods)
 * and VariableV3Interface (V3-native methods) to provide the complete
 * Variable API.
 *
 * Implementations of this interface support both migrated code from lib/
 * and new V3-specific functionality.
 *
 * @category Horde
 * @package  Form
 */
interface Variable extends VariableMigrationInterface, VariableV3Interface {}
