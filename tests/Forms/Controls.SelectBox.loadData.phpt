<?php

/**
 * Test: Nette\Forms\Controls\SelectBox.
 */

declare(strict_types=1);

use Nette\Forms\Form;
use Nette\Utils\DateTime;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


before(function () {
	$_SERVER['REQUEST_METHOD'] = 'POST';
	$_POST = $_FILES = [];
});


$series = [
	'red-dwarf' => 'Red Dwarf',
	'the-simpsons' => 'The Simpsons',
	0 => 'South Park',
	'' => 'Family Guy',
];


test(function () use ($series) { // Select
	$_POST = ['select' => 'red-dwarf'];

	$form = new Form;
	$input = $form->addSelect('select', null, $series);

	Assert::true($form->isValid());
	Assert::same('red-dwarf', $input->getValue());
	Assert::same('Red Dwarf', $input->getSelectedItem());
	Assert::true($input->isFilled());
});


test(function () { // Empty select
	$_POST = ['select' => 'red-dwarf'];

	$form = new Form;
	$input = $form->addSelect('select');

	Assert::true($form->isValid());
	Assert::same(null, $input->getValue());
	Assert::same(null, $input->getSelectedItem());
	Assert::false($input->isFilled());
});


test(function () use ($series) { // Select with prompt
	$_POST = ['select' => 'red-dwarf'];

	$form = new Form;
	$input = $form->addSelect('select', null, $series)->setPrompt('Select series');

	Assert::true($form->isValid());
	Assert::same('red-dwarf', $input->getValue());
	Assert::same('Red Dwarf', $input->getSelectedItem());
	Assert::true($input->isFilled());
});


test(function () use ($series) { // Select with more visible options and no input
	$form = new Form;
	$input = $form->addSelect('select', null, $series);
	$input->getControlPrototype()->size = 2;

	Assert::true($form->isValid());
	Assert::same(null, $input->getValue());
	Assert::same(null, $input->getSelectedItem());
	Assert::false($input->isFilled());
});


test(function () { // Select with optgroups
	$_POST = ['select' => 'red-dwarf'];

	$form = new Form;
	$input = $form->addSelect('select', null, [
		'usa' => [
			'the-simpsons' => 'The Simpsons',
			0 => 'South Park',
		],
		'uk' => [
			'red-dwarf' => 'Red Dwarf',
		],
	]);

	Assert::true($form->isValid());
	Assert::same('red-dwarf', $input->getValue());
	Assert::same('Red Dwarf', $input->getSelectedItem());
	Assert::true($input->isFilled());
});


test(function () use ($series) { // Select with invalid input
	$_POST = ['select' => 'days-of-our-lives'];

	$form = new Form;
	$input = $form->addSelect('select', null, $series);

	Assert::false($form->isValid());
	Assert::null($input->getValue());
	Assert::null($input->getSelectedItem());
	Assert::false($input->isFilled());
});


test(function () use ($series) { // Select with prompt and invalid input
	$form = new Form;
	$input = $form->addSelect('select', null, $series)->setPrompt('Select series');

	Assert::true($form->isValid());
	Assert::null($input->getValue());
	Assert::null($input->getSelectedItem());
	Assert::false($input->isFilled());
});


test(function () use ($series) { // Indexed arrays
	$_POST = ['zero' => 0];

	$form = new Form;
	$input = $form->addSelect('zero', null, $series);

	Assert::true($form->isValid());
	Assert::same(0, $input->getValue());
	Assert::same(0, $input->getRawValue());
	Assert::same('South Park', $input->getSelectedItem());
	Assert::true($input->isFilled());
});


test(function () use ($series) { // empty key
	$_POST = ['empty' => ''];

	$form = new Form;
	$input = $form->addSelect('empty', null, $series);

	Assert::true($form->isValid());
	Assert::same('', $input->getValue());
	Assert::same('Family Guy', $input->getSelectedItem());
	Assert::true($input->isFilled());
});


