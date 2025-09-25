<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcu</title>
</head>
<body>
    <h2>Simple calcu</h2> <br> <br>
<form method="Post">
     <label>Enter First Number</label>
    <input type="number" name = "num1" step = "any">
    <label>Enter Second Number</label>
    <input type="number" name = "num2" step = "any">

    <label>Operation</label>
    <select name="operation" required>
        <option value="add">add</option>
        <option value="subtract">subtract</option>
        <option value="multiply">multiplication</option>
        <option value="divide">division</option>
    </select><br><br>
    <input type="submit" name = "calculate" value = "Calculate">
    <input type="submit" name = "clear" value = "Clear ">
   
</form>    
<?php
$result = "";
if(isset($_POST['clear'])){
    $result = "";
}
elseif(isset($_POST['calculate'])){
    $num1 = $_POST['num1'];
    $num2 = $_POST['num2'];
    $operation = $_POST['operation'];
    if($num1 === ""||$num2==="" ){
        $result = "input both numbers first";
    }
    else{

    switch($operation){
        case "add":
            $result = $num1 + $num2;
            break; 
        case "subtract":
            $result = $num1 - $num2;
            break; 
        case "multiply":
            $result = $num1 * $num2;
            break; 
        case "divide":
            if($num2 != 0){
            $result = $num1 / $num2;
            }
            else{
                $result = "Invalid";
            } 
            break;                  
    
    }
}
    if($result != ""){
        echo "<h3>Result: $result </h3>";
    }

}


?>
</body>
</html>