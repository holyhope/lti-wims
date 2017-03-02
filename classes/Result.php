<?php


namespace LTI;


abstract class Result {
	public $encoding;

	abstract function render();
}