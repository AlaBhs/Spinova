<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <!-- no index no follow -->
    <meta name="robots" content="noindex" />
    <meta name="robots" content="nofollow">
    <meta name="robots" content="noimageindex">
    <meta name="googlebot" content="noindex" />
    <meta name="googlebot" content="noindex">
    <meta name="googlebot-news" content="noindex" />
    <meta name="googlebot-news" content="nosnippet">
    
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="">

    <title><?= htmlspecialchars($title) ?></title>

    <!-- Fav Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
    <link href="/images/favicon.ico" rel="shortcut icon" />

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500|Poppins:400,500,600,700|Roboto:400,500" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Source+Sans+Pro:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="/vendors/css/bootstrap.css" />
    <link type="text/css" rel="stylesheet" href="/css/styles.css" />
</head>

<body id="login-page">
    <div class="login" id="login-container">
        <div class="login-screen">
            <div class="login-logo">
                <img class="brand-icon" src="/images/S.png">
            </div>

            <div class="login-logo-title mb-2">
                <h1 class="text-center">Spinova</h1>
            </div>

            <div class="app-title">
                <h4>Admin Login</h4>
            </div>

            <hr>

            <div class="login-form">
                <form action="/login" method="post">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                    <div class="control-group">
                        <input type="text" class="login-field" name="username" id="login-name" autocomplete="off" placeholder="username" required>
                        <label for="login-name"></label>
                    </div>

                    <div class="control-group">
                        <input type="password" class="login-field" name="password" placeholder="password" id="login-pass" required>
                        <label for="login-pass"></label>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="error-msg" id="error-msg">
                            <p><?= htmlspecialchars($error) ?></p>
                        </div>
                    <?php endif; ?>
          
                    <button class="btn btn-primary btn-large btn-block" type="submit">login</button>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="/vendors/js/jquery-3.6.0.js"></script>
    <script type="text/javascript" src="/vendors/js/popper.js"></script>
    <script type="text/javascript" src="/vendors/js/bootstrap.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.1.2/dist/ionicons/ionicons.esm.js"></script>
    <script type="text/javascript" src="/js/main.js"></script>
</body>
</html>