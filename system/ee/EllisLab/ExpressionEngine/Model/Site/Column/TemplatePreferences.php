<?php
/**
 * ExpressionEngine (https://expressionengine.com)
 *
 * @link      https://expressionengine.com/
 * @copyright Copyright (c) 2003-2017, EllisLab, Inc. (https://ellislab.com)
 * @license   https://expressionengine.com/license
 */

namespace EllisLab\ExpressionEngine\Model\Site\Column;

use EllisLab\ExpressionEngine\Service\Model\Column\Serialized\Base64Native;
use EllisLab\ExpressionEngine\Service\Model\Column\CustomType;

/**
 * ExpressionEngine (https://expressionengine.com)
 *
 * @link      https://expressionengine.com/
 * @copyright Copyright (c) 2003-2017, EllisLab, Inc. (https://ellislab.com)
 * @license   https://expressionengine.com/license
 */

/**
 * ExpressionEngine Template Preferences
 *
 * @package		ExpressionEngine
 * @subpackage	Site\Preferences
 * @category	Model
 * @author		EllisLab Dev Team
 * @link		https://ellislab.com
 */
class TemplatePreferences extends CustomType {

	protected $enable_template_routes;
	protected $strict_urls;
	protected $site_404;
	protected $save_tmpl_revisions;
	protected $max_tmpl_revisions;
	protected $tmpl_file_basepath;

	/**
	* Called when the column is fetched from db
	*/
	public function unserialize($db_data)
	{
		return Base64Native::unserialize($db_data);
	}

	/**
	* Called before the column is written to the db
	*/
	public function serialize($data)
	{
		return Base64Native::serialize($data);
	}

}

// EOF
