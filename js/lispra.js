// FORM UTILS
function formExtractDataAsObjet($f) {
    var $inputs = $f.serializeArray();
    var o = {};
    $inputs.forEach(function (element, index, array) {
        o[element.name] = element.value;
    });
    return o;
}

function serializedFormDataToObject(data) {
    var inputs_chunks = data.split('&');
    var inputs = {};
    inputs_chunks.forEach(function (element, index, array) {
        var item_chunks = element.split("=", 2);
        if (item_chunks.length === 2) {
            inputs[item_chunks[0]] = item_chunks[1];
        }
    });
    return inputs;
}

// LISPRA-MODAL
function showModal(title, body, onConfirm) {

//    var modal = $("[lispra-modal]").first();
    $("[lispra-modal]").find("[lispra-modal-title]").html(title);
    $("[lispra-modal]").find("[lispra-modal-body]").html(body);
    $("[lispra-modal]").find("[lispra-modal-confirm]").button().click(onConfirm);
    $("[lispra-modal]").modal('show');
}

function dissmissModal() {
    $("[lispra-modal]").modal('hide');
}

// PNOTIFY SHOW
function showPNotify(options) {
    var o = options;
    o['styling'] = 'bootstrap3';
    new PNotify(o);
//    new PNotify({
//        title: 'Regular Success',
//        text: 'That thing that you were trying to do worked!',
//        type: 'success',
//        styling: 'bootstrap3'
//    });
}

function getElementBottomPx($e) {
//    var bottom = $el.position().top + $el.offset().top + $el.outerHeight(true);
    if ($e.length) {
        var a1 = (typeof ($e.position().top) === "undefined") ? 0 : $e.position().top;
        var a2 = (typeof ($e.offset().top) === "undefined") ? 0 : $e.offset().top;
        var a3 = (typeof ($e.outerHeight(true)) === "undefined") ? 0 : $e.outerHeight(true);
        console.log("a1 " + a1);
        console.log("a2 " + a2);
        console.log("a3 " + a3);
        return a1 + a2 + a3;
    }
    return 0;
}




$(document).ready(function () {
    Lispra.components.updateUserTodoListsList();
    Lispra.components.updateUserTodoList();
    Lispra.components.setCreateTodoListFormSubmitFunction();
    Lispra.components.setCreateTodoListItemFormSubmitFunction();
    $("[hides-parent]").click(function () {
        $(this).parent().addClass("hidden");
    });
});
function Lispra() {}
Lispra.user = function () {}

Lispra.components = function () {}
Lispra.components.todoListsList = function () {}
Lispra.components.todoList = function () {}




Lispra.components.updateUserTodoListsList = function ($jquery_selector_str) {


    var $selector = "[lispra-component='user-todo-lists-list']";
    if (typeof ($jquery_selector_str) === 'string')
        $selector = $jquery_selector_str;
    if ($($selector).length) {
        Lispra.user.action(
                "getLists",
                "*",
                function (data, status) {
                    try {

                        console.log(data);
                        var o = JSON.parse(data);
                           console.log(o.isDataSet);
                        if (o.isDataSet === 1) {
                             console.log(o.isDataSet);
                            $($selector).empty();
                            o.data.forEach(function addToList($list) {
                                var item_content = 'id : ' + $list.list_id + ' name : ' + $list.list_name;
                                var $item = Lispra.components.todoListsList.getItem($list, item_content);
                                $($selector).append($item);
                                //                        $($selector).each(function (i, e) {
                                //                            $(this).append($item);
                                //                        });
                            });
                        }

                    } catch (e) {
                        console.log(e);
                    }


                },
                function (msg) {
//                    console.log($selector + " update done");
                },
                function (xhr, textStatus, errorThrown) {
//                    console.log("[lispra-component='user-todo-lists-list'] update failed");
                    showPNotify({title: "updateUserTodoListsList " + textStatus, text: "Error :" + errorThrown, type: "error"});
                });
    }

}

