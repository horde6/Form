<?php

declare(strict_types=1);

/**
 * Tests for V3 Action classes: getHelperScript(), getActionScript(), and
 * ActionV3Interface compliance.
 *
 * Copyright 2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL-2.1).
 *
 * @category   Horde
 * @package    Form
 * @subpackage UnitTests
 * @author     Ralf Lang <ralf.lang@ralf-lang.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL-2.1
 */

namespace Horde\Form\Test\V3;

use Horde\Form\V3\ActionV3Interface;
use Horde\Form\V3\BaseAction;
use Horde\Form\V3\BaseForm;
use Horde\Form\V3\ConditionalsetvalueAction;
use Horde\Form\V3\SetcursorposAction;
use Horde\Form\V3\SubmitAction;
use Horde\Form\V3\UpdatefieldAction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for BaseAction defaults and ActionV3Interface compliance.
 */
#[CoversClass(BaseAction::class)]
class V3ActionTest extends TestCase
{
    public function testBaseActionGetHelperScriptReturnsEmptyString(): void
    {
        $action = new SubmitAction();

        // SubmitAction inherits BaseAction default — no helper JS needed
        $this->assertSame('', $action->getHelperScript());
    }

    public function testBaseActionImplementsActionV3Interface(): void
    {
        $action = new SubmitAction();

        $this->assertInstanceOf(ActionV3Interface::class, $action);
    }

    public function testBaseActionIdReturnsNonEmptyString(): void
    {
        $action = new SubmitAction();

        $this->assertNotEmpty($action->id());
        $this->assertEquals(32, strlen($action->id()));
    }

    public function testTwoActionsHaveDifferentIds(): void
    {
        $a = new SubmitAction();
        $b = new SubmitAction();

        $this->assertNotEquals($a->id(), $b->id());
    }

    // UpdatefieldAction

    public function testUpdatefieldGetHelperScriptReturnsFunction(): void
    {
        $action = new UpdatefieldAction([
            'target' => 'fullname',
            'format' => '%s %s',
            'fields' => ['firstname', 'lastname'],
        ]);

        $js = $action->getHelperScript();

        $this->assertNotEmpty($js);
        $this->assertStringContainsString('function updateField_' . $action->id(), $js);
    }

    public function testUpdatefieldGetHelperScriptReferencesFields(): void
    {
        $action = new UpdatefieldAction([
            'target' => 'fullname',
            'format' => '%s %s',
            'fields' => ['firstname', 'lastname'],
        ]);

        $js = $action->getHelperScript();

        $this->assertStringContainsString('fullname', $js);
        $this->assertStringContainsString('firstname', $js);
        $this->assertStringContainsString('lastname', $js);
    }

    public function testUpdatefieldGetHelperScriptEmptyForMissingParams(): void
    {
        $action = new UpdatefieldAction([]);

        $this->assertSame('', $action->getHelperScript());
    }

    public function testUpdatefieldGetActionScriptCallsFunction(): void
    {
        $action = new UpdatefieldAction([
            'target' => 'fullname',
            'format' => '%s %s',
            'fields' => ['firstname', 'lastname'],
        ]);

        $form = new BaseForm([], 'Test', 'testform');
        $script = $action->getActionScript($form, null, 'firstname');

        $this->assertStringContainsString('updateField_' . $action->id() . '()', $script);
    }

    public function testUpdatefieldTriggers(): void
    {
        $action = new UpdatefieldAction();

        $this->assertEquals(['onchange', 'onload', 'onkeyup'], $action->getTrigger());
    }

    // ConditionalsetvalueAction

    public function testConditionalsetvalueGetHelperScriptReturnsFunction(): void
    {
        $action = new ConditionalsetvalueAction([
            'target' => 'state_code',
            'map' => ['California' => 'CA', 'New York' => 'NY'],
        ]);

        $js = $action->getHelperScript();

        $this->assertNotEmpty($js);
        $this->assertStringContainsString('function mapValue_' . $action->id(), $js);
    }

    public function testConditionalsetvalueGetHelperScriptContainsMap(): void
    {
        $action = new ConditionalsetvalueAction([
            'target' => 'state_code',
            'map' => ['California' => 'CA', 'New York' => 'NY'],
        ]);

        $js = $action->getHelperScript();

        $this->assertStringContainsString('_map_' . $action->id(), $js);
    }

    public function testConditionalsetvalueGetHelperScriptContainsMappedValues(): void
    {
        $action = new ConditionalsetvalueAction([
            'target' => 'state_code',
            'map' => ['California' => 'CA', 'Texas' => 'TX'],
        ]);

        $js = $action->getHelperScript();

        $this->assertStringContainsString('"California"', $js);
        $this->assertStringContainsString('"CA"', $js);
        $this->assertStringContainsString('"Texas"', $js);
        $this->assertStringContainsString('"TX"', $js);
    }

    public function testConditionalsetvalueGetActionScriptCallsFunction(): void
    {
        $action = new ConditionalsetvalueAction([
            'target' => 'state_code',
            'map' => ['California' => 'CA'],
        ]);

        $form = new BaseForm([], 'Test', 'testform');
        $script = $action->getActionScript($form, null, 'state_name');

        $this->assertStringContainsString('mapValue_' . $action->id(), $script);
    }

    public function testConditionalsetvalueTriggers(): void
    {
        $action = new ConditionalsetvalueAction();

        $this->assertEquals(['onchange', 'onload'], $action->getTrigger());
    }

    // SetcursorposAction

    public function testSetcursorposGetHelperScriptReturnsFunction(): void
    {
        $action = new SetcursorposAction([10, 15]);

        $js = $action->getHelperScript();

        $this->assertNotEmpty($js);
        $this->assertStringContainsString('function setCursorPosition_' . $action->id(), $js);
    }

    public function testSetcursorposGetHelperScriptContainsSelectionRange(): void
    {
        $action = new SetcursorposAction([0, 5]);

        $js = $action->getHelperScript();

        $this->assertStringContainsString('setSelectionRange', $js);
    }

    public function testSetcursorposGetActionScriptCallsFunction(): void
    {
        $action = new SetcursorposAction([10, 15]);

        $form = new BaseForm([], 'Test', 'testform');
        $script = $action->getActionScript($form, null, 'myfield');

        $this->assertStringContainsString('setCursorPosition_' . $action->id(), $script);
        $this->assertStringContainsString('10,15', $script);
    }

    public function testSetcursorposGetActionScriptSinglePosition(): void
    {
        $action = new SetcursorposAction([5]);

        $form = new BaseForm([], 'Test', 'testform');
        $script = $action->getActionScript($form, null, 'myfield');

        $this->assertStringContainsString('5', $script);
    }

    public function testSetcursorposTrigger(): void
    {
        $action = new SetcursorposAction();

        $this->assertEquals(['onload'], $action->getTrigger());
    }
}
