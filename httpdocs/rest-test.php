<html class="no-js" lang="pt-br" prefix="og: http://ogp.me/ns#">
    <head>		
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
    </head>
    
    <body>
        <fieldset>
            <legend>Teste REST</legend>
            <pre>
                Exemplo XML
                <?=htmlentities('<?xml version="1.0"?><data><users><user><id>abc</id></user></users><id>123</id><name>Dodi</name></data>');?>

                Exemplo JSON
                {"id":"123","name":"Dodi"}
            </pre>
            <p>
                <label>Url: <input type="text" id="url"/></label>
            </p>
            <p>
                <label>Verbo: 
                    <select id="verbo">
                        <option value="DELETE">DELETE</option>
                        <option value="GET">GET</option>
                        <option value="POST">POST</option>
                        <option value="PUT">PUT</option>
                    </select>
                </label>
            </p>
            <p>
                <label>Parâmetros: <input type="text" id="param"/></label>
            </p>
            <button id="doRequest">Processar!</button>
        </fieldset>

        <pre id="restresponse"></pre>

        <script type="text/javascript" src="/resources/js/jquery/jquery-1.8.2.min.js"></script>
        <script>
            $(document).ready(function(){
                $('#doRequest').click(function(e){
                    e.preventDefault();
                    e.stopPropagation();

                    $.ajax({
                        url     : $('#url').val(),
                        data    : $('#param').val(),
                        type    : $('#verbo').val(),
                        headers : {
                            userId  : 1,
                            token   : 3
                        },
                        success : function(a){
                            $('#restresponse').html(a);
                        }
                    });
                });
            });
        </script>        
    </body>
</html>