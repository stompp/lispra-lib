<?php function create_add_title_form($lispra_component_type = "add-title-form", $attrs = null, $title_label = null) { ?>
    <form class="form-horizontal form-label-left" lispra-component="<?php echo $lispra_component_type; ?>" <?php echo array_to_attrs_string($attrs); ?> >
        <!--<form class="form-horizontal form-label-left" lispra-component="test-form">-->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">

                    <div class="input-group">
                        <input name="title" type="text" class="form-control" placeholder="...">
                        <!--<input name="test-form-input2" type="text" class="form-control" placeholder="...">-->
                        <span class="input-group-btn">
                            <button class="btn btn-default lispra-form-submit" lispra-form-submit type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin' ></i>" autocomplete="off">
                                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> 
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php create_lispra_dismissable_alert(); ?>
            </div>
        </div>

    </form>

<?php } ?>
