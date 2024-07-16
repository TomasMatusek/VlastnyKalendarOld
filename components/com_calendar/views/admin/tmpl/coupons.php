<?php defined('_JEXEC') or die('Restricted access'); ?>

<script>
    jQuery(function($){

        /*************************************************
         * Coupon edit and save
         *************************************************/

        $('.coupon-edit').on('click', function(event) {
            var coupon_id = $(this).data('id');
            var coupon_row = $('tr[id="coupon-'+coupon_id+'"]');
            var save_link = coupon_row.find('.coupon-save');
            var edit_link = coupon_row.find('.coupon-edit');

            edit_link.hide();
            save_link.show();

            $.each(coupon_row.find('td'), function() {
                var td = $(this);
                var field_name = $(this).data('name');
                var field_value = $(this).data('value');
                if (field_name != undefined) {
                    td.html('<input value="'+field_value+'" name="'+field_name+'"/>');
                }
            });

            coupon_row.find('td[data-name="valid_from"] input').datepicker({ dateFormat: 'yy-mm-dd' });
            coupon_row.find('td[data-name="valid_till"] input').datepicker({ dateFormat: 'yy-mm-dd' });
        });

        $('.coupon-save').on('click', function() {
            var coupon_id = $(this).data('id');
            var coupon_row = $('tr[id="coupon-'+coupon_id+'"]');

            $.ajax({
                type: "POST",
                url: "/index.php?option=com_calendar&task=admin.updateCoupon&tmpl=component",
                data: {
                    'coupon_id'  : coupon_id,
                    'coupon_code': coupon_row.find('input[name="coupon_code"]').val(),
                    'discount'   : coupon_row.find('input[name="discount"]').val(),
                    'unlimited'  : coupon_row.find('input[name="unlimited"]').val(),
                    'valid_from' : coupon_row.find('input[name="valid_from"]').val(),
                    'valid_till' : coupon_row.find('input[name="valid_till"]').val(),
                    'category'   : coupon_row.find('input[name="category"]').val(),
                    'name'       : coupon_row.find('input[name="name"]').val()
                },
                dataType: 'html'
            }).error(function(jqXHR, textStatus, errorThrown) {
                console.log('Faild');
            }).success(function(response) {
                $.each(coupon_row.find('td'), function() {
                    var save_link = coupon_row.find('.coupon-save');
                    var edit_link = coupon_row.find('.coupon-edit');

                    edit_link.show();
                    save_link.hide();

                    var td = $(this);
                    var field_name = td.data('name');
                    if (field_name != undefined) {
                        var newValue = td.find('input[name="'+field_name+'"]').val();
                        td.attr('data-value', newValue);
                        td.html(newValue);
                    }
                });
            });
        });

        /*************************************************
         * Coupon delete
         *************************************************/

        $('.coupon-delete').on('click', function() {
            var coupon_id = $(this).data('id');

            $.ajax({
                type: "POST",
                url: "/index.php?option=com_calendar&task=admin.deleteCoupon&tmpl=component",
                data: {
                    'coupon_id' : $(this).data('id')
                },
                dataType: 'html'
            }).error(function(jqXHR, textStatus, errorThrown) {
                console.log('Faild');
            }).success(function(response) {
                var coupon_row = $('tr[id="coupon-'+coupon_id+'"]');
                coupon_row.remove();
            });
        });

        /*************************************************
         * Coupon create new
         *************************************************/

        $('.coupon-create').on('click', function() {
            var coupon_row = $('tr[id="coupon-create"]');

            var coupon_code = coupon_row.find('input[name="coupon_code"]');
            var discount    = coupon_row.find('input[name="discount"]');
            var unlimited   = coupon_row.find('input[name="unlimited"]');
            var valid_from  = coupon_row.find('input[name="valid_from"]');
            var valid_till  = coupon_row.find('input[name="valid_till"]');
            var category    = coupon_row.find('input[name="category"]');
            var name        = coupon_row.find('input[name="name"]');

            $.ajax({
                type: "POST",
                url: "/index.php?option=com_calendar&task=admin.createCoupon&tmpl=component",
                data: {
                    'coupon_code': coupon_code.val(),
                    'discount'   : discount.val(),
                    'unlimited'  : unlimited.val(),
                    'valid_from' : valid_from.val(),
                    'valid_till' : valid_till.val(),
                    'category'   : category.val(),
                    'name'       : name.val()
                },
                dataType: 'html'
            }).error(function(jqXHR, textStatus, errorThrown) {
                console.log('Faild');
            }).success(function(data) {
                var parsed_data = JSON.parse(data);
                var id = parseInt(parsed_data['coupon_id']);
                if (id > 0) {
                    var html = "";
                    html += "<tr>";
                        html += "<td>"+id+"</td>"
                        html += "<td data-name='coupon_code' data-value='"+coupon_code.val()+"'>"+coupon_code.val()+"</td>";
                        html += "<td data-name='discount' data-value='"+discount.val()+"'>"+discount.val()+"</td>";
                        html += "<td data-name='unlimited' data-value='"+unlimited.val()+"'>"+unlimited.val()+"</td>";
                        html += "<td data-name='valid_from' data-value='"+valid_from.val()+"'>"+valid_from.val()+"</td>";
                        html += "<td data-name='valid_till' data-value='"+valid_till.val()+"'>"+valid_till.val()+"</td>";
                        html += "<td data-name='category' data-value='"+category.val()+"'>"+category.val()+"</td>";
                        html += "<td data-name='name' data-value='"+name.val()+"'>"+name.val()+"</td>";
                        html += "<td>";
                            // html += "<span class='orange coupon-edit' data-id='"+id+"'>Upraviť</span>";
                            // html += "<span class='orange coupon-save' data-id='"+id+"'>Uložiť</span>";
                        html += "</td>";
                        html += "<td>";
                            // html += "<span class='orange coupon-delete' data-id='"+id+"'>Zmazať</span>";
                        html += "</td>";
                    html += "</tr>";
                    $('#calendar-coupons table tbody tr:first').after(html);

                    $('#coupon-create input[name="coupon_code"]').val('')
                }
            });
        });

        var coupon_create_row = $('#coupon-create');
        coupon_create_row.find('input[name="valid_from"]').datepicker({ dateFormat: 'yy-mm-dd' });
        coupon_create_row.find('input[name="valid_till"]').datepicker({ dateFormat: 'yy-mm-dd' });
    });
