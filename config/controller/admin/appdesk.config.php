<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

Nos\I18n::current_dictionary(array('novius_mediamassupload::common', 'nos::common'));

if (!\Nos\User\Permission::check('nos::access', 'novius_mediamassupload')) {
    return array();
}
return array(
    'toolbar' => array(
        'actions' => array(
            'mass_upload' => array(
                'label' => __('Add many files at once'),
                'action' => array(
                    'action' => 'nosTabs',
                    'method' => 'add',
                    'tab' => array(
                        'url' => 'admin/novius_mediamassupload/upload',
                    ),
                ),
                'targets' => array(
                    'toolbar-grid' => true,
                ),
            ),
        ),
    ),
);
