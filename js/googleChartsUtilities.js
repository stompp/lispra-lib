
function LispraHelper() {

}

//LispraHelper.baseUrl = function(){
//    return "lispra.riggitt.org";
//};

LispraHelper.fullBaseUrl = function(){
    return "http://lispra.riggitt.org";
};

LispraHelper.formatServerPath = function(path) {

//    alert(document.domain);       
    var p = path;
    var x =  LispraHelper.fullBaseUrl() + "/" + p;
//        alert("P " + p + " X " + x);
//    alert(x);
    return x;
    

};

RiggittHelper.getServerScriptResponse = function(path, data) {

    var requestURL = RiggittHelper.formatServerPath(path);
    var o = {
        url: requestURL,
        dataType: "text/json",
        async: false
    };
    if (typeof data !== 'undefined') {
        o.data = data;
    }
    var r = $.ajax(o).responseText;
    return r;
};

RiggittHelper.getServerScriptResponseAsync = function(path, data, func) {

    var requestURL = RiggittHelper.formatServerPath(path);

    if (data !== null) {
        $.get(requestURL, data, func, 'text');
    } else {
        $.get(requestURL, func, 'text');
    }

};
RiggittHelper.postServerScriptResponseAsync = function(path, data, func) {

    var requestURL = RiggittHelper.formatServerPath(path);

    if (data !== null) {
        $.post(requestURL, data, func, 'text');
    } else {
        $.post(requestURL, func, 'text');
    }

};








