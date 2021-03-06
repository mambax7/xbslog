<?php declare(strict_types=1);

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Installation callback functions
 *
 * Functions called during the module installation, update or delete process
 *
 * @copyright     Ashley Kitson
 * @copyright     XOOPS Project https://xoops.org/
 * @license       GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author        Ashley Kitson http://akitson.bbcb.co.uk
 * @author        XOOPS Development Team
 * @package       XBSLOG
 * @subpackage    Installation
 * @access        private
 * @version       1
 */

/**
 * Must have module defines
 */
require_once XOOPS_ROOT_PATH . '/modules/xbslog/include/defines.php';

/**
 * Function: Module Update callback
 *
 * Called during update process to alter data table structure or values in tables
 *
 * @param xoopsModule &$module     handle to the module object being updated
 * @param int          $oldVersion version * 100 prior to update
 * @return bool True if successful else False
 * @version 1
 */
function xoops_module_update_xbs_log(&$module, $oldVersion)
{
    global $xoopsDB;

    /*
    if ($oldVersion < 110) { //upgrading from version 1.00
    }
    */

    //notify xoobs.net of update

    xbsNotify('Updated');

    return true;
}//end function

/**
 * admin make a directory
 *
 * Thanks to the NewBB2 Development Team
 * http://www.php.net/manual/en/function.mkdir.php
 * saint at corenova.com
 * bart at cdasites dot com
 *
 * @param string $target directory to make
 *
 * @return bool
 */
function admin_mkdir($target)
{
    if (is_dir($target) || empty($target)) {
        return true; // best case check first
    }

    if (is_dir($target) && !is_dir($target)) {
        return false;
    }

    if (admin_mkdir(mb_substr($target, 0, mb_strrpos($target, '/')))) {
        if (!is_dir($target)) {
            $res = mkdir($target, 0700); // crawl back up & create dir tree

            admin_chmod($target);

            return $res;
        }
    }

    return is_dir($target);
}

/**
 * Thanks to the NewBB2 Development Team
 *
 * @param string     $target directory to set permission on
 * @param int|\octal $mode   directory permission
 *
 * @return bool
 */
function admin_chmod($target, $mode = 0700)
{
    return @chmod($target, $mode);
}

/**
 * Function: Module Install callback
 *
 * @param xoopsModule &$module Handle to module object being installed
 * @return bool True if successful else False
 * @version 1
 */
function xoops_module_install_xbs_log($module)
{
    #global $xoopsDB;

    //create the log directory

    if (!admin_mkdir(XBSLOG_LOG_PATH)) {
        $module->setErrors('Unable to create logging directory: ' . XBSLOG_LOG_PATH . ' Please create it yourself');

        return false;
    }

    //notify xoobs.net of install

    xbsNotify('Installed');

    return true;
}//end function

/**
 * Function: Module Pre Install callback
 *
 * This will only work for Xoops at version 2.2+
 *
 * @param xoopsModule &$module Handle to module object being installed
 * @return bool True if successful else False
 * @version 1
 */
function xoops_module_pre_install_xbs_log(&$module)
{
    #global $xoopsDB;

    return true;
}//end function

/**
 * remove and empty a directory
 *
 * Contact information:
 *   Dao Gottwald  <dao at design-noir.de>
 *   Herltestraße 12
 *   D-01307, Germany
 *
 * @param $dir
 * @return bool
 * @version  1.0
 */
function rmdirr($dir)
{
    if (is_dir($dir)) {
        if (cleardir($dir)) {
            return rmdir($dir);
        }

        return false;
    }

    return unlink($dir);
}

/**
 * empty a directory
 *
 * Contact information:
 *   Dao Gottwald  <dao at design-noir.de>
 *   Herltestraße 12
 *   D-01307, Germany
 *
 * @param $dir
 * @return bool
 * @version  1.0
 */
function cleardir($dir)
{
    if (!($dir = dir($dir))) {
        return false;
    }

    while (false !== $item = $dir->read()) {
        if ('.' != $item && '..' != $item && !rmdirr($dir->path . DIRECTORY_SEPARATOR . $item)) {
            $dir->close();

            return false;
        }
    }

    $dir->close();

    return true;
}

/**
 * Function: Module deletion callback
 *
 * XBSLOG tables are deleted via the Xoops uninstaller
 *
 * @param xoopsModule &$module Handle to module object being installed
 * @return bool True if successful else False
 * @version 1
 */
function xoops_module_uninstall_xbs_log($module)
{
    #global $xoopsDB;

    //Notify Xoobs.net of uninstall

    xbsNotify('Uninstall');

    //remove the log directory

    $cfg = getXBSLOGModConfigs();

    $logpath = $cfg['def_logpath'] ?? XBSLOG_LOG_PATH;

    if (rmdirr($logpath)) {
        return true;
    }

    $module->setErrors('Unable to remove logging directory: ' . XBSLOG_LOG_PATH);

    return false;
}//end function
