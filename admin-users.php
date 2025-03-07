<?php 
require_once 'config.php';

// Fetch users from database
$sql = "SELECT u.id, u.first_name, u.middle_name, u.last_name, u.suffix, u.sex, 
        u.contact_no, u.email, c.name as classification_name, cp.name as position_name 
        FROM users u 
        LEFT JOIN users_lnd ul ON u.id = ul.user_id
        LEFT JOIN class_position cp ON ul.position_id = cp.id 
        LEFT JOIN classification c ON ul.classification_id = c.id 
        WHERE u.deleted_at IS NULL 
        ORDER BY u.id";
$result = $conn->query($sql);

// Error handling for SQL query
if ($result === false) {
    die("SQL Error: " . $conn->error);
}

//Query to count the number of Users
$userCount = $conn->prepare("
    SELECT COUNT(*) as count
        FROM users_lnd");
$userCount->execute();
$userResult = $userCount->get_result();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
	<title>Users</title>
</head>
<style type="text/css">
	* {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }

    body, html {
        height: 100%;
    }

    .sidebar {
        position: fixed;
        width: 250px;
        height: 100vh;
        background-color: #2b3a8f;
        color: #ffffff;
        padding: 2rem 1rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .sidebar .logo {
        margin-bottom: 1rem;
        margin-left: 5%;
    }

    hr{
        border: 1px solid white;
    }

    .sidebar .menu {
    	margin-top: 50%;
        display: flex;
        flex-direction: column;
        margin-bottom: 18rem;
    }

    .sidebar .menu a {
        color: #ffffff;
        text-decoration: none;
        padding: 1rem;
        display: flex;
        align-items: center;
        font-size: 1rem;
        border-radius: 5px;
        transition: background 0.3s;
        font-family: Tilt Warp Regular;
        margin-bottom: .5rem;
    }

    .sidebar .menu a:hover, .sidebar .menu a.active {
        background-color: white;
        color: #2b3a8f;
    }

    .sidebar .menu a i {
        margin-right: 0.5rem;
    }

    .profile {
        text-decoration: none;
        margin-top: 2%;
        margin-bottom: 7%;
        margin-left: 1.3rem;
        color: white;
        font-family: Tilt Warp;
        font-size: 1rem;
    }

    .profile i{
        font-size: 18px;
        margin-right: 0.5rem;
    }

    .content {
        flex: 1;
        background-color: #ffffff;
        padding: 4rem;
        margin-left: 17%;
    }

    .content-header h1 {
        font-size: 1.5rem;
        color: #333333;
        font-family: Wensley Demo;
        margin-left: 32%;
    }

    .content-header p {
        color: #999;
        font-size: 1rem;
        margin-top: -3%;
        font-family: LT Cushion Light;
        margin-left: 44%;
    }

    .content-header img {
        float: left;
        margin-left: 22%;
        margin-top: -1%;
        filter: drop-shadow(0px 4px 5px rgba(0, 0, 0, 0.3));
    }

    .content-body h1{
    	font-family: Montserrat ExtraBold;
    	font-size: 2rem;
    	padding: 10px;
    }

    .content-body hr{
    	border: 1px solid #95A613;
    }

    .personnel{
        display: flex;
    }

    .school{
        width: 30%;
        border-radius: 5px;
        background: #19a155;
        color: white;
        padding: 15px;
        font-size: 18px;
        margin-bottom: 4%;
        margin-right: 3%;
    }

    .division{
        width: 30%;
        border-radius: 5px;
        background: #d7f3e4;
        color: #19a155;
        padding: 15px;
        font-size: 18px;
        margin-bottom: 4%;
    }

    .school p, .division p{
        font-weight: bold;
        font-family: Montserrat;
    }

    .filter-bar {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        background-color: #f5f5f5;
        padding: 8px;
        border-radius: 4px;
    }
    .filter-icon {
        background-color: #ddd;
        padding: 5px 10px;
        margin-right: 10px;
        border-radius: 4px;
    }
    .search-container {
        position: relative;
    }
    .search-icon {
        position: absolute;
        left: 8px;
        top: 50%;
        transform: translateY(-50%);
    }
    .search-input {
        padding: 6px 10px 6px 30px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 200px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th {
        background-color: #374ab6;
        color: white;
        text-align: left;
        padding: 15px;
        border: 1px solid #ddd;
        font-weight: bolder;
        font-family: Montserrat;
        font-size: 14px;
    }
    td {
        padding: 8px 10px;
        border: 1px solid #ddd;
        font-family: Montserrat;
        font-weight: medium;
        font-size: 13px;
    }
    tr:nth-child(even) {
        background-color:  rgb(215, 222, 247);
    }
    .checkbox-cell {
        text-align: center;
    }

    .checkbox-cell {
    width: 40px;
    text-align: center;
    }

    tr:hover {
        background-color: #f5f5f5;
    }
</style>
<body>

<div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
        
            <div class="menu">
                <a href="admin-dashboard.php"><i class="fas fa-home"></i>Home</a>
                <a href="admin-events.php"><i class="fas fa-calendar-alt"></i>Events</a>
                <a href="admin-users.php" class="active"><i class="fas fa-users"></i>Users</a>
                <a href="admin-notification.php"><i class="fas fa-bell"></i>Notification</a> 
            </div>
        </div>

    <div class="content">
    	<div class="content-header">
	    	<img src="styles/photos/DO-LOGO.png" width="70px" height="70px">
	    	<p>Learning and Development</p>
	    	<h1>EVENT MANAGEMENT SYSTEM</h1>
    	</div><br><br><br><br><br>

    	<div class="content-body">
	    	<h1>Users</h1>
	    	<hr><br>

            <div class="personnel">
                <div class="school">
                    <p>School personnel: </p>
                </div>

                <?php
                if ($userResult) {
                    $row = $userResult->fetch_assoc();
                }
                ?>
                <div class="division">
                    <p>Division personnel: <?php echo $row['count']; ?></p>
                </div>
            </div>
    
            <div class="filter-bar">
                <span class="filter-icon"><i class="fa fa-filter" aria-hidden="true"></i></span>
                <div class="search-container">
                    <span class="search-icon"><i class="fa fa-search" aria-hidden="true"></i></span>
                    <input type="text" class="search-input" placeholder="Search for users...">
                </div>
            </div>
    
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>#</th>
                        <th>Name</th>
                        <th>Sex</th>
                        <th>Contact Number</th>
                        <th>School Assignment</th>
                        <th>Position</th>
                        <th>E-mail</th>
                        <th>Password</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        $count = 1;
                        while ($row = $result->fetch_assoc()) {
                            // Format name with middle initial and suffix
                            $middle_initial = !empty($row["middle_name"]) ? " " . substr($row["middle_name"], 0, 1) . "." : "";
                            $suffix = !empty($row["suffix"]) ? " " . $row["suffix"] : "";
                            $full_name = $row["first_name"] . $middle_initial . " " . $row["last_name"] . $suffix;

                            echo "<tr>";
                            echo "<td class='checkbox-cell'><input type='checkbox' class='user-checkbox' data-id='" . $row["id"] . "'></td>";
                            echo "<td>" . $count . "</td>";
                            echo "<td>" . $full_name . "</td>";
                            echo "<td>" . $row["sex"] . "</td>";
                            echo "<td>" . $row["contact_no"] . "</td>";
                            echo "<td>" . ($row["classification_name"] ?? "Not Assigned") . "</td>";
                            echo "<td>" . ($row["position_name"] ?? "Not Assigned") . "</td>";
                            echo "<td>" . $row["email"] . "</td>";
                            echo "<td>*****</td>";
                            echo "<td>
                                    <button class='delete-btn' data-id='" . $row["id"] . "'>Delete</button>
                                  </td>";
                            echo "</tr>";
                            $count++;
                        }
                    } else {
                        echo "<tr><td colspan='10' style='text-align:center'>No users found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
 <script>
        // Handle select all checkbox
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Handle edit button clicks
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                // Redirect to edit page or open modal with user data
                window.location.href = 'edit_user.php?id=' + userId;
            });
        });
        
        // Handle delete button clicks
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this user?')) {
                    const userId = this.getAttribute('data-id');
                    // Send AJAX request to delete user
                    fetch('delete_user.php?id=' + userId, {
                        method: 'POST'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove row from table
                            this.closest('tr').remove();
                        } else {
                            alert('Error deleting user: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
            });
        });
</script>        

</body>
</html>

<?php 
$conn->close();
?>