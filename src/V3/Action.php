<?php

declare(strict_types=1);

/**
 * Copyright 2002-2017 Horde LLC (http://www.horde.org/)
 * Copyright 2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Chuck Hagenbuch <chuck@horde.org>
 * @author   Ralf Lang <ralf.lang@ralf-lang.de>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Form
 */

namespace Horde\Form\V3;

/**
 * The Action interface provides an API for adding actions to Form variables.
 *
 * Actions are triggered by form events (e.g., field value changes) and can:
 * - Enable/disable other fields conditionally
 * - Update field values dynamically
 * - Perform calculations
 * - Trigger JavaScript behaviors
 *
 * Extends both ActionMigrationInterface (lib/ compatibility methods)
 * and ActionV3Interface (V3-native methods) to provide the complete
 * Action API.
 *
 * V3 improvements over lib/:
 * - Strict typing
 * - No singleton pattern (just use new)
 * - Named parameters
 * - Removed PHP 4 constructor
 * - Modern factory pattern
 * - Added id() method for action tracking
 *
 * @author    Chuck Hagenbuch <chuck@horde.org>
 * @author    Ralf Lang <ralf.lang@ralf-lang.de>
 * @category  Horde
 * @copyright 2002-2017 Horde LLC
 * @copyright 2026 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Form
 */
interface Action extends ActionMigrationInterface, ActionV3Interface {}
