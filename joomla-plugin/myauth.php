<?php
/**
 * @package     Joomla.Tutorials
 * @subpackage  Authentication.myauth
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Authentication\Authentication;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\User\User;
use Joomla\Event\SubscriberInterface;

/**
 * Example Authentication Plugin for the Joomla Docs.
 *
 * @since  1.0
 */
class PlgAuthenticationMyauth extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Database object
	 *
	 * @var    \Joomla\Database\DatabaseDriver
	 * @since  1.0
	 */
	protected $db;

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onUserAuthenticate' => 'authenticate',
		];
	}

	/**
	 * This method should handle any authentication and report back to the subject
	 * This example uses simple authentication - it checks if the password is the reverse
	 * of the username (and the user exists in the database).
	 *
	 * @access    public
	 * @param     array     $credentials    Array holding the user credentials ('username' and 'password')
	 * @param     array     $options        Array of extra options
	 * @param     object    $response       Authentication response object
	 *
	 * @return    boolean
	 *
	 * @since 1.0
	 */
	public function authenticate( $credentials, $options, &$response )
	{
		/*
		 * Here you would do whatever you need for an authentication routine with the credentials
		 *
		 * In this example the mixed variable $return would be set to false
		 * if the authentication routine fails or an integer userid of the authenticated
		 * user if the routine passes
		 */
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('id'))
			->from($this->db->quoteName('#__users'))
			->where($this->db->quoteName('username') . ' = :username')
			->bind(':username', $credentials['username']);

		$this->db->setQuery($query);
		$result = $this->db->loadResult();

		if (!$result)
		{
			$response->status = STATUS_FAILURE;
			$response->error_message = 'User does not exist';
		}

		/**
		 * To authenticate, the username must exist in the database, and the password should be equal
		 * to the reverse of the username (so user joeblow would have password wolbeoj)
		 */
		if($result && ($credentials['username'] == strrev( $credentials['password'] )))
		{
			$email = User::getInstance($result); // Bring this in line with the rest of the system
			$response->email = $email->email;
			$response->status = Authentication::STATUS_SUCCESS;
		}
		else
		{
			$response->status = Authentication::STATUS_FAILURE;
			$response->error_message = 'Invalid username and password';
		}
	}
}