<?php

namespace Abo\Generalutil\V1\Exceptions;

abstract class BaseException extends \Exception
{
	protected $codes;


	protected abstract function exceptions();


	private function _setBaseException() {
		return array(
			100 => 'params error.',
			200 => 'success.',
			301 => 'moved permanently.',
			400 => 'bad request.',
			403 => 'forbidden.',
			404 => 'not found.',
			408 => 'request timeout.',
			500 => 'server error.',
			600 => 'failed.',
			601 => 'records not found.',
		    602 => 'extra_data is empty or not array.',
		    603 => 'extra_data is invalid.',
		    604 => 'params is invalid.',
		    605 => 'no more msg now.',
		    901 => 'params is invalid.',
		);
	}
	

	public function __construct($code = 500, $message = '') {
		parent::__construct();

		// 索引为数字时，不能用array_merge，否则合并后会重新索引。
		$this->codes = $this->_setBaseException() + $this->exceptions();
		
		$this->code = empty($code) ? 500 : $code;
		$this->message = $message ? $message :$this->codes[$code];
	}
}
