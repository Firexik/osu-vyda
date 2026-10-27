<?php

require_once './config/Database.php';
require_once './classes/Student.php';

require_once './html/htmlHead.php';

$database = new Database();

$student1 = new Student(3, 'Karel', 'Novák');
$student2 = new Student(4, 'Jana', 'Nováková');

echo 'db' . '<br>';
echo $student1->getId().'<br>';
echo $student1->getName().'<br>';
echo $student1->getSurname().'<br>';

$student1->setId(10);
//Chyba přístupu - private property
//echo $student1->getId().'<br>';

try{
    $dbConnection = $database->connect();
    $sqlSelectStudents = 'SELECT id, name, surname FROM students';
    $prepareStatementStudents = $dbConnection->prepare($sqlSelectStudents);
    $prepareStatementStudents->execute();
    $collectionStudents = $prepareStatementStudents->fetchAll();

    foreach($collectionStudents as $row){
        echo $row['id'] . '<br>';
        echo $row['name'] . '<br>';
        echo $row['surname'] . '<br>';
    }
}catch (PDOException $e){
    echo 'Connection failed: ' . $e->getMessage();
}

?>