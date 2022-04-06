<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$name = $email = $cro = $cro_uf = "";
$name_err = $email_err = $cro_err = $cro_uf_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
    // Validate name
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $name_err = "Insira o nome.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $name_err = "Nome inválido.";
    } else{
        $name = $input_name;
    }
    
    // Validate email email
    $input_email = trim($_POST["email"]);
    if(empty($input_email)){
        $email_err = "Insira um email.";     
    } else{
        $email = $input_email;
    }
    
    // Validate cro
    $input_cro = trim($_POST["cro"]);
    if(empty($input_cro)){
        $cro_err = "Insira o CRO.";     
    } elseif(!ctype_digit($input_cro)){
        $cro_err = "CRO Inválido.";
    } else{
        $cro = $input_cro;
    }

     // Validate uf
    $input_cro_uf = trim($_POST["cro_uf"]);
    if(empty($input_cro_uf)){
        $cro_uf_err = "Insira a UF.";     
    } elseif(!ctype_digit($input_cro_uf)){
        $cro_uf_err = "UF Inválido.";
    } else{
        $cro_uf = $input_cro_uf;
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($email_err) && empty($cro_err) && empty($cro_uf_err)){
        // Prepare an update statement
        $sql = "UPDATE dentistas SET name=?, email=?, cro=?, cro_uf=? WHERE id=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssi", $param_name, $param_email, $param_cro, $param_cro_uf, $param_id);
            
            // Set parameters
            $param_name = $name;
            $param_email = $email;
            $param_cro = $cro;
            $param_cro_uf = $cro_uf;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Algo deu errado, tente mais tarde.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM dentistas WHERE id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $name = $row["name"];
                    $email = $row["email"];
                    $cro = $row["cro"];
                    $cro_uf = $row["cro_uf"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Algo deu errado tente mais tarde.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($link);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Atualizar cadastro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Atualizar cadastro</h2>
                    <p>Edite os campos do formulário e atualize..</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Nome</label>
                            <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <textarea name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"><?php echo $email; ?></textarea>
                            <span class="invalid-feedback"><?php echo $email_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>CRO</label>
                            <input type="text" name="cro" class="form-control <?php echo (!empty($cro_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $cro; ?>">
                            <span class="invalid-feedback"><?php echo $cro_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>UF</label>
                            <input type="text" name="cro_uf" class="form-control <?php echo (!empty($cro_uf_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $cro_uf; ?>">
                            <span class="invalid-feedback"><?php echo $cro_uf_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-danger ml-2">Cancelar</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>