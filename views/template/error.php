<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="">

    <title><?= htmlspecialchars($title) ?></title>

    <!-- Fav Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
    <link rel="manifest" href="/images/">
    <link href="/images/favicon.ico" rel="shortcut icon" />

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@600;900&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
    <link type="text/css" rel="stylesheet" href="/vendors/css/bootstrap.css" />
    <link type="text/css" rel="stylesheet" href="/css/styles.css" />

    <script src="https://kit.fontawesome.com/4b9ba14b0f.js" crossorigin="anonymous"></script>
</head>

<body id="not-found-page">

    <?php if (!empty($error)) : ?>

        <div class="mainbox">
            <div class="err">4</div>
                <i class="far fa-question-circle fa-spin"></i>
                <div class="err2">4</div>
                    <div class="msg">
                        Maybe this link moved? Got deleted? Never existed in the first place?
                        <p class="my-5" style="font-size: 2rem; font-weight: 600;"><?= htmlspecialchars($error) ?></p>
                    </div>
                </div>
                
    <?php else : ?>
        <div class="mainbox">
            <div class="err">
                4
            </div>
            <i class="far fa-question-circle fa-spin"></i>
            <div class="err2">
                4
            </div>
            <div class="msg">
                Maybe this page moved? Got deleted? Never existed in the first place?
                <p class="my-5" style="font-size: 2rem; font-weight: 600;">Let's go 
                    <a href="/dashboard">home</a> and try from there.
                </p>
            </div>
        </div>
    <?php endif; ?>

</body>
</html>