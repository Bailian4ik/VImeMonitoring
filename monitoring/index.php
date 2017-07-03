<?php 
include("config.php");
function tplRead($name) {
    return str_replace(array('{', '}', "\r", "\n"), array("'+", "+'", '', ''), file_get_contents('tpl/'.$name));
} 
?>
<html>
<head>

    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link rel="stylesheet" href="tpl/style.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
</head>
<body onload="get()">
    <SCRIPT LANGUAGE='JavaScript' src='js/ajax.js'></script>
    <script>
        function showResult(d) {
            var d = JSON.parse(d);
                var out = d.map(function(x){
                    if(x.offline != 1){
                        var name = x.motd;
                        var cur = x.cur_p;
                        var max = x.max_p;
                        var percent = 100/max*cur;
                        return '<?php echo tplRead('server.tpl')?>';
                    }else{
                         var name = x.motd;
                         var off = "<?php echo $off?>"
                         return '<?php echo tplRead('server_off.tpl')?>';
                    }
                    }).join("\n");

            $.get("get/status.php?all=1", function(data) {
                var data = JSON.parse(data);
                var cur = data.cur;
                var precent = 100/data.max*data.cur;
                var max = data.max;
                var day = data.day;
                var absolute = data.absolute; 
                out += '<?php echo tplRead('other.tpl')?>';
            
             
            document.getElementById('monitoring-content').innerHTML = out;
            });
        }
        function get() {
            var url = 'get/status.php';   
            getAjax(url, showResult);
        }
        setInterval('get()', <?php echo $timeout; ?>);
    </script>
    <div class="monitoring-box">
        <div id="monitoring-content">

        </div></div>
    </p>
     <?php
        if(file_put_contents("get/test", 'Monitoring by Bailian4ik')){
            unlink('get/test');
        }else{
            echo "<b>Не удается создать тестовый файл! Выставите каталогу 'get' права 777!</b>";
        }
    ?>
</body>
</html>