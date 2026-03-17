<?php

/**
 * Bootstrap file for PHPUnit tests.
 */

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Setup minimal global mocks for Horde dependencies
// These are needed by Horde_Form when form tokens are enabled

// Mock injector
if (!isset($GLOBALS['injector'])) {
    $GLOBALS['injector'] = new class {
        public function getInstance($interface)
        {
            if ($interface === 'Horde_Token') {
                return new class {
                    public function verify($token)
                    {
                        // Mock token verification - always returns true for tests
                        return true;
                    }
                };
            }
            return null;
        }
    };
}

// Mock session
if (!isset($GLOBALS['session'])) {
    $GLOBALS['session'] = new class {
        public function get($app, $key)
        {
            // Mock session - returns true to simulate valid form secret
            return true;
        }
    };
}
