<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <!-- meta -->
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

    <title><?php echo htmlspecialchars($title ?? 'Spinova'); ?></title>

    <!-- Fav Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
    <link href="/images/favicon.ico" rel="shortcut icon" />

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500|Poppins:400,500,600,700|Roboto:400,500" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Source+Sans+Pro:wght@400;600;700;900&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
    <link type="text/css" rel="stylesheet" href="/vendors/css/bootstrap.css" />
    <link type="text/css" rel="stylesheet" href="/css/styles.css" />

    <script type="text/javascript">
        if (localStorage.theme) document.documentElement.setAttribute("data-theme", localStorage.theme);
    </script>

</head>

<body class="header-fixed sidebar-fixed sidebar-dark header-light" id="body">

    <div class="wrapper">

        <aside class="left-sidebar bg-sidebar">
            <div id="sidebar" class="sidebar sidebar-with-footer">

                <div class="app-brand">
                    <a href="/dashboard" title="Spinova" class="pl-3">
                        <img class="brand-icon" src="/images/S.png">
                        <span class="brand-name text-truncate">Spinova</span>
                    </a>
                </div>

                <div>
                    <ul class="nav sidebar-inner" id="sidebar-menu">
                        <li>
                            <a class="sidenav-item-link" href="/dashboard" title="Links" role="button">
                                <ion-icon name="link"></ion-icon>
                                <span class="nav-text">Links</span>
                            </a>
                        </li>
                        <li>
                            <a class="sidenav-item-link" href="/create" title="Add Link" role="button">
                                <ion-icon name="add-outline"></ion-icon>
                                <span class="nav-text">Add Link</span>
                            </a>
                        </li>

                        <li>
                            <a class="sidenav-item-link" href="/archive" title="Archive" role="button">
                                <ion-icon name="archive"></ion-icon>
                                <span class="nav-text">Archive</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="sidebar-footer">
                    <hr class="separator mb-0" />
                    <div class="sidebar-footer-content" id="footer-links">
                        <ul class="nav sidebar-inner">
                            <?php if (isset($currentUser)) : ?>
                                <li>
                                    <a class="sidenav-item-link" title="<?php echo htmlspecialchars($currentUser['username'] ?? ''); ?>">
                                        <ion-icon name="person-sharp"></ion-icon>
                                        <span class="nav-text"><?php echo htmlspecialchars($currentUser['username'] ?? ''); ?></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/settings" class="sidenav-item-link" title="Settings">
                                        <ion-icon name="settings-outline"></ion-icon>
                                        <span class="nav-text">Settings</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/logout" class="sidenav-item-link" title="Logout">
                                        <ion-icon name="power-outline"></ion-icon>
                                        <span class="nav-text">Logout</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </aside>

        <div class="page-wrapper">
            <header class="main-header" id="header">
                <nav class="navbar navbar-static-top navbar-expand-lg">
                    <div class="header-toggle-btn">
                        <a id="sidebar-toggler" class="sidebar-toggle">
                            <ion-icon name="chevron-back-outline"></ion-icon>
                            <span class="sr-only">Toggle navigation</span>
                        </a>
                    </div>

                    <div class="header-title">
                        <h4>Dashboard</h4>
                    </div>

                    <div class="navbar-right ml-auto">
                        <ul class="nav navbar-nav">
                            <?php if (isset($error) && !empty($error)) : ?>
                                <li class="error-msg mr-3">
                                    <ion-icon name="warning" class="mr-1"></ion-icon>
                                    <p class="m-0 p-0"><?php echo htmlspecialchars($error); ?></p>
                                </li>
                            <?php endif; ?>

                            <?php if (isset($success) && !empty($success)) : ?>
                                <li class="success-msg mr-3">
                                    <ion-icon name="checkmark-done-outline" class="mr-1"></ion-icon>
                                    <p class="m-0 p-0"><?php echo htmlspecialchars($success); ?></p>
                                </li>
                            <?php endif; ?>

                            <li class="disp" id="dark-mode-button">
                                <input id="chck" type="checkbox">
                                <label for="chck" id="dark-mode-icon">
                                    <ion-icon name="sunny-outline" id="light-icon"></ion-icon>
                                    <ion-icon name="moon" id="dark-icon"></ion-icon>
                                </label>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>