<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use EllisLab\ExpressionEngine\Library\CP\Pagination;
use EllisLab\ExpressionEngine\Library\CP\Table;
use EllisLab\ExpressionEngine\Library\CP\URL;

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 2.5
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Rich Text Editor Module
 *
 * @package		ExpressionEngine
 * @subpackage	Modules
 * @category	Modules
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */
class Rte_mcp {

	public $name = 'Rte';

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	public function __construct()
	{
		// Let's make sure they're allowed...
		$this->_permissions_check();

		// Load it all
		ee()->load->helper('form');
		ee()->load->library('rte_lib');
		ee()->load->model('rte_tool_model');

		// set some properties
		$this->_base_url = new URL('addons/settings/rte', ee()->session->session_id());
		ee()->rte_lib->form_url = 'addons/settings/rte';

		// Delete missing tools
		ee()->rte_tool_model->delete_missing_tools();
	}

	// --------------------------------------------------------------------

	/**
	 * Homepage
	 *
	 * @access	public
	 * @return	string The page
	 */
	public function index()
	{
		ee()->load->model('rte_toolset_model');

		$toolsets = ee()->rte_toolset_model->get_toolset_list();

		// prep the Default Toolset dropdown
		$toolset_opts = array();

		$data = array();
		foreach ($toolsets as $t)
		{
			$url = new URL('addons/settings/rte/edit_toolset', ee()->session->session_id());
			$url->setQueryStringVariable('toolset_id', $t['toolset_id']);

			$toolset = array(
				'tool_set' => htmlentities($t['name'], ENT_QUOTES),
				'status' => lang('disabled'),
				array('toolbar_items' => array(
						'edit' => array(
							'href' => $url,
							'title' => lang('edit'),
						)
					)
				),
				array(
					'name' => 'selection[]',
					'value' => $t['toolset_id']
				)
			);

			if ($t['enabled'] == 'y')
			{
				$toolset_opts[$t['toolset_id']] = htmlentities($t['name'], ENT_QUOTES);
				$toolset['status'] = lang('enabled');
			}
			$data[] = $toolset;
		}

		$vars = array(
			'cp_page_title' => lang('rte_module_name') . ' ' . lang('configuration'),
			'save_btn_text' => 'btn_save_settings',
			'save_btn_text_working' => 'btn_save_settings_working',
			'sections' => array(
				array(
					array(
						'title' => 'enable_rte',
						'desc' => 'enable_rte_desc',
						'fields' => array(
							'rte_enabled' => array(
								'type' => 'inline_radio',
								'choices' => array(
									'y' => 'enable',
									'n' => 'disable'
								)
							)
						)
					),
					array(
						'title' => 'default_toolset',
						'desc' => '',
						'fields' => array(
							'rte_default_toolset_id' => array(
								'type' => 'dropdown',
								'choices' => $toolset_opts
							)
						)
					)
				)
			)
		);

		$table = Table::create(array('autosort' => TRUE, 'autosearch' => FALSE, 'limit' => 20));
		$table->setColumns(
			array(
				'tool_set',
				'status',
				'manage' => array(
					'type'	=> Table::COL_TOOLBAR
				),
				array(
					'type'	=> Table::COL_CHECKBOX
				)
			)
		);
		$table->setData($data);

		$vars['table'] = $table->viewData($this->_base_url);
		$vars['base_url'] = $vars['table']['base_url'];
		$vars['base_url']->path = 'addons/settings/rte/prefs_update';

		if ( ! empty($vars['table']['data']))
		{
			// Paginate!
			$pagination = new Pagination(
				$vars['table']['limit'],
				$vars['table']['total_rows'],
				$vars['table']['page']
			);
			$vars['pagination'] = $pagination->cp_links($this->_base_url);
		}

		// return the page
		return ee()->load->view('index', $vars, TRUE);
	}


	// --------------------------------------------------------------------

	/**
	 * Update prefs form action
	 *
	 * @access	public
	 * @return	void
	 */
	public function prefs_update()
	{
		// set up the validation
		ee()->load->library('form_validation');
		ee()->form_validation->set_rules(
			'rte_enabled',
			lang('enabled_question'),
			'required|enum[y,n]'
		);

		ee()->form_validation->set_rules(
			'rte_default_toolset_id',
			lang('default_toolset'),
			'required|is_numeric'
		);

		if (ee()->form_validation->run())
		{
			// update the prefs
			$this->_do_update_prefs();
			ee()->session->set_flashdata('message_success', lang('settings_saved'));
		}
		else
		{
			ee()->session->set_flashdata('message_failure', lang('settings_not_saved'));
		}

		ee()->functions->redirect($this->_base_url);
	}

	// --------------------------------------------------------------------

