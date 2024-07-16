<?php defined('_JEXEC') or die; ?>

<!-- Force latest IE rendering engine or ChromeFrame if installed -->
<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
<meta charset="utf-8">
<!-- Bootstrap CSS Toolkit styles -->
<!-- Bootstrap CSS fixes for IE6 -->
<!--[if lt IE 7]><link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap-ie6.min.css"><![endif]-->
<!-- Bootstrap Image Gallery styles -->
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<!-- Shim to make HTML5 elements usable in older Internet Explorer versions -->
<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<!-- The table listing the files available for upload/download -->

<div id="calendar-upload">
    <form id="fileupload" action="/calendar/" method="POST" enctype="multipart/form-data">
        <div class="container uploader">
            <div class="content-box box-orange">
                <div class="content-header">
                    <h2>Vaša galéria obrázkov</h2>
                </div>
                <div class="content-body">
                    <div class="row fileupload-buttonbar">

                        <div class="col-xs-3">
                            <a href="/index.php?option=com_calendar&view=calendar&layout=edit" class="btn-cal btn-orange">Návrat k úprave kalendára</a>
                        </div>

                        <div class="col-xs-5">
                            <div class="fileupload-progress fade">
                                <div class="progress-extended">&nbsp;</div>
                            </div>
                        </div>

                        <div class="col-xs-4">
                            <span class="btn-cal btn-orange fileinput-button pull-right">
                                <span>Pridať fotografie</span>
                                <input type="file" name="files[]" multiple>
                            </span>

                            <!--
                            <button type="reset" class="btn-cal btn-orange cancel pull-right" style="margin-right: 10px;">
                                <span>Zrušiť upload</span>
                            </button>
                            -->
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <!-- The global progress information -->
                            <div class="fileupload-progress fade">
                                <!-- The global progress bar -->
                                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                    <div class="bar" style="width:0%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> <!-- ./content-body -->
            </div> <!-- ./content-box -->

            <div class="row">

                <script id="template-download" type="text/x-tmpl">
                    {% for (var i=0, file; file=o.files[i]; i++) { %}
                    <div class="col-md-3 pb-30 template-download fade">
                        <div class="sppb-addon sppb-addon-pricing-table sppb-text-center">
                            <div style="" class="sppb-pricing-box ">
                                <div class="sppb-pricing-header">
                                    <div class="preview_head">
                                        <div class="preview-overlay"></div>
                                        <div class="preview" style="height: 180px; overflow: hidden;">
                                            <a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}">
                                                <img src="{%=file.thumbnail_url%}" style="{%= (file.width >= file.height) ? 'width' : 'height' %}: 100%; margin:auto;">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="sppb-pricing-title">Obrázok</div>
                                </div>
                                <div class="sppb-pricing-features">
                                    <ul style="text-align: center;">
                                        <li><strong>{%=file.width%} x {%=file.height%} PX</strong></li>
                                        <li><strong>{%=o.formatFileSize(file.size)%}</strong></li>
                                    </ul>
                                </div>
                                <div class="sppb-pricing-footer delete">
                                    <button class="sppb-btn sppb-btn-default sppb-btn sppb-btn-block" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">
                                        Zmazať
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {% } %}
                </script>

                <script id="template-upload" type="text/x-tmpl">
                {% for (var i=0, file; file=o.files[i]; i++) { %}
                    <div class="col-md-3 pb-30 template-upload">
                        <div class="sppb-addon sppb-addon-pricing-table sppb-text-center">
                            <div style="" class="sppb-pricing-box ">
                                <div class="sppb-pricing-header">
                                    <div class="preview_head">
                                        <div class="preview-overlay"></div>
                                        <div class="preview" style="height: 180px; overflow: hidden;">
                                            <a href="#" title="" rel="gallery" download=""><img src="/components/com_calendar/assets/img/placeholder.jpg" style="width: 100%;"></a>
                                        </div>
                                    </div>
                                    {% if (file.error) { %}
                                        <div class="sppb-pricing-title">Chyba</div>
                                    {% } else if (o.files.valid && !i) { %}
                                        <div class="image-upload progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                            <div class="bar" style="width:0%;"></div>
                                        </div>
                                    {% } %}
                                </div>
                                <div class="sppb-pricing-features">
                                    <ul style="text-align: center;">
                                        <li><strong>-</strong></li>
                                        <li><strong>-</strong></li>
                                    </ul>
                                </div>
                                <div class="sppb-pricing-footer cancel">
                                    {% if (!i) { %}
                                        <button class="sppb-btn sppb-btn-default sppb-btn sppb-btn-block">
                                            Zrušiť
                                        </button>
                                    {% } %}
                                </div>
                            </div>
                        </div>
                    </div>
                {% } %}
                </script>

            </div>
        </div>

        <!-- The loading indicator is shown during file processing -->
        <div class="fileupload-loading"></div>

        <!-- The table listing the files available for upload/download -->
        <div class="files"></div>

    </form>
</div>

<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td>
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td class="start">{% if (!o.options.autoUpload) { %}
                <button class="button blue">
                    <i class="icon-upload icon-white"></i>
                    <span>{%=locale.fileupload.start%}</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button class="button yellow">
                <i class="icon-ban-circle icon-white"></i>
                <span>{%=locale.fileupload.cancel%}</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>

<!-- The template to display files available for download-->
<script id="template-download" type="text/x-tmpl">
{% var row_index=0; %}
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade" data-index="{%=row_index%}">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else { %}
						<td class="check"><input type="checkbox" class="toggle" name="delete" value="1"></td>
            <td class="preview">
						{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" rel="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        <td class="delete">
            <button class="button red" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">
                <i class="icon-trash icon-white"></i>
                <span>{%=locale.fileupload.destroy%}</span>
            </button>
        </td>
    </tr>
    {% if (row_index == 4) row_index = 0; %}
    {% row_index++; %}
{% } %}
</script>

<script src="/components/com_calendar/assets/js/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="/components/com_calendar/assets/js/tmpl_render.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="/components/com_calendar/assets/js/load-image.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="/components/com_calendar/assets/js/canvas-to-blob.js"></script>
<!-- Bootstrap JS and Bootstrap Image Gallery are not required, but included for the demo -->
<script src="/components/com_calendar/assets/js/bootstrap.js"></script>
<script src="/components/com_calendar/assets/js/bootstrap-image-gallery.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="/components/com_calendar/assets/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="/components/com_calendar/assets/js/jquery.fileupload.js"></script>
<!-- The File Upload file processing plugin -->
<script src="/components/com_calendar/assets/js/jquery.fileupload-fp.js"></script>
<!-- The File Upload user interface plugin -->
<script src="/components/com_calendar/assets/js/jquery.fileupload-ui.js"></script>
<!-- The localization script -->
<script src="/components/com_calendar/assets/js/locale.js"></script>
<!-- The main application script -->
<script src="/components/com_calendar/assets/js/main.js"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
<!--[if gte IE 8]><script src="<?php echo CAL_COMPONENT_WEB.'assets/';?>js/cors/jquery.xdr-transport.js"></script><![endif]-->