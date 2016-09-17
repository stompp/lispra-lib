/**
 * Resize function without multiple trigger
 * 
 * Usage:
 * $(window).smartresize(function(){  
 *     // code here
 * });
 */
(function ($, sr) {
    // debouncing function from John Hann
    // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
    var debounce = function (func, threshold, execAsap) {
        var timeout;

        return function debounced() {
            var obj = this, args = arguments;
            function delayed() {
                if (!execAsap)
                    func.apply(obj, args);
                timeout = null;
            }

            if (timeout)
                clearTimeout(timeout);
            else if (execAsap)
                func.apply(obj, args);

            timeout = setTimeout(delayed, threshold || 100);
        };
    };

    // smartresize 
    jQuery.fn[sr] = function (fn) {
        return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr);
    };

})(jQuery, 'smartresize');
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var CURRENT_URL = window.location.href.split('?')[0],
        $BODY = $('body'),
        $MENU_TOGGLE = $('#menu_toggle'),
        $SIDEBAR_MENU = $('#sidebar-menu'),
        $SIDEBAR_FOOTER = $('.sidebar-footer'),
        $LEFT_COL = $('.left_col'),
        $RIGHT_COL = $('.right_col'),
        $NAV_MENU = $('.nav_menu'),
        $FOOTER = $('footer');

// Sidebar
$(document).ready(function () {
    // TODO: This is some kind of easy fix, maybe we can improve this
    var setContentHeight = function () {
        // reset height
        $RIGHT_COL.css('min-height', $(window).height());

        var bodyHeight = $BODY.outerHeight(),
                footerHeight = $BODY.hasClass('footer_fixed') ? -10 : $FOOTER.height(),
                leftColHeight = $LEFT_COL.eq(1).height() + $SIDEBAR_FOOTER.height(),
                contentHeight = bodyHeight < leftColHeight ? leftColHeight : bodyHeight;

        // normalize content
        contentHeight -= $NAV_MENU.height() + footerHeight;

        $RIGHT_COL.css('min-height', contentHeight);
    };

    $SIDEBAR_MENU.find('a').on('click', function (ev) {
        var $li = $(this).parent();

        if ($li.is('.active')) {
            $li.removeClass('active active-sm');
            $('ul:first', $li).slideUp(function () {
                setContentHeight();
            });
        } else {
            // prevent closing menu if we are on child menu
            if (!$li.parent().is('.child_menu')) {
                $SIDEBAR_MENU.find('li').removeClass('active active-sm');
                $SIDEBAR_MENU.find('li ul').slideUp();
            }

            $li.addClass('active');

            $('ul:first', $li).slideDown(function () {
                setContentHeight();
            });
        }
    });

    // toggle small or large menu
    $MENU_TOGGLE.on('click', function () {
        if ($BODY.hasClass('nav-md')) {
            $SIDEBAR_MENU.find('li.active ul').hide();
            $SIDEBAR_MENU.find('li.active').addClass('active-sm').removeClass('active');
        } else {
            $SIDEBAR_MENU.find('li.active-sm ul').show();
            $SIDEBAR_MENU.find('li.active-sm').addClass('active').removeClass('active-sm');
        }

        $BODY.toggleClass('nav-md nav-sm');

        setContentHeight();
    });

    // check active menu
    $SIDEBAR_MENU.find('a[href="' + CURRENT_URL + '"]').parent('li').addClass('current-page');

    $SIDEBAR_MENU.find('a').filter(function () {
        return this.href == CURRENT_URL;
    }).parent('li').addClass('current-page').parents('ul').slideDown(function () {
        setContentHeight();
    }).parent().addClass('active');

    // recompute content when resizing
    $(window).smartresize(function () {
        setContentHeight();
    });

    setContentHeight();

    // fixed sidebar
    if ($.fn.mCustomScrollbar) {
        $('.menu_fixed').mCustomScrollbar({
            autoHideScrollbar: true,
            theme: 'minimal',
            mouseWheel: {preventDefault: true}
        });
    }
});
// /Sidebar

// Panel toolbox
$(document).ready(function () {
    $('.collapse-link').on('click', function () {
        var $BOX_PANEL = $(this).closest('.x_panel'),
                $ICON = $(this).find('i'),
                $BOX_CONTENT = $BOX_PANEL.find('.x_content');

        // fix for some div with hardcoded fix class
        if ($BOX_PANEL.attr('style')) {
            $BOX_CONTENT.slideToggle(200, function () {
                $BOX_PANEL.removeAttr('style');
            });
        } else {
            $BOX_CONTENT.slideToggle(200);
            $BOX_PANEL.css('height', 'auto');
        }

        $ICON.toggleClass('fa-chevron-up fa-chevron-down');
    });

    $('.close-link').click(function () {
        var $BOX_PANEL = $(this).closest('.x_panel');

        $BOX_PANEL.remove();
    });
});
// /Panel toolbox

