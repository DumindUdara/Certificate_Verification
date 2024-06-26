<?php

if(session_status()!=PHP_SESSION_ACTIVE){
  session_start();
}

if(!isset($_SESSION['isadmin']) || $_SESSION['isadmin']!=1){
  header("Location:../index.php", true, 301);
  return;
}
include_once '../config/Database.php';
include_once '../config/User.php';
include_once '../config/Course.php';
$user=new User(Database::getDB());
$courses = getAllCourse(Database::getDB());
$admin = $user->getAdmin($_SESSION['email']);

$allstudents = null;

if(isset($_GET['course']) && !empty($_GET['course'])){
  $allstudents = getEnroledStudentByCourse(Database::getDB(),$_GET['course'] );
}else{
  $allstudents = $user->getAllStudents();
}

// print_r(getEnroledStudentByCourse(Database::getDB(),"Software Enginnering"));

// print_r($user->getAllStudents());
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin- Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>

</head>

<body>
    <div class="container py-5">
        <div class="container my-3">
            <?php
        if(isset($_SESSION['error'])){?>
            <div class="container my-2">
                <p class="alert alert-danger text-center"><?=ucfirst($_SESSION['error']) ?></p>
            </div>
            <?php $_SESSION['error'] = null;}
        if(isset($_SESSION['msg'])){?>
            <div class="container my-2">
                <p class="alert alert-success text-center"><?=ucfirst($_SESSION['msg']) ?></p>
            </div>
            <?php $_SESSION['msg'] = null;}
      ?>
            <a href='../index.php' class='btn btn-sm btn-danger'>Back</a>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#addcourseModel">
                Add Course
            </button>

            <a class="btn btn-primary btn-sm" href="./view.courses.php">
                View Courses
            </a>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#adminConfigeModel">
                Admin Config
            </button>
            <!-- Modal -->
            <div class="modal fade" id="addcourseModel" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Add Course</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="../controllers/addCourseController.php" method="post">
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="" class="form-label">Title</label>
                                    <input type="text" placeholder="Course Title" name="title" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="" class="form-label">Fee</label>
                                    <input type="number" placeholder="Course Fee" name="fees" class="form-control">
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="add-course" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Admin config model -->
            <div class="modal fade" id="adminConfigeModel" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Change Admin Info</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="../controllers/AdminController.php" method="post">
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="" class="form-label">Email</label>
                                    <input type="email" value='<?= $admin['email']?>' placeholder="Email" name="email"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="" class="form-label">Current Password</label>
                                    <input type="password" placeholder="Password" name="cpass" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="" class="form-label">New Password</label>
                                    <input type="password" placeholder="New Password" name="newpass"
                                        class="form-control">
                                    <input type="hidden" name='adminid' value='<?= $admin['id']?>' />
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="edit-admin" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
        <div class="container">
            <form action="<?= $_SERVER['PHP_SELF']?>">
                <div class="col-12">
                    <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search" class="form-control">
                </div>

                <div class="col-12 my-2">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" checked type="radio" name="inlineRadioOptions" id="inlineRadio1"
                            value="NAME">
                        <label class="form-check-label" for="inlineRadio1">Name</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1"
                            value="NUMBER">
                        <label class="form-check-label" for="inlineRadio1">Contact</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1"
                            value="EMAIL">
                        <label class="form-check-label" for="inlineRadio1">Email</label>
                    </div>
                </div>

                <div class="col-12 my-3">
                    <select name="course" id="" class="form-select">
                        <?php
              foreach ($courses as $course) {
                echo '<option value="'.$course['name'].'">'.ucfirst($course['name']).'</option>';
              }
              ?>

                    </select>
                </div>
                <div class="col-10 mx-auto">
                    <input type="submit" value="Search" class="w-100 btn btn-sm btn-success" name="search">
                </div>
            </form>
            <a href="./admin.dashboard.php" class='btn btn-sm btn-warning'>All</a>
        </div>
        <?php
      if(isset($_SESSION['error'])){?>
        <div class="row">
            <p class="alert alert-danger text-center"><?= $_SESSION['error']?></p>
        </div>
        <?php $_SESSION['error']=null; }else if(isset($_SESSION['msg'])){?>
        <div class="row">
            <p class="alert alert-success text-center"><?= $_SESSION['msg']?></p>
        </div>
        <?php $_SESSION['msg']=null;}

      ?>
        <div class="row">


            <div class="col-12 mt-4">
                <table class="table table-striped" id="myTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="d-none d-md-flex">Email</th>
                            <th>Contact</th>
                            <th><span class="d-none d-md-flex">Student</span> Id</th>
                            <th>Status</th>
                            <th class="d-none d-md-flex">Courses</th>
                            <th></th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php

                foreach($allstudents as $student){?>
                        <tr>
                            <td><?= ucfirst($student['name'])?></td>
                            <td class="d-none d-md-flex"><?= $student['email']?></td>
                            <td><?= $student['contact']?></td>
                            <td><?= $student['stid']?></td>
                            <?= $student['completed']==0?'<td class="bg-warning text-white"><small>N/A</small></td>':'<td class="bg-success text-white"><small>Completed</small></td>' ?>
                            <td class="d-none d-md-flex">
                                <?php
                    $courses = GetEntroledCourse($student['sid'], Database::getDB());
                    if($courses==null){?>

                                N/A

                                <?php }else{
                      echo '<select name="" id="" class="border-0 bg-transparent">';
                      foreach ($courses as $course) {?>
                                <option><?= ucfirst($course['name'])?></option>
                                <?php }
                      echo '</select>';
                    }
                    ?>

                            </td>
                            <td>
                                <a href="./edit.view.php?stid=<?=$student['sid']?>" class="text-success"><i
                                        class="fa-solid fa-pen"></i></a>
                                <button type="button" class="border-0 text-danger" data-bs-toggle="modal"
                                    data-bs-target="#exampleModal<?=$student['sid']?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal<?=$student['sid']?>" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Delete Conformation
                                                </h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>this action can not be undone</p>
                                            </div>
                                            <div class="modal-footer">
                                                <form action="../controllers/deleteStudentController.php" method="POST">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <input type="hidden" name="stid" value="<?= $student['sid']?>">
                                                    <button type="submit" name="delete"
                                                        class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </td>
                        </tr>

                        <?php }
              
              ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
    function myFunction() {
        // Declare variables
        var input, filter, table, tr, td, i, txtValue, fileds, searchfor;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");
        fileds = document.querySelectorAll('.form-check-input')

        fileds.forEach(f => {
            if (f.checked) {
                searchfor = f.value
            }
        })



        // Loop through all table rows, and hide those who don't match the search query
        for (i = 0; i < tr.length; i++) {
            switch (searchfor) {
                case 'NAME':
                    td = tr[i].getElementsByTagName("td")[0];
                    break
                case 'EMAIL':
                    td = tr[i].getElementsByTagName("td")[1];
                    break;
                case "NUMBER":
                    td = tr[i].getElementsByTagName("td")[2];
                    break;
                default:
                    td = tr[i].getElementsByTagName("td")[0];
            }

            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
    document.addEventListener("DOMContentLoaded", () => {
        clear()
    })

    function clear() {
        setTimeout(() => {
            let error = document.querySelector('.alert')
            if (error) {
                error.remove()
            }
        }, 2000)
    }
    </script>


</body>

</html>