Lispra.components.updateUserTodoList = function ($list) {
    var $component_selector = "[lispra-component='user-todo-list']";
    var $l = $($component_selector);
    if ($l.length) {

        var $list_id, $list_name;
        if (typeof ($list) === 'undefined') {
            $list_id = $l.first().attr('list_id');
        } else if (typeof ($list) === 'object') {
            $list_id = $list.list_id;
        }

        if (typeof ($list_id) === 'undefined') {
//            showPNotify({title: $component_selector, text: "'list_id undefined", type: "error"});
            return;
        } else if ($list_id < 1) {
//            showPNotify({title: $component_selector, text: "'list_id < 1", type: "error"});
            return;
        }
//        console.log("updateLispraComponentUserTodoList list_id : " + $list_id);


        if (typeof ($list) === 'object') {
            $list_name = $list.list_name;
//            showPNotify({title: "[lispra-component='user-todo-list']", text: "'list name is " + $list_name});

            var form_title = $("[lispra-component='create-todo-list-item-form']")
                    .find("[lispra-component='add-title-form-title']");
            form_title.removeClass("hidden");
            form_title.text($list_name);
        } else
            $list_name = "List " + $list_id;
        Lispra.user.action(
                "getListContent",
                $list_id,
                function (data, status) {
                    try {
                        var o = JSON.parse(data);
                        $("[lispra-component='user-todo-list'][list_id='" + $list_id + "']").empty();
                        o.data.forEach(
                                function addToList($task) {
                                    $task["list_id"] = $list_id;
                                    var item_content = 'id : ' + $task._id + ' title : ' + $task.title;
//                                var $item = '<li class="list-group-item"><button type="button" class="btn close" aria-label="Close"><span aria-hidden="true">&times;</span></button><span >' + item_content + '</span></li>';
//                                var $item_old = '<li class="list-group-item">' + item_content + '</li>';
                                    var $item = Lispra.components.getTodoListItem($task, item_content);
                                    $("[lispra-component='user-todo-list'][list_id='" + $list_id + "']").each(
                                            function (i, e) {
                                                $(this).append($item);
                                            }
                                    );
                                }
                        );
                    } catch (e) {
                        console.log(e);
                    }
                },
                function (msg) {
                    console.log("[lispra-component='user-todo-list'] update done");
                },
                function (xhr, textStatus, errorThrown) {
                    console.log("[lispra-component='user-todo-list'] update failed");
                    showPNotify({title: "[lispra-component='user-todo-list'] update failed " + textStatus, text: "Error :" + errorThrown, type: "error"});
                });
    }

}

Lispra.components.getAddTitleFormElements = function ($f) {

    var $form_data = $f.serialize();
    var $data = formExtractDataAsObjet($f);
    var $btn = $f.find("[lispra-form-submit]");
    var $form_alert = $f.find("[lispra-dismissable-alert]").first();
    var $form_alert_content = $form_alert.children("[lispra-dismissable-alert-content]").first();
    var out = {
        $f: $f,
        data: $data,
        submit_button: $btn,
        dismissable_alert: $form_alert,
        dismissable_alert_content: $form_alert_content,
        showNotify:
                function (options) {
                    showPNotify(options);
                },
        showDismissableAlert:
                function (content, class_def, hideDelay)
                {
                    this.dismissable_alert.attr("class", class_def);
                    this.dismissable_alert_content.text(content);
                    this.dismissable_alert.delay((typeof hideDelay !== 'undefined') ? hideDelay : 7500).queue(function () {
                        $(this).attr("class", "hidden");
                    });
                },
        clearInputs:
                function () {
                    $(this.$f).find("input").val("");
                },
        resetSubmitButton:
                function () {
                    this.submit_button.button('reset');
                },
        reset:
                function (result) {
                    if (result === "success") {
                        this.clearInputs();
                    }
                    this.resetSubmitButton();
                },
        resetAndShowNotify:
                function (result, showNotifyOptions) {
                    this.reset();
                    this.showNotify(showNotifyOptions);
                },
        setLoadingMode:
                function () {

                    this.submit_button.button('loading');
                },
        getName:
                function (name) {
                    return this.data[name];
                },
        getAttr:
                function (key) {
                    return this.$f.attr(key);
                },
        settAttr:
                function (key, value) {
                    this.$f.attr(key, value);
                }


    };
    return out;
}

