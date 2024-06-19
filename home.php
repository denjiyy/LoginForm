<?php
    session_start();

    if(isset($_SESSION['username'])) {
        ?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>HOME</title>
        </head>
        <body>
            <h1>Hello, <?php echo $_SESSION['username']; ?> </h1>
            <a href="logout.php">LOGOUT</a>
            <a href="#" onclick="confirmDelete()">DELETE</a>
            <a href="editpage.php">EDIT ACCOUNT</a>

            <script src="confirmDelete.js" defer></script>
        </body>
        </html>

        <?php
    }
    else {
        header("Location: index.php");
        exit();
    }