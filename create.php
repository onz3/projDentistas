<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$name = $email = $cro = $cro_uf = "";
$name_err = $email_err = $cro_err = $cro_uf_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // valida nome
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $name_err = "Insira um nome.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $name_err = "Por favor insira um nome válido.";
    } else{
        $name = $input_name;
    }
    
    // valida email
    $input_email = trim($_POST["email"]);
    if(empty($input_email)){
        $email_err = "Por favor insira o email.";     
    } else{
        $email = $input_email;
    }
    
    // valida cro
    $input_cro = trim($_POST["cro"]);
    if(empty($input_cro)){
        $cro_err = "Insira o CRO.";     
    } elseif(!ctype_digit($input_cro)){
        $cro_err = "CRO Inválido.";
    } else{
        $cro = $input_cro;
    }

    // valida cro_uf
    $input_cro_uf = trim($_POST["cro_uf"]);
    if(empty($input_cro_uf)){
        $cro_err = "Insira o UF do CRO.";     
    } elseif(!ctype_digit($input_cro)){
        $cro_err = "UF Inválido.";
    } else{
        $cro_uf = $input_cro_uf;
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($email_err) && empty($cro_err) && empty($cro_uf_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO dentistas (name, email, cro, cro_uf) VALUES (?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssss", $param_name, $param_email, $param_cro, $param_cro_uf);
            
            // Set parameters
            $param_name = $name;
            $param_email = $email;
            $param_cro = $cro;
            $param_cro_uf = $cro_uf;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Algo de errado aconteceu. Tente novamente mais tarde.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inserir Dentista</title>
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
                    <h2 class="mt-5">Inserir</h2>
                    <p>Por favor, preencha o formulário para inserir o dentista no banco de dados.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
                            <label>CRO UF</label>
                            <input type="text" name="cro_uf" class="form-control <?php echo (!empty($cro_uf_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $cro_uf; ?>">
                            <span class="invalid-feedback"><?php echo $cro_uf_err;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Gravar">
                        <a href="index.php" class="btn btn-danger ml-2">Cancelar</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>