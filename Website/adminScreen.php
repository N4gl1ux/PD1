<!DOCTYPE html>
<html>
<head>
<title>Admin Screen</title>

    <meta charset="UTF-8">
    <meta name="description" content="Admin Screen">
    <meta name="keywords" content="synthwave, music, favorite, retrowave">
    <meta name="author" content="Naglis Seliokas, Dovydas Kasulis, Lukas Malijauskas, Nedas Orlingis">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="date" content="2023-12-09">

<style>
    body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #837f7f;
        }
        
        ul.options {
            list-style-type: none;
            padding: 0;
            text-align: center;
        }
        
        ul.options li {
            display: inline-block;
            margin: 10px;
        }

        ul.options li a {
            text-decoration: none;
            color: #333;
            padding: 10px 20px;
            border: 1px solid #333;
            border-radius: 5px;
            background-color: #fff;
            transition: background-color 0.3s, color 0.3s;
        }

        ul.options li a:hover {
            background-color: #333;
            color: #fff;
        }
</style>
</head>
<body>

<ul class="options">
    <li><a href="data_base_editor/addData.php">Add Data</a></li>
    <li><a href="data_base_editor/deleteData.php">Delete Data</a></li>
    <li><a href="data_base_editor/editData.php">Edit Data</a></li>
    <li><a href="index.html">Log out</a></li>
</ul>

</body>
</html>