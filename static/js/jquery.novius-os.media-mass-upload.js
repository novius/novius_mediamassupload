/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */
define(
    [
        'jquery-nos',
        'wijmo.wijprogressbar',
        'jquery-nos-loadspinner'
    ],
    function($) {
        "use strict";

        $.fn.extend({
            nosMediaMassUpload : function(params) {
                params = params || {
                    tabParams: {},
                    saveField: '',
                    maxFileSize: false,
                    texts : {
                        exceedMaxSize: 'The total file size exceeds the limit upload.'
                    }
                };
                return this.each(function() {
                    var $container = $(this).nosFormUI()
                            .nosFormAjax()
                            .nosTabs('update', params.tabParams);
                    var $toolbar = $container.nosToolbar('create');
                    var $progressbar;
                    var $fileInput = $container.find(':file').on('change', function() {
                            progress.validate(this.files);
                        });
                    var currentTab = $container.nosTabs('current');
                    var $save = $container.nosToolbar('add', params.saveField)
                            .filter(':submit')
                            .click(function() {
                                submit();
                            });
                    var progress = {
                            validate: function(files) {
                                var total = 0,
                                    zip = false;
                                if (typeof files !== "undefined") {
                                    for (var i=0, l=files.length; i<l; i++) {
                                        var file = files[i];
                                        total += file.size;

                                        if (typeof FileReader !== "undefined" && file.type === 'application/zip') {
                                            zip = true;
                                        }
                                    }
                                } else {
                                    zip = true;
                                }
                                $container.find('.zip-file')[zip ? 'show' : 'hide']();
                                if (params.maxFileSize && total > params.maxFileSize) {
                                    $.nosNotify(params.texts.exceedMaxSize, 'error');
                                    return false;
                                }
                                return true;
                            },
                            begin: function() {
                                $progressbar = $('<div></div>')
                                    .css({
                                        position: 'absolute',
                                        top: '0px',
                                        left: '0px',
                                        right: '0px',
                                        width: '100%',
                                        borderRadius: 0,
                                        boxSizing: 'content-box',
                                        height: $toolbar.innerHeight(),
                                        opacity: 0.8
                                    })
                                    .insertAfter($toolbar)
                                    .wijprogressbar({
                                        maxValue: 100,
                                        value: 0
                                    });
                                $progressbar.find('.ui-progressbar-value').css('borderRadius', 0);

                                currentTab.tab.addClass('ui-state-processing')
                                    .find('a span.nos-ostabs-icon').each(function() {
                                        var $icon = $(this);
                                        $icon.addClass('ui-state-processing')
                                            .loadspinner({
                                                diameter : $icon.width(),
                                                scaling : true
                                            });
                                    });

                                $save.find('.ui-button-icon-primary').each(function() {
                                    var $icon = $(this);
                                    $icon.css({
                                            backgroundSize: '0%',
                                            textIndent: 0
                                        })
                                        .loadspinner({
                                            diameter : $icon.width(),
                                            scaling : true
                                        });
                                });
                            },
                            progress: function(pourcent) {
                                $progressbar.wijprogressbar('option', 'value', pourcent * 100);
                            },
                            end: function(pourcent) {
                                $progressbar.wijprogressbar('option', 'value', 100);
                                setTimeout(function() {
                                    $progressbar.remove();
                                }, 200);

                                currentTab.tab.removeClass('ui-state-processing')
                                    .find('a span.nos-ostabs-icon')
                                    .removeClass('ui-state-processing')
                                    .loadspinner('destroy');

                                $save.find('.ui-button-icon-primary').each(function() {
                                    var $icon = $(this);
                                    $icon.css({
                                            backgroundSize: 'inherit',
                                            textIndent: 'inherit'
                                        })
                                        .loadspinner('destroy');
                                });
                            }
                        };
                    var submit = function() {
                        progress.begin();
                        if (!$container.valid() || !progress.validate($fileInput[0].files)) {
                            return false;
                        }
                        if (typeof FormData !== "undefined") {
                            var xhr = $.ajaxSettings.xhr();
                            $.ajax({
                                url: $container.attr('action'),
                                data: new FormData($container[0]),
                                cache: false,
                                contentType: false,
                                processData: false,
                                type: 'POST',
                                success: function(json) {
                                    $container.nosAjaxSuccess(json);
                                    progress.end();
                                },
                                error: function(x, e) {
                                    $container.nosAjaxError(x, e);
                                    progress.end();
                                },
                                xhr: function() {
                                    var myXhr = $.ajaxSettings.xhr();
                                    if(myXhr.upload){
                                        myXhr.upload.addEventListener('progress', function (evt) {
                                            progress.progress(evt.loaded / evt.total);
                                        }, false);
                                    }
                                    return myXhr;
                                }
                            });
                        } else {
                            $container.submit();
                        }
                    };

                    if (typeof FormData === "undefined") {
                        $container.bind('ajax_success', function(e, json) {
                            progress.end();
                        });
                    }
                });
            }
        });

        return $;
    });
