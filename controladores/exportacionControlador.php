<?php
// Incluir los archivos de configuración y autoload de Composer
require_once "../config/APP.php";
require_once "../vendor/autoload.php";

// Usar las clases necesarias de PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class exportacionControlador
{
    public function exportar_excel_controlador()
    {
        // Obtener el ID del proyecto enviado por POST
        $id_proyecto = $_POST['export'];

        // Crear una nueva hoja de cálculo
        $spreadsheet = new Spreadsheet();

        // Configuración de la conexión a la base de datos
        $dsn = 'mysql:host=localhost;dbname=xcoring';
        $username = 'root';
        $password = '';
        
        // Establecer la conexión utilizando PDO
        $conexion = new PDO($dsn, $username, $password);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Función para obtener datos de una tabla específica
        function obtener_datos($conexion, $tabla, $campos, $filtro = "", $valor_filtro = null) {
            // Construir la consulta SQL
            $consulta = "SELECT $campos FROM $tabla $filtro";
            $stmt = $conexion->prepare($consulta);
            if ($valor_filtro !== null) {
                $stmt->bindParam(':valor_filtro', $valor_filtro);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Función para obtener datos de criterios basados en el ID del proyecto
        function obtener_datos_criterio($conexion, $id_proyecto) {
            $consulta = "SELECT c.id_criterio, c.nombre_criterio, c.descripcion_criterio, c.peso_criterio, c.id_categoria, c.descripcion_calificacion1_criterio, c.descripcion_calificacion2_criterio, c.descripcion_calificacion3_criterio, c.descripcion_calificacion4_criterio, c.descripcion_calificacion5_criterio
                         FROM criterio c
                         JOIN categoria ca ON c.id_categoria = ca.id_categoria
                         WHERE ca.id_proyecto = :id_proyecto";
            $stmt = $conexion->prepare($consulta);
            $stmt->bindParam(':id_proyecto', $id_proyecto);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Exportar datos de categorías a la hoja de cálculo
        $sheet = $spreadsheet->createSheet(0);
        $sheet->setTitle('Categorias');
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nombre');
        $sheet->setCellValue('C1', 'Peso');
        $sheet->setCellValue('D1', 'Descripcion');
        $sheet->setCellValue('E1', 'ID Proyecto');

        $categorias = obtener_datos($conexion, 'categoria', 'id_categoria, nombre_categoria, peso_categoria, descripcion_categoria, id_proyecto', 'WHERE id_proyecto = :valor_filtro', $id_proyecto);
        if (!empty($categorias)) {
            $row = 2;
            foreach ($categorias as $categoria) {
                $sheet->setCellValue('A' . $row, $categoria['id_categoria']);
                $sheet->setCellValue('B' . $row, $categoria['nombre_categoria']);
                $sheet->setCellValue('C' . $row, $categoria['peso_categoria']);
                $sheet->setCellValue('D' . $row, $categoria['descripcion_categoria']);
                $sheet->setCellValue('E' . $row, $categoria['id_proyecto']);
                $row++;
            }
        }

        // Exportar datos de criterios a la hoja de cálculo
        $sheet = $spreadsheet->createSheet(1);
        $sheet->setTitle('Criterios');
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nombre');
        $sheet->setCellValue('C1', 'Descripcion');
        $sheet->setCellValue('D1', 'Peso');
        $sheet->setCellValue('E1', 'ID Categoria');
        $sheet->setCellValue('F1', 'Descripcion Cal1');
        $sheet->setCellValue('G1', 'Descripcion Cal2');
        $sheet->setCellValue('H1', 'Descripcion Cal3');
        $sheet->setCellValue('I1', 'Descripcion Cal4');
        $sheet->setCellValue('J1', 'Descripcion Cal5');

        $criterios = obtener_datos_criterio($conexion, $id_proyecto);
        if (!empty($criterios)) {
            $row = 2;
            foreach ($criterios as $criterio) {
                $sheet->setCellValue('A' . $row, $criterio['id_criterio']);
                $sheet->setCellValue('B' . $row, $criterio['nombre_criterio']);
                $sheet->setCellValue('C' . $row, $criterio['descripcion_criterio']);
                $sheet->setCellValue('D' . $row, $criterio['peso_criterio']);
                $sheet->setCellValue('E' . $row, $criterio['id_categoria']);
                $sheet->setCellValue('F' . $row, $criterio['descripcion_calificacion1_criterio']);
                $sheet->setCellValue('G' . $row, $criterio['descripcion_calificacion2_criterio']);
                $sheet->setCellValue('H' . $row, $criterio['descripcion_calificacion3_criterio']);
                $sheet->setCellValue('I' . $row, $criterio['descripcion_calificacion4_criterio']);
                $sheet->setCellValue('J' . $row, $criterio['descripcion_calificacion5_criterio']);
                $row++;
            }
        }

        // Exportar datos de evaluaciones a la hoja de cálculo
        $sheet = $spreadsheet->createSheet(2);
        $sheet->setTitle('Evaluaciones');
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'ID Cliente');
        $sheet->setCellValue('C1', 'ID Proyecto');
        $sheet->setCellValue('D1', 'ID Usuario');
        $sheet->setCellValue('E1', 'ID Proveedor');
        $sheet->setCellValue('F1', 'ID Categoria');
        $sheet->setCellValue('G1', 'ID Criterio');
        $sheet->setCellValue('H1', 'Calificacion');

        $evaluaciones = obtener_datos($conexion, 'evaluacion', 'id_evaluacion, id_cliente, id_proyecto, id_usuario, id_proveedor, id_categoria, id_criterio, calificacion_evaluacion', 'WHERE id_proyecto = :valor_filtro', $id_proyecto);
        if (!empty($evaluaciones)) {
            $row = 2;
            foreach ($evaluaciones as $evaluacion) {
                $sheet->setCellValue('A' . $row, $evaluacion['id_evaluacion']);
                $sheet->setCellValue('B' . $row, $evaluacion['id_cliente']);
                $sheet->setCellValue('C' . $row, $evaluacion['id_proyecto']);
                $sheet->setCellValue('D' . $row, $evaluacion['id_usuario']);
                $sheet->setCellValue('E' . $row, $evaluacion['id_proveedor']);
                $sheet->setCellValue('F' . $row, $evaluacion['id_categoria']);
                $sheet->setCellValue('G' . $row, $evaluacion['id_criterio']);
                $sheet->setCellValue('H' . $row, $evaluacion['calificacion_evaluacion']);
                $row++;
            }
        }

        // Exportar datos de clientes a la hoja de cálculo
        $sheet = $spreadsheet->createSheet(3);
        $sheet->setTitle('Clientes');
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nombre');
        $sheet->setCellValue('C1', 'Nombre Contacto');
        $sheet->setCellValue('D1', 'Email Contacto');
        $sheet->setCellValue('E1', 'Telefono Contacto');

        $clientes = obtener_datos($conexion, 'cliente', 'id_cliente, nombre_cliente, nombreContacto_cliente, emailContacto_cliente, telefonoContacto_cliente');
        if (!empty($clientes)) {
            $row = 2;
            foreach ($clientes as $cliente) {
                $sheet->setCellValue('A' . $row, $cliente['id_cliente']);
                $sheet->setCellValue('B' . $row, $cliente['nombre_cliente']);
                $sheet->setCellValue('C' . $row, $cliente['nombreContacto_cliente']);
                $sheet->setCellValue('D' . $row, $cliente['emailContacto_cliente']);
                $sheet->setCellValue('E' . $row, $cliente['telefonoContacto_cliente']);
                $row++;
            }
        }

        // Exportar datos de proyectos a la hoja de cálculo
        $sheet = $spreadsheet->createSheet(4);
        $sheet->setTitle('Proyectos');
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nombre');
        $sheet->setCellValue('C1', 'Email Contacto');

        $proyectos = obtener_datos($conexion, 'proyecto', 'id_proyecto, nombre_proyecto, emailContacto_proyecto', 'WHERE id_proyecto = :valor_filtro', $id_proyecto);
        if (!empty($proyectos)) {
            $row = 2;
            foreach ($proyectos as $proyecto) {
                $sheet->setCellValue('A' . $row, $proyecto['id_proyecto']);
                $sheet->setCellValue('B' . $row, $proyecto['nombre_proyecto']);
                $sheet->setCellValue('C' . $row, $proyecto['emailContacto_proyecto']);
                $row++;
            }
        }

        // Eliminar la primera hoja en blanco
        $spreadsheet->removeSheetByIndex($spreadsheet->getSheetCount() - 1);

        // Enviar el archivo al navegador para descargar
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="exportacion_evaluacion.xlsx"');
        header('Cache-Control: max-age=0');
    
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        exit();
    }
}