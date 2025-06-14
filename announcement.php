<?php 
include 'php/midware.php';
include 'php/config.php';
include 'base.php';  

if(isset($_GET['post_id'])) {
    $post = $_GET['post_id'];

    $sql = "SELECT * FROM articles WHERE id=$post";
    $result = mysqli_query($conn, $sql);
    $result = mysqli_fetch_assoc($result);

    $user = $_SESSION['user_id'];
    $user_query = "SELECT * FROM users WHERE id=$user";
    $user_result = mysqli_query($conn, $user_query);
    $user_data = mysqli_fetch_assoc($user_result);

    // Get comments
    $comments = "SELECT * FROM comments WHERE article_id=$post ORDER BY created_at DESC";
    $comments_result = mysqli_query($conn, $comments);

    // Get like count
    $like_count_sql = "SELECT COUNT(*) as like_count FROM likes WHERE article_id=$post";
    $like_count_result = mysqli_query($conn, $like_count_sql);
    $like_count = mysqli_fetch_assoc($like_count_result)['like_count'];

    // Check if current user has liked
    $user_like_sql = "SELECT * FROM likes WHERE user_id=$user AND article_id=$post";
    $user_like_result = mysqli_query($conn, $user_like_sql);
    $has_liked = mysqli_num_rows($user_like_result) > 0;
?>

    <div class="announce-main">
        <div>
            <section>
                <article>
                    <div class="logo"></div>
                    <div class="container">
                        <h2><?= $result['title']; ?></h2>
                        <p>
                            <?= $result['content']; ?>
                        </p>
                        <?php if($result['file_path']): ?>
                            <div class="img-section">
                                <img src="php/<?= $result['file_path']; ?>" alt="Not available" width="400" height="250">
                            </div>
                        <?php endif; ?>
                        <small><i><?= $result['created_at'] ?></i></small>
                        <div class="icons">
                            <a href="javascript:void(0)" class="like-btn" data-article-id="<?= $post ?>" data-liked="<?= $has_liked ? 'true' : 'false' ?>">
                                <span class="material-icons <?= $has_liked ? 'liked' : '' ?>">thumb_up</span>
                                <span class="like-count"><?= $like_count ?></span>
                            </a>
                            <a href="javascript:void(0)" onclick="toggleComments()">
                                <span class="material-icons">comment</span>
                                <span class="comment-count"><?= mysqli_num_rows($comments_result) ?></span>
                            </a>
                        </div>

                        <!-- comment section -->
                        <div id="comment-section" class="container my-5 py-5" style="display:none;">
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-12 col-lg-10 col-xl-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <?php if(mysqli_num_rows($comments_result) > 0): ?>
                                                <?php while($comment = mysqli_fetch_assoc($comments_result)): 
                                                    $comment_user_id = $comment['user_id'];
                                                    $comment_user_query = "SELECT * FROM users WHERE id=$comment_user_id";
                                                    $comment_user_result = mysqli_query($conn, $comment_user_query);
                                                    $comment_user = mysqli_fetch_assoc($comment_user_result);
                                                ?>
                                                    <div class="d-flex flex-start align-items-center mb-4">
                                                        <img class="rounded-circle shadow-1-strong me-3" 
                                                             src="php/<?= $comment_user['profile_img'] ?>" 
                                                             alt="avatar" width="40" height="40" />
                                                        <div>
                                                            <h6 class="fw-bold text-primary mb-1"><?= $comment_user['user_name'] ?></h6>
                                                            <p class="text-muted small mb-0">Added at <i><?= $comment['created_at'] ?></i></p>
                                                        </div>
                                                    </div>

                                                    <p class="mt-3 mb-4 pb-2">
                                                        <?= $comment['content'] ?>
                                                    </p>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <p class="text-center text-muted">No comments yet. Be the first to comment!</p>
                                            <?php endif; ?>
                                        </div>

                                        <form action="php/comment.php" method="post">
                                            <div class="card-footer py-3 border-0" style="background-color: #f8f9fa;">
                                                <div class="d-flex flex-start w-100">
                                                    <input type="hidden" name="user" value="<?= $user; ?>">
                                                    <input type="hidden" name="article" value="<?= $post; ?>">
                                                    <img class="rounded-circle shadow-1-strong me-3" 
                                                         src="php/<?= $user_data['profile_img'] ?>" 
                                                         alt="avatar" width="40" height="40" />
                                                    <div class="form-outline w-100">
                                                        <textarea class="form-control" name='comment' 
                                                                  id="textAreaExample" rows="4" 
                                                                  style="background: #fff;"
                                                                  placeholder="Write your comment here..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="float-end mt-2 pt-1">
                                                    <button name='submit' class="btn btn-primary btn-sm">Post comment</button>
                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleComments()">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </section>
        </div>
    </div>  

    <style>
        .icons {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }
        .icons a {
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            color: #666;
        }
        .icons .material-icons {
            font-size: 24px;
            transition: all 0.3s ease;
        }
        .icons .material-icons.liked {
            color: #1a73e8;
        }
        .like-count, .comment-count {
            font-size: 14px;
            color: #666;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card-body {
            padding: 20px;
        }
        .form-outline textarea {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 10px;
        }
        .form-outline textarea:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 0.2rem rgba(26,115,232,0.25);
        }
        .btn-primary {
            background-color: #1a73e8;
            border-color: #1a73e8;
        }
        .btn-outline-primary {
            color: #1a73e8;
            border-color: #1a73e8;
        }
        .btn-outline-primary:hover {
            background-color: #1a73e8;
            color: white;
        }
    </style>

    <script>
        function toggleComments() {
            const commentSection = document.getElementById('comment-section');
            commentSection.style.display = commentSection.style.display === 'none' ? 'block' : 'none';
        }

        // Like functionality
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function() {
                const articleId = this.dataset.articleId;
                const likeIcon = this.querySelector('.material-icons');
                const likeCount = this.querySelector('.like-count');

                fetch('php/like.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'article_id=' + articleId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        likeCount.textContent = data.like_count;
                        if (data.action === 'liked') {
                            likeIcon.classList.add('liked');
                        } else {
                            likeIcon.classList.remove('liked');
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    </script>

<?php } ?>

    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/ditso.js"></script>
</body>
</html>
