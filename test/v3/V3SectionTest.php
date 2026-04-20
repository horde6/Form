<?php

declare(strict_types=1);

namespace Horde\Form\Test\V3;

use Horde\Form\V3\FieldGroup;
use Horde\Form\V3\Section;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Section::class)]
class V3SectionTest extends TestCase
{
    public function testExtendsFieldGroup(): void
    {
        $section = new Section('personal');

        $this->assertInstanceOf(FieldGroup::class, $section);
    }

    public function testConstructorSetsAllProperties(): void
    {
        $section = new Section(
            name: 'billing',
            title: 'Billing Address',
            description: 'Enter your billing address',
            image: '/icons/billing.png',
            expanded: false,
            prefix: 'bill',
        );

        $this->assertSame('billing', $section->getName());
        $this->assertSame('Billing Address', $section->getTitle());
        $this->assertSame('Enter your billing address', $section->getDescription());
        $this->assertSame('/icons/billing.png', $section->getImage());
        $this->assertFalse($section->isExpanded());
        $this->assertSame('bill', $section->getPrefix());
    }

    public function testConstructorDefaults(): void
    {
        $section = new Section('basic');

        $this->assertSame('basic', $section->getName());
        $this->assertSame('', $section->getTitle());
        $this->assertSame('', $section->getDescription());
        $this->assertSame('', $section->getImage());
        $this->assertTrue($section->isExpanded());
        $this->assertSame('', $section->getPrefix());
    }

    public function testSetTitle(): void
    {
        $section = new Section('s');
        $section->setTitle('Updated Title');

        $this->assertSame('Updated Title', $section->getTitle());
    }

    public function testSetDescription(): void
    {
        $section = new Section('s');
        $section->setDescription('Updated description');

        $this->assertSame('Updated description', $section->getDescription());
    }

    public function testSetImage(): void
    {
        $section = new Section('s');
        $section->setImage('/new/image.png');

        $this->assertSame('/new/image.png', $section->getImage());
    }

    public function testSetExpanded(): void
    {
        $section = new Section('s');

        $section->setExpanded(false);
        $this->assertFalse($section->isExpanded());

        $section->setExpanded(true);
        $this->assertTrue($section->isExpanded());
    }

    public function testInheritsPrefixScopingFromFieldGroup(): void
    {
        $section = new Section('billing', prefix: 'billing');

        $var = $section->addVariable('Street', 'street', 'text', true);

        $this->assertSame('billing[street]', $var->getVarName());
    }

    public function testNoPrefixVariablesUnscoped(): void
    {
        $section = new Section('info');

        $var = $section->addVariable('Name', 'name', 'text', true);

        $this->assertSame('name', $var->getVarName());
    }

    public function testInheritsValidateGroup(): void
    {
        $section = new class ('billing') extends Section {
            public function __construct(string $name)
            {
                parent::__construct($name, prefix: 'billing');
            }
            protected function validateGroup(array $vars, array &$errors): void
            {
                if (empty($vars['zip'])) {
                    $errors['billing[zip]'] = 'ZIP required.';
                }
            }
        };

        $errors = [];
        $section->validate(['billing' => ['street' => '123']], $errors);

        $this->assertSame('ZIP required.', $errors['billing[zip]']);
    }

    public function testVariableManagementInherited(): void
    {
        $section = new Section('details');

        $section->addVariable('A', 'a', 'text', true);
        $section->addVariable('B', 'b', 'text', false);

        $this->assertCount(2, $section->getVariables());

        $section->removeVariable('a');
        $this->assertCount(1, $section->getVariables());
    }
}
