<?php
// include 'php/midware.php';
include 'php/config.php';
include 'php/midware.php';
include 'base.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: registration.php");
    exit();
}

// Prevent admin users from accessing user page
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
    header("Location: production/admin_dashboard.php");
    exit();
}

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

// Get category from URL, default to 'all'
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Get articles with error handling
$sql = "SELECT a.*, u.first_name, u.last_name 
        FROM articles a 
        LEFT JOIN users u ON a.user_id = u.id";

// Add category filter if not 'all'
if ($category !== 'all') {
    $category = mysqli_real_escape_string($conn, $category);
    $sql .= " WHERE a.category = '$category'";
}

$sql .= " ORDER BY a.id DESC";
$results = mysqli_query($conn, $sql);

if (!$results) {
    die("Error fetching articles: " . mysqli_error($conn));
}

// Debug: Print the first article to check fields
if ($first_article = mysqli_fetch_assoc($results)) {
    error_log("First article data: " . print_r($first_article, true));
    // Reset the result pointer
    mysqli_data_seek($results, 0);
}

// Render the header
renderHeader();
?>

<style>
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

.article-image {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: 15px 15px 0 0;
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
    margin-bottom: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.article-meta-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.article-meta-bottom {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.article-author {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #2A3F54;
    text-decoration: none;
    transition: all 0.3s ease;
    padding: 0.5rem;
    border-radius: 8px;
    background: #f8f9fa;
}

.article-author:hover {
    color: #26B99A;
    background: #f0f0f0;
    transform: translateY(-1px);
}

.article-author i {
    color: #26B99A;
    font-size: 1.1rem;
}

.article-date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #95a5a6;
    font-size: 0.85rem;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.article-date i {
    color: #26B99A;
}

.category-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    text-transform: uppercase;
    background: #f8f9fa;
    color: #666;
    transition: all 0.3s ease;
}

.category-badge:hover {
    transform: translateY(-1px);
}

.category-badge i {
    font-size: 1rem;
}

.category-badge.academic { 
    background: #e8f6f3; 
    color: #26B99A; 
}
.category-badge.sports { 
    background: #eaf2f8; 
    color: #3498db; 
}
.category-badge.entertainment { 
    background: #f5eef8; 
    color: #9b59b6; 
}
.category-badge.technology { 
    background: #fdedec; 
    color: #e74c3c; 
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

/* Add styles for liked state */
.like-button.liked {
    color: #e74c3c;
}

.like-button.liked i {
    animation: heartBeat 0.3s ease-in-out;
}

/* Share button styles */
.share-button.shared {
    color: #26B99A;
}

.share-button.shared i {
    animation: sharePulse 0.3s ease-in-out;
}

@keyframes sharePulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

@keyframes heartBeat {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
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

/* Comment section styles */
.comments-section {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.comments-list {
    margin-bottom: 1rem;
}

.comment {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.5rem;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.comment-author {
    font-weight: 500;
    color: #2A3F54;
}

.comment-date {
    color: #95a5a6;
}

.comment-content {
    color: #73879C;
    line-height: 1.4;
}

.comment-form {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.comment-input {
    width: 100%;
    min-height: 80px;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    resize: vertical;
    font-family: inherit;
}

.comment-submit {
    align-self: flex-end;
    background: #26B99A;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.comment-submit:hover {
    background: #219a82;
}

@media (max-width: 768px) {
    .article-content {
        padding: 1rem;
    }

    .article-title {
        font-size: 1.3rem;
    }
}

.category-filters {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.category-filter {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.category-filter i {
    font-size: 1.1rem;
}

.category-filter.all {
    background: #f8f9fa;
    color: #2A3F54;
}

.category-filter.academic {
    background: #e8f6f3;
    color: #26B99A;
}

.category-filter.sports {
    background: #eaf2f8;
    color: #3498db;
}

.category-filter.entertainment {
    background: #f5eef8;
    color: #9b59b6;
}

.category-filter.technology {
    background: #fdedec;
    color: #e74c3c;
}

.category-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.category-filter.active {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>

<div class="main">
    <div class="category-filters">
        <a href="?category=all" class="category-filter all <?= $category === 'all' ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i>
            All Posts
        </a>
        <a href="?category=academic" class="category-filter academic <?= $category === 'academic' ? 'active' : '' ?>">
            <i class="fas fa-graduation-cap"></i>
            Academic
        </a>
        <a href="?category=sports" class="category-filter sports <?= $category === 'sports' ? 'active' : '' ?>">
            <i class="fas fa-running"></i>
            Sports
        </a>
        <a href="?category=entertainment" class="category-filter entertainment <?= $category === 'entertainment' ? 'active' : '' ?>">
            <i class="fas fa-film"></i>
            Entertainment
        </a>
        <a href="?category=technology" class="category-filter technology <?= $category === 'technology' ? 'active' : '' ?>">
            <i class="fas fa-microchip"></i>
            Technology
        </a>
    </div>

    <?php if(mysqli_num_rows($results) == 0): ?>
        <div class="no-posts">
            <p>No posts available in this category.</p>
        </div>
    <?php else: ?>
        <?php foreach ($results as $result): ?>
            <div class="article-card">
                <?php if (!empty($result['image'])): ?>
                    <img src="<?= htmlspecialchars($result['image']) ?>" alt="<?= htmlspecialchars($result['title']) ?>" class="article-image">
                <?php endif; ?>
                <div class="article-content">
                    <h2 class="article-title"><?= htmlspecialchars($result['title']) ?></h2>
                    <div class="article-meta">
                        <div class="article-meta-top">
                            <a href="profile.php?id=<?= $result['user_id'] ?>" class="article-author">
                                <i class="fas fa-user"></i>
                                <?= htmlspecialchars($result['first_name'] . ' ' . $result['last_name']) ?>
                            </a>
                            <span class="article-date">
                                <i class="far fa-clock"></i>
                                <?= date('M d, Y', strtotime($result['created_at'])) ?>
                            </span>
                        </div>
                        <div class="article-meta-bottom">
                            <span class="category-badge <?= strtolower($result['category']) ?>">
                                <i class="fas fa-tag"></i>
                                <?= ucfirst($result['category']) ?>
                            </span>
                        </div>
                    </div>
                    <p class="article-text"><?= nl2br(htmlspecialchars($result['content'])) ?></p>
                    <div class="article-actions">
                        <a href="#" class="action-button like-button" data-article-id="<?= $result['id'] ?>">
                            <i class="fas fa-heart"></i>
                            <span class="like-count">0</span>
                        </a>
                        <a href="#" class="action-button comment-button" data-article-id="<?= $result['id'] ?>">
                            <i class="fas fa-comment"></i>
                            <span class="comment-count">0</span>
                        </a>
                        <a href="#" class="action-button share-button" data-article-id="<?= $result['id'] ?>">
                            <i class="fas fa-share"></i>
                            <span>Share</span>
                        </a>
                    </div>
                    <!-- Add comment section -->
                    <div id="comments-<?= $result['id'] ?>" class="comments-section" style="display: none;">
                        <div class="comments-list"></div>
                        <form class="comment-form">
                            <textarea id="comment-text-<?= $result['id'] ?>" class="comment-input" placeholder="Write a comment..." required></textarea>
                            <button type="submit" class="comment-submit">Post Comment</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
// Render the footer
renderFooter();
?>