Lispra.components.setCreateTodoListFormSubmitFunction = function () {
    $("[lispra-component='create-todo-list-form']").submit(
            function (event) {

                event.preventDefault();
                var o = Lispra.components.getAddTitleFormElements($(this));
                o.setLoadingMode();
                var $title = o.getName("title");
                var $list_class = "todo";
                var $data = {
                    list_name: $title,
                    list_class: $list_class
                };
                if ($title.length === 0) {
                    o.resetAndShowNotify("error", {title: "Create TODO list", text: "Title is empty"});
//                            o.showNotify({title: "Create TODO list", text: "Title is empty"});
//                            o.reset("error");
                    return;
                }

                Lispra.user.action(
                        "createList",
                        $data,
                        function (data, status) {
                            if (status == 'success') {
//                                        o.showDismissableAlert("Done", "alert alert-success", 7500);
                                o.showNotify({title: "Create TODO list", text: "List " + $data.list_name + " created", type: "success"});
                                o.reset("success");
                                Lispra.components.updateUserTodoListsList();
                            } else
                                o.reset("error");
                        },
                        function (msg) {
                            console.log("createList post done");
                        },
                        function (xhr, textStatus, errorThrown) {
//                                    o.showDismissableAlert("Create TODO list error : " + errorThrown, "alert alert-danger");
                            o.showNotify({title: "Create TODO list ", text: "error : " + errorThrown, type: "error"});
                            o.reset("error");
                        }
                );
            }
    );
}

Lispra.components.setCreateTodoListItemFormSubmitFunction = function () {
    $("[lispra-component='create-todo-list-item-form']").submit(
            function (event) {

                event.preventDefault();
                var o = Lispra.components.getAddTitleFormElements($(this));
                o.setLoadingMode();
                var $title = o.data["title"];
                var $list_id = $(this).attr("list_id");
                if (typeof ($list_id) === 'undefined') {
                    o.showNotify({title: "[lispra-component='create-todo-list-item-form']", text: "'list_id undefined", type: "error"});
                    o.reset("error");
                    return;
                } else if ($list_id < 1) {
                    o.showNotify({title: "[lispra-component='create-todo-list-item-form']", text: "'list_id < 1", type: "error"});
                    o.reset("error");
                    return;
                }
                if ($title.length === 0) {
                    o.showNotify({title: "[lispra-component='create-todo-list-item-form']", text: "Title is empty"});
                    o.reset("error");
                    return;
                }

                Lispra.user.action(
                        "createListItem",
                        {
                            list_id: $list_id,
                            title: $title,
                        },
                        function (data, status) {

                            if (status == 'success') {
                                var t = 'Task ' + $title + " created.";
                                o.showNotify({title: "List " + $list_id, text: t, type: "success"});
                                o.reset("success");
                                Lispra.components.updateUserTodoList();
                            } else
                                o.showNotify({title: "List " + $list_id, text: 'Task ' + $title + " no success.", type: "error"});
                            o.reset("error");
                        },
                        function (msg) {
                            console.log("[lispra-component='create-todo-list-item-form'] item created");
                        },
                        function (xhr, textStatus, errorThrown) {
                            o.showNotify({title: "Create TODO list task", text: "error : " + errorThrown, type: "error"});
                            o.reset("error");
                        }
                );
            }
    );
}




