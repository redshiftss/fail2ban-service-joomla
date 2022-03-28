<?php
/**
* Plugin Name: Fail2Ban Joomla
* Plugin URI: 
* Description: Adds fail2ban functionality to Joomla.
* Version: 1.0.0
* Author: Laura Pircalaboiu
* Author URI: https://redshifts.xyz
* License: MIT
*/
// function get_data($ip) {
//     return json_encode(array(
//         'source' => $ip,
//         'service' => 'joomla',
//         'timestamp' => time()
//     ));
// }

defined( '_JEXEC' ) or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

class JoomlaFail2ban extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Load the language file on instantiation
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'<onUserLoginFailure>' => 'authy_failiure',
		];
	}

	/**
	 * This method will be called in the event of a failiure to authenticate.
	 */
	 public function authy_failiure(Event $event)
	 {
        
        if ($ip !== '') {
            $today          = new JDate();
            $todayFormatted = $today->format('Y-m-d');
        }
	}
}
?>
