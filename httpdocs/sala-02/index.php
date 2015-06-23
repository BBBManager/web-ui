<!DOCTYPE html><!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="pt-br" prefix="og: http://ogp.me/ns#"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="pt-br" prefix="og: http://ogp.me/ns#"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="pt-br" prefix="og: http://ogp.me/ns#"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="pt-br" prefix="og: http://ogp.me/ns#">
    <!--<![endif]-->
    <head>		
        <title>Ministério Público do Rio Grande do Sul</title>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
        <meta http-equiv="Content-Language" content="pt-BR" >
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" >
        <meta name="keywords" content="" >
        <meta name="description" content="" >
        <meta name="author" content="iMDT - Negócios Inteligentes &lt;contato@imdt.com.br&gt;" >
        <meta name="viewport" content="width=device-width" >
        <meta name="og:title" content="" >
        <meta name="og:type" content="website" >
        <meta name="og:site_name" content="IMDb" >
        <meta name="og:description" content="" >        
        <link href="/resources/css/bootstrap/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css" >
        <link href="/resources/css/bootstrap/bootstrap-responsive.min.css" media="screen" rel="stylesheet" type="text/css" >
        <script src="/resources/js/modernizr/modernizr-2.6.2.min.js" type="text/javascript"></script>
        <style>
            .cntSeparator {
              font-size: 54px;
              margin: 20px 7px;
              color: #000;
            }
            .desc { margin: 7px 3px; }
            .desc div {
              float: left;
              font-family: Arial;
              width: 100px;
              margin-right: 35px;
              font-size: 13px;
              font-weight: bold;
              color: #000;
              text-align: center;
            }
        </style>
    </head>
    <body>	
        <div class="container">
            <div class="row-fluid text-center">
                <div class="span12">
                    <img src="/resources/img/logo.png" />
                    <h4>Web Conference</h4>
                </div>
            </div>
            
            <div class="innerContainer">
                <div class="row-fluid">
                    <div class="span6 offset3 text-center">
                        <h2 class="form-signin-heading">Acesso à sala "Sala 02"</h2>
                        <p>
                            Essa sala ainda não está aberta.
                        </p>
                        <p>
                            Tempo restante para o início da sala:
                        </p>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6 offset3">
                        <div id="counter"></div>
                        <div class="desc">
                            <div>Días</div>
                            <div>Horas</div>
                            <div>Minutos</div>
                            <div>Segundos</div>
                          </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="/resources/js/jquery/jquery-1.8.2.min.js"></script>
        <script type="text/javascript" src="/resources/js/jquery/ui/jquery.ui.custom.min-1.9.1.js"></script>
        <script type="text/javascript" src="/resources/js/jquery/plugins/countdown/jquery.countdown.js"></script>
        <script>
            $(document).ready(function(){
                $('#counter').countdown({
                    image: '/resources/js/jquery/plugins/countdown/img/digits.png',
                    startTime: '03:12:45:00'
                });    
            });
        </script>
    </body>
</html>