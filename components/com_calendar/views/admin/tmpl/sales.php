<?php defined('_JEXEC') or die('Restricted access'); ?>

<script>
    jQuery(function($){

        /*************************************************
         * Sale edit and save
         *************************************************/

        $('.sale-edit').on('click', function(event) {
            var sale_id = $(this).data('id');
            var sale_row = $('tr[id="sale-'+sale_id+'"]');
            var save_link = sale_row.find('.sale-save');
            var edit_link = sale_row.find('.sale-edit');

            edit_link.hide();
            save_link.show();

            $.each(sale_row.find('td'), function() {
                var td = $(this);
                var field_name = $(this).data('name');
                var field_value = $(this).data('value');
                if (field_name != undefined) {
                    td.html('<input value="'+field_value+'" name="'+field_name+'"/>');
                }
            });

            sale_row.find('td[data-name="valid_from"] input').datepicker({ dateFormat: 'yy-mm-dd' });
            sale_row.find('td[data-name="valid_till"] input').datepicker({ dateFormat: 'yy-mm-dd' });
        });

        $('.sale-save').on('click', function() {
            var sale_id = $(this).data('id');
            var sale_row = $('tr[id="sale-'+sale_id+'"]');

            $.ajax({
                type: "POST",
                url: "/index.php?option=com_calendar&task=admin.updateCalendarSale&tmpl=component",
                data: {
                    'calendar_type' : sale_id,
                    'discount'      : sale_row.find('input[name="discount"]').val(),
                    'valid_from'    : sale_row.find('input[name="valid_from"]').val(),
                    'valid_till'    : sale_row.find('input[name="valid_till"]').val()
                },
                dataType: 'html'
            }).error(function(jqXHR, textStatus, errorThrown) {
                console.log('Failed');
            }).success(function(response) {
                $.each(sale_row.find('td'), function() {
                    var save_link = sale_row.find('.sale-save');
                    var edit_link = sale_row.find('.sale-edit');

                    edit_link.show();
                    save_link.hide();

                    var td = $(this);
                    var field_name = $(this).data('name');
                    if (field_name != undefined) {
                        var newValue = $(this).find('input[name="'+field_name+'"]').val();
                        td.attr('data-value', newValue);
                        td.html(newValue);
                    }
                });
            });
        });
    });
</script>

<style>
    .sale-save, .sale-edit {
        color: rgba(230, 132, 14, 0.8);
        cursor: pointer;
    }

    .sale-save:hover, .sale-edit:hover {
        color: rgba(230, 132, 14, 1);
    }

    .sale-save {
        display: none;
    }

    input {
        width: 80px;
        height: 20px;
    }
</style>

<div id="calendar-sales">

    <div class="row">
        <div class="col-xs-12">
            <div class="content-box box-orange">
                <div class="content-header">
                    <h2>Výpredaje</h2>
                    <span class="pull-right">
                        <a href="/index.php/component/calendar/?view=admin&layout=orders" class="btn-cal btn-orange">
                            Spať na zoznam objednávok
                        </a>
                    </span>
                </div>
                <div class="content-body no-padding">
                    <table width="100%">
                        <thead>
                        <tr>
                            <td width="20%"><strong>Typ kalendára</strong></td>
                            <td width="20%"><strong>Zľava %</strong></td>
                            <td width="20%"><strong>Platný od</strong></td>
                            <td width="20%"><strong>Platný do</strong></td>
                            <td width="20%"><strong>Upraviť</strong></td>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach ($this->data['sales'] as $calendar_type => $sale): ?>
                            <tr id="sale-<?php echo $calendar_type; ?>">
                                <td>
                                    <?php echo $calendar_type; ?>
                                </td>
                                <td data-name="discount" data-value="<?php echo $sale['percentSale']; ?>">
                                    <?php echo $sale['percentSale']; ?>
                                </td>
                                <td data-name="valid_from" data-value="<?php echo $sale['validFrom']; ?>">
                                    <?php echo $sale['validFrom']; ?>
                                </td>
                                <td data-name="valid_till" data-value="<?php echo $sale['validTo']; ?>">
                                    <?php echo $sale['validTo']; ?>
                                </td>

                                <td>
                                    <span class="orange sale-edit" data-id="<?php echo $calendar_type; ?>">
                                        Upraviť
                                    </span>
                                    <span class="orange sale-save" data-id="<?php echo $calendar_type; ?>">
                                        Uložiť
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>