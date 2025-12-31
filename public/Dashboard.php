<?php
session_start();

if (!isset($_SESSION['user'])){
    header('location: login.php');
    exit();
}

?>

<?php
switch($_SESSION['role']){
    case 'admin':
        echo "<h3>Admin controle panel</h3>";
        echo "<ul>
                <li>User Management</li>
                <li>Project Management</li>
                <li>View Statistics</li>
             </ul>";
             break;
    case 'scrum':
        echo "<h3>Scrum Master controle panel</h3>";
        echo "<ul>
                <li>Sprints managment</li>
                <li>Task Distribution</li>
             </ul>";
             break;
    case 'member':
        echo "<h3>Member controle panel</h3>";
        echo "<ul>
                <li>Display my tasks</li>
                <li>Task status update</li> 
              </ul>";
              break;

    default:
    echo "<p>Unknown role</p>";
}
?>

<a href="logout.php">Logout</a>