test(function () use ($series) { // missing key
	$form = new Form;
	$input = $form->addSelect('missing', null, $series);

	Assert::false($form->isValid());
	Assert::null($input->getValue());
	Assert::null($input->getSelectedItem());
	Assert::false($input->isFilled());
});


test(function () use ($series) { // disabled key
	$_POST = ['disabled' => 'red-dwarf'];

	$form = new Form;
	$input = $form->addSelect('disabled', null, $series)
		->setDisabled();

	Assert::true($form->isValid());
	Assert::null($input->getValue());
});


test(function () use ($series) { // malformed data
	$_POST = ['malformed' => [null]];

	$form = new Form;
	$input = $form->addSelect('malformed', null, $series);

	Assert::false($form->isValid());
	Assert::null($input->getValue());
	Assert::null($input->getSelectedItem());
	Assert::false($input->isFilled());
});


test(function () use ($series) { // setItems without keys
	$_POST = ['select' => 'red-dwarf'];

	$form = new Form;
	$input = $form->addSelect('select')->setItems(array_keys($series), false);
	Assert::same([
		'red-dwarf' => 'red-dwarf',
		'the-simpsons' => 'the-simpsons',
		0 => 0,
		'' => '',
	], $input->getItems());

	Assert::true($form->isValid());
	Assert::same('red-dwarf', $input->getValue());
	Assert::same('red-dwarf', $input->getSelectedItem());
	Assert::true($input->isFilled());
});


test(function () { // setItems without keys
	$form = new Form;
	$input = $form->addSelect('select')->setItems(range(1, 5), false);
	Assert::same([1 => 1, 2, 3, 4, 5], $input->getItems());
});


test(function () { // setItems without keys with optgroups
	$_POST = ['select' => 'red-dwarf'];

	$form = new Form;
	$input = $form->addSelect('select')->setItems([
		'usa' => ['the-simpsons', 0],
		'uk' => ['red-dwarf'],
	], false);

	Assert::true($form->isValid());
	Assert::same('red-dwarf', $input->getValue());
	Assert::same('red-dwarf', $input->getSelectedItem());
	Assert::true($input->isFilled());
});


test(function () use ($series) { // setCurrentValue() and invalid argument
	$form = new Form;
	$input = $form->addSelect('select', null, $series);
	$input->setCurrentValue(null);

	Assert::exception(function () use ($input) {
		$input->setCurrentValue('unknown');
	}, Nette\InvalidArgumentException::class, "Value 'unknown' is out of allowed set ['red-dwarf', 'the-simpsons', 0, ''] in field 'select'.");
});


test(function () { // object as value
	$form = new Form;
	$input = $form->addSelect('select', null, ['2013-07-05 00:00:00' => 1])
		->setCurrentValue(new DateTime('2013-07-05'));

	Assert::same('2013-07-05 00:00:00', $input->getValue());
});


test(function () { // object as item
	$form = new Form;
	$input = $form->addSelect('select')
		->setItems([
			'group' => [new DateTime('2013-07-05')],
			new DateTime('2013-07-06'),
		], false)
		->setCurrentValue('2013-07-05 00:00:00');

	Assert::equal(new DateTime('2013-07-05'), $input->getSelectedItem());
});


test(function () use ($series) { // disabled one
	$_POST = ['select' => 'red-dwarf'];

	$form = new Form;
	$input = $form->addSelect('select', null, $series)
		->setDisabled(['red-dwarf']);

	Assert::null($input->getValue());

	unset($form['select']);
	$input = new Nette\Forms\Controls\SelectBox(null, $series);
	$input->setDisabled(['red-dwarf']);
	$form['select'] = $input;

	Assert::null($input->getValue());
});

test(function () {
	$_POST = ['select' => 1];

	$form = new Form;
	$input = $form->addSelect('select', null, [
		1 => null,
		2 => 'Red dwarf',
	]);

	Assert::same(1, $input->getValue());
});
