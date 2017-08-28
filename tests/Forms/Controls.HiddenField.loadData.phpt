<?php

/**
 * Test: Nette\Forms\Controls\HiddenField.
 */

declare(strict_types=1);

use Nette\Forms\Form;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


before(function () {
	$_SERVER['REQUEST_METHOD'] = 'POST';
	$_POST = $_FILES = [];
});


test(function () {
	$_POST = ['text' => "  a\r b \n c "];
	$form = new Form;
	$input = $form->addHidden('text');
	Assert::same("  a\n b \n c ", $input->getValue());
	Assert::true($input->isFilled());
});


test(function () {
	$form = new Form;
	$input = $form->addHidden('unknown');
	Assert::same('', $input->getValue());
	Assert::false($input->isFilled());
});


test(function () { // invalid data
	$_POST = ['malformed' => [null]];
	$form = new Form;
	$input = $form->addHidden('malformed');
	Assert::same('', $input->getValue());
	Assert::false($input->isFilled());
});


test(function () { // errors are moved to form
	$form = new Form;
	$input = $form->addHidden('hidden');
	$input->addError('error');
	Assert::same([], $input->getErrors());
	Assert::same(['error'], $form->getErrors());
});


test(function () { // setValue() and invalid argument
	$form = new Form;
	$input = $form->addHidden('hidden');
	$input->setValue(null);

	Assert::exception(function () use ($input) {
		$input->setValue([]);
	}, Nette\InvalidArgumentException::class, "Value must be scalar or null, array given in field 'hidden'.");
});


test(function () { // object
	$form = new Form;
	$input = $form->addHidden('hidden')
		->setValue(new Nette\Utils\DateTime('2013-07-05'));

	Assert::same('2013-07-05 00:00:00', $input->getValue());
});


test(function () { // persistent
	$form = new Form;
	$input = $form['hidden'] = new Nette\Forms\Controls\HiddenField('persistent');
	$input->setValue('other');

	Assert::same('persistent', $input->getValue());
});


test(function () { // nullable
	$form = new Form;
	$input = $form['hidden'] = new Nette\Forms\Controls\HiddenField();
	$input->setNullable();

	Assert::null($input->getValue());
});
