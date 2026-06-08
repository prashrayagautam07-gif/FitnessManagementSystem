<?php
include "auth.php";
include "../includes/header.php";
include "../config/db.php";

$type = $_GET['type'] ?? '';

$stmt = $conn->prepare("SELECT * FROM members WHERE membership_type LIKE ?");
$search = "%$type%";
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();
?>

<form class="search-box" method="get">
    <input type="text" name="type" placeholder="Membership Type" value="<?= htmlspecialchars($type) ?>">
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
