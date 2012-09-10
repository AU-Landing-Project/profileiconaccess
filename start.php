<?php

	/**
	 * Elgg avatar access
	 * 
	 * @package ElggProfile
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd <info@elgg.com>
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.com/
	 */

	function avataraccess_init()
	{
		register_plugin_hook("action", "profile/iconupload", "avataraccess_action_hook");
		register_plugin_hook('entity:icon:url', 'user', 'avataraccess_usericon_acl', 1); 
	}
	
	function avataraccess_action_hook($hook, $entity_type, $returnvalue, $params)
	{
		// Apply access permission here
		$iconaccess = (int)get_input('iconaccess');
		$username = get_input('username');
		
		$user = get_user_by_username($username);
		
		if ($user)
		{
			create_metadata($user->guid, 'iconaccess', $iconaccess, 'integer', $user->guid, $iconaccess);	
		}
		
		
	}
	
	function avataraccess_usericon_acl($hook, $entity_type, $returnvalue, $params)
	{
		global $CONFIG;
		
		if ( ($hook == 'entity:icon:url') && ($params['entity'] instanceof ElggUser))
		{
			$entity = $params['entity'];
			
			// Check access
			
			// See if we can access the metadata
			$metadata = get_metadata_byname($entity->guid, 'iconaccess');
			
			// If we can access the metadata, that must mean that we can see the picture (as they are saved as the same value)
			if ($metadata)
				$hasaccess = true;
					
			// Now handle situations where the information is unsaved.	
			// Private and me
			if (($access == 0) && ($entity->guid == get_loggedin_userid()))	
				$hasaccess = true;
			
			// Admin can see everything
			if (isadminloggedin())
				$hasaccess = true;
			
			// If we don't have access then blank profile picture
			if (!$hasaccess) {
				$type = $entity->type;
				$subtype = get_subtype_from_id($entity->subtype);
				$viewtype = $params['viewtype'];
				$size = $params['size'];
				
				return elgg_view('icon/user/default/'.$size);
			}
		}
	}
	
	register_elgg_event_handler('init','system','avataraccess_init');
?>