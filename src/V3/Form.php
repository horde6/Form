<?php
/**
 * Copyright 2001-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL
 * @package  Form
 */

namespace Horde\Form\V3;

/**
 * Combined interface for forms.
 *
 * Extends both FormMigrationInterface (lib/ compatibility methods)
 * and FormV3Interface (V3-native methods) to provide the complete
 * Form API.
 *
 * Implementations of this interface support both migrated code from lib/
 * and new V3-specific functionality.
 *
 * @category Horde
 * @package  Form
 */
interface Form extends FormMigrationInterface, FormV3Interface
{
}
