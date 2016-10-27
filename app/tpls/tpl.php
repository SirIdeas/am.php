(: $siteTitle='Amathista Framework'
(: $pageTitle='Home'
<html>
  <head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="(:/:)/favicon.ico"/>
    <title>
      (:= $siteTitle :) | (:= $pageTitle :)
    </title>
    
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link rel="stylesheet" href="(:/:)/fixes/ie10-viewport-bug-workaround.css">
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="(:/:)/fixes/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="(:/:)/fixes/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    (: child :)
    <!-- 
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-32728007-15', 'auto');ga('send', 'pageview');
    </script>
    -->
    <!-- <script type="text/javascript" src="(:/:)/fixes/docs.min.js"></script> -->
    <script type="text/javascript" src="(:/:)/fixes/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>