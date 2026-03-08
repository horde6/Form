<?php
declare(strict_types=1);

/**
 * Copyright 2002-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Chuck Hagenbuch <chuck@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Form
 */

namespace Horde\Form\V3;

/**
 * V3-native interface for Action methods introduced in the V3 implementation.
 *
 * These methods are new to V3 and provide modernized functionality
 * not present in lib/ (Horde_Form_Action).
 *
 * @category Horde
 * @package  Form
 */
interface ActionV3Interface
{
    /**
     * Get action ID.
     *
     * Returns a unique identifier for this action instance. This is new
     * in V3 for better action tracking and debugging.
     *
     * @return string  Unique action identifier
     */
    public function id(): string;
}
