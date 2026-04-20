<?php

declare(strict_types=1);

namespace Horde\Form\Test\V3;

use Horde\Form\V3\BaseForm;
use Horde\Form\V3\BaseVariable;
use Horde\Form\V3\FieldGroup;
use Horde\Form\V3\Section;
use Horde\Form\V3\TextVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for bracket-notation (nested) value resolution and
 * full-form integration with FieldGroup/Section.
 */
#[CoversClass(BaseVariable::class)]
#[CoversClass(BaseForm::class)]
class V3NestedValueTest extends TestCase
{
    // ========================================================================
    // resolveValue() with bracket-notation names
    // ========================================================================

    public function testResolveValueSimpleKey(): void
    {
        $var = new TextVariable('Name', 'name', true);

        $value = $var->resolveValue(['name' => 'Alice']);

        $this->assertSame('Alice', $value);
    }

    public function testResolveValueBracketNotation(): void
    {
        $var = new TextVariable('Street', 'billing[street]', true);

        $value = $var->resolveValue([
            'billing' => ['street' => '123 Main St', 'city' => 'Springfield'],
        ]);

        $this->assertSame('123 Main St', $value);
    }

    public function testResolveValueBracketNotationMissingKey(): void
    {
        $var = new TextVariable('Street', 'billing[street]', true);

        $value = $var->resolveValue(['shipping' => ['street' => '456 Oak']]);

        $this->assertNull($value);
    }

    public function testResolveValueBracketNotationMissingNestedKey(): void
    {
        $var = new TextVariable('ZIP', 'billing[zip]', true);

        $value = $var->resolveValue(['billing' => ['street' => '123 Main']]);

        $this->assertNull($value);
    }

    public function testResolveValueBracketNotationFallsBackToDefault(): void
    {
        $var = new TextVariable('Street', 'billing[street]', false);
        $var->setDefault('no address');

        $value = $var->resolveValue([]);

        $this->assertSame('no address', $value);
    }

    public function testResolveValueSimpleKeyFallsBackToDefault(): void
    {
        $var = new TextVariable('Name', 'name', false);
        $var->setDefault('anonymous');

        $value = $var->resolveValue([]);

        $this->assertSame('anonymous', $value);
    }

    public function testResolveValueEmptyStringIsNotMissing(): void
    {
        $var = new TextVariable('Name', 'name', true);
        $var->setDefault('default');

        $value = $var->resolveValue(['name' => '']);

        $this->assertSame('', $value);
    }

    public function testResolveValueBracketNotationEmptyStringIsNotMissing(): void
    {
        $var = new TextVariable('Street', 'billing[street]', true);
        $var->setDefault('default');

        $value = $var->resolveValue(['billing' => ['street' => '']]);

        $this->assertSame('', $value);
    }

    // ========================================================================
    // Full-form integration with groups and prefix
    // ========================================================================

    public function testAddGroupRegistersGroup(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $group = new FieldGroup('billing', 'billing');
        $form->addGroup($group);

        $this->assertSame($group, $form->getGroup('billing'));
    }

    public function testAddGroupReturnsSelfForFluency(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $result = $form->addGroup(new FieldGroup('x'));

        $this->assertSame($form, $result);
    }

    public function testAddGroupSetsCurrentGroup(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $form->addGroup(new FieldGroup('billing', 'billing'));
        $var = $form->addVariable('Street', 'street', 'text', true);

        // Variable should be prefixed because current group is 'billing'
        $this->assertSame('billing[street]', $var->getVarName());
    }

    public function testVariablesInPrefixedGroupHaveScopedNames(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $form->addGroup(new FieldGroup('billing', 'billing'));
        $form->addVariable('Street', 'street', 'text', true);
        $form->addVariable('City', 'city', 'text', true);

        $vars = $form->getVariables(flat: true);
        $names = array_map(fn($v) => $v->getVarName(), $vars);

        $this->assertSame(['billing[street]', 'billing[city]'], $names);
    }

