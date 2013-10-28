<?php
$I = new WebGuy($scenario);
$I->wantTo('see the test form');
$I->amOnPage('/');
$I->see('form', '#container');

// no errors must be displayed
$I->dontSee('error');
$I->dontSee('Error');

// address field
$I->see('#postcode');

