<?php
require_once 'includes.php';

echo get_header("Hospital Reviews", "Rating this teaching hospital below 4 stars is a symptom of lupus!");

$reviews = get_reviews();
?>

<div class="panel">
    <h2>Hospital Reviews</h2>
    
    <div class="panel">
        <h3>Submit Your Review</h3>
        <form action="submit_review.php" method="POST">
            <div class="form-group">
                <label for="name">Your Name:</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label for="rating">Rating:</label>
                <select name="rating" id="rating" required>
                    <option value="5">★★★★★ (Excellent)</option>
                    <option value="4">★★★★☆ (Good)</option>
                    <option value="3">★★★☆☆ (Average)</option>
                    <option value="2">★★☆☆☆ (Poor)</option>
                    <option value="1">★☆☆☆☆ (Terrible)</option>
                </select>
            </div>
            <div class="form-group">
                <label for="comment">Your Review:</label>
                <textarea name="comment" id="comment" rows="4" required></textarea>
            </div>
            <button type="submit" name="submit_review">Submit Review</button>
        </form>
    </div>
    
    <div class="panel">
        <h3>Recent Reviews</h3>
        <?php foreach($reviews as $review): ?>
            <div class="review-card">
                <div class="review-header">
                    <div class="review-name"><?= htmlspecialchars($review['name']) ?></div>
                    <div class="review-date"><?= $review['date'] ?></div>
                </div>
                <div class="review-rating">
                    <?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?>
                </div>
                <div class="review-comment"><?= $review['comment'] ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php echo get_footer(); ?>