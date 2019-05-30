<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <style>
        body{margin: 0px;}
        table, table a { color: #636b6f; }
        table { position: fixed;height: 100%;width: 100%;background: #fff;text-align: center;font-size: 30px; }
        table a { border-bottom: 1px solid #b1b7ba;font-size: inherit; text-decoration: none; }
        table a:hover { border-bottom-color: black; }
    </style>
</head>
<body>
    <div class="exception-summary">
        <table>
            <tr>
                <td>
                    {{$msg}}
                    <br/><a href="javascript:history.go(-1);">点击此处返回</a>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>