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
 * @author     Benoit Gregoire <bock@step.polymtl.ca>
 * @copyright  2005 Benoit Gregoire <bock@step.polymtl.ca> - Technologies Coeus
 * inc.
 * @version    CVS: $Id$
 * @link       http://sourceforge.net/projects/wifidog/
 */

require_once('classes/Session.php');

/**
 */
class Security {
  private $session;

  function Security() {
    $this->session = new Session();
  }

  function requireAdmin() {
       $current_user = User::getCurrentUser();
     if (!$current_user || ($current_user && !User::getCurrentUser()->isSuperAdmin()))
      {
      echo '<p class=error>'._("You do not have administrator privileges")."</p>\n";
      exit;
    } else {
      /* Access granted */
      //echo '<p class=error>'._("Access granted")."</p>\n";
    }

  }

  function requireOwner($node_id) {
      // If the user has super power let him in !
      $current_user = User::getCurrentUser();
      if ($current_user && User::getCurrentUser()->isSuperAdmin())
          return true;

      $node=Node::getObject($node_id);

    if(!$node->isOwner($current_user)) {
        echo '<p class=error>'._("You do not have owner privileges")."</p>\n";
        exit;
    } else {
      /* Access granted */
      //echo '<p class=error>'._("Access granted")."</p>\n";
    }
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