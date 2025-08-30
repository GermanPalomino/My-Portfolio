<?php
// Requerir el archivo de configuración y la carga automática de Composer
require_once "../config/APP.php";
require_once "../vendor/autoload.php";
//importar la libreria para el manejo de las hojas de excel
use PhpOffice\PhpSpreadsheet\IOFactory;

// Definición de la clase importacionControlador
class importacionControlador
{
    /*----------  Controlador para importar Excel  ----------*/
    public function importar_excel_controlador()
    {
        // Iniciar sesión
        session_start(['name' => 'xcoring']);

        // Verificar si $_SESSION['datos_categoria'] y $_SESSION['datos_criterio'] están inicializados como un arreglo
        if (!isset($_SESSION['datos_categoria'])) {
            $_SESSION['datos_categoria'] = array();
        }

        if (!isset($_SESSION['datos_criterio'])) {
            $_SESSION['datos_criterio'] = array();
        }

        // Verificar si se ha subido un archivo Excel sin errores
        if (isset($_FILES['excelFile']) && $_FILES['excelFile']['error'] == UPLOAD_ERR_OK) {
            $file = $_FILES['excelFile']['tmp_name'];

            try {
                // Cargar el archivo Excel
                $spreadsheet = IOFactory::load($file);

                // Conexión a la base de datos
                $dsn = 'mysql:host=localhost;dbname=xcoring';
                $username = 'root';
                $password = '';

                try {
                    // Establecer conexión a la base de datos
                    $conexion = new PDO($dsn, $username, $password);
                    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    throw new Exception("Error en la conexión a la base de datos: " . $e->getMessage());
                }

                $conexion->beginTransaction(); // Iniciar transacción

                // Procesar la hoja de categorías
                $sheet = $spreadsheet->getSheetByName('categoria');
                if ($sheet) {
                    $sheetData = $sheet->toArray(null, true, true, true);

                    foreach ($sheetData as $row) {
                        if ($row['A'] != 'id_categoria') {
                            // Asignar los datos de cada fila a variables
                            $id_categoria = $row['A'];
                            $nombre_categoria = $row['B'];
                            $peso_categoria = $row['C'];
                            $descripcion_categoria = $row['D'];
                            $id_proyecto = $row['E'];
                            $id_rol = $row['F'];

                            // SQL para insertar o actualizar una categoría
                            $sql = "INSERT INTO categoria (id_categoria, nombre_categoria, peso_categoria, descripcion_categoria, id_proyecto, id_rol) 
                                    VALUES (:id_categoria, :nombre_categoria, :peso_categoria, :descripcion_categoria, :id_proyecto, :id_rol) 
                                    ON DUPLICATE KEY UPDATE 
                                    nombre_categoria = VALUES(nombre_categoria), 
                                    peso_categoria = VALUES(peso_categoria), 
                                    descripcion_categoria = VALUES(descripcion_categoria), 
                                    id_proyecto = VALUES(id_proyecto),
                                    id_rol = VALUES(id_rol)";

                            $stmt = $conexion->prepare($sql);
                            $stmt->bindParam(':id_categoria', $id_categoria);
                            $stmt->bindParam(':nombre_categoria', $nombre_categoria);
                            $stmt->bindParam(':peso_categoria', $peso_categoria);
                            $stmt->bindParam(':descripcion_categoria', $descripcion_categoria);
                            $stmt->bindParam(':id_proyecto', $id_proyecto);
                            $stmt->bindParam(':id_rol', $id_rol);

                            // Ejecutar la inserción/actualización y agregar a la variable de sesión
                            if ($stmt->execute()) {
                                $_SESSION['datos_categoria'][] = array(
                                    "ID" => $id_categoria,
                                    "Nombre" => $nombre_categoria,
                                    "Peso" => $peso_categoria
                                );
                            } else {
                                throw new Exception("Error al insertar/actualizar categoría: " . implode(", ", $stmt->errorInfo()));
                            }
                        }
                    }
                } else {
                    throw new Exception("No se encontró la hoja 'categoria' en el archivo.");
                }

                // Procesar la hoja de criterios
                $sheet = $spreadsheet->getSheetByName('criterio');
                if ($sheet) {
                    $sheetData = $sheet->toArray(null, true, true, true);

                    foreach ($sheetData as $row) {
                        if ($row['A'] != 'id_criterio') {
                            // Asignar los datos de cada fila a variables
                            $id_criterio = $row['A'];
                            $nombre_criterio = $row['B'];
                            $descripcion_criterio = $row['C'];
                            $peso_criterio = $row['D'];
                            $id_categoria = $row['E'];
                            $tipopregunta_criterio = $row['F'];
                            $descripcion_calificacion1_criterio = $row['G'] ?? '';
                            $descripcion_calificacion2_criterio = $row['H'] ?? '';
                            $descripcion_calificacion3_criterio = $row['I'] ?? '';
                            $descripcion_calificacion4_criterio = $row['J'] ?? '';
                            $descripcion_calificacion5_criterio = $row['K'] ?? '';

                            // SQL para insertar o actualizar un criterio
                            $sql = "INSERT INTO criterio (id_criterio, nombre_criterio, descripcion_criterio, peso_criterio, id_categoria, tipopregunta_criterio, descripcion_calificacion1_criterio, descripcion_calificacion2_criterio, descripcion_calificacion3_criterio, descripcion_calificacion4_criterio, descripcion_calificacion5_criterio) 
                                    VALUES (:id_criterio, :nombre_criterio, :descripcion_criterio, :peso_criterio, :id_categoria, :tipopregunta_criterio, :descripcion_calificacion1_criterio, :descripcion_calificacion2_criterio, :descripcion_calificacion3_criterio, :descripcion_calificacion4_criterio, :descripcion_calificacion5_criterio) 
                                    ON DUPLICATE KEY UPDATE 
                                    nombre_criterio = VALUES(nombre_criterio), 
                                    descripcion_criterio = VALUES(descripcion_criterio), 
                                    peso_criterio = VALUES(peso_criterio), 
                                    id_categoria = VALUES(id_categoria),
                                    tipopregunta_criterio = VALUES (tipopregunta_criterio),
                                    descripcion_calificacion1_criterio = VALUES(descripcion_calificacion1_criterio),
                                    descripcion_calificacion2_criterio = VALUES(descripcion_calificacion2_criterio),
                                    descripcion_calificacion3_criterio = VALUES(descripcion_calificacion3_criterio),
                                    descripcion_calificacion4_criterio = VALUES(descripcion_calificacion4_criterio),
                                    descripcion_calificacion5_criterio = VALUES(descripcion_calificacion5_criterio)";

                            $stmt = $conexion->prepare($sql);
                            $stmt->bindParam(':id_criterio', $id_criterio);
                            $stmt->bindParam(':nombre_criterio', $nombre_criterio);
                            $stmt->bindParam(':descripcion_criterio', $descripcion_criterio);
                            $stmt->bindParam(':peso_criterio', $peso_criterio);
                            $stmt->bindParam(':id_categoria', $id_categoria);
                            $stmt->bindParam(':tipopregunta_criterio', $tipopregunta_criterio);
                            $stmt->bindParam(':descripcion_calificacion1_criterio', $descripcion_calificacion1_criterio);
                            $stmt->bindParam(':descripcion_calificacion2_criterio', $descripcion_calificacion2_criterio);
                            $stmt->bindParam(':descripcion_calificacion3_criterio', $descripcion_calificacion3_criterio);
                            $stmt->bindParam(':descripcion_calificacion4_criterio', $descripcion_calificacion4_criterio);
                            $stmt->bindParam(':descripcion_calificacion5_criterio', $descripcion_calificacion5_criterio);

                            // Ejecutar la inserción/actualización y agregar a la variable de sesión
                            if ($stmt->execute()) {
                                // Obtener los datos de la categoría correspondiente
                                $sql_categoria = "SELECT nombre_categoria, peso_categoria FROM categoria WHERE id_categoria = :id_categoria";
                                $stmt_categoria = $conexion->prepare($sql_categoria);
                                $stmt_categoria->bindParam(':id_categoria', $id_categoria);
                                $stmt_categoria->execute();
                                $categoria = $stmt_categoria->fetch(PDO::FETCH_ASSOC);

                                if ($categoria) {
                                    $_SESSION['datos_criterio'][] = array(
                                        "ID" => $id_criterio,
                                        "Nombre" => $nombre_criterio,
                                        "Peso" => $peso_criterio,
                                        "DescripcionCal1" => $descripcion_calificacion1_criterio,
                                        "DescripcionCal2" => $descripcion_calificacion2_criterio,
                                        "DescripcionCal3" => $descripcion_calificacion3_criterio,
                                        "DescripcionCal4" => $descripcion_calificacion4_criterio,
                                        "DescripcionCal5" => $descripcion_calificacion5_criterio,
                                        "IDC" => $id_categoria,
                                        "NombreC" => $categoria['nombre_categoria'],
                                        "PesoC" => $categoria['peso_categoria']
                                    );
                                } else {
                                    throw new Exception("Categoría no encontrada para el criterio con ID: $id_criterio.");
                                }
                            } else {
                                throw new Exception("Error al insertar/actualizar criterio: " . implode(", ", $stmt->errorInfo()));
                            }
                        }
                    }
                } else {
                    throw new Exception("No se encontró la hoja 'criterio' en el archivo.");
                }

                $conexion->commit(); // Confirmar transacción

                // Generar alerta de éxito
                $alerta = [
                    "Alerta" => "redireccionar",
                    "Titulo" => "¡Importación exitosa!",
                    "Texto" => "Los datos han sido importados correctamente y las variables de sesión actualizadas.",
                    "Tipo" => "success",
                    "URL" => SERVERURL . "evaluacion-new/"
                ];
                return json_encode($alerta);
            } catch (Exception $e) {
                $conexion->rollback(); // Revertir transacción en caso de error

                // Generar alerta de error
                $alerta = [
                    "Alerta" => "simple",
                    "Titulo" => "Error al importar datos",
                    "Texto" => "Error al importar datos: " . $e->getMessage(),
                    "Tipo" => "error"
                ];
                return json_encode($alerta);
            }
        } else {
            // Generar alerta de error al subir el archivo
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Error al subir el archivo",
                "Texto" => "No se pudo subir el archivo. Por favor, inténtelo de nuevo.",
                "Tipo" => "error"
            ];
            return json_encode($alerta);
        }
    }
}