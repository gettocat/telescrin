var telescrin = function (pageoptions) {
    this.url = pageoptions.uri;
    this.action = pageoptions.uri == '/' ? 'create' : 'show';
    this.token = pageoptions.token;
    this.id = forge.util.bytesToHex(forge.random.getBytesSync(32));
    this.iv = forge.util.bytesToHex(forge.random.getBytesSync(32));
    this.password = '';
}


telescrin.prototype.prepareToSend = function () {
    var error = false;

    if (this.submit)
        return false;

    try {
        var data = {
            title: '',
            content: '',
            author: '',
            securityKey: '',
        };

        var text = this.redactor.getContents();
        data.content = text.ops;
        data.title = $("#title").val();
        data.author = $("#author").val();
        var password = $("#password").val();
        var password2 = $("#password2").val();

        if (password && password != password2)
            throw new Error('Passwords is not equal');

        if (password) {

            //generate digest from securityKey and send it to blockchain. Edit message can only user with sha256(sha256(password)) == securityKey
            data.title = this.encrypt(data.title, password);
            data.author = this.encrypt(data.author, password);
            data.securityKey = this.sha256d(password);
            data.content = this.encrypt(JSON.stringify(data.content), password);
            data.secure = 1;
            data.secureIv = this.iv;
            data.secureAlgorithm = 'AES-CBC';
            data.oid = 0xffffffff;
            var bytes = this.getSize(JSON.stringify(data));

            console.log(JSON.stringify(data), bytes, bytes / 1024, 'kb');
            if (bytes > 1 * 1024 * 1024)
                throw new Error('You page can not be more then 1MB. Please split this page for 2-3 smaller pages, now size is ' + Math.round(bytes / 1024 / 1024, 2) + " MB");

        } else {


            if (data.title.length < 20)
                throw new Error('Title is too small');

            if (data.content.length < 1)
                throw new Error('Content cant be empty');

            if (data.content.length == 1) {
                if (data.content[0].insert == '\n')
                    throw new Error('Content cant be empty');
            }

            data.oid = 0xffffffff;
            var bytes = this.getSize(JSON.stringify(data));

            console.log(JSON.stringify(data), bytes, bytes / 1024, 'kb');
            if (bytes > 1 * 1024 * 1024)
                throw new Error('You page can not be more then 1MB. Please split this page for 2-3 smaller pages, now size is ' + Math.round(bytes / 1024 / 1024, 2) + " MB");

            data.content = JSON.stringify(data.content);//for safety

        }

    } catch (e) {
        error = true;
        this.showError(e.message, 10000);
        console.log(e);
        //todo show error on page
    }

    console.log(data);
    if (!error) {
        this.save(data);
        this.submit = true;
        $("#saveButton").addClass('hide');
        $("#editSection").addClass('hide');
        $("#loader").removeClass('hide')
    }
    return false;

}

telescrin.prototype.save = function (content, isEdit) {
    var f = this;
    sar("content.save", content, function (resp) {

        f.submit = false;
        $("#saveButton").removeClass('hide');
        $("#editSection").removeClass('hide');
        $("#loader").addClass('hide')

        console.log(resp);
        if (resp.errors && resp.errors.length) {
            for (var i in resp.errors) {
                f.showError(resp.errors[i], 10000);
            }
        } else if (resp.oid) {
            if (!isEdit)
                location.href = "/" + resp.oid + "-" + resp.title_url;
            else {
                f.showError('info saved in tx <a target="_blank" href="http://orwellscan.org/tx/' + resp.txid + '">' + resp.txid + '</a>', 30000, 'alert-success')
            }
        }
        //redirect to resp.oid+"-"+resp.title_translit
    })

    return false;

}

telescrin.prototype.enterEntryptedPage = function () {

    var password = $("#password").val();
    if (this.data.securityKey == this.sha256d(password)) {
        this.password = password;
        $(".encrypted_page").addClass('hide')
        $(".page").removeClass('hide');
        this.data.title = this.decrypt(this.data.secureIv, this.data.title, this.password).content;
        $("title").html(this.data.title);
        this.data.author = this.decrypt(this.data.secureIv, this.data.author, this.password).content;
        this.data.content = JSON.parse(this.decrypt(this.data.secureIv, this.data.content, this.password).content);
        console.log(this.data);
        this.initContent(this.data);
    } else {
        this.showError("password is wrong", 10000);
    }

}

telescrin.prototype.initContent = function (content) {
    this.redactor.enable(false);
    $("#title").html(content.title)
    $("#author").html(content.author);
    $("#date").attr('datetime', content.added_formated).html(content.added_text);
    this.redactor.setContents(content.content)
    this.data = content;
}

