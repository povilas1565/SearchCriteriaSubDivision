<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-offers_product" data-toggle="tooltip"
                        title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                   class="btn btn-default"><i class="fa fa-reply"></i></a>
                <a href="javascript:void(0)" data-toggle="tooltip" onclick="apply()" title="" class="btn btn-info"
                   data-original-title="<?php echo $button_apply; ?>"><i class="fa fa-refresh"></i></a>
                <script language="javascript">
                    function apply() {
                        $('#form-offers_product').append('<input type="hidden" id="apply" name="apply" value="1" />');
                        $('#form-offers_product').submit();
                    }
                </script>
            </div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data"
                      id="form-offers_product" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="offers_product_status" id="input-status" class="form-control">
                                <?php if ($offers_product_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-log">
                            <?php echo $entry_log; ?>
                        </label>
                        <div class="col-sm-10">
                            <label class="radio">
                                <input type="checkbox" value="1" id="input-log" name="offers_product_en_log" <?php echo ($offers_product_en_log == 1)? 'checked' : ''; ?> />
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="input-view-only-master">
                            <?php echo $entry_view_only_master; ?>
                        </label>
                        <div class="col-sm-10">
                            <label class="radio">
                                <input type="checkbox" value="1" id="input-view-only-master" name="offers_product_view_only_master" <?php echo ($offers_product_view_only_master == 1)? 'checked' : ''; ?> />
                            </label>
                        </div>
                    </div>
                    <div class="form-group hide">
                        <label class="col-sm-2 control-label"
                               for="input-attr-<?php echo $attr['attribute_id']; ?>"><?php echo $label_attr; ?></label>
                    </div>
                    <?php if(isset($attributes)){ ?>
                    <?php foreach($attributes as $key => $attr){ ?>
                    <div class="form-group hide">
                        <label class="col-sm-2 control-label"
                               for="input-attr-<?php echo $attr['attribute_id']; ?>"><?php echo $attr['name']; ?></label>
                        <div class="col-sm-2">
                            <input name="offers_product_prefix_attr[<?php echo $attr['attribute_id']; ?>]" placeholder="<?php echo $entry_prefix_attr; ?>" id="input-attr-<?php echo $attr['attribute_id']; ?>" class="form-control"
                                   type="text" value="<?php echo $attr['prefix']; ?>"/>
                        </div>
                    </div>
                    <?php }?>
                    <?php }?>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>