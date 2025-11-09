<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div style="border: 1px solid #ccc; width: 500px; display: flex; flex-direction: column;">
        <ul>
            <?php
                while($row = mysqli_fetch_array($result)){
                    echo "<a href='/study/".$row['id']."'><li>".$row['id']."</li></a>";
                }
            ?>
        </ul>
    </div>
</body>
</html>