    public function testSetSectionWithPrefixScopesVariables(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $form->setSection('Billing', 'Enter billing info', '', true, 'billing');
        $var = $form->addVariable('Street', 'street', 'text', true);

        $this->assertSame('billing[street]', $var->getVarName());
    }

    public function testSetSectionWithoutPrefixDoesNotScope(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $form->setSection('Personal', 'Personal info');
        $var = $form->addVariable('Name', 'name', 'text', true);

        $this->assertSame('name', $var->getVarName());
    }

    public function testGetGroupReturnsNullForUnknown(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $this->assertNull($form->getGroup('nonexistent'));
    }

    public function testGetVariablesFlatAcrossMultipleGroups(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $form->addGroup(new FieldGroup('billing', 'billing'));
        $form->addVariable('Street', 'street', 'text', true);

        $form->addGroup(new FieldGroup('shipping', 'shipping'));
        $form->addVariable('Street', 'street', 'text', true);

        $vars = $form->getVariables(flat: true);
        $names = array_map(fn($v) => $v->getVarName(), $vars);

        $this->assertSame(['billing[street]', 'shipping[street]'], $names);
    }

    public function testGetVariablesStructuredByGroup(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $form->addGroup(new FieldGroup('billing', 'billing'));
        $form->addVariable('Street', 'street', 'text', true);

        $form->addGroup(new FieldGroup('shipping', 'shipping'));
        $form->addVariable('Street', 'street', 'text', true);

        $grouped = $form->getVariables(flat: false);

        $this->assertArrayHasKey('billing', $grouped);
        $this->assertArrayHasKey('shipping', $grouped);
        $this->assertCount(1, $grouped['billing']);
        $this->assertCount(1, $grouped['shipping']);
    }

    public function testValidatePrefixedGroupFieldsFromNestedData(): void
    {
        $form = new BaseForm(
            ['formname' => 'test', 'billing' => ['street' => '123 Main']],
            'Test',
            'test',
        );
        $form->useToken(false);

        $form->addGroup(new FieldGroup('billing', 'billing'));
        $form->addVariable('Street', 'street', 'text', true);

        $this->assertTrue($form->validate());
    }

    public function testValidatePrefixedGroupMissingRequiredField(): void
    {
        $form = new BaseForm(
            ['formname' => 'test', 'billing' => ['city' => 'Springfield']],
            'Test',
            'test',
        );
        $form->useToken(false);

        $form->addGroup(new FieldGroup('billing', 'billing'));
        $form->addVariable('Street', 'street', 'text', true);

        $this->assertFalse($form->validate());
        $this->assertNotNull($form->getError('billing[street]'));
    }

    public function testValidateGroupLevelValidatorRunsDuringFormValidate(): void
    {
        $group = new class ('billing') extends FieldGroup {
            public function __construct(string $name)
            {
                parent::__construct($name, 'billing');
            }
            protected function validateGroup(array $vars, array &$errors): void
            {
                if (empty($vars['zip'])) {
                    $errors['billing[zip]'] = 'ZIP is required for billing.';
                }
            }
        };

        $form = new BaseForm(
            ['formname' => 'test', 'billing' => ['street' => '123 Main']],
            'Test',
            'test',
        );
        $form->useToken(false);
        $form->addGroup($group);
        $form->addVariable('Street', 'street', 'text', true);

        $result = $form->validate();

        $this->assertFalse($result);
        $this->assertSame('ZIP is required for billing.', $form->getError('billing[zip]'));
    }

    public function testGetInfoWithPrefixedVariables(): void
    {
        $form = new BaseForm(
            ['billing' => ['street' => '123 Main', 'city' => 'Springfield']],
            'Test',
            'test',
        );

        $form->addGroup(new FieldGroup('billing', 'billing'));
        $form->addVariable('Street', 'street', 'text', true);
        $form->addVariable('City', 'city', 'text', true);

        $info = $form->getInfo();

        // Bracket-notation keys are nested into arrays (like PHP POST decoding)
        $this->assertArrayHasKey('billing', $info);
        $this->assertIsArray($info['billing']);
        $this->assertSame('123 Main', $info['billing']['street']);
        $this->assertSame('Springfield', $info['billing']['city']);
    }

