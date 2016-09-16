function FormatNumberLength(num, length) {
    var r = "" + num;
    while (r.length < length) {
        r = "0" + r;
    }
    return r;
}
String.prototype.endsWith = function(suffix) {
    return this.indexOf(suffix, this.length - suffix.length) !== -1;
};
function endsWith(str, suffix) {
    return str.indexOf(suffix, str.length - suffix.length) !== -1;
}

function getObjectKeys(object) {
    var keys = [];
    for (var k in object)
        keys.push(k);
    return keys;
}
function cloneObject(source, destination) {
    var keys = getObjectKeys(source);
    for (var n = 0; n < keys.length; n++) {
        destination[keys[n]] = source[keys[n]];
    }
}
function objetToh(object) {
    var table = document.createElement('table');
    var keys = getObjectKeys(object);
    for (var n = 0; n < keys.length; n++) {
        if (keys[n] in object) {
            var value = object[keys[n]];
            var row = document.createElement('tr');
            var t = document.createElement('th');
            t.appendChild(document.createTextNode(keys[n]));
            row.appendChild(t);
            var v = document.createElement('td');
            if (typeof value === 'object') {
                var newTableID = tableID + "_table_" + keys[n];
                var rt = document.createElement('table');
                rt.id = newTableID;
                v.appendChild(rt);
                row.appendChild(v);
                table.appendChild(row);
                drawObjectInTable(newTableID, value);
            } else {
                v.appendChild(document.createTextNode(value));
                row.appendChild(v);
                table.appendChild(row);
            }



        }
    }
}
function arrayGetIndexWhereValueIs(array, value) {
    var i = [];
    for (var n = 0; n < array.length; n++) {
        if (array[n] === value) {
            i.push(n);
        }
    }
    return i;
}
function arrayReplaceValuesFromMapArray(array, values) {
    var i = [];
    for (var n in array) {
        if (array[n] in values) {
            i[n] = values[array[n]];
        } else {
            i[n] = array[n];
        }
    }
    return i;
}

function showObjectInDOMTable(object, table) {

//    var table = document.createElement('table');
    table.innerHTML = "";
    //  table.className = "table table-bordered table-striped";
    table.className = "table table-bordered";
    var tbody = document.createElement('tbody');

    var keys = getObjectKeys(object);
    for (var n = 0; n < keys.length; n++) {
        if (keys[n] in object) {
            var value = object[keys[n]];
            var row = document.createElement('tr');
            var t = document.createElement('th');
            t.appendChild(document.createTextNode(keys[n]));
            row.appendChild(t);
            var v = document.createElement('td');
            if ((typeof value === 'object') || (typeof value === 'array')) {
//                var newTableID = tableID + "_table_" + keys[n];
//                var rt = document.createElement('table');
                var nt = document.createElement('table');
                var rt = showObjectInDOMTable(nt, value);
//                var rt = objectToDOMTable(newChild);
//                rt.id = newTableID;
                v.appendChild(rt);
                row.appendChild(v);
                tbody.appendChild(row);
//                drawObjectInTable(newTableID, value);
            } else {
                v.appendChild(document.createTextNode(value));
                row.appendChild(v);
                tbody.appendChild(row);
            }



        }
    }
    table.appendChild(tbody);
    return table;
}

function objectToDOMTable(object) {

    var table = document.createElement('table');
     table.className = "table table-bordered";
//    return showObjectInDOMTable(table, object);

    var tbody = document.createElement('tbody');
    var keys = getObjectKeys(object);
    for (var n = 0; n < keys.length; n++) {
        if (keys[n] in object) {
            var value = object[keys[n]];
            var row = document.createElement('tr');
            var t = document.createElement('th');
            t.appendChild(document.createTextNode(keys[n]));
            row.appendChild(t);
            var v = document.createElement('td');
            if ((typeof value === 'object') || (typeof value === 'array')) {
//                var newTableID = tableID + "_table_" + keys[n];
//                var rt = document.createElement('table');
                var rt = objectToDOMTable(value);
//                rt.id = newTableID;
                v.appendChild(rt);
                row.appendChild(v);
                tbody.appendChild(row);
//                drawObjectInTable(newTableID, value);
            } else {
                v.appendChild(document.createTextNode(value));
                row.appendChild(v);
                tbody.appendChild(row);
            }



        }
    }
    table.appendChild(tbody);
    return table;
}

function drawObjectInTable(tableID, object) {

    var table = document.getElementById(tableID);
//    table.innerHTML = "";
    return showObjectInDOMTable(object,table);
//    var domTable = objectToDOMTable(object);
//    table.innerHTML = domTable.innerHTML;
}

