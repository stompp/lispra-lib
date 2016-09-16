<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'core/WPLispra.php';
include_once 'components/components.php';

//define("TEST_SHOW_TEST_ONLY", 0);
//define("TEST_SHOW_BOTH", 1);
//define("TEST_SHOW_BODY_ONLY", 2);
//$test = TEST_SHOW_BODY_ONLY;

lispra_modal();

?>



<div class="container">


    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-body">

                    <!--<div class="row">-->
                    <!--<div class="col-xs-12">-->
                    <?php
                    lispra_add_title_form(
                            "create-todo-list-form", array(
                        "form-title" => '<strong>TODO</strong> Lists',
                        "input-title-placeholder" => __("Crea una lista")
                            )
                    );
                    ?>     
                    <!--</div>-->

                    <!--<div class="col-xs-12">-->   
                    <?php lispra_div_list_group("user-todo-lists-list"); ?>
                    <?php // lispra_list_group("user-todo-lists-list"); ?>
                    <!--</div>-->
                    <!--</div>-->
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <!--                        <div class="row">-->
                    <div class="row-fluid">
                        <?php
                        lispra_add_title_form(
                                "create-todo-list-item-form", array("input-title-placeholder" => "Add a task")
                        );
                        ?>
                    </div>
                    <!--                    </div>
                                        <div class="row">-->
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
</div>


