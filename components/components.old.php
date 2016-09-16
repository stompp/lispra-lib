<?php

function lispra_modal() { ?>
    <div class="modal fade" lispra-modal tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" lispra-modal-title></h4>
                </div>
                <div class="modal-body" lispra-modal-body></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" lispra-modal-cancel>No</button>
                    <button type="button" class="btn btn-primary" lispra-modal-confirm>Yes</button>
                </div>
            </div>
        </div>
    </div>

<?php } ?>


<?php

function bootstrap_dismissable_alert($attr) { ?>
    <div class="alert <?php echo getIfSet($attr["class"], "alert-info") ?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php echo getIfSet($attr["content"], "No content") ?>
    </div>
<?php } ?>

<?php

function dismissable_alert($attr, $content) { ?>
    <div class="alert <?php echo getIfSet($attr["class"], "alert-info") ?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php echo getIfSet($content, "No content") ?>
    </div>
<?php } ?>

<?php

function component_not_found_alert() {
    $attr = array("class" => "alert-error");
    $content = "<strong>Lispra Error!</strong>Component function not found";
    dismissable_alert($attr, $content);
}
?>

<?php

function lispra_component_not_found() { ?>
    <div class="alert alert-error alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Lispra Error!</strong>Component function not found
    </div>
<?php } ?>

<?php

function lispra_hideable_alert() { ?>
    <div class="hidden" lispra-dismissable-alert role="alert">
        <button type="button" class="btn close"  hides-parent aria-label="Close"><span aria-hidden="true">&times;</span></button> 
        <span lispra-dismissable-alert-content></span>
    </div>
<?php } ?>

<?php

function lispra_add_title_form($lispra_component_type = "add-title-form", $opts = array()) { ?>
    <form lispra-component="<?php echo $lispra_component_type; ?>" <?php echo array_to_attrs_string(getIfSet($opts["attrs"], array())); ?> >
        <?php if (!isNullorEmpty($opts["form-title-html"])) : ?>
            <?php echo $opts["form-title-html"]; ?>
        <?php else : ?>
            <h4 lispra-component="add-title-form-title" <?php
            if (isNullorEmpty($opts["form-title"])) {
                echo ' class="hidden" ';
            }
            ?>><?php
                    if (!isNullorEmpty($opts["form-title"])) {
                        echo $opts["form-title"];
                    }
                    ?></h4>
    <?php endif ?>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group lispra-add-title-form-group">

                    <div class="input-group">
                        <input name="title" type="text" class="form-control lispra-add-title-form-title-input" placeholder="<?php echo (is_null($opts["input-title-placeholder"])) ? "..." : $opts["input-title-placeholder"]; ?>">
                        <!--<input name="test-form-input2" type="text" class="form-control" placeholder="...">-->
                        <div class="input-group-btn">
                            <button class="btn lispra-add-title-form-add-btn" lispra-form-submit type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" autocomplete="off">
                                <i class="fa fa-plus"></i> 
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
    <?php lispra_hideable_alert(); ?>
            </div>
        </div>
        <!--        <div class="row">
                    
                </div>-->

    </form>

<?php } ?>

<?php

function lispra_div_list_group($lispra_component_type, $opts = null) { ?>
    <?php if (!is_null($lispra_component_type)) : ?>
        <div lispra-component="<?php echo $lispra_component_type; ?>" <?php echo array_to_attrs_string($opts["attrs"]); ?> ></div>
    <?php endif ?>
<?php } ?>

<?php

function lispra_list_group($lispra_component_type, $opts = null) { ?>
    <?php if (!is_null($lispra_component_type)) : ?>
        <ul lispra-component="<?php echo $lispra_component_type; ?>" <?php echo array_to_attrs_string($opts["attrs"], array("class" => "list-group")); ?> ></ul>
    <?php endif ?>
<?php } ?>





<?php

function lispra_user_list_of_lists_panel() { ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?php
                    lispra_add_title_form(
                            "create-todo-list-form", array(
                        "form-title" => '<strong>TODO</strong> Lists',
                        "input-title-placeholder" => __("Crea una lista")
                            )
                    );
                    ?>     
    <?php lispra_div_list_group("user-todo-lists-list"); ?>                 
                </div>
            </div>
        </div>
    </div>

<?php } ?>

<?php

function lispra_user_list_panel() { ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row-fluid">
                        <?php
                        lispra_add_title_form(
                                "create-todo-list-item-form", array("input-title-placeholder" => "Add a task")
                        );
                        ?>
                    </div>

                    <div class="row-fluid">
                        <?php
                        lispra_div_list_group(
                                "user-todo-list")
                        ;
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

<?php } ?>

<?php

function lispra_user_tests_panel() { ?>       
    <div class="container">

        <?php lispra_user_list_of_lists_panel(); ?>
    <?php lispra_user_list_panel(); ?>
    </div>
<?php } ?>









