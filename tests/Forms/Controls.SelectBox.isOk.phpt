<?php

/**
 * Test: Nette\Forms\Controls\SelectBox::isOk()
 */

declare(strict_types=1);

use Nette\Forms\Form;
use Nette\Forms\Validator;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


$form = new Form;
$select = $form->addSelect('foo', null, ['bar' => 'Bar']);

Assert::false($select->isOk());

$select->setDisabled(true);
Assert::true($select->isOk());
$select->setDisabled(false);

$select->setPrompt('Empty');
Assert::true($select->isOk());
$select->setPrompt(false);

$select->setCurrentValue('bar');
Assert::true($select->isOk());
$select->setCurrentValue(null);

$select->setItems([]);
Assert::true($select->isOk());
$select->setItems(['bar' => 'Bar']);

$select->getControlPrototype()->size = 2;
Assert::true($select->isOk());
$select->getControlPrototype()->size = 1;
Assert::false($select->isOk());


// error message is processed via Rules
$_SERVER['REQUEST_METHOD'] = 'POST';
Validator::$messages[Nette\Forms\Controls\SelectBox::VALID] = 'SelectBox "%label" must be filled.';
$form = new Form;
$form->addSelect('foo', 'Foo', ['bar' => 'Bar']);
$form->fireEvents();
Assert::same(['SelectBox "Foo" must be filled.'], $form->getErrors());
