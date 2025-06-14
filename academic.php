<?php 
include 'php/midware.php';
include 'php/config.php';
include 'base.php';  

// Verify database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Verify articles table structure
$check_table = "SHOW TABLES LIKE 'articles'";
$table_exists = mysqli_query($conn, $check_table);

if (mysqli_num_rows($table_exists) == 0) {
    die("Error: Articles table does not exist. Please run verify_tables.php first.");
}

// Verify category column
$check_column = "SHOW COLUMNS FROM articles LIKE 'category'";
$column_exists = mysqli_query($conn, $check_column);

if (mysqli_num_rows($column_exists) == 0) {
    die("Error: Category column does not exist. Please run verify_tables.php first.");
}

// Query articles
$sql = "SELECT * FROM articles WHERE category='academic' ORDER BY id DESC";
$results = mysqli_query($conn, $sql);

if (!$results) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<style>
.page-header {
    background: linear-gradient(135deg, #26B99A 0%, #2A3F54 100%);
    color: white;
    padding: 3rem 0;
    margin-bottom: 2rem;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.page-header h1 {
    font-size: 2.5rem;
    font-weight: 600;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.main {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.article-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.article-card:hover {
    transform: translateY(-5px);
}

.article-content {
    padding: 1.5rem;
}

.article-title {
    color: #2A3F54;
    font-size: 1.5rem;
    margin-bottom: 1rem;
    text-decoration: none;
    transition: color 0.3s ease;
}

.article-title:hover {
    color: #26B99A;
}

.article-text {
    color: #73879C;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.article-meta {
    color: #95a5a6;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.article-actions {
    display: flex;
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.action-button {
    color: #95a5a6;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: color 0.3s ease;
}

.action-button:hover {
    color: #26B99A;
}

.action-button i {
    font-size: 1.2rem;
}

.no-posts {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
}

.no-posts p {
    color: #95a5a6;
    font-size: 1.2rem;
    margin: 0;
}

@media (max-width: 768px) {
    .page-header {
        padding: 2rem 0;
    }

    .page-header h1 {
        font-size: 2rem;
    }

    .article-content {
        padding: 1rem;
    }

    .article-title {
        font-size: 1.3rem;
    }
}
</style>

<div class="page-header">
    <h1>Academic News</h1>
</div>

<div class="main">
    <?php if(mysqli_num_rows($results) == 0): ?>
        <div class="no-posts">
            <p>No academic posts available at the moment.</p>
        </div>
    <?php else: ?>
        <?php foreach ($results as $result): ?>
            <div class="article-card">
                <div class="article-content">
                    <h2 class="article-title">
                        <a href="announcement.php?post_id=<?= $result['id']; ?>">
                            <?= htmlspecialchars($result['title']) ?>
                        </a>
                    </h2>
                    <p class="article-text">
                        <?= htmlspecialchars(substr($result['content'], 0, 200)) . "..."; ?>
                    </p>
                    <div class="article-meta">
                        <i>Published on: <?= date('F j, Y', strtotime($result['created_at'])) ?></i>
                    </div>
                    <div class="article-actions">
                        <a href="#" class="action-button">
                            <i class="material-icons">thumb_up</i>
                            <span>Like</span>
                        </a>
                        <a href="#" class="action-button">
                            <i class="material-icons">comment</i>
                            <span>Comment</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="js/ditso.js"></script>
</body>
</html>