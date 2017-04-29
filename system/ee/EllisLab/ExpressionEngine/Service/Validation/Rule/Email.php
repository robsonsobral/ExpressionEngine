<?php
/**
 * ExpressionEngine (https://expressionengine.com)
 *
 * @link      https://expressionengine.com/
 * @copyright Copyright (c) 2003-2017, EllisLab, Inc. (https://ellislab.com)
 * @license   https://expressionengine.com/license
 */

namespace EllisLab\ExpressionEngine\Service\Validation\Rule;

use EllisLab\ExpressionEngine\Service\Validation\ValidationRule;

/**
 * ExpressionEngine Email Validation Rule
 *
 * @package		ExpressionEngine
 * @subpackage	Validation\Rule
 * @category	Service
 * @author		EllisLab Dev Team
 * @link		https://ellislab.com
 */
class Email extends ValidationRule {

	public function validate($key, $value)
	{
		if ($value != filter_var($value, FILTER_SANITIZE_EMAIL) OR ! filter_var($value, FILTER_VALIDATE_EMAIL))
		{
			return FALSE;
		}

		return TRUE;
	}

	public function getLanguageKey()
	{
		return 'valid_email';
	}

}

// EOF
