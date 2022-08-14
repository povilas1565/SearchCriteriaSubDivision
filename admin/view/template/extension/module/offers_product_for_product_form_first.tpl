<div class="form-group">
    <label class="col-sm-2 control-label"
           for="input-master-product"><span data-toggle="tooltip"
                                            title="<?php echo $entry_master_product_helper; ?>"><?php echo $entry_master_product; ?></span>
    </label>
    <div class="col-sm-10">
        <label class="radio">
            <input type="checkbox" value="1" id="input-master-product"
                   name="master_product" <?php echo ($master_product == 1)? 'checked' : ''; ?> />
        </label>
    </div>
</div>

<div class="form-group <?php echo ($master_product == 1)? 'hide' : ''; ?>" id="form-group-cur-master-product-id">
    <label class="col-sm-2 control-label" for="input-master-product-id"><span data-toggle="tooltip"
                                                                              title="<?php echo $select_master_product_id_helper; ?>"><?php echo $select_master_product_id; ?></span></label>
    <div class="col-sm-10">
        <input type="text" name="inp_master_product_id" value="" placeholder="<?php echo $entry_product; ?>"
               id="input-master-product-id" class="form-control"/>
        <div id="wr-cur-master-product-id" class="well well-sm" style="height: 50px; overflow: auto;">
            <?php if(isset($cur_master_product) AND count($cur_master_product)>0){ ?>
            <div id="cur-master-product-id"><?php echo $cur_master_product['name']; ?>
                <input type="hidden" name="cur_master_product_id" value="<?php echo $cur_master_product['id']; ?>"/>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<script type="application/javascript">
    // Master product id
    $('input[name=\'inp_master_product_id\']').autocomplete({
        'source': function (request, response) {
            $.ajax({
                url: 'index.php?route=extension/module/offers_product/autocomplete&token=<?php echo $token; ?>&filter_name=' + encodeURIComponent(request)+'&master_product=1',
                dataType: 'json',
                success: function (json) {
                    response($.map(json, function (item) {
                        return {
                            label: item['name'],
                            value: item['product_id']
                        }
                    }));
                }
            });
        },
        'select': function (item) {
            $('input[name=\'inp_master_product_id\']').val('');
            $('#cur-master-product-id').remove();
            $('#wr-cur-master-product-id').append('<div id="cur-master-product-id">' + item['label'] + '<input type="hidden" name="cur_master_product_id" value="' + item['value'] + '" /></div>');
        }
    });
    $("#input-master-product").on('click',function (e){
        {
            $aH = ["#form-group-cur-master-product-id"];
            $aS = ['#tab-slave-li',"#tab-slave"];
            if ($(this).prop("checked")){
                $.each($aH, function(index, value){
                    $(value).hide();
                });
                $.each($aS, function(index, value){
                    $(value).show();
                    $(value).removeClass('hide');
                });
            }else{
                $.each($aH, function(index, value){
                    $(value).show();
                    $(value).removeClass('hide');
                });
                $.each($aS, function(index, value){
                    $(value).hide();
                });
            }
        }
    })
</script>