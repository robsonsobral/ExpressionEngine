<?php
/**
 * ExpressionEngine (https://expressionengine.com)
 *
 * @link      https://expressionengine.com/
 * @copyright Copyright (c) 2003-2017, EllisLab, Inc. (https://ellislab.com)
 * @license   https://expressionengine.com/license
 */

namespace EllisLab\ExpressionEngine\Model\Security;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * ExpressionEngine (https://expressionengine.com)
 *
 * @link      https://expressionengine.com/
 * @copyright Copyright (c) 2003-2017, EllisLab, Inc. (https://ellislab.com)
 * @license   https://expressionengine.com/license
 */

/**
 * ExpressionEngine Reset Password Model
 *
 * @package		ExpressionEngine
 * @subpackage	Security
 * @category	Model
 * @author		EllisLab Dev Team
 * @link		https://ellislab.com
 */
class ResetPassword extends Model {

	protected static $_primary_key = 'reset_id';
	protected static $_table_name = 'reset_password';

	protected static $_relationships = array(
		'Member'	=> array(
			'type' => 'belongsTo'
		)
	);

	protected $reset_id;
	protected $member_id;
	protected $resetcode;
	protected $date;
}

// EOF
