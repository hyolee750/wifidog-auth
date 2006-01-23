<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

// +-------------------------------------------------------------------+
// | WiFiDog Authentication Server                                     |
// | =============================                                     |
// |                                                                   |
// | The WiFiDog Authentication Server is part of the WiFiDog captive  |
// | portal suite.                                                     |
// +-------------------------------------------------------------------+
// | PHP version 5 required.                                           |
// +-------------------------------------------------------------------+
// | Homepage:     http://www.wifidog.org/                             |
// | Source Forge: http://sourceforge.net/projects/wifidog/            |
// +-------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or     |
// | modify it under the terms of the GNU General Public License as    |
// | published by the Free Software Foundation; either version 2 of    |
// | the License, or (at your option) any later version.               |
// |                                                                   |
// | This program is distributed in the hope that it will be useful,   |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of    |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the     |
// | GNU General Public License for more details.                      |
// |                                                                   |
// | You should have received a copy of the GNU General Public License |
// | along with this program; if not, contact:                         |
// |                                                                   |
// | Free Software Foundation           Voice:  +1-617-542-5942        |
// | 59 Temple Place - Suite 330        Fax:    +1-617-542-2652        |
// | Boston, MA  02111-1307,  USA       gnu@gnu.org                    |
// |                                                                   |
// +-------------------------------------------------------------------+

/**
 * @package    WiFiDogAuthServer
 * @subpackage Authenticators
 * @author     Benoit Gregoire <bock@step.polymtl.ca>
 * @copyright  2005-2006 Benoit Gregoire, Technologies Coeus inc.
 * @version    Subversion $Id$
 * @link       http://www.wifidog.org/
 */

/**
 * Load include files
 */
require_once('classes/Authenticator.php');
require_once('classes/User.php');

/**
 * Internal wifidog user database authentication source
 *
 * @package    WiFiDogAuthServer
 * @subpackage Authenticators
 * @author     Benoit Gregoire <bock@step.polymtl.ca>
 * @copyright  2005-2006 Benoit Gregoire, Technologies Coeus inc.
 */
class AuthenticatorLocalUser extends Authenticator
{

    function __construct($account_orgin)
    {
        parent :: __construct($account_orgin);
    }

    /**
     * Callback function used to discriminate Local User account origins
     *
     * @param array $account_origin
     * @return boolean True if the parameter refers to a Local User account
     * origin
     */
    private static function isLocalUserAccountOrigin($account_origin)
    {
        return get_class($account_origin['authenticator']) == "AuthenticatorLocalUser";
    }

    /** Attempts to login a user against the authentication source.  If successfull, returns a User object
     *
     * @param string $username A valid identifying token for the source.  Not necessarily unique.  For local user, bots username and email are valid.
     * @param string $password Clear text password.
     * @return The actual User object if sogin was successfull, false otherwise.
     */
    function login($username, $password, & $errmsg = null)
    {
        global $db;
        $security = new Security();
        $retval = false;
        $username = $db->escapeString($username);
        $password = $db->escapeString($password);
        $password_hash = User :: passwordHash($_REQUEST['password']);

        $sql = "SELECT user_id FROM users WHERE (username='$username' OR email='$username') AND account_origin='".$this->getNetwork()->getId()."' AND pass='$password_hash'";
        $db->execSqlUniqueRes($sql, $user_info, false);

        if ($user_info != null)
        {
            $user = new User($user_info['user_id']);
            if ($user->isUserValid($errmsg))
            {
                $retval = & $user;
                User::setCurrentUser($user);
                $errmsg = _("Login successfull");
            }
            else
            {
                $retval = false;
                //Reason for refusal is already in $errmsg
            }
        }
        else
        {
            $user_info = null;
            /* This is only used to discriminate if the problem was a non-existent user of a wrong password. */
            $db->execSqlUniqueRes("SELECT * FROM users WHERE (username='$username' OR email='$username') AND account_origin='".$this->getNetwork()->getId()."'", $user_info, false);
            if ($user_info == null)
            {
                $errmsg = _('Unknown username or email');
            }
            else
            {
                $errmsg = _('Incorrect password (Maybe you have CAPS LOCK on?)');
            }
            $retval = false;
        }
        return $retval;
    }

    /**
     * Start accounting traffic for the user
     */
    function acctStart($conn_id, & $errmsg = null)
    {
        parent :: acctStart($conn_id);
        return true;
    }

    /**
     * Update traffic counters
     */
    function acctUpdate($conn_id, $incoming, $outgoing, & $errmsg = null)
    {
        // Just call the generic counters update
        parent :: acctUpdate($conn_id, $incoming, $outgoing);
        return true;
    }

    /**
     * Final update and stop accounting
     */
    function acctStop($conn_id, & $errmsg = null)
    {
        parent :: acctStop($conn_id);
        return true;
    }

    /**
     * The basic AuthenticatorLocalUser allows user signup
     */
    function isRegistrationPermitted()
    {
        return true;
    }

}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */

?>