
<?php include 'db_connect.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Task</title>
</head>
<body>
    <h2>Add Task</h2>
    <form method="POST" action="">
        <label>Task Name:</label>
        <input type="text" name="task_name" required><br><br>

        <label>Task Date:</label>
        <input type="date" name="task_date" required><br><br>

        <button type="submit" name="add">Add Task</button>
    </form>
    <br>
    <a href="view_task.php">View Tasks</a>

    <?php
    if (isset($_POST['add'])) {
        $task_name = $_POST['task_name'];
        $task_date = $_POST['task_date'];

       
        $sql = "INSERT INTO task (task_name, task_date, status) 
                VALUES ('$task_name', '$task_date', 'Pending')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color:green;'>Task added successfully!</p>";
        } else {
            echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
        }
    }
    ?>
</body>
</html>
