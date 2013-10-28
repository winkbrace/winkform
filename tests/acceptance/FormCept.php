<?php
/**
 * this acceptance test expects the path http://localhost/winkform/ to point to the directory /example/
 * All input elements are inside the TestForm and rendered in the example.
 */

$I = new WebGuy($scenario);
$I->wantTo('see the test form');
$I->amOnPage('/');
$I->see('form', '#container');

// no errors or warnings must be displayed
$I->dontSee('error');
$I->dontSee('Error');
$I->dontSee('Warning');
$I->dontSee('warning');

// address field
$I->wantTo('check the address field');
$I->see('.address');
$I->seeElement('label[for=address-postal-code]');
$I->seeElement('input#address-postal-code');
$I->seeInField('input#address-postal-code', 'postal code');
$I->see('focus', 'script');

// chained dropdowns
$I->wantTo('check the chained dropdowns');
$I->seeElement('select#one');
$I->see('chainedTo', 'script');
