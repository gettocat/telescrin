<div class="container page-edit">
    <form action='' method='POST' onsubmit=" scrin.prepareToSend(); return false;">

        <div class="form-group row">
            <label for="title" class="col-sm-2 col-form-label">Title</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="title" placeholder="Title">
            </div>
        </div>


        <div class="form-group row">
            <label for="author" class="col-sm-2 col-form-label">Author</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="author" placeholder="Author">
            </div>
        </div>

        <div class="form-group row">
            <label for="text" class="col-sm-2 col-form-label"></label>
            <div class="col-sm-10">
                <div id="editor-container"></div>
            </div>

            <input type='hidden' id="text" >
        </div>

        <div class="form-group row">
            <label for="password" class="col-sm-2 col-form-label">Encrypt content</label>
            <div class="col-sm-5">
                <input type="text" class="form-control" id="password" placeholder="Password">
            </div>
            <div class="col-sm-5">
                <input type="text" class="form-control" id="password2" placeholder="Confirm password">
            </div>
            <div class='col-sm-2'></div>
            <div class='col-sm-10'>
                <small class="form-text text-muted">
                    You can encrypt content with password. In this case - read this page can only users with right password. By default - not encrypted.
                </small>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-2 col-form-label"></label>
            <div class="col-sm-10">
                <button id="saveButton" type="submit" class="btn btn-primary">Add page</button>
                <div class="hide" id="loader">
                    <span class="fa fa-cog fa-2x fa-spin"></span>
                </div>
            </div>
        </div>


    </form>
</div>

<link rel="stylesheet" href="/assets/plugins/highlight/monokai-sublime.css" />
<link rel="stylesheet" href="/assets/plugins//quill/quill.snow.css" />
<script type="text/javascript" src="/assets/plugins/highlight/highlight.js"></script>
<script type="text/javascript" src="/assets/plugins/quill/quill.min.js"></script>
<script type="text/javascript" src="/assets/js/redactor.js"></script>