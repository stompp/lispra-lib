<?php

function lispra_modal($attr = null, $content = "") { ?>
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

function lispra_dismissable_alert($attr, $content) { ?>
    <div class="alert <?php echo getIfSet($attr["class"], "alert-info") ?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php echo getIfSet($content, " ") ?>
    </div>
<?php } ?>



<?php

function lispra_alert_component_not_found() {
    $attr = array("class" => "alert-error");
    $content = "<strong>Lispra Error!</strong>Component function not found";
    lispra_dismissable_alert($attr, $content);
}
?>


<?php

function lispra_hideable_alert() { ?>
    <div class="hidden" lispra-dismissable-alert role="alert">
        <button type="button" class="btn close"  hides-parent aria-label="Close"><span aria-hidden="true">&times;</span></button> 
        <span lispra-dismissable-alert-content></span>
    </div>
<?php } ?>

<?php

//function lispra_add_title_form($lispra_component_type = "add-title-form", $opts = array()) { 
function lispra_add_title_form($attr, $content = "") {
    $attrs_string = array_to_attrs_string(getIfSet($attr["attrs"], array()));
    $placeholder = getIfSet($attr["input-title-placeholder"], "...");
    $title_html = getIfSet($attr["form-title-html"]);
    $form_title = getIfSet($attr["form-title"]);
    ?>
    <form <?php echo $attrs_string; ?> >
        <?php if (!is_null($title_html)) : ?> 
            <?php echo $title_html; ?>
        <?php elseif (is_null($form_title)): ?>
            <h4 lispra-component="add-title-form-title" class="hidden"></h4>
        <?php else : ?>
            <h4 lispra-component="add-title-form-title"><?php echo $form_title; ?></h4>
        <?php endif ?>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group lispra-add-title-form-group">
                    <div class="input-group">
                        <input name="title" type="text" class="form-control lispra-add-title-form-title-input" placeholder="<?php echo $placeholder ?>">           
                        <div class="input-group-btn">
                            <button class="btn lispra-add-title-form-add-btn" lispra-form-submit type="submit" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>" autocomplete="off">
                                <i class="fa fa-plus"></i> 
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!--<div class="col-md-12">-->
                <?php // lispra_hideable_alert(); ?>
            <!--</div>-->
        </div>


    </form>

<?php } ?>

<?php

function lispra_div_list_group($attr, $content = "") {
    $attrs_string = array_to_attrs_string(getIfSet($attr["attrs"], array()));
    ?>
    <div <?php echo $attrs_string; ?> ><?php echo getIfSet($content, ""); ?></div>
<?php } ?>

<?php

function lispra_list_group($attr, $content = "") {
    $attrs_string = array_to_attrs_string($attr["attrs"], array("class" => "list-group"));
    ?>
    <ul <?php echo $attrs_string; ?> ></ul>
<?php } ?>


<?php

function lispra_user_list_of_lists_panel($attr = null, $content = "") { ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?php
                    lispra_add_title_form(
                            array(
                                "attrs" => array("lispra-component" => "create-todo-list-form"),
                                "form-title" => '<strong>TODO</strong> Lists',
                                "input-title-placeholder" => "Crea una lista"
                            )
                    );
                    ?>     
                    <?php
                    lispra_div_list_group(
                            array(
                                "attrs" => array("lispra-component" => "user-todo-lists-list")
                            )
                    );
                    ?>


                </div>
            </div>
        </div>
    </div>

<?php } ?>

<?php

function lispra_user_list_panel($attr = null, $content = "") { ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?php
                    lispra_add_title_form(
                            array(
                                "attrs" => array("lispra-component" => "create-todo-list-item-form"),
                                "input-title-placeholder" => "Crea una tarea"
                            )
                    );
                    ?>     
                    <?php
                    lispra_div_list_group(
                            array(
                                "attrs" => array("lispra-component" => "user-todo-list")
                            )
                    );
                    ?>           
                </div>
            </div>
        </div>
    </div>

<?php } ?>



<?php

function lispra_user_tests_panel($attr = null, $content = "") { ?>       
    <div class="container">

    <?php lispra_user_list_of_lists_panel(); ?>
    <?php lispra_user_list_panel(); ?>
    </div>
<?php } ?>

    
<?php

function lispra_test_api_box($attr = null, $content = "") { ?>       
   <div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <span lispra-component="lispra-test-api-box">Toi Hueco</span>
    </div>
<?php } ?>









