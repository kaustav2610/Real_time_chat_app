<?php
    session_start();
    include_once "config.php";
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    if(!empty($fname) && !empty($lname) && !empty($email) && !empty($password)){
        //validate email
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){ //if email is valid 
            //check email already exists in DB or not
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");

            if(mysqli_num_rows($sql) > 0){ //if mail already exists
                echo "$email - This email already exist!";
            }else{
                //check user uploaded file or not
                if(isset($_FILES['image'])){ //if file uploaded
                    $img_name = $_FILES['image']['name']; //getting user uloaded image name
                    $img_type = $_FILES['image']['type'];  //getting user uloaded image type
                    $tmp_name = $_FILES['image']['tmp_name']; //temporary name used to save file in our folder
                   
                    //exploding
                    $img_explode = explode('.',$img_name);
                    $img_ext = end($img_explode); //getting extension of user uploaded img
    
                    $extensions = ["jpeg", "png", "jpg"]; //valid img extensions

                    if(in_array($img_ext, $extensions) === true){ //if user uploaded extension and valid extension matches
                        $types = ["image/jpeg", "image/jpg", "image/png"];
                        if(in_array($img_type, $types) === true){
                            $time = time(); //return curret time(will use for renaming user file with current time because it is unique for all)

                            //move uploaded img to perticular folder(in DB just url store)
                            $new_img_name = $time.$img_name;
                            if(move_uploaded_file($tmp_name,"images/".$new_img_name)){ //if user upload image ,move to folder successfully
                                $ran_id = rand(time(), 100000000); //creating random id for user

                                $status = "Active now"; // once user signed up then his status will be active now
                                $encrypt_pass = md5($password);

                                //insert all user data
                                $insert_query = mysqli_query($conn, "INSERT INTO users (unique_id, fname, lname, email, password, img, status)
                                VALUES ({$ran_id}, '{$fname}','{$lname}', '{$email}', '{$encrypt_pass}', '{$new_img_name}', '{$status}')");

                                if($insert_query){ //if data inserted successfully
                                    $select_sql2 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
                                    if(mysqli_num_rows($select_sql2) > 0){
                                        $result = mysqli_fetch_assoc($select_sql2);
                                        $_SESSION['unique_id'] = $result['unique_id']; //using this session we will use user unique_id in other php file
                                        echo "success";
                                    }else{
                                        echo "This email address not Exist!";
                                    }
                                }else{
                                    echo "Something went wrong. Please try again!";
                                }
                            }
                        }else{
                            echo "Please upload an image file - jpeg, png, jpg";
                        }
                    }else{
                        echo "Please upload an image file - jpeg, png, jpg";
                    }
                }
            }
        }else{
            echo "$email is not a valid email!";
        }
    }else{
        echo "All input fields are required!";
    }
?>