function arrayToDOMTable(array, keyTitle, valueTitle, keyMap, valueMap) {

    if ((typeof keyTitle === 'undefined') || (keyTitle === null)) {
        keyTitle = "Key";
    }
    if ((typeof valueTitle === 'undefined') || (valueTitle === null)) {
        valueTitle = "Value";
    }
    if ((typeof keyMap === 'undefined')) {
        keyMap = null;
    }
    if ((typeof valueMap === 'undefined')) {
        valueMap = null;
    }


    var table = document.createElement('table');
    table.innerHTML = "";
    // titles
    var row = document.createElement('tr');
    var tk = document.createElement('th');
    var tv = document.createElement('th');
    tk.appendChild(document.createTextNode(keyTitle));
    tv.appendChild(document.createTextNode(valueTitle));
    row.appendChild(tk);
    row.appendChild(tv);
    table.appendChild(row);
    // values
    for (var n = 0; n < array.length; n++) {
        var r = document.createElement('tr');
        var k = document.createElement('td');
        var v = document.createElement('td');
        var key = n;
        var value = array[n];
        if (keyMap !== null) {
            if (key in keyMap) {
                key = keyMap[key];
            }
        }

        if (valueMap !== null) {
            if (value in valueMap) {
                value = valueMap[value];
            }
        }
        k.appendChild(document.createTextNode(key));
        v.appendChild(document.createTextNode(value));
        r.appendChild(k);
        r.appendChild(v);
        table.appendChild(r);
    }

    return table;
}

function drawArrayInTable(tableID, array, keyTitle, valueTitle, keyMap, valueMap) {

    var domTable = arrayToDOMTable(array, keyTitle, valueTitle, keyMap, valueMap);
    var table = document.getElementById(tableID);
    table.innerHTML = domTable.innerHTML;
//    if ((typeof keyTitle === 'undefined') || (keyTitle === null)) {
//        keyTitle = "Key";
//    }
//    if ((typeof valueTitle === 'undefined') || (valueTitle === null)) {
//        valueTitle = "Value";
//    }
//    if ((typeof keyMap === 'undefined')) {
//        keyMap = null;
//    }
//    if ((typeof valueMap === 'undefined')) {
//        valueMap = null;
//    }
//
//
//    var table = document.getElementById(tableID);
//    table.innerHTML = "";
//    // titles
//    var row = document.createElement('tr');
//    var tk = document.createElement('th');
//    var tv = document.createElement('th');
//    tk.appendChild(document.createTextNode(keyTitle));
//    tv.appendChild(document.createTextNode(valueTitle));
//    row.appendChild(tk);
//    row.appendChild(tv);
//    table.appendChild(row);
//    // values
//    for (var n = 0; n < array.length; n++) {
//        var r = document.createElement('tr');
//        var k = document.createElement('td');
//        var v = document.createElement('td');
//        var key = n;
//        var value = array[n];
//        if (keyMap !== null) {
//            if (key in keyMap) {
//                key = keyMap[key];
//            }
//        }
//
//        if (valueMap !== null) {
//            if (value in valueMap) {
//                value = valueMap[value];
//            }
//        }
//        k.appendChild(document.createTextNode(key));
//        v.appendChild(document.createTextNode(value));
//        r.appendChild(k);
//        r.appendChild(v);
//        table.appendChild(r);
//    }
}
function drawObjectKeysInTable(tableID, object, keys) {

    var table = document.getElementById(tableID);
    table.innerHTML = "";
    for (var n = 0; n < keys.length; n++) {
        if (keys[n] in object) {
            var row = document.createElement('tr');
            var t = document.createElement('th');
            var v = document.createElement('td');
            t.appendChild(document.createTextNode(keys[n]));
            v.appendChild(document.createTextNode(object[keys[n]]));
            row.appendChild(t);
            row.appendChild(v);
            table.appendChild(row);
        }
    }
}

function createSelect(context, id, values) {
    var selectList = document.createElement("select");
    if (id !== null) {
        selectList.id = id;
    }
    if (context !== null) {
        context.appendChild(selectList);
    }
//Create and append the options
    for (var i = 0; i < values.length; i++) {
        var option = document.createElement("option");
        option.value = values[i];
        option.text = values[i];
        selectList.appendChild(option);
    }
    return selectList;
}
function createSelectWithValuesAndText(context, id, values, texts) {
    var selectList = document.createElement("select");
    if (context !== null) {
        context.appendChild(selectList);
    }
    if (id !== null) {
        selectList.id = id;
    }

//Create and append the options
    for (var i = 0; i < values.length; i++) {
        var option = document.createElement("option");
        option.value = values[i];
        option.text = texts[i];
        selectList.appendChild(option);
    }
    return selectList;
}