Lispra.components.todoListsList.getItem = function ($list, content) {


//<div class="row">
//    <div class="lispra-todo-lists-list-item">
//    <div class="lispra-todo-lists-list-item-right-div">
//    <div class="lispra-todo-lists-list-item-left-div">
//    <div class="lispra-list-item-title">Compra</div>


//<div class="btn-group btn-flex">
    var $btn_flex_test_options_btn = $('<button type="button" class="btn btn-flex-options-btn"><i class="fa fa-search"></i></button>');
    var $btn_flex_test_options_btn2 = $('<button type="button" class="btn btn-flex-options-btn"><i class="fa fa-search"></i></button>');
    var $btn_flex_test_options_btn3 = $('<button type="button" class="btn btn-flex-options-btn"><i class="fa fa-search"></i></button>');
    var $btn_flex_test_options_btn4 = $('<button type="button" class="btn btn-flex-options-btn"><i class="fa fa-search"></i></button>');
    var $btn_flex_test_options_btn5 = $('<button type="button" class="btn btn-flex-options-btn"><i class="fa fa-search"></i></button>');
    var $btn_flex_test_options_btn6 = $('<button type="button" class="btn btn-flex-options-btn"><i class="fa fa-search"></i></button>');
    var $btn_flex_group = $('<div class="btn-group btn-flex"></div>');
    var $btn_flex_main_btn = $('<button type="" class="btn btn-flex-main-btn"></button>');
    $btn_flex_main_btn.append($list.list_name);
    $btn_flex_main_btn.click(
            function () {
                $("[lispra-component='create-todo-list-item-form']").attr("list_id", $list.list_id);
                $("[lispra-component='user-todo-list']").attr("list_id", $list.list_id);
                Lispra.components.updateUserTodoList($list);
                console.log("show clicked list name is " + $list.list_name);
            });
    var editButton = $('<button type="button" class="btn btn-flex-options-btn"><i class="fa fa-edit"></i></button>');
    editButton.click(
            function () {
                showPNotify({title: "", text: "Edit list clicked"});
            });
    var deleteButton = $('<button type="button" class="btn btn-flex-options-btn" lispra-action="deleteList"><i class="fa fa-trash"></i></button>');
    deleteButton.click(
            function () {
                var $action = $(this).attr("lispra-action");
                var onConfirm = function () {

                    Lispra.user.action(
                            $action,
                            $list.list_id,
                            function (data, status) {
                                console.log("[type='button'][lispra-action='deleteList'] success");
                                Lispra.components.updateUserTodoListsList();
                                showPNotify({title: "TODO Lists", text: "List " + $list.list_name + " deleted.", type: "info"});
                            },
                            function (data) {

                                console.log("[type='button'][lispra-action='deleteList'] done");
                            },
                            function (xhr, textStatus, errorThrown) {
                                dissmissModal();
                                console.log("[type='button'][lispra-action='deleteList'] fail");
                            });
                    dissmissModal();
                };
                showModal(
                        "¿Delete list " + $list.list_name + "?",
                        "¿Delete list " + $list.list_name + "?",
                        onConfirm
                        );
            }
    );
    $btn_flex_group.append(editButton);
    $btn_flex_group.append($btn_flex_main_btn);
    $btn_flex_group.append(deleteButton);
//    var $btn_flex_test_dropdown_btn = $('<button type="button" class="btn btn-flex-options-btn" data-toggle="dropdown" aria-haspopup="true" ><i class="fa fa-gear"></i></button>');
//    var $btn_flex_test_dropdown_btn_menu = $('<ul class="dropdown-menu pull-right list-inline"></ul>');
//    var $btn_flex_test_dropdown_btn_menu_btn_group = $('<div class="btn-group"></div>');
//    var li = $('<li></li>');
//    $btn_flex_test_dropdown_btn_menu_btn_group.append($btn_flex_test_options_btn4);
//    $btn_flex_test_dropdown_btn_menu_btn_group.append($btn_flex_test_options_btn5);
//    $btn_flex_test_dropdown_btn_menu_btn_group.append($btn_flex_test_options_btn6);
//    li.append($btn_flex_test_dropdown_btn_menu_btn_group);
//    $btn_flex_test_dropdown_btn_menu.append(li);
//    $btn_flex_group.append($btn_flex_test_dropdown_btn);
//    $btn_flex_group.append($btn_flex_test_dropdown_btn_menu);

    return $btn_flex_group;
}

