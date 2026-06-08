<?php
include "auth.php";
include "../includes/header.php";
include "../config/db.php";

$query = $_GET['query'] ?? '';

$stmt = $conn->prepare("SELECT * FROM members WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? OR membership_type LIKE ?");
$search = "%$query%";
$stmt->bind_param("ssss", $search, $search, $search, $search);
$stmt->execute();
$result = $stmt->get_result();
?>

<form class="search-box" method="get">
    <input type="text" name="query" placeholder="Search members..." value="<?= htmlspecialchars($query) ?>">
    <button type="submit">Search</button>
</form>

<div class="search-results">
    <ul>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <span class="member-name">
                        <?= htmlspecialchars($row['name']) ?>
                    </span>
                    <span class="membership">
                        <?= htmlspecialchars($row['membership_type']) ?>
                    </span>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="no-results">
    			No results found
			</li>

        <?php endif; ?>
    </ul>
</div>

<?php include "../includes/footer.php"; ?>
