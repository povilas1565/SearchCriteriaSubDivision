div class="tab-pane <?php echo ($master_product == 1)? '' : 'hide'; ?>" id="tab-slave">

<div class="form-group">
    <label class="col-sm-2 control-label" for="input-slave-product-list-search"><span class=""
                                                                                      data-toggle="tooltip"
                                                                                      title="<?php echo $tab_slave_list; ?>"><?php echo $tab_slave_list; ?>
            :</span></label>
    <div class="col-sm-10">
        <input type="text" name="slave_product_list_search" value="" placeholder="<?php echo $entry_product; ?>"
               id="input-slave-product-list-search" class="form-control"/>
        <div id="wr-slave-product-list" class="well well-sm" style="height: 550px; overflow: auto;">
            <?php foreach ($slave_products as $slave_product) { ?>
            <div id="slave-product<?php echo $slave_product['product_id']; ?>"><i class="fa fa-minus-circle"></i><a
                        href="<?php echo $slave_product['edit']; ?>"><?php echo $slave_product['name']; ?></a>
                <input type="hidden" name="slave_products[]" value="<?php echo $slave_product['product_id']; ?>"/>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
</div>
<script type="application/javascript">
    // Slave product
    $('input[name=\'slave_product_list_search\']').autocomplete({
        'source': function (request, response) {
            $.ajax({
                url: 'index.php?route=extension/module/offers_product/autocomplete&token=<?php echo $token; ?>&filter_name=' + encodeURIComponent(request)+'&master_product=0<?php echo (isset($product_id))?"&product_id=".$product_id:""; ?>',
                dataType: 'json',
                success: function (json) {
                    response($.map(json, function (item) {
                        return {
                            label: item['name'],
                            value: item['product_id'],
                        }
                    }));
                }
            });
        },
        'error': function (item) {
            console.log(item);
        },
        'select': function (item) {
            $('input[name=\'slave_product_list_search\']').val('');
            $('#slave-product' + item['value']).remove();
            $('#wr-slave-product-list').append('<div id="slave-product' + item['value'] + '"><i class="fa fa-minus-circle"></i>' + item['label'] + '<input type="hidden" name="slave_products[]" value="' + item['value'] + '" /></div>');
        }
    });
    $('#wr-slave-product-list').delegate('.fa-minus-circle', 'click', function () {
        $(this).parent().remove();
    });
</script>