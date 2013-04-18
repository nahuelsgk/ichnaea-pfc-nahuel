<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Nahuel Velazco">
    <script src="/js/jquery-1.8.3.js"></script>
    <script src="/js/jquery-ui-1.9.2.custom.min.js"></script>
    <title>Dashboard</title>
    <link href="/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
    body {
      padding-top: 60px;
      padding-bottom: 40px;
    }
    </style>
    <link href="/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="/css/ichnaea.css" rel="stylesheet">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->
<link rel="stylesheet" type="text/css" href="/css/notifications.css">


<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/js/assets/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/js/assets/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/js/assets/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="/js/assets/ico/apple-touch-icon-57-precomposed.png">
<link href="/css/jquery.pnotify.default.css" media="all" rel="stylesheet" type="text/css" />
<link href="/css/jquery.pnotify.default.icons.css" media="all" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="/js/assets/ico/favicon.png">

</head>
<body>

<div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="/home">Ichnaea</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="/account/settings">Welcome</a></li>
              <li><a href="/admin/users">Admin Users</a></li>
               <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Projects <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="/home">My projects</a></li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Matrix <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="/matrix/my-matrixs">System's matrix</a></li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Variables <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="/vars/list_vars">System's variables</a></li>
                </ul>
              </li>
              <li><a href="/logout">Logout</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
<div class="container">
<?php echo $content; ?>
</div>
</body>
<script src="/js/assets/js/bootstrap-transition.js"></script>
<script src="/js/assets/js/bootstrap-alert.js"></script>
<script src="/js/assets/js/bootstrap-modal.js"></script>
<script src="/js/assets/js/bootstrap-dropdown.js"></script>
<script src="/js/assets/js/bootstrap-scrollspy.js"></script>
<script src="/js/assets/js/bootstrap-tab.js"></script>
<script src="/js/assets/js/bootstrap-tooltip.js"></script>
<script src="/js/assets/js/bootstrap-popover.js"></script>
<script src="/js/assets/js/bootstrap-button.js"></script>
<script src="/js/assets/js/bootstrap-collapse.js"></script>
<script src="/js/assets/js/bootstrap-carousel.js"></script>
<script src="/js/assets/js/bootstrap-typeahead.js"></script>
<script src="/js/ichnaea.common.js"></script>
<script src="/js/notification.js"></script>
<script type="text/javascript" src="/js/jquery.pnotify.min.js"></script>
</html>
 
