(:: set:siteTitle='Amathista Framework' :)
(:: set:pageTitle='Home' :)
<html>
  <head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <title>
      (:= $siteTitle :) |
      (:= $pageTitle :)
    </title>
    <link rel="shortcut icon" href="(:/:)/favicon.ico"/>
    <link rel="stylesheet" href="(:/:)/css/fonts.css"/>
    <link rel="stylesheet" href="(:/:)/css/styles.css"/>
    <link rel="stylesheet" href="(:/:)/vendor/prism/prism.css"/>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    (:: child :)
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', 'UA-32728007-15', 'auto');ga('send', 'pageview');
    </script>
    <script type="text/javascript" src="(:/:)/vendor/jquery/jquery.js"></script>
    <script type="text/javascript" src="(:/:)/vendor/prism/prism.js"></script>
    <script type="text/javascript" src="(:/:)/js/main.js"></script>
  </body>
</html>