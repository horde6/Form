<?php

declare(strict_types=1);

namespace Horde\Form\Test\V3;

use Horde\Form\V3\BaseForm;
use Horde\Form\V3\FormValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BaseForm::class)]
class V3FormValidatorTest extends TestCase
{
    public function testRegisteredValidatorIsCalledDuringValidation(): void
    {
        $form = new BaseForm(
            ['formname' => 'test', 'name' => 'alice'],
            'Test',
            'test',
        );
        $form->useToken(false);
        $form->addVariable('Name', 'name', 'text', true);

        $called = false;
        $form->addValidator(new class ($called) implements FormValidator {
            private bool $called;
            public function __construct(bool &$called)
            {
                $this->called = &$called;
            }
            public function validate(array $vars, array &$errors): void
            {
                $this->called = true;
            }
        });

        $form->validate();
        $this->assertTrue($called);
    }

    public function testValidatorCanAddErrors(): void
    {
        $form = new BaseForm(
            ['formname' => 'test', 'name' => 'alice'],
            'Test',
            'test',
        );
        $form->useToken(false);
        $form->addVariable('Name', 'name', 'text', true);

        $form->addValidator(new class implements FormValidator {
            public function validate(array $vars, array &$errors): void
            {
                $errors['name'] = 'Name is reserved.';
            }
        });

        $result = $form->validate();

        $this->assertFalse($result);
        $this->assertSame('Name is reserved.', $form->getError('name'));
    }

    public function testMultipleValidatorsRunInOrder(): void
    {
        $form = new BaseForm(
            ['formname' => 'test', 'x' => 'val'],
            'Test',
            'test',
        );
        $form->useToken(false);
        $form->addVariable('X', 'x', 'text', true);

        $order = [];

        $form->addValidator(new class ($order) implements FormValidator {
            private array $order;
            public function __construct(array &$order)
            {
                $this->order = &$order;
            }
            public function validate(array $vars, array &$errors): void
            {
                $this->order[] = 'first';
            }
        });

        $form->addValidator(new class ($order) implements FormValidator {
            private array $order;
            public function __construct(array &$order)
            {
                $this->order = &$order;
            }
            public function validate(array $vars, array &$errors): void
            {
                $this->order[] = 'second';
            }
        });

        $form->validate();
        $this->assertSame(['first', 'second'], $order);
    }

    public function testValidateFormOverrideIsCalledAfterRegisteredValidators(): void
    {
        $order = [];

        $form = new class (
            ['formname' => 'test', 'x' => 'val'],
            'Test',
            'test',
            $order,
        ) extends BaseForm {
            private array $order;
            public function __construct(array $vars, string $title, string $name, array &$order)
            {
                parent::__construct($vars, $title, $name);
                $this->order = &$order;
            }
            protected function validateForm(array $vars, array &$errors): void
            {
                $this->order[] = 'subclass';
            }
        };

        $form->useToken(false);
        $form->addVariable('X', 'x', 'text', true);

        $form->addValidator(new class ($order) implements FormValidator {
            private array $order;
            public function __construct(array &$order)
            {
                $this->order = &$order;
            }
            public function validate(array $vars, array &$errors): void
            {
                $this->order[] = 'registered';
            }
        });

        $form->validate();
        $this->assertSame(['registered', 'subclass'], $order);
    }

    public function testValidateFormOverrideCanAddErrors(): void
    {
        $form = new class (
            ['formname' => 'test', 'from' => '2026-12-01', 'to' => '2026-01-01'],
            'Test',
            'test',
        ) extends BaseForm {
            protected function validateForm(array $vars, array &$errors): void
            {
                if (($vars['from'] ?? '') > ($vars['to'] ?? '')) {
                    $errors['to'] = 'Must be after start date.';
                }
            }
        };

        $form->useToken(false);
        $form->addVariable('From', 'from', 'text', true);
        $form->addVariable('To', 'to', 'text', true);

        $result = $form->validate();

        $this->assertFalse($result);
        $this->assertSame('Must be after start date.', $form->getError('to'));
    }

    public function testFieldErrorsVisibleToRegisteredValidators(): void
    {
        $form = new BaseForm(
            ['formname' => 'test', 'name' => ''],
            'Test',
            'test',
        );
        $form->useToken(false);
        $form->addVariable('Name', 'name', 'text', true);

        $seenErrors = [];
        $form->addValidator(new class ($seenErrors) implements FormValidator {
            private array $seenErrors;
            public function __construct(array &$seenErrors)
            {
                $this->seenErrors = &$seenErrors;
            }
            public function validate(array $vars, array &$errors): void
            {
                $this->seenErrors = $errors;
            }
        });

        $form->validate();

        // The required 'name' field was empty, so field validation added an error.
        // The registered validator should see it.
        $this->assertArrayHasKey('name', $seenErrors);
    }

    public function testAddValidatorReturnsSelf(): void
    {
        $form = new BaseForm([], 'Test', 'test');

        $result = $form->addValidator(new class implements FormValidator {
            public function validate(array $vars, array &$errors): void {}
        });

        $this->assertSame($form, $result);
    }

    public function testNoValidatorsStillWorks(): void
    {
        $form = new BaseForm(
            ['formname' => 'test', 'name' => 'alice'],
            'Test',
            'test',
        );
        $form->useToken(false);
        $form->addVariable('Name', 'name', 'text', true);

        $this->assertTrue($form->validate());
    }
}
