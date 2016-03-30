<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Forms\Controls;

use Nette;


/**
 * Select box control that allows single item selection.
 */
class SelectBox extends ChoiceControl
{
	/** validation rule */
	const VALID = ':selectBoxValid';

	/** @var array of option / optgroup */
	private $options = [];

	/** @var mixed */
	private $prompt = FALSE;

	/** @var array */
	private $optionAttributes = [];


	public function __construct($label = NULL, array $items = NULL)
	{
		parent::__construct($label, $items);
		$this->setOption('type', 'select');
	}


	/**
	 * Sets first prompt item in select box.
	 * @param  string
	 * @return self
	 */
	public function setPrompt($prompt)
	{
		$this->prompt = $prompt;
		return $this;
	}


	/**
	 * Returns first prompt item?
	 * @return mixed
	 */
	public function getPrompt()
	{
		return $this->prompt;
	}


	/**
	 * Sets options and option groups from which to choose.
	 * @return self
	 */
	public function setItems(array $items, $useKeys = TRUE)
	{
		if (!$useKeys) {
			$res = [];
			foreach ($items as $key => $value) {
				unset($items[$key]);
				if (is_array($value)) {
					foreach ($value as $val) {
						$res[$key][(string) $val] = $val;
					}
				} else {
					$res[(string) $value] = $value;
				}
			}
			$items = $res;
		}
		$this->options = $items;
		return parent::setItems(Nette\Utils\Arrays::flatten($items, TRUE));
	}


	/**
	 * Generates control's HTML element.
	 * @return Nette\Utils\Html
	 */
	public function getControl()
	{
		$items = $this->prompt === FALSE ? [] : ['' => $this->translate($this->prompt)];
		foreach ($this->options as $key => $value) {
			$items[is_array($value) ? $this->translate($key) : $key] = $this->translate($value);
		}

		return Nette\Forms\Helpers::createSelectBox(
			$items,
			array_merge($this->optionAttributes, [
				'selected?' => $this->value,
				'disabled:' => is_array($this->disabled) ? $this->disabled : NULL,
			])
		)->addAttributes(parent::getControl()->attrs);
	}


	/**
	 * @param array
	 * @return self
	 */
	public function addOptionAttributes(array $attributes)
	{
		$this->optionAttributes = array_merge($this->optionAttributes, $attributes);
		return $this;
	}


	/**
	 * Performs the server side validation.
	 * @return void
	 */
	public function validate()
	{
		parent::validate();
		if (!$this->isDisabled() && $this->prompt === FALSE && $this->getValue() === NULL && $this->options && $this->control->size < 2) {
			$this->addError(Nette\Forms\Validator::$messages[self::VALID]);
		}
	}

}
