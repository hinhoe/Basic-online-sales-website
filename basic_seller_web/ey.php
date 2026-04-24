<?php
header("refresh:2;url=index.php");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ảnh full màn hình</title>

<style>
    html, body {
        margin: 0;
        padding: 0;
        height: 100%;
        background: #000;
    }

    img {
        width: 100vw;
        height: 100vh;
        object-fit: contain; /* hiển thị full ảnh không bị méo */
        display: block;
    }
</style>
</head>

<body>
    <img src="https://m.yodycdn.com/blog/meme-hai-bua-yody-vn-92.jpg">
</body>
</html>