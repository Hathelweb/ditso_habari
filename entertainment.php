<?php 
include 'php/midware.php';
include 'php/config.php';
include 'base.php';  

$sql = "SELECT * FROM articles WHERE category='Entertainment' ORDER BY id DESC";
$results = mysqli_query($conn, $sql);

?>

    <h1>ENTERTAINMENT</h1>
    
    <div class="main">
    <?php  if(mysqli_num_rows($results) == 0) { ?>
            <div>
                <section>
                    <article class='d-flex justify-content-center'>
                        <p>OOPS! No posts yet.</p>
                    </article>
                </section>
            </div>
          <?php  } else {
             foreach ($results as $result):  
        ?>
            <div>
                <section>
                    <article>
                        <div class="logo"></div>
                        <div class="content">
                            <h2><a href="announcement.php?post_id=<?= $result['id']; ?>"><?= $result['title'] ?></a></h2>
                            <p><?=substr($result['content'], 0, 100) . "..."; ?></p>
                            <small><i>Published on: <?= $result['created_at'] ?></i></small>
                            <div class="icons">
                                <a href=""><span class="material-icons">thumb_up</span></a>
                                <a href=""><span class="material-icons">comment</span></a>
                            </div>
                        </div>
                    </article>
                </section>
            </div>
        <?php endforeach; } ?>
    </div>  

    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/ditso.js"></script>
</body>
</html>