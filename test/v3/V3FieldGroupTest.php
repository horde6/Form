<?php

declare(strict_types=1);

namespace Horde\Form\Test\V3;

use Horde\Form\V3\BaseForm;
use Horde\Form\V3\FieldGroup;
use Horde\Form\V3\FormValidator;
use Horde\Form\V3\Variable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FieldGroup::class)]
class V3FieldGroupTest extends TestCase
{
    public function testConstructorSetsNameAndPrefix(): void
    {
        $group = new FieldGroup('billing', 'billing');

        $this->assertSame('billing', $group->getName());
        $this->assertSame('billing', $group->getPrefix());
    }

    public function testConstructorDefaultsToEmptyPrefix(): void
    {
        $group = new FieldGroup('default');

        $this->assertSame('default', $group->getName());
        $this->assertSame('', $group->getPrefix());
    }

    public function testAddVariableWithoutPrefix(): void
    {
        $group = new FieldGroup('basic');

        $var = $group->addVariable('Street', 'street', 'text', true);

        $this->assertInstanceOf(Variable::class, $var);
        $this->assertSame('street', $var->getVarName());
    }

    public function testAddVariableWithPrefixScopesName(): void
    {
        $group = new FieldGroup('billing', 'billing');

        $var = $group->addVariable('Street', 'street', 'text', true);

        $this->assertSame('billing[street]', $var->getVarName());
    }

    public function testAddVariableWithPrefixMultipleFields(): void
    {
        $group = new FieldGroup('shipping', 'shipping');

        $street = $group->addVariable('Street', 'street', 'text', true);
        $city = $group->addVariable('City', 'city', 'text', true);
        $zip = $group->addVariable('ZIP', 'zip', 'text', false);

        $this->assertSame('shipping[street]', $street->getVarName());
        $this->assertSame('shipping[city]', $city->getVarName());
        $this->assertSame('shipping[zip]', $zip->getVarName());
    }

    public function testGetVariablesReturnsAllVariables(): void
    {
        $group = new FieldGroup('fields');

        $group->addVariable('A', 'a', 'text', true);
        $group->addVariable('B', 'b', 'text', false);

        $vars = $group->getVariables();
        $this->assertCount(2, $vars);
    }

    public function testGetVariablesEmptyByDefault(): void
    {
        $group = new FieldGroup('empty');

        $this->assertSame([], $group->getVariables());
    }

    public function testInsertVariableAppends(): void
    {
        $group = new FieldGroup('test');

        $a = $group->addVariable('A', 'a', 'text', true);

        $b = new \Horde\Form\V3\TextVariable('B', 'b', false);
        $group->insertVariable($b);

        $vars = $group->getVariables();
        $this->assertCount(2, $vars);
        $this->assertSame('a', $vars[0]->getVarName());
        $this->assertSame('b', $vars[1]->getVarName());
    }

    public function testInsertVariableBefore(): void
    {
        $group = new FieldGroup('test');

        $a = $group->addVariable('A', 'a', 'text', true);
        $c = $group->addVariable('C', 'c', 'text', true);

        $b = new \Horde\Form\V3\TextVariable('B', 'b', false);
        $group->insertVariable($b, 'c');

        $names = array_map(fn(Variable $v) => $v->getVarName(), $group->getVariables());
        $this->assertSame(['a', 'b', 'c'], $names);
    }

    public function testInsertVariableBeforeNonexistentAppends(): void
    {
        $group = new FieldGroup('test');

        $a = $group->addVariable('A', 'a', 'text', true);

        $b = new \Horde\Form\V3\TextVariable('B', 'b', false);
        $group->insertVariable($b, 'nonexistent');

        $vars = $group->getVariables();
        $this->assertCount(2, $vars);
        $this->assertSame('b', $vars[1]->getVarName());
    }

    public function testRemoveVariableByName(): void
    {
        $group = new FieldGroup('test');

        $group->addVariable('A', 'a', 'text', true);
        $group->addVariable('B', 'b', 'text', true);

        $this->assertTrue($group->removeVariable('a'));
        $this->assertCount(1, $group->getVariables());
        $this->assertSame('b', $group->getVariables()[0]->getVarName());
    }