</script>

<style>
    .coupon-save, .coupon-edit, .coupon-create, .coupon-delete {
        color: rgba(230, 132, 14, 0.8);
        cursor: pointer;
    }

    .coupon-save:hover, .coupon-edit:hover, .coupon-create:hover, .coupon-delete:hover {
        color: rgba(230, 132, 14, 1);
    }

    .coupon-save {
        display: none;
    }

    input {
        width: 80px;
        height: 20px;
    }
</style>

<div id="calendar-coupons">

    <div class="row">
        <div class="col-xs-12">
            <div class="content-box box-orange">
                <div class="content-header">
                    <h2>Zľavové kupóny</h2>
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
                                <td width="6%"><strong>ID</strong></td>
                                <td width="11%"><strong>Kód</strong></td>
                                <td width="11%"><strong>Zľava</strong></td>
                                <td width="11%"><strong>Neobmedzený</strong></td>
                                <td width="11%"><strong>Platný od</strong></td>
                                <td width="11%"><strong>Platný do</strong></td>
                                <td width="11%"><strong>Category</strong></td>
                                <td width="11%"><strong>Popis</strong></td>
                                <td width="8%"></td>
                                <td width="8%"></td>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- New coupon -->
                            <tr id="coupon-create">
                                <td>
                                    -
                                </td>
                                <td>
                                    <input name="coupon_code" value=""/>
                                </td>
                                <td>
                                    <input name="discount" value="10"/>
                                </td>
                                <td>
                                    <input name="unlimited" value="0"/>
                                </td>
                                <td>
                                    <input name="valid_from" value="0000-00-00"/>
                                </td>
                                <td>
                                    <input name="valid_till" value="0000-00-00"/>
                                </td>
                                <td>
                                    <input name="category" value=""/>
                                </td>
                                <td>
                                    <input name="name" value=""/>
                                </td>
                                <td colspan="2">
                                    <span class="orange coupon-create" href="">
                                        Pridať nový kupón
                                    </span>
                                </td>
                            </tr>

                            <!-- Existing coupons -->
                            <?php foreach ($this->data['coupons'] as $coupon): ?>
                                <tr id="coupon-<?php echo $coupon['id']; ?>">
                                    <td>
                                        <?php echo $coupon['id']; ?>
                                    </td>
                                    <td data-name="coupon_code" data-value="<?php echo $coupon['coupon_code']; ?>">
                                        <?php echo $coupon['coupon_code']; ?>
                                    </td>
                                    <td data-name="discount" data-value="<?php echo $coupon['discount']; ?>">
                                        <?php echo $coupon['discount']; ?>
                                    </td>
                                    <td data-name="unlimited" data-value="<?php echo $coupon['unlimited']; ?>">
                                        <?php echo $coupon['unlimited'] == 1 ? 'Áno' : 'Nie'; ?>
                                    </td>
                                    <td data-name="valid_from" data-value="<?php echo $coupon['valid_from']; ?>">
                                        <?php echo $coupon['valid_from']; ?>
                                    </td>
                                    <td data-name="valid_till" data-value="<?php echo $coupon['valid_till']; ?>">
                                        <?php echo $coupon['valid_till']; ?>
                                    </td>
                                    <td data-name="category" data-value="<?php echo $coupon['category']; ?>">
                                        <?php echo $coupon['category']; ?>
                                    </td>
                                    <td data-name="name" data-value="<?php echo $coupon['name']; ?>">
                                        <?php echo $coupon['name']; ?>
                                    </td>
                                    <td>
                                        <!--
                                        <span class="orange coupon-edit" data-id="<?php echo $coupon['id']; ?>">
                                            Upraviť
                                        </span>
                                        <span class="orange coupon-save" data-id="<?php echo $coupon['id']; ?>">
                                            Uložiť
                                        </span>
                                        -->
                                    </td>
                                    <td>
                                        <!--
                                        <span class="orange coupon-delete" data-id="<?php echo $coupon['id']; ?>">
                                            Zmazať
                                        </span>
                                        -->
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