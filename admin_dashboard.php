<?php

require_once 'config.php';

if(!isset($_SESSION['admin_id'])) {
   header('location: index.php');
   exit;
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <link rel="stylesheet" href="style.css">
    <title>Admin Panel</title>
</head>
<body>

<div>
     <div class="wave"></div>
     <div class="wave"></div>
     <div class="wave"></div>
  </div>

<?php if(isset($_SESSION['success_message'])) : ?>

<div class="alert alert-succes alert-dismissible fade show" role="alert" id="succes-alert">
    <?php 
        echo $_SESSION['success_message']; 
        unset( $_SESSION['success_message']);    
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>


<div class="container">

<div class="row">
    <div class="col-md-12">
        <h2>Members List</h2>

        <a href="export.php?what=members" class="btn btn-success btn-sm">Export</a>

        <table class="table table-dark">
        <thead class="thead-dark">
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Trainer</th>
                <th>Photo</th>
                <th>Training Plan</th>
                <th>Access Card</th>
                <th>Created at</th>
                <th>Action</th>
            </thead>

            <tbody>

            <?php
            
                $sql = "SELECT members.*, 
                        training_plans.name AS training_plan_name,
                        trainers.first_name AS trainer_first_name,
                        trainers.last_name AS trainer_last_name
                        FROM members 
                        LEFT JOIN training_plans ON members.training_plan_id = training_plans.plan_id
                        LEFT JOIN  trainers ON members.trainer_id = trainers.trainer_id";
                $run = $conn->query($sql);

                $results = $run->fetch_all(MYSQLI_ASSOC);

                #Memberi su vec ucitavani pa ih unosimo u ovu varijablu kako bi ih iskoristili za assign-ovanje u donjem delu koda kako ih nebi smo opet izlistavali!
                $select_members = $results;

                foreach($results as $result) :  ?>

                    <tr>
                        <td><?php echo $result['first_name']?></td>
                        <td><?php echo $result['last_name']?></td>
                        <td><?php echo $result['email']?></td>
                        <td><?php echo $result['phone_number']?></td>
                        <td><?php 

                            if($result['trainer_first_name']){
                                echo $result['trainer_first_name'] . " " . $result['trainer_last_name'];
                            } else {
                                echo "Nema trenera";
                            }
                        
                        ?></td>
                        <td> <img style="width: 60px;" src="<?php echo $result['photo_path']?>" alt="Slika korisnika!"></td>
                        <td><?php echo $result['training_plan_name']?></td>
                        <td><a href="<?php echo $result['access_card_pdf_path']?>">Access Card</a></td>
                        <td><?php echo $result['created_at']?></td>
                        <td>
                            <form action="delete_member.php" method="POST">
                                <input type="hidden" name="member_id" value="<?php echo $result['member_id']; ?>">
                                <button>DELETE</button>
                            </form>
                        </td>
                       
                    </tr>


            <?php endforeach; ?>

            </tbody>
        </table>
    </div>

        <div class="col-md-12">
            <h2>Trainers List</h2>
            <a href="export.php?what=trainers" class="btn btn-success btn-sm">Export</a>
            <table class="table table-dark">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT * FROM trainers";
                        $run = $conn->query($sql);
                        $results = $run->fetch_all(MYSQLI_ASSOC);
                        $select_trainers = $results;

                        foreach($results as $result) : ?>

                            <tr>
                                <td><?php echo $result['first_name']; ?></td>
                                <td><?php echo $result['last_name']; ?></td>
                                <td><?php echo $result['email']; ?></td>
                                <td><?php echo $result['phone_number']; ?></td>
                                <td><?php echo $result['created_at']; ?></td>
                            </tr>       

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

</div>






<div class="row mb-5">
    <div class="col-md-6">
        <h2>Register Member</h2>
        <!--enctype is because of images-->
        <form action="register_member.php" method="POST" enctype="multipart/form-data" class>
            First Name: <input class="form-control" type="text" name="first_name"><br>
            Last Name: <input class="form-control" type="text" name="last_name"><br>
            Email: <input class="form-control" type="email" name="email"><br>
            Phone Number: <input class="form-control" type="text" name="phone_number"><br>

            Training Plan:
            <select class="form-control" name="training_plan_id">
                <option value="" disabled selected>Training plan</option>
                <?php
                    $sql = "SELECT * FROM training_plans";
                    $run = $conn->query($sql);
                    $results = $run->fetch_all(MYSQLI_ASSOC);

                    foreach($results as $option){
                        echo "<option value='" . $option['plan_id'] . "'>" . $option['name'] . "</option>";
                    }
                ?>
            </select><br>
            <input type="hidden" name="photo_path" id="photoPathInput">

            <div id="dropzone-upload" class="dropzone"></div>
            <br>
            <input class="btn btn-primary mt-3" type="submit" value="Register Member">
        </form>
    </div>
    <?php $conn->close();?>

    <div class="col-md-6">
            <h2>Register Trainer</h2>
            <form action="register_trainer.php" method="POST" enctype="multipart/form-data">
                First Name: <input class="form-control" type="text" name="first_name"><br>
                Last Name: <input class="form-control" type="text" name="last_name"><br>
                Email: <input class="form-control" type="email" name="email"><br>
                Phone Number: <input class="form-control" type="text" name="phone_number"><br>
                <input class="btn btn-primary" type="submit" value="Register Trainer">
            </form>       
    </div>
                
    <div class="row" id="assign_trainer">
        <div class="col-md-6">
            <h2>Assign Trainer to Member</h2>
            <form action="assign_trainer.php" method="POST">
                <label for="">Select Member</label>
                <select name="member" class="form-control">
                    <?php
                    
                     foreach($select_members as $member) :  ?> 

                        <option value="<?php echo $member['member_id'] ?>">
                            <?php echo $member['first_name'] . " " . $member['last_name']; ?>
                        </option>

                    <?php  endforeach; ?>
                
                    
                </select>

                <label for="">Select Trainer</label>
                <select name="trainer" class="form-control">
                    <?php
                    
                     foreach($select_trainers as $trainer) :  ?> 

                        <option value="<?php echo $trainer['trainer_id'] ?>">
                            <?php echo $trainer['first_name'] . " " . $trainer['last_name']; ?>
                        </option>

                    <?php  endforeach; ?>
                
                    
                </select>
                <br>
                <button type="submit" class="btn btn-primary">Assign Trainer</button>
            </form>
        </div>
    </div>
       
    
           
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>       
    <script>
    Dropzone.options.dropzoneUpload = {
        url: "upload_photo.php", //php does the upload from user to database, js does the user interface
        paramName: "photo",
        maxFilesize: 20, // MB
        acceptedFiles: "image/*",
        init: function () {
            this.on("success", function (file, response) {
                // Parse the JSON response
                const jsonResponse = JSON.parse(response);
                // Check if the file was uploaded successfully
                if (jsonResponse.success) {
                    // Set the hidden input's value to the uploaded file's path
                    document.getElementById('photoPathInput').value = jsonResponse.photo_path;
                } else {
                    console.error(jsonResponse.error);
                }
            });
        }
    };
</script>
</body>
</html>