    public function testRemoveVariableByInstance(): void
    {
        $group = new FieldGroup('test');

        $a = $group->addVariable('A', 'a', 'text', true);
        $group->addVariable('B', 'b', 'text', true);

        $this->assertTrue($group->removeVariable($a));
        $this->assertCount(1, $group->getVariables());
    }

    public function testRemoveVariableReturnsFalseWhenNotFound(): void
    {
        $group = new FieldGroup('test');
        $group->addVariable('A', 'a', 'text', true);

        $this->assertFalse($group->removeVariable('nonexistent'));
    }

    public function testImplementsFormValidator(): void
    {
        $group = new FieldGroup('test');

        $this->assertInstanceOf(FormValidator::class, $group);
    }

    public function testValidateCallsValidateGroupWithFullVars(): void
    {
        // No prefix → validateGroup receives full form data
        $called = false;
        $receivedVars = [];

        $group = new class ('test', $called, $receivedVars) extends FieldGroup {
            private bool $called;
            private array $receivedVars;
            public function __construct(string $name, bool &$called, array &$receivedVars)
            {
                parent::__construct($name);
                $this->called = &$called;
                $this->receivedVars = &$receivedVars;
            }
            protected function validateGroup(array $vars, array &$errors): void
            {
                $this->called = true;
                $this->receivedVars = $vars;
            }
        };

        $errors = [];
        $group->validate(['name' => 'Alice', 'age' => '30'], $errors);

        $this->assertTrue($called);
        $this->assertSame(['name' => 'Alice', 'age' => '30'], $receivedVars);
    }

    public function testValidateExtractsPrefixedSubArray(): void
    {
        // With prefix → validateGroup receives only the prefix sub-array
        $receivedVars = [];

        $group = new class ('billing', $receivedVars) extends FieldGroup {
            private array $receivedVars;
            public function __construct(string $name, array &$receivedVars)
            {
                parent::__construct($name, 'billing');
                $this->receivedVars = &$receivedVars;
            }
            protected function validateGroup(array $vars, array &$errors): void
            {
                $this->receivedVars = $vars;
            }
        };

        $errors = [];
        $group->validate([
            'billing' => ['street' => '123 Main', 'city' => 'Springfield'],
            'shipping' => ['street' => '456 Oak'],
        ], $errors);

        $this->assertSame(['street' => '123 Main', 'city' => 'Springfield'], $receivedVars);
    }

    public function testValidateWithMissingPrefixPassesEmptyArray(): void
    {
        $receivedVars = null;

        $group = new class ('billing', $receivedVars) extends FieldGroup {
            private ?array $receivedVars;
            public function __construct(string $name, ?array &$receivedVars)
            {
                parent::__construct($name, 'billing');
                $this->receivedVars = &$receivedVars;
            }
            protected function validateGroup(array $vars, array &$errors): void
            {
                $this->receivedVars = $vars;
            }
        };

        $errors = [];
        $group->validate(['other' => 'value'], $errors);

        $this->assertSame([], $receivedVars);
    }

    public function testValidateGroupCanAddErrors(): void
    {
        $group = new class ('billing') extends FieldGroup {
            public function __construct(string $name)
            {
                parent::__construct($name, 'billing');
            }
            protected function validateGroup(array $vars, array &$errors): void
            {
                if (empty($vars['street'])) {
                    $errors['billing[street]'] = 'Street is required for billing.';
                }
            }
        };

        $errors = [];
        $group->validate(['billing' => ['city' => 'Springfield']], $errors);

        $this->assertSame('Street is required for billing.', $errors['billing[street]']);
    }

    public function testDefaultValidateGroupIsNoOp(): void
    {
        $group = new FieldGroup('test');

        $errors = [];
        $group->validate(['x' => 'y'], $errors);

        $this->assertSame([], $errors);
    }

    // ========================================================================
    // Enabled / disabled state
    // ========================================================================

    public function testEnabledByDefault(): void
    {
        $group = new FieldGroup('test');

        $this->assertTrue($group->isEnabled());
    }

    public function testSetEnabledFalse(): void
    {
        $group = new FieldGroup('test');

        $group->setEnabled(false);

        $this->assertFalse($group->isEnabled());
    }

    public function testSetEnabledTrue(): void
    {
        $group = new FieldGroup('test');

        $group->setEnabled(false);
        $group->setEnabled(true);

        $this->assertTrue($group->isEnabled());
    }
}