// Tooltip
$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip({
        container: 'body'
    });
});
// /Tooltip

// Progressbar
if ($(".progress .progress-bar")[0]) {
    $('.progress .progress-bar').progressbar();
}
// /Progressbar

// Switchery
$(document).ready(function () {
    if ($(".js-switch")[0]) {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function (html) {
            var switchery = new Switchery(html, {
                color: '#26B99A'
            });
        });
    }
});
// /Switchery

// iCheck
$(document).ready(function () {
    if ($("input.flat")[0]) {
        $(document).ready(function () {
            $('input.flat').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });
        });
    }
});
// /iCheck

// Table
$('table input').on('ifChecked', function () {
    checkState = '';
    $(this).parent().parent().parent().addClass('selected');
    countChecked();
});
$('table input').on('ifUnchecked', function () {
    checkState = '';
    $(this).parent().parent().parent().removeClass('selected');
    countChecked();
});

var checkState = '';

$('.bulk_action input').on('ifChecked', function () {
    checkState = '';
    $(this).parent().parent().parent().addClass('selected');
    countChecked();
});
$('.bulk_action input').on('ifUnchecked', function () {
    checkState = '';
    $(this).parent().parent().parent().removeClass('selected');
    countChecked();
});
$('.bulk_action input#check-all').on('ifChecked', function () {
    checkState = 'all';
    countChecked();
});
$('.bulk_action input#check-all').on('ifUnchecked', function () {
    checkState = 'none';
    countChecked();
});

function countChecked() {
    if (checkState === 'all') {
        $(".bulk_action input[name='table_records']").iCheck('check');
    }
    if (checkState === 'none') {
        $(".bulk_action input[name='table_records']").iCheck('uncheck');
    }

    var checkCount = $(".bulk_action input[name='table_records']:checked").length;

    if (checkCount) {
        $('.column-title').hide();
        $('.bulk-actions').show();
        $('.action-cnt').html(checkCount + ' Records Selected');
    } else {
        $('.column-title').show();
        $('.bulk-actions').hide();
    }
}

// Accordion
$(document).ready(function () {
    $(".expand").on("click", function () {
        $(this).next().slideToggle(200);
        $expand = $(this).find(">:first-child");

        if ($expand.text() == "+") {
            $expand.text("-");
        } else {
            $expand.text("+");
        }
    });
});


// NProgress
if (typeof NProgress != 'undefined') {
    $(document).ready(function () {
        NProgress.start();
    });

    $(window).load(function () {
        NProgress.done();
    });
}

