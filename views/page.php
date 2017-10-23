<?php if ($page['secure']): ?>

    <div class="encrypted_page container page-edit">
        <form action='' method='POST' onsubmit="scrin.enterEntryptedPage(); return false;">


            <div class="form-group row">
                <label for="password" class="col-sm-2 col-form-label">Enter password</label>
                <div class="col-7">
                    <input type="password" class="form-control" id="password" placeholder="Password">
                </div>
                <div class="col-3">
                    <button type="submit" class="btn btn-primary">Enter password</button>
                </div>
                <div class='col-sm-2'></div>
                <div class='col-sm-10'>
                    <small class="form-text text-muted">
                        This page is encrypted. To view this page you must enter a password.
                    </small>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">

                </div>
            </div>
        </form>
    </div>

<?php endif; ?>

<div class="page <?php if ($page['secure']) echo 'hide' ?>">

    <header class="page_header">
        <div class="row">
            <div class="col">
                <h1 class="title" id="title" dir="auto"></h1> 
            </div>
            <?php if ($page['canEdit']): ?>
                <div class="col-md-2 col-3">
                    <div id="editSection">
                        <a onclick="return scrin.initEdit()" id="editBtn" class="btn-edit btn-outline-dark btn" role="button">Edit</a>
                        <a onclick="return scrin.saveEdit()" id="saveBtn" class="hide btn-save btn-outline-dark btn" role="button">save</a>
                    </div>
                    <div class="hide" id="loader">
                        <span class="fa fa-cog fa-2x fa-spin"></span>
                    </div>
                </div>
            <?php endif ?>
        </div>
        <address class="meta" dir="auto">
            <a id="author" rel="author"></a>
            <time id="date" datetime=""></time>
        </address>
    </header>

    <div id="editor-container"></div>

    <div class="hide password_change form-group row">
        <label for="password" class="col-sm-2 col-form-label">Change password</label>
        <div class="col-sm-5">
            <input type="text" class="form-control" id="password_change" placeholder="Password">
        </div>
        <div class="col-sm-5">
            <input type="text" class="form-control" id="password_change2" placeholder="Confirm password">
        </div>
        <div class='col-sm-2'></div>
        <div class='col-sm-10'>
            <small class="form-text text-muted">
                You can encrypt content with password. In this case - read this page can only users with right password. By default - not encrypted.
            </small>
        </div>
    </div>



    <?php if (count($revisions) || $genesis): ?>
        <h3>Revisions</h3>
        <div class="revisions">

            <?php foreach ($revisions as $rev): ?>
                <div class="tx text-muted">
                    <a href="http://orwellscan.org/tx/<?php echo $rev ?>"><?php echo $rev ?></a>
                </div>
            <?php endforeach ?>

            <div class="tx text-muted">
                <a href="http://orwellscan.org/tx/<?php echo $genesis ?>"><?php echo $genesis ?></a>
            </div>

        </div>
    <?php endif ?>

</div>

<link rel="stylesheet" href="/assets/plugins/highlight/monokai-sublime.css" />
<link rel="stylesheet" href="/assets/plugins//quill/quill.snow.css" />
<script type="text/javascript" src="/assets/plugins/highlight/highlight.js"></script>
<script type="text/javascript" src="/assets/plugins/quill/quill.min.js"></script>
<script type="text/javascript" src="/assets/js/redactor.js"></script>
<script type="text/javascript">
                            scrin.initContent({
                                oid: "<?php echo htmlentities($page['oid']) ?>",
                                title: "<?php echo htmlentities($page['title']) ?>",
                                author: "<?php echo htmlentities($page['author']) ?>",
                                added_formated: "<?php echo date(DATE_ATOM, $page['added']) ?>",
                                added_text: "<?php echo date("F d, Y", $page['added']) ?>",
                                content: <?php echo json_encode($page['content']) ?>,
                                secureIv: "<?php echo $page['secureIv'] ?>",
                                secureAlgorithm: "<?php echo $page['secureAlgorithm'] ?>",
                                securityKey: "<?php echo $page['securityKey'] ?>",
                                secure: <?php echo intval($page['secure']) ?>
                            });
</script>