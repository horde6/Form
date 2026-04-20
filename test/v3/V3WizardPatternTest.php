<?php

declare(strict_types=1);

namespace Horde\Form\Test\V3;

use Horde\Form\V3\BaseForm;
use Horde\Form\V3\BaseRenderer;
use Horde\Form\V3\FieldGroup;
use Horde\Form\V3\HtmlRenderer;
use Horde\Form\V3\Section;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the single-form wizard pattern:
 * multiple groups with enabled/disabled state, selective validation,
 * getInfo across all groups, and renderMixed() output.
 */
#[CoversClass(BaseForm::class)]
#[CoversClass(FieldGroup::class)]
#[CoversClass(BaseRenderer::class)]
class V3WizardPatternTest extends TestCase
{
    // ========================================================================
    // setActiveGroup / setGroupsEnabledUpTo
    // ========================================================================

    public function testSetActiveGroupEnablesOneDisablesOthers(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true);

        $form->setSection('step2', 'Type');
        $form->addVariable('Type', 'type', 'text', true);

        $form->setSection('step3', 'State');
        $form->addVariable('State', 'state', 'text', true);

        $form->setActiveGroup('step2');

        $this->assertFalse($form->getGroup('step1')->isEnabled());
        $this->assertTrue($form->getGroup('step2')->isEnabled());
        $this->assertFalse($form->getGroup('step3')->isEnabled());
    }

    public function testSetGroupsEnabledUpToDisablesPriorGroups(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true);

        $form->setSection('step2', 'Type');
        $form->addVariable('Type', 'type', 'text', true);

        $form->setSection('step3', 'State');
        $form->addVariable('State', 'state', 'text', true);

        $form->setGroupsEnabledUpTo('step3');

        $this->assertFalse($form->getGroup('step1')->isEnabled());
        $this->assertFalse($form->getGroup('step2')->isEnabled());
        $this->assertTrue($form->getGroup('step3')->isEnabled());
    }

    public function testSetGroupsEnabledUpToLeavesLaterGroupsUnchanged(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true);

        $form->setSection('step2', 'Type');
        $form->addVariable('Type', 'type', 'text', true);

        // step2 starts enabled (default)
        $form->setGroupsEnabledUpTo('step2');

        $this->assertFalse($form->getGroup('step1')->isEnabled());
        $this->assertTrue($form->getGroup('step2')->isEnabled());
    }

    // ========================================================================
    // getVariables with enabledOnly filter
    // ========================================================================

    public function testGetVariablesEnabledOnlyTrue(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true);

        $form->setSection('step2', 'Type');
        $form->addVariable('Type', 'type', 'text', true);

        $form->setActiveGroup('step2');

        $vars = $form->getVariables(flat: true, enabledOnly: true);

        $this->assertCount(1, $vars);
        $this->assertSame('type', $vars[0]->getVarName());
    }

    public function testGetVariablesEnabledOnlyFalse(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true);

        $form->setSection('step2', 'Type');
        $form->addVariable('Type', 'type', 'text', true);

        $form->setActiveGroup('step2');

        $vars = $form->getVariables(flat: true, enabledOnly: false);

        $this->assertCount(1, $vars);
        $this->assertSame('queue', $vars[0]->getVarName());
    }

    public function testGetVariablesEnabledOnlyNull(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true);

        $form->setSection('step2', 'Type');
        $form->addVariable('Type', 'type', 'text', true);

        $form->setActiveGroup('step2');

        $vars = $form->getVariables(flat: true, enabledOnly: null);

        $this->assertCount(2, $vars);
    }

    public function testGetVariablesStructuredEnabledOnly(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true);

        $form->setSection('step2', 'Type');
        $form->addVariable('Type', 'type', 'text', true);

        $form->setActiveGroup('step2');

        $grouped = $form->getVariables(flat: false, enabledOnly: true);

        $this->assertArrayNotHasKey('step1', $grouped);
        $this->assertArrayHasKey('step2', $grouped);
        $this->assertCount(1, $grouped['step2']);
    }

    // ========================================================================
    // Validation: only enabled groups are validated
    // ========================================================================

    public function testValidateSkipsDisabledGroups(): void
    {
        $form = new BaseForm(
            ['formname' => 'test', 'type' => 'Bug'],
            'Test',
            'test',
        );
        $form->useToken(false);

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true); // required, but step1 disabled

        $form->setSection('step2', 'Type');
        $form->addVariable('Type', 'type', 'text', true); // required, step2 enabled

        $form->setActiveGroup('step2');

        // 'queue' is missing from formVars — normally would fail validation.
        // Since step1 is disabled, it should be skipped.
        $this->assertTrue($form->validate());
    }

    public function testValidateChecksEnabledGroups(): void
    {
        $form = new BaseForm(
            ['formname' => 'test'],
            'Test',
            'test',
        );
        $form->useToken(false);

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true);

        $form->setSection('step2', 'Type');
        $form->addVariable('Type', 'type', 'text', true); // required, missing

        $form->setActiveGroup('step2');

        // 'type' is in the enabled group but missing → should fail
        $this->assertFalse($form->validate());
        $this->assertNotNull($form->getError('type'));
    }

    public function testValidateSkipsGroupValidatorForDisabledGroups(): void
    {
        $group = new class ('step1') extends FieldGroup {
            public function __construct(string $name)
            {
                parent::__construct($name);
            }
            protected function validateGroup(array $vars, array &$errors): void
            {
                $errors['step1_error'] = 'This should not fire.';
            }
        };

        $form = new BaseForm(
            ['formname' => 'test', 'type' => 'Bug'],
            'Test',
            'test',
        );
        $form->useToken(false);
        $form->addGroup($group);
        $form->addVariable('Queue', 'queue', 'text', false);

        $form->setSection('step2', 'Type');
        $form->addVariable('Type', 'type', 'text', true);

        $form->setActiveGroup('step2');

        $this->assertTrue($form->validate());
        $this->assertNull($form->getError('step1_error'));
    }

    // ========================================================================
    // getInfo: returns values from ALL groups (enabled and disabled)
    // ========================================================================

    public function testGetInfoIncludesAllGroups(): void
    {
        $form = new BaseForm(
            ['queue' => 'Support', 'type' => 'Bug', 'state' => 'New'],
            'Test',
            'test',
        );

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true);

        $form->setSection('step2', 'Type');
        $form->addVariable('Type', 'type', 'text', true);

        $form->setSection('step3', 'State');
        $form->addVariable('State', 'state', 'text', true);

        $form->setActiveGroup('step3');

        $info = $form->getInfo();

        $this->assertSame('Support', $info['queue']);
        $this->assertSame('Bug', $info['type']);
        $this->assertSame('New', $info['state']);
    }

    public function testGetInfoIncludesHiddenVariables(): void
    {
        $form = new BaseForm(
            ['id' => '42', 'queue' => 'Support', 'type' => 'Bug'],
            'Test',
            'test',
        );

        $form->addHidden('', 'id', 'int', true, true);

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true);

        $form->setSection('step2', 'Type');
        $form->addVariable('Type', 'type', 'text', true);

        $form->setActiveGroup('step2');

        $info = $form->getInfo();

        $this->assertSame(42, $info['id']);
        $this->assertSame('Support', $info['queue']);
        $this->assertSame('Bug', $info['type']);
    }

    // ========================================================================
    // renderMixed: produces single form with mixed rendering
    // ========================================================================

    public function testRenderMixedProducesSingleFormTag(): void
    {
        $form = new BaseForm(
            ['queue' => 'Support', 'type' => 'Bug'],
            'Test',
            'test',
        );

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true);

        $form->setSection('step2', 'Type');
        $form->addVariable('Type', 'type', 'text', true);

        $form->setActiveGroup('step2');

        $renderer = new HtmlRenderer();
        $html = $renderer->renderMixed($form, '/action', 'post');

        // Single form open and close
        $this->assertSame(1, substr_count($html, '<form'));
        $this->assertSame(1, substr_count($html, '</form>'));
    }

    public function testRenderMixedDisabledGroupHasHiddenInputs(): void
    {
        $form = new BaseForm(
            ['queue' => 'Support', 'type' => 'Bug'],
            'Test',
            'test',
        );

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true);

        $form->setSection('step2', 'Type');
        $form->addVariable('Type', 'type', 'text', true);

        $form->setActiveGroup('step2');

        $renderer = new HtmlRenderer();
        $html = $renderer->renderMixed($form, '/action', 'post');

        // Disabled step1 should have a hidden input preserving queue value
        $this->assertStringContainsString('name="queue"', $html);
        $this->assertStringContainsString('value="Support"', $html);
        // Contains a hidden input for queue
        $this->assertMatchesRegularExpression(
            '/<input type="hidden" name="queue" value="Support">/',
            $html,
        );
    }

    public function testRenderMixedEnabledGroupHasEditableControls(): void
    {
        $form = new BaseForm(
            ['queue' => 'Support', 'type' => 'Bug'],
            'Test',
            'test',
        );

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true);

        $form->setSection('step2', 'Type');
        $form->addVariable('Type', 'type', 'text', true);

        $form->setActiveGroup('step2');

        $renderer = new HtmlRenderer();
        $html = $renderer->renderMixed($form, '/action', 'post');

        // Enabled step2 should have an editable text input for 'type'
        $this->assertMatchesRegularExpression(
            '/<input[^>]*type="text"[^>]*name="type"/',
            $html,
        );
    }

    public function testRenderMixedHasSubmitButton(): void
    {
        $form = new BaseForm(
            ['queue' => 'Support'],
            'Test',
            'test',
        );
        $form->setButtons(true);

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true);

        $form->setActiveGroup('step1');

        $renderer = new HtmlRenderer();
        $html = $renderer->renderMixed($form, '/action', 'post');

        $this->assertStringContainsString('type="submit"', $html);
    }

    public function testRenderMixedFormNameHidden(): void
    {
        $form = new BaseForm(
            ['queue' => 'Support'],
            'Test',
            'test',
        );

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true);

        $form->setActiveGroup('step1');

        $renderer = new HtmlRenderer();
        $html = $renderer->renderMixed($form, '/action', 'post');

        $this->assertStringContainsString('name="formname"', $html);
        $this->assertStringContainsString('value="test"', $html);
    }

    // ========================================================================
    // Full 3-step wizard simulation
    // ========================================================================

    public function testThreeStepWizardStep1(): void
    {
        $form = $this->buildWizardForm(step: 1, vars: [
            'formname' => 'test',
        ]);

        // Step 1 is active, steps 2-3 are disabled
        $this->assertTrue($form->getGroup('step1')->isEnabled());
        $this->assertFalse($form->getGroup('step2')->isEnabled());
        $this->assertFalse($form->getGroup('step3')->isEnabled());
    }

    public function testThreeStepWizardStep2ValidatesOnlyStep2(): void
    {
        $form = $this->buildWizardForm(step: 2, vars: [
            'formname' => 'test',
            'queue' => 'Support',
            'type' => '',  // required but empty — should fail
        ]);

        $this->assertFalse($form->validate());
        // queue is in step1 (disabled) — no error
        $this->assertNull($form->getError('queue'));
        // type is in step2 (enabled) — should have error
        $this->assertNotNull($form->getError('type'));
    }

    public function testThreeStepWizardStep3GetInfo(): void
    {
        $form = $this->buildWizardForm(step: 3, vars: [
            'formname' => 'test',
            'queue' => 'Support',
            'type' => 'Bug',
            'state' => 'New',
        ]);

        $info = $form->getInfo();

        $this->assertSame('Support', $info['queue']);
        $this->assertSame('Bug', $info['type']);
        $this->assertSame('New', $info['state']);
    }

    public function testThreeStepWizardStep2RenderMixed(): void
    {
        $form = $this->buildWizardForm(step: 2, vars: [
            'queue' => 'Support',
            'type' => 'Bug',
        ]);
        $form->setButtons(true);

        $renderer = new HtmlRenderer();
        $html = $renderer->renderMixed($form, '/action', 'post');

        // Step 1 (disabled): queue value preserved as hidden
        $this->assertStringContainsString(
            '<input type="hidden" name="queue" value="Support">',
            $html,
        );

        // Step 2 (enabled): type rendered as editable
        $this->assertMatchesRegularExpression(
            '/<input[^>]*type="text"[^>]*name="type"/',
            $html,
        );

        // Step 3 (disabled): state field preserved as hidden
        // (no value set, so hidden with empty value)
        $this->assertStringContainsString(
            '<input type="hidden" name="state"',
            $html,
        );

        // Submit button present
        $this->assertStringContainsString('type="submit"', $html);

        // Single form tag
        $this->assertSame(1, substr_count($html, '<form'));
    }

    /**
     * Build a 3-step wizard form for testing.
     */
    private function buildWizardForm(int $step, array $vars = []): BaseForm
    {
        $form = new BaseForm($vars, 'Wizard', 'test');
        $form->useToken(false);

        $form->setSection('step1', 'Queue');
        $form->addVariable('Queue', 'queue', 'text', true);

        $form->setSection('step2', 'Type');
        $form->addVariable('Type', 'type', 'text', true);

        $form->setSection('step3', 'State');
        $form->addVariable('State', 'state', 'text', true);

        $form->setActiveGroup('step' . $step);

        return $form;
    }
}
