<?php
//print_r($_POST);
//echo $_POST['txtID'];

$txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";
$txtNombre=(isset($_POST['txtNombre']))?$_POST['txtNombre']:"";
$txtApellidoP=(isset($_POST['txtApellidoP']))?$_POST['txtApellidoP']:"";
$txtApellidoM=(isset($_POST['txtApellidoM']))?$_POST['txtApellidoM']:"";
$txtCorreo=(isset($_POST['txtCorreo']))?$_POST['txtCorreo']:"";
$txtFoto=(isset($_FILES['txtFoto']["name"]))?$_FILES['txtFoto']["name"]:"";
$accion=(isset($_POST['accion']))?$_POST['accion']:"";
$accionAgregar="";
$accionModificar=$accionEliminar=$accionCancelar="disabled";
$mostrarModal = false;
include ("../conexion/conexion.php");

switch($accion){
    case "btnAgregar":
        $sentencia =$conn->prepare("INSERT INTO empleados(Nombre,ApellidoP,ApellidoM,Correo,Foto) VALUES 
        (:Nombre,:ApellidoP,:ApellidoM,:Correo,:Foto)");
        $sentencia->bindParam(':Nombre', $txtNombre);
        $sentencia->bindParam(':ApellidoP', $txtApellidoP);
        $sentencia->bindParam(':ApellidoM', $txtApellidoM);
        $sentencia->bindParam(':Correo', $txtCorreo);

        $Fecha = new DateTime();
        $nombreArchivo = ($txtFoto!="")?$Fecha->getTimestamp()."_".$_FILES["txtFoto"]["name"]:"user.png";
        $tmpFoto = $_FILES["txtFoto"]["tmp_name"];
        if($tmpFoto!=""){
            move_uploaded_file($tmpFoto, "../img/".$nombreArchivo);
        }
        $sentencia->bindParam(':Foto', $nombreArchivo);
        $sentencia->execute();
        echo $txtID;
        echo "Presionaste btnAgregar";
    break;
    case "btnModificar":

        $sentencia =$conn->prepare("UPDATE empleados SET 
        Nombre=:Nombre,
        ApellidoP=:ApellidoP,
        ApellidoM=:ApellidoM,
        Correo=:Correo WHERE id=:id");
        $sentencia->bindParam(':Nombre', $txtNombre);
        $sentencia->bindParam(':ApellidoP', $txtApellidoP);
        $sentencia->bindParam(':ApellidoM', $txtApellidoM);
        $sentencia->bindParam(':Correo', $txtCorreo);
        $sentencia->bindParam(':id', $txtID);
        $sentencia->execute();

        $Fecha = new DateTime();
        $nombreArchivo = ($txtFoto!="")?$Fecha->getTimestamp()."_".$_FILES["txtFoto"]["name"]:"user.png";
        $tmpFoto = $_FILES["txtFoto"]["tmp_name"];
        if($tmpFoto!=""){
            move_uploaded_file($tmpFoto, "../img/".$nombreArchivo);

            $sentencia =$conn->prepare("SELECT Foto FROM empleados WHERE id=:id");
            $sentencia->bindParam(':id', $txtID);
            $sentencia->execute();
            $empleado = $sentencia->fetch(PDO::FETCH_LAZY);
            print_r($empleado);
            if(isset($empleado["Foto"])){
                if(file_exists("../img/".$empleado["Foto"])){
                    if($item["Foto"]!="user.png"){
                        unlink("../img/".$empleado["Foto"]);
                    }
                }
            }
            $sentencia =$conn->prepare("UPDATE empleados SET Foto=:Foto WHERE id=:id");
            $sentencia->bindParam(':Foto', $nombreArchivo);
            $sentencia->bindParam(':id', $txtID);
            $sentencia->execute();
        }
      

        header('Location: index.php');
        echo $txtID;
        echo "Presionaste btnModificar";
    break;
    case "btnEliminar":
        $sentencia =$conn->prepare("SELECT Foto FROM empleados WHERE id=:id");
        $sentencia->bindParam(':id', $txtID);
        $sentencia->execute();
        $empleado = $sentencia->fetch(PDO::FETCH_LAZY);
        print_r($empleado);
        if(isset($empleado["Foto"])&&($item["Foto"]!="user.png")){
            if(file_exists("../img/".$empleado["Foto"])){
                unlink("../img/".$empleado["Foto"]);
            }
        }
        $sentencia =$conn->prepare("DELETE FROM empleados WHERE id=:id");
        $sentencia->bindParam(':id', $txtID);
        $sentencia->execute();
        header('Location: index.php');
        echo $txtID;
        echo "Presionaste btnEliminar";
    break;
    case "btnCancelar":
        header('Location: index.php');
    break;
    case "Seleccionar":
        $accionAgregar="disabled";
        $accionModificar=$accionEliminar=$accionCancelar="";
        $mostrarModal=true;
        break;

}
    $sentencia= $conn->prepare("SELECT * FROM `empleados` WHERE 1");
    $sentencia->execute();
    $listaEmpleados=$sentencia->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <form action="" method="POST" enctype="multipart/form-data">

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-row" >
                    <input type="hidden" required name="txtID" value="<?php echo $txtID;?>" placeholder="" id="txtID" require="">

                    <div class="form-group col-md-12">
                    <label for="">Nombre(s):</label>
                    <input type="text" name="txtNombre" class="form-control" required value="<?php echo $txtNombre; ?>" placeholder="" id="txtNombre" require="">
                    </div>

                    <div class="form-group col-md-12">
                    <label for="">Apellido Paterno:</label>
                    <input type="text" name="txtApellidoP"  class="form-control" required value="<?php echo $txtApellidoP; ?>" placeholder="" id="txtApellidoP" require="">
                    </div>

                    <div class="form-group col-md-12">
                    <label for="">Apellido Materno:</label>
                    <input type="text" name="txtApellidoM"  class="form-control" required value="<?php echo $txtApellidoM; ?>" placeholder="" id="txtApellidoM" require="">
                    </div>

                    <div class="form-group col-md-12">
                    <label for="">Correo:</label>
                    <input type="email" name="txtCorreo"  class="form-control" required value="<?php echo $txtCorreo; ?>" placeholder="" id="txtCorreo" require="">
                    </div>
                    
                    <div class="form-group col-md-12">
                    <label for="">Foto:</label>
                    <input type="file" accept="image/*" class="form-control" name="txtFoto" value="<?php echo $txtFoto; ?>" placeholder="" id="txtFoto" require="">
                    </div>

                    </div>
                </div>
                <div class="modal-footer">                    
                    <button value="btnAgregar" <?php echo $accionAgregar; ?> class="btn btn-outline-success" type="submit" name="accion">Agregar</button>
                    <button value="btnModificar" <?php echo $accionModificar; ?>  class="btn btn-outline-warning" type="submit" name="accion">Modificar</button>
                    <button value="btnEliminar" <?php echo $accionEliminar; ?>  class="btn btn-outline-danger" type="submit" name="accion">Eliminar</button>
                    <button value="btnCancelar" <?php echo $accionCancelar; ?>  class="btn btn-outline-primary" type="submit" name="accion">Cancelar</button>
                </div>
                </div>
            </div>
            </div>
           
             <!-- Button trigger modal -->
             <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModal">
             Agregar registro +
            </button>

        </form>
        <div class="row mt-5">
            <table class="table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nombre Completo</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <?php foreach($listaEmpleados as $empleado){ ?>
                    <tr>
                        <td><img class="img-thumbnail" width="100px" src="../img/<?php echo $empleado['Foto'];?>"/></td>
                        <td><?php echo $empleado['ApellidoP']; ?> <?php echo $empleado['ApellidoM']; ?> <?php echo $empleado['Nombre']; ?></td>
                        <td><?php echo $empleado['Correo']; ?></td>
                        <td>
                        <form action="" method="POST">
                            <input type="hidden" name="txtID" value="<?php echo $empleado['ID']; ?>">
                            <input type="hidden" name="txtNombre" value="<?php echo $empleado['Nombre']; ?>">
                            <input type="hidden" name="txtApellidoP" value="<?php echo $empleado['ApellidoP']; ?>">
                            <input type="hidden" name="txtApellidoM" value="<?php echo $empleado['ApellidoM']; ?>">
                            <input type="hidden" name="txtCorreo" value="<?php echo $empleado['Correo']; ?>">
                            <input type="hidden" name="txtFoto" value="<?php echo $empleado['Foto']; ?>">
                            <input type="submit" class="btn btn-primary" value="Seleccionar" name="accion">
                            <button value="btnEliminar" class="btn btn-danger" type="submit" name="accion">Eliminar</button>
                        </form>
                        </td>
                    </tr>
                    
                <?php } ?>
            </table>
        </div>
        <?php if($mostrarModal){?>
            <script type='text/javascript'>
                $(document).ready(function(){
                $('#exampleModal').modal('show');
                });
            </script>
        <?php } ?>
    </div>
</body>
</html>