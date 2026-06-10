<?php

declare(strict_types=1);

/**
 * Demo: All V3 Variable Types with Sections
 *
 * Runnable example that creates a BaseForm showcasing every available
 * V3 variable type, organized into sections. Renders the form as HTML
 * and demonstrates validation + data extraction.
 *
 * Usage:
 *   php doc/example/demo_all_types.php
 *
 * Copyright 2026 Horde LLC (http://www.horde.org/)
 *
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Form
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../test/stubs/HordeStubs.php';

use Horde\Form\V3\BaseForm;
use Horde\Form\V3\HtmlRenderer;

// Stub browser for file/image validation (no real uploads in this demo)
if (!isset($GLOBALS['browser'])) {
    $GLOBALS['browser'] = new class {
        public function wasFileUploaded(string $name): void
        {
            throw new Horde_Browser_Exception('No file uploaded');
        }
    };
}

$form = new BaseForm(
    vars: [
        'name' => 'Jane Doe',
        'email_field' => 'jane@example.com',
        'bio' => "Software engineer at Horde.\nLoves open source.",
        'age' => '34',
        'score' => '99.5',
        'permissions' => '755',
        'newsletter' => '1',
        'country' => 'de',
        'priority' => 'high',
        'tags' => ['php', 'horde'],
        'features' => ['wifi', 'pool'],
        'date_field' => '2026-04-20',
        'time_field' => '14:30:00',
        'website' => 'https://www.horde.org',
        'phone_field' => '+49-30-12345678',
        'ip_field' => '192.168.1.1',
        'color_field' => '#3498db',
        'entity_id' => '42',
    ],
    title: 'All Variable Types Demo'
);

// ========================================================================
// Section 1: Text Input
// ========================================================================
$form->setSection('text', 'Text-based input fields', expanded: true);

$form->addVariable('Full Name', 'name', 'text', true, false, 'Your full name');
$form->addVariable('Email', 'email_field', 'email', true, false, 'Contact email address');
$form->addVariable('Password', 'pw_field', 'password', false, false, 'Choose a password');
$form->addVariable('Biography', 'bio', 'longtext', false, false, 'Tell us about yourself', [4, 60]);
$form->addVariable('Short Bio', 'counted_bio', 'countedtext', false, false, 'Max 200 chars', [200]);
$form->addVariable('Mailing Address', 'address_field', 'address', false);
$form->addVariable('Keywords', 'keywords', 'stringarray', false, false, 'One per line');
$form->addVariable('Comma-separated Tags', 'csv_tags', 'stringlist', false);
$form->addVariable('Favorite Numbers', 'fav_numbers', 'intlist', false, false, 'Comma-separated integers');

// ========================================================================
// Section 2: Numbers
// ========================================================================
$form->setSection('numbers', 'Numeric input fields');

$form->addVariable('Age', 'age', 'int', false, false, 'Whole number');
$form->addVariable('Score', 'score', 'number', false, false, 'Decimal number');
$form->addVariable('Unix Permissions', 'permissions', 'octal', false, false, 'e.g. 755');

// ========================================================================
// Section 3: Selection
// ========================================================================
$form->setSection('selection', 'Selection and choice fields');

$form->addVariable('Subscribe to Newsletter', 'newsletter', 'boolean', false);
$form->addVariable('Country', 'country', 'enum', true, false, null, [
    ['us' => 'United States', 'de' => 'Germany', 'fr' => 'France', 'jp' => 'Japan'],
    'Select a country...',
]);
$form->addVariable('Priority', 'priority', 'radio', false, false, null, [
    ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'],
]);
$form->addVariable('Programming Languages', 'tags', 'multienum', false, false, 'Select all that apply', [
    ['php' => 'PHP', 'python' => 'Python', 'js' => 'JavaScript', 'go' => 'Go', 'rust' => 'Rust', 'horde' => 'Horde'],
]);
$form->addVariable('Amenities', 'features', 'set', false, false, null, [
    ['wifi' => 'WiFi', 'pool' => 'Pool', 'gym' => 'Gym', 'parking' => 'Parking'],
]);
// mlenum: renderer has a known issue with nested array values, skipped for now
// $form->addVariable('Department / Role', 'dept_role', 'mlenum', false, false, 'Two-level selection', [
//     [
//         'eng' => ['fe' => 'Frontend', 'be' => 'Backend', 'devops' => 'DevOps'],
//         'sales' => ['ae' => 'Account Exec', 'sdr' => 'SDR'],
//     ],
// ]);
$form->addVariable('Category', 'category_field', 'category', false, false, null, [
    ['bug' => 'Bug', 'feature' => 'Feature', 'docs' => 'Documentation'],
]);
// keyvalmultienum: type string not yet resolvable via factory (legacy name keyval_multienum)

// ========================================================================
// Section 4: Date & Time
// ========================================================================
$form->setSection('datetime', 'Date and time fields');

$form->addVariable('Date', 'date_field', 'date', false);
$form->addVariable('Time', 'time_field', 'time', false);
$form->addVariable('Date and Time', 'datetime_field', 'datetime', false);
$form->addVariable('Card Expiry', 'card_expiry', 'monthyear', false);
$form->addVariable('Birthday', 'birthday', 'monthdayyear', false);
$form->addVariable('Duration', 'duration', 'hourminutesecond', false);

// ========================================================================
// Section 5: File & Media
// ========================================================================
$form->setSection('files', 'File upload fields');

$form->addVariable('Attachment', 'file_field', 'file', false);
$form->addVariable('Avatar', 'image_field', 'image', false);
$form->addVariable('Documents', 'selectfiles_field', 'selectfiles', false, false, null, ['demo_selectid']);

// ========================================================================
// Section 6: Network & Identity
// ========================================================================
$form->setSection('network', 'Network and identity fields');

$form->addVariable('Website', 'website', 'link', false);
$form->addVariable('Phone', 'phone_field', 'phone', false);
$form->addVariable('Mobile', 'cell_field', 'cellphone', false);
$form->addVariable('IPv4 Address', 'ip_field', 'ipaddress', false);
$form->addVariable('IPv6 Address', 'ip6_field', 'ip6address', false);
$form->addVariable('Credit Card', 'cc_field', 'creditcard', false);

// ========================================================================
// Section 7: Security & Confirmation
// ========================================================================
$form->setSection('security', 'Password confirmation and security fields');

$form->addVariable('New Password', 'new_pw', 'passwordconfirm', false);
$form->addVariable('Confirm Email', 'confirm_email', 'emailconfirm', false);
$form->addVariable('PGP Key', 'pgp_field', 'pgp', false);
$form->addVariable('S/MIME Certificate', 'smime_field', 'smime', false);
$form->addVariable('Captcha', 'captcha_field', 'captcha', false, false, null, ['VERIFY', 'standard']);

// ========================================================================
// Section 8: Display-only
// ========================================================================
$form->setSection('display', 'Display-only (non-input) fields');

$form->addVariable('Form Options', 'header_field', 'header', false);
$form->addVariable('', 'spacer_field', 'spacer', false);
$form->addVariable('This is a help paragraph rendered as plain text.', 'desc_field', 'description', false);
$form->addVariable('<em>Rich HTML content</em> can be embedded here.', 'html_field', 'html', false);
$form->addVariable('HORDE', 'figlet_field', 'figlet', false, false, null, ['HORDE', 'standard']);
$form->addVariable('This type is disabled', 'invalid_field', 'invalid', false);

// ========================================================================
// Section 9: Complex / Interactive
// ========================================================================
$form->setSection('complex', 'Complex interactive fields');

$form->addVariable('Sort Items', 'sorter_field', 'sorter', false, false, null, [
    ['alpha' => 'Alpha', 'beta' => 'Beta', 'gamma' => 'Gamma'],
]);
$form->addVariable('Assign Users', 'assign_field', 'assign', false, false, null, [
    ['alice' => 'Alice', 'bob' => 'Bob', 'carol' => 'Carol'],
    ['bob' => 'Bob'],
    'Available',
    'Assigned',
]);
$form->addVariable('Permissions Matrix', 'matrix_field', 'matrix', false, false, null, [
    ['Read', 'Write', 'Admin'],
    ['Users', 'Guests'],
]);
$form->addVariable('Select from Table', 'tableset_field', 'tableset', false, false, null, [
    ['row1' => 'Row 1', 'row2' => 'Row 2', 'row3' => 'Row 3'],
    ['ID', 'Value'],
]);
$form->addVariable('Favorite Color', 'color_field', 'colorpicker', false);
// dblookup: requires a live database connection, skipped in this demo
$form->addVariable('Object Browser', 'obrowser_field', 'obrowser', false);
$form->addVariable('Linked Address', 'addresslink_field', 'addresslink', false);

// ========================================================================
// Hidden field (not in any section)
// ========================================================================
$form->addHidden('Entity ID', 'entity_id', 'text', false);

// ========================================================================
// Buttons
// ========================================================================
$form->setButtons(['Save', 'Save and Continue'], 'Reset');

// ========================================================================
// Render
// ========================================================================
echo "Rendering form with all V3 variable types...\n\n";

$renderer = new HtmlRenderer();
$html = $renderer->render($form, '/demo/submit', 'post');

echo $html . "\n\n";

$variables = $form->getVariables(flat: true, withHidden: true);
echo "Total fields: " . count($variables) . "\n";

// ========================================================================
// Validate and extract data
// Some types (image, captcha) require live services and may throw in this
// standalone demo.  Wrap in try/catch so the demo stays runnable.
// ========================================================================
echo "\nValidation:\n";
try {
    $valid = $form->validate();
    echo "  Result: " . ($valid ? 'PASS' : 'FAIL') . "\n";

    $errors = $form->getErrors();
    if ($errors) {
        echo "  Errors:\n";
        foreach ($errors as $field => $message) {
            echo "    $field: $message\n";
        }
    }
} catch (Throwable $e) {
    echo "  Skipped (service dependency): " . $e->getMessage() . "\n";
}

echo "\nData extraction (getInfo):\n";
try {
    $info = $form->getInfo();
    foreach ($info as $key => $value) {
        $display = is_array($value) ? json_encode($value) : (string) $value;
        echo "  $key = $display\n";
    }
} catch (Throwable $e) {
    echo "  Skipped (service dependency): " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