function setSelectText(select, values) {

    while (select.options.length > 0) {
        select.options[0] = null;
    }
//Create and append the options
    for (var i = 0; i < values.length; i++) {
        var option = document.createElement("option");
        option.text = values[i];
        select.add(option);
    }
    return select;
}
function createInput(context, id, type, value, func) {
    var input = document.createElement("input");
    if (type !== null)
        input.type = type;
    if (id !== null)
        input.id = id;
    if (value !== null)
        input.value = value;
    if (func !== null)
        input.onclick = func;
    if (context !== null)
        context.appendChild(input);
    return input;
}
function createButton(context, id, value, func) {
    var button = document.createElement("input");
    button.type = "button";
    if (id !== null)
        button.id = id;
    if (value !== null)
        button.value = value;
    if (func !== null)
        button.onclick = func;
    if (context !== null)
        context.appendChild(button);
    return button;
}

var oneDayMS = 24 * 60 * 60 * 1000;
function getDayStartDate(timeOrDate) {
    var date;
//    if( (typeof (timeOrDate)) === 'undefined'){
//        date = new Date();
//        var todayStartMS = (date.getTime() - (date.getTime() % (oneDayMS)));
//        return new Date(todayStartMS + (60000*date.getTimezoneOffset()));
//    } else{
//        date = new Date(timeOrDate);
//    }
    var date = (((typeof (timeOrDate)) === 'undefined')) ? new Date() : new Date(timeOrDate);
    var ts = date.getTime() - 60000 * date.getTimezoneOffset();
    var todayStartMS = (ts - (ts % (oneDayMS)));
    return new Date(todayStartMS + (60000 * date.getTimezoneOffset()));
}

function getPreviousDayStartDate(timeOrDate) {

    var today = getDayStartDate(timeOrDate);
    return new Date(today.getTime() - oneDayMS);
//    var date = (((typeof (timeOrDate)) === 'undefined')) ? new Date() : new Date(timeOrDate);
//    var oneDayMS = 24 * 60 * 60 * 1000;
//    var todayStartMS = date.getTime() - (date.getTime() % (oneDayMS));
//    return new Date(todayStartMS - oneDayMS);
}
function getNextDayStartDate(timeOrDate) {
    var today = getDayStartDate(timeOrDate);
    return new Date(today.getTime() + oneDayMS);
//    var date = (((typeof (timeOrDate)) === 'undefined')) ? new Date() : new Date(timeOrDate);
//    var oneDayMS = 24 * 60 * 60 * 1000;
//    var todayStartMS = date.getTime() - (date.getTime() % (oneDayMS));
//    return new Date(todayStartMS + oneDayMS);
}

function getDayStartUTC(timeOrDate) {
//    var date = (((typeof (timeOrDate)) === 'undefined')) ? new Date() : new Date(timeOrDate);
//    var oneDayMS = 24 * 60 * 60 * 1000;
//    var todayStartMS = date.getTime() - (date.getTime() % (oneDayMS));
//    return new Date(todayStartMS).toUTCString();

    return getDayStartDate(timeOrDate).toUTCString();
}

function getPreviousDayStartUTC(timeOrDate) {
//    var date = (((typeof (timeOrDate)) === 'undefined')) ? new Date() : new Date(timeOrDate);
//    var oneDayMS = 24 * 60 * 60 * 1000;
//    var todayStartMS = date.getTime() - (date.getTime() % (oneDayMS));
//    return new Date(todayStartMS - oneDayMS).toUTCString();
    return getPreviousDayStartDate(timeOrDate).toUTCString();
}
function getNextDayStartUTC(timeOrDate) {
//    var date = (((typeof (timeOrDate)) === 'undefined')) ? new Date() : new Date(timeOrDate);
//    var oneDayMS = 24 * 60 * 60 * 1000;
//    var todayStartMS = date.getTime() - (date.getTime() % (oneDayMS));
//    return new Date(todayStartMS + oneDayMS).toUTCString();
    return getNextDayStartDate(timeOrDate).toUTCString();
}

function getGETResponse(requestURL, data, dataType) {

    var jsonData = $.ajax({
        url: requestURL,
        data: data,
        dataType: dataType,
        async: false
    }).responseText;
    return jsonData;
}
function getResponse(requestURL, data) {

    var o = {
        url: requestURL,
        dataType: "text/json",
        async: false
    };
    if (typeof data !== 'undefined') {
        o.data = data;
    }
    var jsonData = $.ajax(o).responseText;
    return jsonData;
}
function getJSONFromURL(requestURL) {

    var jsonData = $.ajax({
        url: requestURL,
        dataType: "text/json",
        async: false
    }).responseText;
    return jsonData;
}


