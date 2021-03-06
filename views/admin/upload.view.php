<?php
/**
 * Media Mass Upload is an application for Novius OS for upload multiple files in media center
 *
 * @copyright  2013 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link https://github.com/novius/novius_mediamassupload
 */

Nos\I18n::current_dictionary(array('novius_mediamassupload::default', 'nos::common'));

$uniqid = uniqid('id_');

$fields = array(
    'media[]' => array(
        'form' => array(
            'type' => 'file',
            'multiple' => true,
        ),
        'validation' => array(
            'required',
        ),
        'label' => __('Files from your hard drive:'),
    ),
    'unzip' => array(
        'form' => array(
            'type' => 'select',
            'options' => array(
                'unzip' => __('Unzip (both files and folders)'),
                'leave' => __('Leave unzipped'),
            ),
        ),
        'label' => __('What to do with ZIP files:'),
    ),
    'media_folder_id' => array(
        'renderer' =>  'Nos\Media\Renderer_Folder',
        'form' => array(
            'type'  => 'hidden',
        ),
        'label' => __('Select a folder where to put your media files:'),
    ),
    'save' => array(
        'label' => '',
        'form' => array(
            'type' => 'submit',
            'tag' => 'button',
            'value' => __('Add'),
            //@todo : remove 'primary'.  Only for chiba 1 compatibility.
            'class' => 'ui-priority-primary primary',
            'data-icon' => 'check',
        ),
    ),
);
$fieldset = \Fieldset::build_from_config($fields);
$form_attributes = $fieldset->get_config('form_attributes', array());
$form_attributes['enctype'] = 'multipart/form-data';
$fieldset->set_config('form_attributes', $form_attributes);
$fieldset->js_validation();

echo $fieldset->open('admin/novius_mediamassupload/upload/save');
$fieldset->form()->set_config('field_template', '{field}');
echo $fieldset->build_hidden_fields();

?>
<div class="page line mediamassupload">
    <div class="col c1"></div>
    <div class="col c10">
        <div class="line" style="margin-bottom:1em;">
            <table class="fieldset standalone">
                <tr>
                    <th></th>
                    <td>
                        <p><strong><?= __('Hold the Ctrl/Cmd key to select multiple files.') ?></strong></p>
                    </td>
                </tr>
                <tr class="title">
                    <th><?= $fieldset->field('media[]')->label ?></th>
                    <td><?= $fieldset->field('media[]')->build() ?></td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <p><em><?= strtr(__('Total files size must not exceed {{size}}.'), array('{{size}}' => ini_get('upload_max_filesize'))) ?> <?= strtr(__('What’s more these file types are not allowed: {{extensions}}.'), array('{{extensions}}' => implode(', ', \Config::get('novius-os.upload.disabled_extensions', array('php'))))) ?></em></p>
                    </td>
                </tr>
                <tr>
                    <th><?= $fieldset->field('unzip')->label ?></th>
                    <td><?= $fieldset->field('unzip')->build() ?></td>
                </tr>
                <tr>
                    <th><?= $fieldset->field('media_folder_id')->label ?></th>
                    <td><?= $fieldset->field('media_folder_id')->build() ?></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col c1"></div>
</div>
<?= $fieldset->close() ?>

<?php
    $json = array(
        'tabParams' => $tab_params,
        'saveField' => (string) \View::forge('form/layout_save', array('save_field' => $fieldset->field('save')), false),
        'maxFileSize' => \Num::bytes(ini_get('upload_max_filesize')),
        'texts' => array(
            'exceedMaxSize' => strtr(
                __('Total files size exceeds the upload limit {{size}}.'),
                array('{{size}}' => ini_get('upload_max_filesize'))
            ),
        ),
    );
?>
<script type="text/javascript">
require(
    [
        'static/apps/novius_mediamassupload/js/jquery.novius-os.media-mass-upload',
        //@todo : to remove.  Only for chiba 1 compatibility.
        'jquery-ui.progressbar',
        'link!static/apps/novius_mediamassupload/css/admin.css'
    ],
    function($) {
        $(function() {
            $('#<?= $fieldset->form()->get_attribute('id') ?>')
                .nosMediaMassUpload(<?= \Format::forge($json)->to_json() ?>);
        });
    });
</script>
