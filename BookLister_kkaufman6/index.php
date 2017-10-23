<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Book Lister</title>
        <link href="css/bookLister.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div id="container">
        <?php
        
        function getBookCategories($closeSelect) {
            require 'dbConnect.php';
            try {
                
                 //create sql command
                 $sql = "SELECT * FROM categories";

                 //execute query and store the returned result set
                  return $pdo->query($sql)->fetchColumn();
             } catch (Exception $ex) {
                   $error = "Could not select categories: " . $ex->getMessage();
                   include 'error.html.php';
                   exit();
             }
        }
        
        function doesrecordExist($column, $table, $value) {
            require 'dbConnect.php';
            
            try {
                $sql = "SELECT COUNT(*) FROM $table WHERE $column = $value";
                return $pdo->query($sql);
            } catch (Exception $ex) {
                $error = "Could not check existing: " . $ex->getMessage();
                   include 'error.html.php';
                   exit();
            }
        }
        if(isset($_GET['clicked'])) {
        ?>
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                <label id="bookArea" for="newBookTitle">Enter the book's title</label><br>
                <textarea name="newBookTitle" id="newBookTitle" rows="10" cols="40">Enter book title</textarea><br><br>
              <label id="bookAuthor" for="newAuthor">Enter the author of this book</label><br>
              <input type="text" name="newAuthor" id="newAuthor"><br>
              <br><br>
              <label id="genre" for="bookCategory">Choose Book Genre</label><br>
              <select name="bookCategory" id="bookCategory">
                  <?php
                  
                  //run query to select the book genres from the database
                  $categoryResults = getBookCategories(true);
                  
                  // step through the result set, printing out an <option> tag for each individual result
                  while($row = $categoryResults->fetch()) {
                      echo "\t\t<option value=\"$row[id]\">$row[name]</option>\n";
                  }
                  
                  ?>
              </select><br><br>
              <input type="submit" name="addBook" value="Add Book">
            </form>
        <?php
            
        } else {
            echo "<h2 id=\"topHeading\">The Book Review</h2>";
            
            //TODO: Later we will check to see if the user submitted a book. If so
            // we will validate the user's input and submit the book to the database
            if(isset($_POST['newBookTitle'])) { 
                if ($_POST['newBookTitle']!== "Enter book title"
                        && ($newTitle = trim(strip_tags($_POST['newBookTitle'])))){
                    if(!doesRecordExist("bookTitle", "bookstuff", $newTitle)){
                        if(!$newAuthor = trim(strip_tags($_POST['newAuthor']))) {
                            $newAuthor = "Anonymous";
                        }
                        if(!doesRecordExist("authorName", "authors", $newAuthor)) {
                            require 'dbConnect.php';
                            
                            try {
                                $sql = "INSERT INTO authors SET authorName = :newAuthor";
                                $statement = $pdo->prepare($sql);
                                $statement->bindValue(':newAuthorName', $newAuthor);
                                $statement->execute();
                                echo "<h2>$newAuthor has been added</h2>";
                            } catch (Exception $ex) {
                                $error = "Could not check existing: " . $ex->getMessage();
                                include 'error.html.php';
                                exit();
                            }
                        }
                    } else {
                        echo "<h2>Author Already Exists</h2>";
                    }

                } else {
                    echo "<h2>Why didn't you enter a valid title?</h2>";
                }
            }
            $categoryResults = getBookCategories(false);
            
            //step through the book genres and create a div for each one
            foreach($categoryResults->fetchAll() as $row) {
                ?>
            
            <div class="bookGenre">
                <h3><?= $row['name'] ?></h3>
               
                <?php
               // we now need to go to the database and get all the books for this
               // genre only, order them by book title
               require 'dbConnect.php';
               
               try {
                   $sql = "SELECT bookTitle, authorName FROM bookstuff, authors "
                           . "WHERE bookstuff.authorId = authors.id "
                           . "AND bookstuff.catId = $row[id] "
                           . "ORDER BY bookTitle";
                   
                   $bookResults = $pdo->query($sql);
                   
               } catch (Exception $ex) {
                    $error = "Could not select book information: " . $ex->getMessage();
                   include 'error.html.php';
                   exit();
               }
               echo "\t\t<blockquote>\n";
               
               foreach($bookResults->fetchAll() as $bookRow) {
                   echo "\t\t\t<p>$bookRow[bookTitle]<br>"
                           . "<span class=\"author\">$bookRow[authorName]</span></p>\n";
               }
               
               echo "\t\t</blockquote>\n";
               ?>
            </div>
            
            <?php
            }
            
            echo "<a href=\"$_SERVER[PHP_SELF]?clicked=1\">Add Book</a>";
        }
        
        ?>
        </div>
    </body>
</html>
