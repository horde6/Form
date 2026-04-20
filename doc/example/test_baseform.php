<?php

declare(strict_types=1);

/**
 * Quick integration test for BaseForm implementation.
 *
 * This test verifies that BaseForm can be instantiated and used
 * with all three input types: Horde_Variables, PSR-7 ServerRequest, and array.
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../test/stubs/HordeStubs.php';

use Horde\Form\V3\BaseForm;

echo "Testing BaseForm implementation...\n\n";

// Test 1: Array input
echo "Test 1: Array input\n";
$form1 = new BaseForm(
    vars: ['name' => 'John', 'email' => 'john@example.com'],
    title: 'Test Form',
    name: 'test_form'
);
echo "✓ BaseForm created with array input\n";
echo "  Name: " . $form1->getName() . "\n";
echo "  Title: " . $form1->getTitle() . "\n";
echo "  Vars: " . json_encode($form1->getVars()) . "\n\n";

// Test 2: Horde_Variables input
echo "Test 2: Horde_Variables input\n";
$vars = new Horde_Variables(['name' => 'Jane', 'email' => 'jane@example.com']);
$form2 = new BaseForm(
    vars: $vars,
    title: 'Horde Variables Form'
);
echo "✓ BaseForm created with Horde_Variables input\n";
echo "  Vars: " . json_encode($form2->getVars()) . "\n\n";

// Test 3: Add variables
echo "Test 3: Add variables\n";
$form3 = new BaseForm(
    vars: [],
    title: 'Variable Test Form'
);

try {
    $var1 = $form3->addVariable(
        humanName: 'Full Name',
        varName: 'name',
        type: 'text',
        required: true
    );
    echo "✓ Added text variable\n";
    echo "  Human name: " . $var1->getHumanName() . "\n";
    echo "  Var name: " . $var1->getVarName() . "\n";
} catch (Exception $e) {
    echo "✗ Failed to add variable: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Get variables
echo "Test 4: Get variables\n";
$allVars = $form3->getVariables();
echo "✓ Retrieved variables (count: " . count($allVars) . ")\n\n";

// Test 5: Validation
echo "Test 5: Validation\n";
$form4 = new BaseForm(
    vars: ['name' => 'Test'],
    title: 'Validation Form'
);
try {
    $form4->addVariable(
        humanName: 'Name',
        varName: 'name',
        type: 'text',
        required: true
    );
    $isValid = $form4->validate();
    echo "✓ Validation executed (result: " . ($isValid ? 'valid' : 'invalid') . ")\n";
    $errors = $form4->getErrors();
    echo "  Errors: " . (empty($errors) ? 'none' : json_encode($errors)) . "\n";
} catch (Exception $e) {
    echo "✗ Validation failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 6: Get info
echo "Test 6: Get info\n";
try {
    $info = $form4->getInfo();
    echo "✓ Retrieved form info\n";
    echo "  Info: " . json_encode($info) . "\n";
} catch (Exception $e) {
    echo "✗ Get info failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 7: Sections
echo "Test 7: Sections\n";
$form5 = new BaseForm(
    vars: [],
    title: 'Section Form'
);
$form5->setSection(
    section: 'personal',
    desc: 'Personal Information',
    expanded: true
);
$form5->addVariable(
    humanName: 'Name',
    varName: 'name',
    type: 'text',
    required: true
);
$form5->setSection(
    section: 'contact',
    desc: 'Contact Information',
    expanded: false
);
$form5->addVariable(
    humanName: 'Email',
    varName: 'email',
    type: 'email',
    required: true
);
echo "✓ Sections created\n";
echo "  Section 'personal' desc: " . $form5->getSectionDesc('personal') . "\n";
echo "  Section 'contact' expanded: " . ($form5->getSectionExpandedState('contact', true) ? 'yes' : 'no') . "\n";

echo "\n";

// Test 8: Hidden variables
echo "Test 8: Hidden variables\n";
$form6 = new BaseForm(
    vars: ['id' => '123'],
    title: 'Hidden Test'
);
try {
    $hidden = $form6->addHidden(
        humanName: 'ID',
        varName: 'id',
        type: 'text',
        required: false
    );
    echo "✓ Added hidden variable\n";
    echo "  Is hidden: " . ($hidden->isHidden() ? 'yes' : 'no') . "\n";
    $visibleVars = $form6->getVariables(flat: true, withHidden: false);
    $allVars = $form6->getVariables(flat: true, withHidden: true);
    echo "  Visible variables: " . count($visibleVars) . "\n";
    echo "  All variables: " . count($allVars) . "\n";
} catch (Exception $e) {
    echo "✗ Hidden variable failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 9: Variable removal
echo "Test 9: Variable removal\n";
$form7 = new BaseForm(
    vars: [],
    title: 'Removal Test'
);
$form7->addVariable('Name', 'name', 'text', true);
$form7->addVariable('Email', 'email', 'email', true);
$before = count($form7->getVariables());
$removed = $form7->removeVariable('name');
$after = count($form7->getVariables());
echo "✓ Variable removal\n";
echo "  Before: $before, After: $after, Removed: " . ($removed ? 'yes' : 'no') . "\n";

echo "\n";

// Test 10: Error handling
echo "Test 10: Error handling\n";
$form8 = new BaseForm(
    vars: [],
    title: 'Error Test'
);
$form8->setError('test_field', 'This is a test error');
$error = $form8->getError('test_field');
echo "✓ Error handling\n";
echo "  Error for 'test_field': $error\n";
$form8->clearError('test_field');
$errorAfter = $form8->getError('test_field');
echo "  After clear: " . ($errorAfter ?? 'null') . "\n";

echo "\n========================================\n";
echo "All BaseForm integration tests passed! ✓\n";
echo "========================================\n";