telescrin.prototype.initEdit = function () {
    scrin.redactor.enable(true);
    $("#title").attr('contenteditable', true);
    $(".page").addClass('page-editing')

    //show save button
    $(".btn-save").removeClass('hide')
    $(".btn-edit").addClass('hide');
    $("#title").focus();

    //show password change form
    if (this.password) {
        $('.password_change').removeClass('hide')
    }
}

telescrin.prototype.saveEdit = function () {

    this.redactor.enable(false);
    $("#title").attr('contenteditable', false);
    $(".page").removeClass('page-editing')

    //show save button
    $(".btn-save").addClass('hide')
    $(".btn-edit").removeClass('hide');

    if (this.password) {
        var newpass = $("#password_change").val().trim();
        if (newpass) {
            if (newpass != $("#password_change2").val().trim()) {
                this.showError("password is not equal. Using old password")
            } else {
                this.password = newpass;
                this.showError("password was changed", null, 'alert-info')
            }
        }
        $('.password_change').addClass('hide')
    }

    var obj = {};
    if (this.password) {
        this.data.title = $("#title").html()
        this.data.content = this.redactor.getContents();
        this.data.title = this.encrypt(this.data.title, this.password);
        this.data.author = this.encrypt(this.data.author, this.password);
        this.data.content = this.encrypt(JSON.stringify(this.data.content), this.password);
        this.data.secureIv = this.iv;
        //todo: change password
        //this.data.securityKey = this.sha256d(this.password);
        //data.secureAlgorithm = 'AES-CBC';

        obj = {
            oid: this.data.oid,
            title: this.data.title,
            content: this.data.content,
            secureIv: this.data.secureIv,
            securityKey: this.sha256d(this.password),
            author: this.data.author, //we need change author encryption too, because initial vector is changed.
        }
    } else {
        this.data.title = $("#title").html()
        this.data.content = this.redactor.getContents();
        this.data.content = JSON.stringify(this.data.content)
        obj = {
            oid: this.data.oid,
            title: this.data.title,
            content: this.data.content
        }
    }

    $("#editSection").addClass('hide');
    $("#loader").removeClass('hide')
    console.log(obj)
    this.save(obj, true);
}

telescrin.prototype.getKeyByPassword = function (password) {
    return forge.pkcs5.pbkdf2('password', password, 128, 16);
    ;
}

telescrin.prototype.sha256d = function (data) {
    var md = forge.md.sha256.create();
    md.update(data);
    var hex = md.digest().toHex();
    md = forge.md.sha256.create();
    md.update(hex);
    return md.digest().toHex()
}

telescrin.prototype.getSize = function (data) {
    var bytes = 0
    if (window['Blob'])
        bytes = new Blob([JSON.stringify(data)]).size;
    else
        bytes = byteLength(JSON.stringify(data));

    return bytes;
}

telescrin.prototype.encrypt = function (data, password) {
    var iv = forge.util.hexToBytes(this.iv);
    var buff = forge.util.createBuffer(data, 'utf8');
    var cipher = forge.cipher.createCipher('AES-CBC', this.getKeyByPassword(password));
    cipher.start({iv: iv});
    cipher.update(buff);
    cipher.finish();
    var encrypted = cipher.output;
    return encrypted.toHex()
}

telescrin.prototype.decrypt = function (iv, data, password) {
    iv = forge.util.hexToBytes(iv)
    data = forge.util.createBuffer(forge.util.hexToBytes(data), 'raw');
    var decipher = forge.cipher.createDecipher('AES-CBC', this.getKeyByPassword(password));
    decipher.start({iv: iv});
    decipher.update(data);
    var result = decipher.finish(); // check 'result' for true/false
// outputs decrypted hex
    return {content: forge.util.decodeUtf8(decipher.output.getBytes()), result: result};
}

telescrin.prototype.showError = function (error, dismiss, type) {
    if (!type)
        type = 'alert-warning';

    var k = rand(0, 10e6);
    if (dismiss) {
        setTimeout(function () {
            $("#error" + k).remove();
        }, dismiss)
    }

    $(".errors").append("<div id='error" + k + "' class='alert " + type + "'>" + error + " <button type='button' onclick='$(this).closest(\".alert\").remove();return false;' class='close' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>")
    $('html, body').animate({
        scrollTop: $(".errors").offset().top
    }, 1000);
}

function byteLength(str) {
    // returns the byte length of an utf8 string
    var s = str.length;
    for (var i = str.length - 1; i >= 0; i--) {
        var code = str.charCodeAt(i);
        if (code > 0x7f && code <= 0x7ff)
            s++;
        else if (code > 0x7ff && code <= 0xffff)
            s += 2;
        if (code >= 0xDC00 && code <= 0xDFFF)
            i--; //trail surrogate
    }
    return s;
}