//
//// LISPRA Navar with wp admin bar
//$(document).ready(function () {
//
//    if ($("#wpadminbar").length) {
//        $("#lispra_top_nav").css("top", $("#wpadminbar").height() + "px");
//        updateTopNavBarPosition();
//
//        $(window).resize(function () {
//            updateTopNavBarPosition();
//        });
//
//    }
//
//
//});
//
//function updateTopNavBarPosition() {
//    if ($("#wpadminbar").length)
//        if ($("#lispra_top_nav").length) {
//            if ($("#wpadminbar").css("position") == "fixed") {
//                $("#lispra_top_nav").css("top", $("#wpadminbar").height() + "px");
//                $(window).off("scroll", navBarSticktoAbsoulteWPAdminBarScrollUpdater);
//            } else if ($("#wpadminbar").css("position") == "absolute") {
////                a$("#lispra_top_nav").css("top", $("#wpadminbar").height() + "px");
//                $(window).scroll(navBarSticktoAbsoulteWPAdminBarScrollUpdater);
////                navBarSticktoAbsoulteWPAdminBarScrollUpdater();
//            }
//        }
//}
//
//function navBarSticktoAbsoulteWPAdminBarScrollUpdater() {
//
//    var scroll = $(window).scrollTop();
////    console.log("scroll : " + scroll);
//
//    if ($("#wpadminbar").length)
//        if ($("#lispra_top_nav").length) {
//            var top = 0;
//            var minTop = $("#wpadminbar").height();
//            var offset = (minTop - scroll);
//            if (offset > 0) {
//
//                top = Math.min(offset, minTop);
//            }
////            console.log("lispra_top_nav top offset : " + top + "px");
//            $("#lispra_top_nav").css("top", top + "px");
//
//        }
//
//
//
//
//
//}
//
//
//
//
////$(document).ready(function () {
////
////
////    $("[lispra-create-todo-list-form-button-add]").click(
////            function () {
////                var $btn = $(this).button('loading');
////                var $title_input = $(this).parents(".input-group").first().children("[lispra-create-todo-list-form-input-title]").first();
////
////                var $form_alert = $(this).parents(".panel-body").first().children("[lispra-dismissable-alert]").first();
////                var $form_alert_content = $form_alert.children("[lispra-dismissable-alert-content]").first();
////
////                var $title = $title_input.val();
////                var $list_class = "todo";
////
////                if ($title.length === 0) {
////                    console.log("add_list_form_button_add title is empty");
////                    $form_alert.attr("class", "alert alert-warning");
////                    $form_alert_content.text("Title is empty");
////                    $btn.button('reset');
////                    $form_alert.delay(7500).queue(function () {
////                        $(this).attr("class", "hidden");
////                    });
////                    return;
////                }
////
////                lispraUserAction(
////                        "createList",
////                        {
////                            list_name: $title,
////                            list_class: $list_class
////                        },
////                        function (data, status) {
////                            if (status == 'success') {
////                                var content = data;
////                                $form_alert.attr("class", "alert alert-success");
////                                $form_alert_content.html("Done");
////                                $form_alert.delay(10000).queue(function () {
////                                    $(this).attr("class", "hidden");
////                                });
////                            }
////                            $title_input.val("");
////                            $btn.button('reset');
////                            updateLispraComponentUserTodoListsList();
////                        },
////                        function (msg) {
////                            console.log("post done" + msg);
////                        },
////                        function (xhr, textStatus, errorThrown) {
////                            //alert(xhr.statusText);
////                            $form_alert.attr("class", "alert alert-danger");
////                            $form_alert_content.text("error : " + errorThrown);
////                            $form_alert.delay(10000).queue(function () {
////                                $(this).attr("class", "hidden");
////                            });
////                            $btn.button('reset');
////                        }
////                );
////            }
////    );
////
////
////});
//
//$(document).ready(
//        function () {
//            lispraCreateTodoListButtonsStart();
//            updateLispraComponentUserTodoListsList();
//
//        }
//);
//
//function lispraCreateTodoListButtonsStart() {
//
//
//    $("[lispra-create-todo-list-form-button-add]").click(
//            function () {
//                var $btn = $(this).button('loading');
//                var $title_input = $(this).parents(".input-group").first().children("[lispra-create-todo-list-form-input-title]").first();
//
//                var $form_alert = $(this).parents(".panel-body").first().children("[lispra-dismissable-alert]").first();
//                var $form_alert_content = $form_alert.children("[lispra-dismissable-alert-content]").first();
//
//                var $title = $title_input.val();
//                var $list_class = "todo";
//
//                if ($title.length === 0) {
//                    console.log("add_list_form_button_add title is empty");
//                    $form_alert.attr("class", "alert alert-warning");
//                    $form_alert_content.text("Title is empty");
//                    $btn.button('reset');
//                    $form_alert.delay(7500).queue(function () {
//                        $(this).attr("class", "hidden");
//                    });
//                    return;
//                }
//
//                lispraUserAction(
//                        "createList",
//                        {
//                            list_name: $title,
//                            list_class: $list_class
//                        },
//                        function (data, status) {
//                            if (status == 'success') {
//                                var content = data;
//                                $form_alert.attr("class", "alert alert-success");
//                                $form_alert_content.html("Done");
//                                $form_alert.delay(10000).queue(function () {
//                                    $(this).attr("class", "hidden");
//                                });
//                            }
//                            $title_input.val("");
//                            $btn.button('reset');
//                            updateLispraComponentUserTodoListsList();
//                        },
//                        function (msg) {
//                            console.log("createList post done");
//                        },
//                        function (xhr, textStatus, errorThrown) {
//                            //alert(xhr.statusText);
//                            $form_alert.attr("class", "alert alert-danger");
//                            $form_alert_content.text("error : " + errorThrown);
//                            $form_alert.delay(10000).queue(function () {
//                                $(this).attr("class", "hidden");
//                            });
//                            $btn.button('reset');
//                        }
//                );
//            }
//    );
//
//
//}
//function updateLispraComponentUserTodoListsList() {
//
//    if ($("[lispra-component='user-todo-lists-list']").length) {
//        lispraUserAction(
//                "getLists",
//                "*",
//                function (data, status) {
//                    o = JSON.parse(data);
//                    $("[lispra-component='user-todo-lists-list']").empty();
//                    o.forEach(
//                            function addToList($list) {
//                                var item_content = 'id : ' + $list.list_id + ' name : ' + $list.list_name;
//
//                                var $item = '<li class="list-group-item"><button type="button" class="btn close" lispra-action="deleteList" list_id="' + $list.list_id + '"  aria-label="Close"><span aria-hidden="true">&times;</span></button><span >' + item_content + '</span></li>';
//                                var $item_old = '<li class="list-group-item">' + item_content + '</li>';
//                                $("[lispra-component='user-todo-lists-list']").each(
//                                        function (i, e) {
//
//                                            $(this).append($item);
//                                        }
//                                );
//                            }
//                    );
//
//                    $("[type='button'][lispra-action='deleteList']").click(
//                            function () {
////                                console.log("Delete list button list is : " + $(this).attr("list_id"));
//                                lispraUserAction(
//                                        $(this).attr("lispra-action"),
//                                        $(this).attr("list_id"),
//                                        function (data, status) {
//                                            console.log("[type='button'][lispra-action='deleteList'] success");
//                                            updateLispraComponentUserTodoListsList();
//                                        },
//                                        function (data) {
//                                            console.log("[type='button'][lispra-action='deleteList'] done");
//                                        },
//                                        function (xhr, textStatus, errorThrown) {
//                                            console.log("[type='button'][lispra-action='deleteList'] fail");
//                                        });
//
//                            }
//                    );
//
//
//
//                },
//                function (msg) {
//                    console.log("[lispra-component='user-todo-lists-list'] update done");
//                },
//                function (xhr, textStatus, errorThrown) {
//                    console.log("[lispra-component='user-todo-lists-list'] update failed");
//                });
//    }
//
//}
//
//
//$(document).ready(
//        function () {
//            lispraCreateTodoTaskButtonsStart();
//            updateLispraComponentUserTodoList();
//
//        }
//);
//
//function lispraCreateTodoTaskButtonsStart() {
//
//
//    $("[lispra-create-todo-list-item-form-button-add]").click(
//            function () {
//
//                var $list_id = $(this).attr('list_id');
//                console.log("list_id is " + $list_id);
//                var $btn = $(this).button('loading');
//                var $title_input = $(this).parents(".input-group").first().children("[lispra-create-todo-list-item-form-input-title]").first();
//
//                var $form_alert = $(this).parents(".panel-body").first().children("[lispra-dismissable-alert]").first();
//                var $form_alert_content = $form_alert.children("[lispra-dismissable-alert-content]").first();
//
//                var $title = $title_input.val();
//                var $list_class = "todo";
//
//                if ($title.length === 0) {
//                    console.log("add_list_item_form_button_add title is empty");
//                    $form_alert.attr("class", "alert alert-warning");
//                    $form_alert_content.text("Item title is empty");
//                    $btn.button('reset');
//                    $form_alert.delay(7500).queue(function () {
//                        $(this).attr("class", "hidden");
//                    });
//                    return;
//                }
////                console.log("add_list_item_form_button_add title is " + $title);
////                $form_alert.attr("class", "alert alert-success");
////                $form_alert_content.text("Item title is " + $title);
////                $btn.button('reset');
////                $form_alert.delay(7500).queue(function () {
////                    $(this).attr("class", "hidden");});
//
//                lispraUserAction(
//                        "createListItem",
//                        {
//                            list_id: $list_id,
//                            title: $title,
//                        },
//                        function (data, status) {
//                            if (status == 'success') {
//                                alert(data);
//                                var content = data;
//                                $form_alert.attr("class", "alert alert-success");
//                                $form_alert_content.html("Done");
//                                $form_alert.delay(10000).queue(function () {
//                                    $(this).attr("class", "hidden");
//                                });
//                            }
//                            $title_input.val("");
//                            $btn.button('reset');
//                            updateLispraComponentUserTodoList();
//                        },
//                        function (msg) {
////                            console.log("post done" + msg);
//                        },
//                        function (xhr, textStatus, errorThrown) {
//                            //alert(xhr.statusText);
//                            $form_alert.attr("class", "alert alert-danger");
//                            $form_alert_content.text("error : " + errorThrown);
//                            $form_alert.delay(10000).queue(function () {
//                                $(this).attr("class", "hidden");
//                            });
//                            $btn.button('reset');
//                        }
//                );
//            }
//    );
//
//
//}
//function updateLispraComponentUserTodoList() {
//    if ($("[lispra-component='user-todo-list']").length) {
////        var $list_id = $(this).attr('list_id');
//        var $list_id = $("[lispra-component='user-todo-list']").first().attr('list_id');
//        console.log("updateLispraComponentUserTodoList list_id : " + $list_id);
//        lispraUserAction(
//                "getListContent",
//                $list_id,
//                function (data, status) {
////                    alert(data);
//                    o = JSON.parse(data);
//                    $("[lispra-component='user-todo-list'][list_id='" + $list_id + "']").empty();
//                    o.forEach(
//                            function addToList($task) {
//                                var item_content = 'id : ' + $task._id + ' title : ' + $task.title;
//
//                                var $item = '<li class="list-group-item"><button type="button" class="btn close" aria-label="Close"><span aria-hidden="true">&times;</span></button><span >' + item_content + '</span></li>';
//                                var $item_old = '<li class="list-group-item">' + item_content + '</li>';
//                                $("[lispra-component='user-todo-list'][list_id='" + $list_id + "']").each(
//                                        function (i, e) {
//
//                                            $(this).append($item);
//                                        }
//                                );
//                            }
//                    );
//
////                    $("[type='button'][lispra-action='deleteList']").click(
////                            function () {
//////                                console.log("Delete list button list is : " + $(this).attr("list_id"));
////                                lispraUserAction(
////                                        $(this).attr("lispra-action"),
////                                        $(this).attr("list_id"),
////                                        function (data, status) {
////                                            console.log("[type='button'][lispra-action='deleteList'] success");
////                                            updateLispraComponentUserTodoListsList();
////                                        },
////                                        function (data) {
////                                            console.log("[type='button'][lispra-action='deleteList'] done");
////                                        },
////                                        function (xhr, textStatus, errorThrown) {
////                                            console.log("[type='button'][lispra-action='deleteList'] fail");
////                                        });
////
////                            }
////                    );
//
//
//
//                },
//                function (msg) {
//                    console.log("[lispra-component='user-todo-list'] update done");
//                },
//                function (xhr, textStatus, errorThrown) {
//                    console.log("[lispra-component='user-todo-list'] update failed");
//                });
//    }
//
//}
//
//
//function serializedFormDataToObject(data) {
//    var inputs_chunks = data.split('&');
//    var inputs = {};
//    inputs_chunks.forEach(function (element, index, array) {
//        var item_chunks = element.split("=", 2);
//        if (item_chunks.length === 2) {
//            inputs[item_chunks[0]] = item_chunks[1];
//        }
//    });
//
//    return inputs;
//}
//
//function getLispraTestFormParts($f) {
//
//    var $form_data = $f.serialize();
//    var $inputs = serializedFormDataToObject($form_data);
//    var $btn = $f.find("[lispra-form-submit]");
//    var $form_alert = $f.find("[lispra-dismissable-alert]").first();
//    var $form_alert_content = $form_alert.children("[lispra-dismissable-alert-content]").first();
//
//    var out = {
//        inputs: $inputs,
//        submit_button: $btn,
//        dismissable_alert: $form_alert,
//        dismissable_alert_content: $form_alert_content
//    };
//
//    return out;
//}
//$(document).ready(
//        function () {
//            $("[lispra-component='test-form']").submit(
//                    function (event) {
//                        event.preventDefault();
//                        var o = getLispraTestFormParts($(this));
//                        var $title = o.inputs["title"];
//                        o.submit_button.button('loading');
//                        o.dismissable_alert.attr("class", "alert alert-success");
//                        o.dismissable_alert_content.html("Title is : " + $title);
//                        $(this).find("input").text('');
//                        setTimeout(function () {
//                            o.submit_button.button('reset');
//                        }, 5000);
//
//                    }
//            );
//        }
//);
//$(document).ready(
//        function () {
//            $("[lispra-component='create-todo-list-form']").submit(
//                    function (event) {
//
//                        event.preventDefault();
//
//                        var o = getLispraTestFormParts($(this));
//                        var $title = o.inputs["title"];
//                        var $list_class = "todo";
//                        var $inputs = $(this).find("input");
//                        o.submit_button.button('loading');
//                        o.dismissable_alert.attr("class", "alert alert-success");
//                        o.dismissable_alert_content.html("[lispra-component='create-todo-list-form'] Title is : " + $title);
//
//                        $(this).find("input").text('');
//
//
//                        if ($title.length === 0) {
////                            console.log("add_list_form_button_add title is empty");
//                            o.dismissable_alert.attr("class", "alert alert-warning");
//                            o.dismissable_alert_content.text("Title is empty");
//                            o.submit_button.button('reset');
//                            o.dismissable_alert.delay(7500).queue(function () {
//                                $(this).attr("class", "hidden");
//                            });
//                            return;
//                        }
//
//                        lispraUserAction(
//                                "createList",
//                                {
//                                    list_name: $title,
//                                    list_class: $list_class
//                                },
//                                function (data, status) {
//                                    if (status == 'success') {
//                                        var content = data;
//                                        o.dismissable_alert.attr("class", "alert alert-success");
//                                        o.dismissable_alert_content.html("Done");
//                                        o.dismissable_alert.delay(10000).queue(function () {
//                                            $(this).attr("class", "hidden");
//                                        });
//                                    }
////                                    $title_input.val("");
//                                    $inputs.val("");
//                                    o.submit_button.button('reset');
//                                    updateLispraComponentUserTodoListsList();
//                                },
//                                function (msg) {
//                                    console.log("createList post done");
//                                },
//                                function (xhr, textStatus, errorThrown) {
//                                    //alert(xhr.statusText);
//                                    o.dismissable_alert.attr("class", "alert alert-danger");
//                                    o.dismissable_alert_content.text("error : " + errorThrown);
//                                    o.dismissable_alert.delay(10000).queue(function () {
//                                        $(this).attr("class", "hidden");
//                                    });
//                                    o.submit_button.button('reset');
//                                }
//                        );
//
//
//
//                    }
//            );
//        }
//);
//$(document).ready(
//        function () {
//            $("[lispra-component='create-todo-list-item-form'][list_id]").submit(
//                    function (event) {
//
//                        event.preventDefault();
//
//                        var o = getLispraTestFormParts($(this));
//                        var $title = o.inputs["title"];
//                        var $list_id = $(this).attr("list_id");
//
//                        o.submit_button.button('loading');
//
//
//
//
//
//                        if ($title.length === 0) {
//                            console.log("[lispra-component='create-todo-list-item-form'] title is empty");
//                            o.dismissable_alert.attr("class", "alert alert-warning");
//                            o.dismissable_alert_content.text("Item title is empty");
//                            o.submit_button.button('reset');
//                            o.dismissable_alert.delay(7500).queue(function () {
//                                $(this).attr("class", "hidden");
//                            });
//                            return;
//                        }
//
//                        lispraUserAction(
//                                "createListItem",
//                                {
//                                    list_id: $list_id,
//                                    title: $title,
//                                },
//                                function (data, status) {
//                                    if (status == 'success') {
////                                        alert(data);
////                                        var content = data;
//                                        o.dismissable_alert.attr("class", "alert alert-success");
//                                        o.dismissable_alert_content.html("Done");
//                                        o.dismissable_alert.delay(10000).queue(function () {
//                                            $(this).attr("class", "hidden");
//                                        });
//                                    }
//                                    $(this).find("input").text('');
//                                    o.submit_button.button('reset');
//                                    updateLispraComponentUserTodoList();
//                                },
//                                function (msg) {
//                                    console.log("[lispra-component='create-todo-list-item-form'] item created");
//                                },
//                                function (xhr, textStatus, errorThrown) {
//                                    //alert(xhr.statusText);
//                                    o.dismissable_alert.attr("class", "alert alert-danger");
//                                    o.dismissable_alert_content.text("error : " + errorThrown);
//                                    o.dismissable_alert.delay(10000).queue(function () {
//                                        $(this).attr("class", "hidden");
//                                    });
//                                    o.submit_button.button('reset');
//                                }
//                        );
//
//
//                    }
//            );
//        }
//);
//$(document).ready(function () {
//
//    $("[hides-parent]").click(function () {
//        $(this).parent().addClass("hidden");
////        console.log("Parent hidden");
//    });
//});
//
//function lispraUserAction(name, data, f_succes, f_done, f_fail) {
//
//    console.log("lispraUserAction start");
//
//    var d = {
//        user_actions: [
//            {
//                name: name,
//                data: data
//            }
//        ]
//    };
//
//    $.post(
//            "../lispra_actions",
//            JSON.stringify(d),
//            f_succes
//            ).done(
//            f_done
//            ).fail(
//            f_fail
//            );
//    console.log("lispraUserAction end");
//}
