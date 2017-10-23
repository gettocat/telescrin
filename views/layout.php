
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title><?php echo $title ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta property="og:title" content="<?php echo htmlspecialchars(strip_tags($title)); ?>"/>
        <meta property="og:type" content="article"/>
        <meta property="og:url" content="<?php echo $metaurl; ?>"/>
        <meta property="og:image" content="http://<?php echo $_SERVER['HTTP_HOST'] ?><?php echo $metaimage; ?>"/>
        <meta property="og:description" content="<?php echo htmlspecialchars(strip_tags($metadescription)); ?>"/>
        <link rel="image_src" href="http://<?php echo $_SERVER['HTTP_HOST'] ?><?php echo $metaimage; ?>"/>
        <meta name="title" content="<?php echo htmlspecialchars(strip_tags($metatitle)); ?>" />
        <meta name="description" content="<?php echo htmlspecialchars(strip_tags($metadescription)); ?>" />
        <?php if ($metaimage): ?>
            <link rel="image_src" href="http://<?php echo $_SERVER['HTTP_HOST'] ?><?php echo $metaimage; ?>" />
        <?php endif; ?>

        <?php if ($metacanonical): ?>
            <link rel="canonical" href="<?php echo $metacanonical; ?>" />
        <?php endif; ?>

        <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="/assets/css/styles.css" rel="stylesheet">
        <link href="/assets/css/font-awesome.min.css" rel="stylesheet">
        <script src="/assets/js/jquery-3.2.1.min.js"></script>
        <script src="/assets/js/tools.js"></script>
        <script type="text/javascript" src="/assets/plugins/forge/forge.js"></script>
        <script src="/assets/js/telescrin.js"></script>
        <script type='text/javascript'>
            var app = {};

            window.scrin = new telescrin({
                uri: "<?php echo $_SERVER['REQUEST_URI'] ?>",
                token: {
                    value: "<?php echo md5($_SERVER['HTTP_USER_AGENT'] . '/' . $_SERVER['REMOTE_ADDR'] . '/' . (time() / 60)) ?>",
                    time: '<?php echo time() ?>'
                }
            })
        </script>  
    </head>

    <body>


        <div class="container content-container">

            <div class="errors">

            </div>

            <?php echo $content ?>


            <div class="footer">

                <div class="row">
                    <div class="links col text-left">
                        <span> <a href="/eb0c65722a14-Some words about telescr.in">About</a> </span>
                        <span> <a href="/a1863c2f9dd9-Hello world, or some words about orwell">Orwell protocol</a> </span>
                    </div>
                    <div class="col text-right">
                        All information is stored in <a target="_blank" href="http://orwellscan.org">Orwell blockchain</a>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col">
                        © <?php echo date("Y") ?> Powered by <a rel="nofollow" target="_blank" href="http://twitter.com/orwellcat">Nanocat</a><br /><a rel="nofollow" target="_blank" href="http://github.com/gettocat/telescrin">Source code</a>
                    </div>
                </div>

            </div>
        </div>

        <script src="/assets/js/popper.js"></script>
        <script src="/assets/js/bootstrap.min.js"></script>
    </body>
</html>
