(function (scrin) {
    var quill = new Quill('#editor-container', {
        modules: {
            formula: false,
            syntax: true,
            history: {
                delay: 2000,
                maxStack: 500,
                userOnly: false
            },
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'], // toggled buttons
                ['blockquote'],
                [{'header': 1}, {'header': 2}], // custom button values
                [{'list': 'ordered'}, {'list': 'bullet'}],
                [{'script': 'sub'}, {'script': 'super'}], // superscript/subscript
                [{'indent': '-1'}, {'indent': '+1'}], // outdent/indent
                [{'direction': 'rtl'}], // text direction
                [{'size': ['small', false, 'large', 'huge']}], // custom dropdown
                [{'header': [1, 2, 3, 4, 5, 6, false]}],
                [{'font': []}],
                [{'align': []}],
                ['image', 'video', 'link', 'code-block'],
                ['clean'] // remove formatting button
            ]
        },
        placeholder: 'Compose an epic...',
        theme: 'snow'
    });

    /* TODO:
     quill.getModule('toolbar').addHandler('image', function () {
     selectLocalImage();
     });
     
     
     function selectLocalImage() {
     var input = document.createElement('input');
     input.setAttribute('type', 'file');
     input.click();
     // Listen upload local image and save to server
     input.onchange = function () {
     var file = input.files[0];
     // file type is only image.
     if (/^image\//.test(file.type)) {
     saveToServer(file);
     } else {
     console.warn('You could only upload images.');
     }
     
     }
     }
     
     
     function saveToServer(file) {
     var fd = new FormData();
     fd.append("action", "image.upload");
     fd.append('image', file);
     var xhr = new XMLHttpRequest();
     xhr.open('POST', scrin.url, true);
     xhr.onload = function () {
     if (xhr.status === 200) {
     var url = JSON.parse(xhr.responseText).url;
     insertToEditor(url);
     }
     };
     xhr.send(fd);
     }
     
     
     function insertToEditor(url) {
     // push image url to rich editor.
     var range = quill.getSelection();
     quill.insertEmbed(range.index, 'image', url);
     }
     */

    scrin.redactor = quill;
})(scrin)

