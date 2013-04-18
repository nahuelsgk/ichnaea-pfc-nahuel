<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Nahuel Velazco">
    <script src="/js/jquery/jquery-1.8.3.js"></script>
    <script src="/js/jquery/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="/js/jsrender/jsrender.js"></script>
    <title>Ichnaea Web Services</title>
    <link href="/css/bootstrap/bootstrap.css" rel="stylesheet">
    <style type="text/css">
    body {
      padding-top: 60px;
      padding-bottom: 40px;
    }
    </style>
    <link href="/css//bootstrap/bootstrap-responsive.css" rel="stylesheet">
    <link href="/css/ichnaea.css" rel="stylesheet">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->
<link rel="stylesheet" type="text/css" href="/css/notifications.css">
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="/js/bootstrap/assets/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="/js/bootstrap/assets/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="/js/bootstrap/assets/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="/js/bootstrap/assets/ico/apple-touch-icon-57-precomposed.png">
<link href="/css/pnotify/jquery.pnotify.default.css" media="all" rel="stylesheet" type="text/css" />
<link href="/css/pnotify/jquery.pnotify.default.icons.css" media="all" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="/js/assets/ico/favicon.png">
<script src="/js/bootstrap/assets/js/bootstrap-transition.js"></script>
<script src="/js/bootstrap/assets/js/bootstrap-alert.js"></script>
<script src="/js/bootstrap/assets/js/bootstrap-modal.js"></script>
<script src="/js/bootstrap/assets/js/bootstrap-dropdown.js"></script>
<script src="/js/bootstrap/assets/js/bootstrap-scrollspy.js"></script>
<script src="/js/bootstrap/assets/js/bootstrap-tab.js"></script>
<script src="/js/bootstrap/assets/js/bootstrap-tooltip.js"></script>
<script src="/js/bootstrap/assets/js/bootstrap-popover.js"></script>
<script src="/js/bootstrap/assets/js/bootstrap-button.js"></script>
<script src="/js/bootstrap/assets/js/bootstrap-collapse.js"></script>
<script src="/js/bootstrap/assets/js/bootstrap-carousel.js"></script>
<script src="/js/bootstrap/assets/js/bootstrap-typeahead.js"></script>
<script src="/js/ichnaea.common.js"></script>
<script src="/js/notification.js"></script>
<script type="text/javascript" src="/js/pnotify/jquery.pnotify.min.js"></script>

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
               <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Project <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="/project">My projects</a></li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Matrix <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="/matrix/">System's matrix</a></li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Variables <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="/single_variables">System's variables</a></li>
                </ul>
              </li>
              <li><a href="/project/logout">Logout</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
<?= $content; ?>
</body>
</html>
