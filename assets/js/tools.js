var app = {};

function rand(min, max) {
    return Math.round(Math.random() * (max - min + 1)) + min;
}

function ar(url, action, data, callback) {
    data.token = scrin.token.value;
    data.token_time = scrin.token.time;
    data.pageId = scrin.id;
    $.ajax(url, {
        data: {
            action: action,
            data: data
        },
        dataType: 'json',
        type: 'POST',
        success: function (resp) {

            if (resp.token) {
                scrin.token = resp.token;
            }

            if (resp.status == "OK" || resp.status == 1)
                callback(resp);
            else
                console.log("error:" + JSON.stringify(resp));
        }
    });
}

function sar(action, data, callback) {
    return ar(scrin.url, action, data, callback);
}


function generateId(len) {
    if (window['Uint8Array'] && window['crypto']) {
        var arr = new Uint8Array((len || 40) / 2)
        window.crypto.getRandomValues(arr)
        return Array.from(arr, function (dec) {
            return ('0' + dec.toString(16)).substr(-2)
        }).join('')
    } else {
        var arr = [], i = 0
        for (; i++ < len; ) {
            arr.push((Math.random().toString(36) + '00000000000000000').slice(2, 13));
        }

        var str = arr.join(''), start = rand(0, str.length - len - 1);
        return str.slice(start, len);
    }
}