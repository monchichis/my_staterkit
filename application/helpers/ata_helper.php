<?php

function is_logged_in()
{
	$ci = get_instance();
	if (!$ci->session->userdata('email')) {
		redirect('auth');
	}
}

function is_admin()
{
	$ci = get_instance();
	
	// Load database if not already loaded
	if (!isset($ci->db)) {
		$ci->load->database();
	}
	
	// Check if user has Super Admin role or any admin-related permissions
	// Super Admin should always pass this check
	if (has_role('Super Admin')) {
		return true;
	}
	
	// For other users, check if they have any administrative permissions
	// This allows for flexible admin roles beyond just "Super Admin"
	$admin_permissions = [
		'user.create', 'user.edit', 'user.delete',
		'rbac.roles.manage', 'rbac.permissions.manage'
	];
	
	if (can_any($admin_permissions)) {
		return true;
	}
	
	// If no admin permissions, block access
	redirect('auth/blocked');
}

function is_user()
{
	$ci = get_instance();
	
	// Load database if not already loaded
	if (!isset($ci->db)) {
		$ci->load->database();
	}
	
	// Check if user is logged in but NOT an admin
	// Regular users should have basic permissions only
	if (!has_role('Super Admin') && !can('user.create')) {
		return true;
	}
	
	// If user has admin permissions, they shouldn't access user-only pages
	redirect('auth/blocked');
}

function is_kepsek()
{
	$ci = get_instance();
	
	// Load database if not already loaded
	if (!isset($ci->db)) {
		$ci->load->database();
	}
	
	// Check for Manager/Kepsek role
	if (has_role('Manager') || has_role('Kadis')) {
		return true;
	}
	
	redirect('auth/blocked');
}
