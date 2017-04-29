<?php
/**
 * ExpressionEngine (https://expressionengine.com)
 *
 * @link      https://expressionengine.com/
 * @copyright Copyright (c) 2003-2017, EllisLab, Inc. (https://ellislab.com)
 * @license   https://expressionengine.com/license
 */

namespace EllisLab\ExpressionEngine\Library\Curl;

class RequestFactory {

	public function get($url, $data = array(), $callback = NULL)
	{
		return new GetRequest($url, $data, $callback);
	}

	public function post($url, $data = array(), $callback = NULL)
	{
		return new PostRequest($url, $data, $callback);
	}

}

// EOF
