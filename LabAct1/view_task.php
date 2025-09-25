
<?php include 'db_connect.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>View Tasks</title>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 8px;
        }
    </style>
</head>
<body>
    <h2>Task List</h2>
    <a href="add_task.php">Add New Task</a>
    <br><br>

    <table>
        <tr>
            <th>Task ID</th>
            <th>Task Name</th>
            <th>Task Date</th>
            <th>Status</th>
        </tr>

        <?php
        $sql = "SELECT * FROM task";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['id'] . "</td>
                        <td>" . $row['task_name'] . "</td>
                        <td>" . $row['task_date'] . "</td>
                        <td>" . $row['status'] . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No tasks found</td></tr>";
        }
        ?>
    </table>
</body>
</html>
