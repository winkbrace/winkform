<?php
$I = new WebGuy($scenario);
$I->wantTo('see a form');
$I->amOnPage('/');
$I->see('form', '#container');