	/**
	 * Provides New Toolset Screen HTML
	 *
	 * @access	public
	 * @param	int $toolset_id The Toolset ID to be edited (optional)
	 * @return	string The page
	 */
	public function new_toolset()
	{
		return ee()->rte_lib->edit_toolset(0);
	}

	// --------------------------------------------------------------------

	/**
	 * Provides Edit Toolset Screen HTML
	 *
	 * @access	public
	 * @param	int $toolset_id The Toolset ID to be edited (optional)
	 * @return	string The page
	 */
	public function edit_toolset($toolset_id = FALSE)
	{
		$toolset_id = ($toolset_id) ?: ee()->input->get_post('toolset_id');
		return ee()->rte_lib->edit_toolset($toolset_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Saves a toolset
	 *
	 * @access	public
	 * @return	void
	 */
	public function save_toolset()
	{
		ee()->rte_lib->save_toolset();
	}

	// --------------------------------------------------------------------

	/**
	 * Enables or disables a toolset
	 *
	 * @access	public
	 * @return	void
	 */
	public function toggle_toolset()
	{
		ee()->load->model('rte_toolset_model');

		$toolset_id = ee()->input->get_post('toolset_id');
		$enabled = ee()->input->get_post('enabled') != 'n' ? 'y' :'n';

		if (ee()->rte_toolset_model->save_toolset(array('enabled' => $enabled), $toolset_id))
		{
			ee()->session->set_flashdata('message_success', lang('toolset_updated'));
		}
		else
		{
			ee()->session->set_flashdata('message_failure', lang('toolset_update_failed'));
		}

		ee()->functions->redirect($this->_base_url);
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a toolset
	 *
	 * @access	public
	 * @return	void
	 */
	public function delete_toolset()
	{
		ee()->load->model('rte_toolset_model');

		$toolset_id = ee()->input->get_post('toolset_id');

		// Delete
		if (ee()->rte_toolset_model->delete($toolset_id))
		{
			ee()->session->set_flashdata('message_success', lang('toolset_deleted'));

			// If the default toolset was deleted
			if ($toolset_id == ee()->config->item('rte_default_toolset_id'))
			{
				$toolsets = ee()->rte_toolset_model->get_toolset_list();

				// Make the new default toolset the first available
				if ( ! empty($toolsets))
				{
					$default_toolset_pref = array(
						'rte_default_toolset_id' => $toolsets[0]['toolset_id']
					);
				}
				// Or set it to zero if there are no toolsets left
				else
				{
					$default_toolset_pref = array(
						'rte_default_toolset_id' => 0
					);
				}

				ee()->config->update_site_prefs($default_toolset_pref);
			}
		}
		else
		{
			ee()->session->set_flashdata('message_failure', lang('toolset_not_deleted'));
		}

		ee()->functions->redirect($this->_base_url);
	}

	// --------------------------------------------------------------------

	/**
	 * Enables or disables a tool
	 *
	 * @access	public
	 * @return	void
	 */
	public function toggle_tool()
	{
		ee()->load->model('rte_tool_model');

		$tool_id = ee()->input->get_post('tool_id');
		$enabled = ee()->input->get_post('enabled') != 'n' ? 'y' :'n';

		if (ee()->rte_tool_model->save_tool(array('enabled' => $enabled), $tool_id))
		{
			ee()->session->set_flashdata('message_success', lang('tool_updated'));
		}
		else
		{
			ee()->session->set_flashdata('message_failure', lang('tool_update_failed'));
		}

		ee()->functions->redirect($this->_base_url);
	}

	// --------------------------------------------------------------------

	/**
	 * Actual preference-updating code
	 *
	 * @access	private
	 * @return	void
	 */
	private function _do_update_prefs()
	{
		// update the config
		ee()->config->update_site_prefs(array(
			'rte_enabled'				=> ee()->input->get_post('rte_enabled'),
			'rte_default_toolset_id'	=> ee()->input->get_post('rte_default_toolset_id')
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Makes sure users can access a given method
	 *
	 * @access	private
	 * @return	void
	 */
	private function _permissions_check()
	{
		// super admins always can
		$can_access = (ee()->session->userdata('group_id') == '1');

		if ( ! $can_access)
		{
			// get the group_ids with access
			$result = ee()->db->select('module_member_groups.group_id')
				->from('module_member_groups')
				->join('modules', 'modules.module_id = module_member_groups.module_id')
				->where('modules.module_name',$this->name)
				->get();

			if ($result->num_rows())
			{
				foreach ($result->result_array() as $r)
				{
					if (ee()->session->userdata('group_id') == $r['group_id'])
					{
						$can_access = TRUE;
						break;
					}
				}
			}
		}

		if ( ! $can_access)
		{
			show_error(lang('unauthorized_access'));
		}
	}

}
// END CLASS

/* End of file mcp.rte.php */
/* Location: ./system/expressionengine/modules/rte/mcp.rte.php */