//Lispra.components.todoListsList.getItem = function ($list, content) {
//
//    var list_item = $('<li></li>');
//    var input_group = $('<div class="input-group lispra-input-group"></div>');
//    var title_content = $('<a class="lispra-list-item-title"></a>').append($list.list_name);
//
//
//    title_content.click(
//            function () {
//                $("[lispra-component='create-todo-list-item-form']").attr("list_id", $list.list_id);
//                $("[lispra-component='user-todo-list']").attr("list_id", $list.list_id);
//                Lispra.components.updateUserTodoList($list);
//                console.log("show clicked list name is " + $list.list_name);
//            });
//
//
//
//    var editButton = $('<button type="button" class="btn btn-default lispra-list-item-title-button"><i class="fa fa-edit"></i></button>');
//
//    editButton.click(
//            function () {
////                $("[lispra-component='create-todo-list-item-form']").attr("list_id", $list.list_id);
////                $("[lispra-component='user-todo-list']").attr("list_id", $list.list_id);
////                Lispra.components.updateUserTodoList($list);
////                console.log("show clicked list name is " + $list.list_name);
//                showPNotify({title: "", text: "Edit list clicked"});
//            });
//
//
//    var deleteButton = $('<button type="button" class="btn btn-default lispra-list-item-title-button" lispra-action="deleteList"><i class="fa fa-trash"></i></button>');
//    deleteButton.click(
//            function () {
//                var $action = $(this).attr("lispra-action");
//                var onConfirm = function () {
//
//                    Lispra.user.action(
//                            $action,
//                            $list.list_id,
//                            function (data, status) {
//                                console.log("[type='button'][lispra-action='deleteList'] success");
//                                Lispra.components.todoListsListItem();
//                                showPNotify({title: "TODO Lists", text: "List " + $list.list_name + " deleted.", type: "info"});
//
//                            },
//                            function (data) {
//
//                                console.log("[type='button'][lispra-action='deleteList'] done");
//                            },
//                            function (xhr, textStatus, errorThrown) {
//                                dissmissModal();
//                                console.log("[type='button'][lispra-action='deleteList'] fail");
//                            });
//                    dissmissModal();
//                };
//                showModal(
//                        "¿Delete list " + $list.list_name + "?",
//                        "¿Delete list " + $list.list_name + "?",
//                        onConfirm
//                        );
//            }
//    );
//// var editButton = $('<button type="button" class="btn btn-default lispra-list-item-title-button"><i class="fa fa-edit"></i></button>');
//// var optionsButton = $('<button type="button" class="btn btn-default lispra-list-item-title-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>');
////    var optionsDropDownMenu = $('<ul class="dropdown-menu dropdown-menu-left"></ul>');
//    var optionsButton = $('<button type="button" class="btn btn-default lispra-list-item-title-button" data-toggle="dropdown"><i class="fa fa-caret-square-o-down"></i></button>');
//    var optionsDropDownMenu = $('<ul class="dropdown-menu pull-right"></ul>');
//    var optionsMenu = $('<ul class="list-inline"></ul>');
//    var x = $('<span class="input-group-btn"></span>');
//    x.append(editButton);
//    x.append(deleteButton);
//    optionsMenu.append(x);
////    optionsMenu.append($("<li></li>").append($("<a></a>").append(editButton)));
////    optionsMenu.append($("<li></li>").append($("<a></1a>").append(deleteButton)));
//
//    optionsDropDownMenu.append(optionsMenu);
////     var dropDownLink2 = $('<li><a>Another action</a></li>');
////      optionsDropDownMenu.append(dropDownLink2);
////    optionsButton.append(optionsDropDownMenu);
////    title_content.append(title);
//    var right_button_bar = $('<span class="input-group-btn"></span>');
//
//
//
////    right_button_bar.append(editButton);
////    right_button_bar.append(deleteButton);
//    right_button_bar.append(optionsButton);
//    right_button_bar.append(optionsDropDownMenu);
//
//    input_group.append(title_content);
//    input_group.append(right_button_bar);
//    list_item.append(input_group);
//    return input_group;
//
////    list_content.append(list_title);
////    list_content.append(right_button_bar);
////    input_group.append(list_content);
////    list_item.append(input_group);
////    return list_item;
//}

//
//

