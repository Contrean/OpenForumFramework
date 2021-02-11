<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/index.css">
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <title>Forum</title>
</head>
<body>
    <div id="header">
        <div id="menu">
            <div class="item">
                {{ item1 }}
            </div>
        </div>
    </div>
    <div id="app"></div>
    <script>
        var menu = new Vue({
            el: '#menu',
            data: {
                item1: '<?php echo $LANG->get("system.hello"); ?>'
            }
        });
    </script>
</body>
</html>