    public function testGetInfoNestsMultipleBracketGroups(): void
    {
        $form = new BaseForm(
            [
                'billing' => ['street' => '123 Main'],
                'shipping' => ['street' => '456 Oak'],
                'name' => 'Alice',
            ],
            'Test',
            'test',
        );

        $form->addVariable('Name', 'name', 'text', true);
        $form->addGroup(new FieldGroup('billing', 'billing'));
        $form->addVariable('Street', 'street', 'text', true);
        $form->addGroup(new FieldGroup('shipping', 'shipping'));
        $form->addVariable('Street', 'street', 'text', true);

        $info = $form->getInfo();

        // Flat keys preserved
        $this->assertSame('Alice', $info['name']);
        // Bracket keys nested
        $this->assertSame('123 Main', $info['billing']['street']);
        $this->assertSame('456 Oak', $info['shipping']['street']);
    }

    public function testGetInfoNestsManualBracketNotation(): void
    {
        // Simulates CreateStepThreeForm's attributes[ID] pattern
        $form = new BaseForm(
            ['attributes' => ['42' => 'high', '99' => 'low']],
            'Test',
            'test',
        );

        $form->addVariable('Priority', 'attributes[42]', 'text', false);
        $form->addVariable('Severity', 'attributes[99]', 'text', false);

        $info = $form->getInfo();

        $this->assertArrayHasKey('attributes', $info);
        $this->assertSame('high', $info['attributes']['42']);
        $this->assertSame('low', $info['attributes']['99']);
    }

    public function testPreserveWithPrefixedVariables(): void
    {
        $form = new BaseForm(
            ['billing' => ['street' => '123 Main']],
            'Test',
            'test',
        );
        $form->useToken(false);

        $form->addGroup(new FieldGroup('billing', 'billing'));
        $form->addVariable('Street', 'street', 'text', true);

        $html = $form->preserve();

        $this->assertStringContainsString('name="billing[street]"', $html);
        $this->assertStringContainsString('value="123 Main"', $html);
    }

    public function testRemoveVariableFromPrefixedGroup(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $form->addGroup(new FieldGroup('billing', 'billing'));
        $var = $form->addVariable('Street', 'street', 'text', true);
        $form->addVariable('City', 'city', 'text', true);

        $this->assertTrue($form->removeVariable($var));
        $this->assertCount(1, $form->getVariables());
    }

    public function testSectionMetadataAccessorsWork(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $form->setSection('billing', 'Billing details', '/icons/bill.png', false);

        $this->assertSame('Billing details', $form->getSectionDesc('billing'));
        $this->assertSame('/icons/bill.png', $form->getSectionImage('billing'));
        $this->assertFalse($form->getSectionExpandedState('billing', true));
        $this->assertSame('none', $form->getSectionExpandedState('billing'));
    }

    public function testSectionMetadataDefaultsForPlainGroups(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $form->addGroup(new FieldGroup('plain'));

        $this->assertSame('', $form->getSectionDesc('plain'));
        $this->assertSame('', $form->getSectionImage('plain'));
        $this->assertTrue($form->getSectionExpandedState('plain', true));
        $this->assertSame('block', $form->getSectionExpandedState('plain'));
    }

    public function testMixedGroupsAndSections(): void
    {
        $form = new BaseForm(
            ['formname' => 'test', 'name' => 'Alice', 'billing' => ['street' => '123']],
            'Test',
            'test',
        );
        $form->useToken(false);

        // Unprefixed section (backward compat)
        $form->setSection('Personal');
        $form->addVariable('Name', 'name', 'text', true);

        // Prefixed group
        $form->addGroup(new FieldGroup('billing', 'billing'));
        $form->addVariable('Street', 'street', 'text', true);

        $allVars = $form->getVariables(flat: true);
        $this->assertCount(2, $allVars);

        $names = array_map(fn($v) => $v->getVarName(), $allVars);
        $this->assertSame(['name', 'billing[street]'], $names);

        $this->assertTrue($form->validate());
    }
}