Lispra.components.getTodoListItem = function ($task, content) {


//    if ($task.status !== 'complete') {
//        showPNotify({title: "Task " + $task.title, text: "Status : " + $task.status});
//    }


    var $btn_flex_test_options_btn6 = $('<button type="button" class="btn btn-flex-options-btn"><i class="fa fa-search"></i></button>');
    var $btn_flex_group = $('<div class="btn-group btn-flex"></div>');
    var $btn_flex_main_btn = $('<button type="button" class="btn btn-flex-main-btn"></button>');
    $btn_flex_main_btn.append($task.title);
    var cb_values = {
        class: ($task.status === 'pending') ? "btn btn-pending" : "btn btn-complete",
        icon: ($task.status === 'pending') ? "fa fa-close" : "fa fa-check"
    };
    var checked_button = $('<button class="' + cb_values.class + '" type="button"><i class="' + cb_values.icon + '"></i></button>');
    checked_button.click(
            function () {
                var r_icon = 'fa fa-refresh fa-spin ';
                $(this).find('i').first().attr("class", r_icon);
                var new_status = "pending";
                if ($task.status === 'pending') {
                    new_status = "complete";
                }

                var $action = "updateListItem";
                var d = {
                    list_id: $task.list_id,
                    _id: $task._id,
                    status: new_status
                };
                Lispra.user.action(
                        $action,
                        d,
                        function (data, status) {
                            Lispra.components.updateUserTodoList();
                            showPNotify({title: "List task", text: "Task " + $task.title + " status changed.", type: "success"});
                        },
                        function (data) {},
                        function (xhr, textStatus, errorThrown) {
                            $(this).find('i').first().attr("class", cb_values.icon);
                            showPNotify({title: "List task", text: "Task " + $task.title + " error on status change.", type: "error"});
                            showPNotify({title: textStatus, text: errorThrown, type: "error"});
                        });
            });
//    var title_content = $('<span class="lispra-list-item-title"></span>');


//    var dropDownDelete = $('<li><a>Delete</a></li>');
    var dropDownDelete = $('<button type="button" class="btn btn-flex-options-btn"><i class="fa fa-trash"></i></button>');
    dropDownDelete.click(
            function () {
//                showPNotify({title:$task._id + " Delete Button",text:content,type:"error"});
                var $action = "updateListItem";
                var d = {
                    list_id: $task.list_id,
                    _id: $task._id,
                    status: "erased"
                };
                var onConfirm = function () {
//                    showPNotify({title:$task._id + " Delete Button Confirmed",text:JSON.stringify(d),type:"error"});
                    Lispra.user.action(
                            $action,
                            d,
                            function (data, status) {
                                Lispra.components.updateUserTodoList();
                                showPNotify({title: "List task", text: "Task " + $task.title + " deleted.", type: "success"});
                            },
                            function (data) {},
                            function (xhr, textStatus, errorThrown) {

                                showPNotify({title: "List task", text: "Task " + $task.title + " error on erase.", type: "error"});
                            });
                    dissmissModal();
                };
                showModal(
                        "¿Delete task " + $task.title + "?",
                        "",
                        onConfirm
                        );
            }
    );
    $btn_flex_group.append(checked_button);
    $btn_flex_group.append($btn_flex_main_btn);
    $btn_flex_group.append(dropDownDelete);
    return $btn_flex_group;
}


Lispra.user.action = function (name, data, f_success, f_done, f_fail) {

//    var actions_path = "../api/lispra_beta/action/";
    var actions_path = "../api/lispra_beta/actions/";
//    var actions_path = "../lispra/actions.php";
    console.log("Lispra.user.action started");
    var d = {
        user_actions: [
            {
                name: name,
                data: data
            }
        ]
    };
    $.post(
            actions_path,
            JSON.stringify(d),
            f_success
            ).done(
            f_done
            ).fail(
            f_fail
            );
//    $.post(
//            actions_path,
//            JSON.stringify(d),
////            f_success
//        function (data) {
//                        console.log("Action done data is : " + data);
//                    }
//            )
//            .done(
//                    function (data) {
//                        console.log("Action done data is : " + data);
//                    })
//            .fail(
//                    function (xhr, textStatus, errorThrown) {
////                showPNotify({title: "List task", text: "Task " + $task.title + " error on erase.", type: "error"});
//                        console.log("Action faile error is : " + errorThrown);
//                    });
    console.log("Lispra.user.action end");
}



$(document).ready(function () {

    $("[lispra-component='lispra-test-api-box']").text("Cebolla");
    if ($("[lispra-component='lispra-test-api-box']").length) {
        $.get("../api/lispra_beta/hello_world/", function (data) {
//        $(".result").html(data);
            $("[lispra-component='lispra-test-api-box']").text(JSON.stringify(data));
            console.log("lispra-component='lispra-test-api-box data is " + data);
        });
    }

});