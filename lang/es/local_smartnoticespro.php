<?php
// This file is part of Moodle - http://moodle.org/

/**
 * Spanish language strings for local_smartnoticespro.
 *
 * @package   local_smartnoticespro
 * @copyright 2026 Jesus Antonio Jimenez Aviña <antoniomexdf@gmail.com> <antoniojamx@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Smart Notices Pro';
$string['privacy:metadata'] = 'El plugin Smart Notices Pro almacena avisos creados por usuarios.';
$string['privacy:metadata:local_smartnoticespro'] = 'Almacena avisos y sus metadatos.';
$string['privacy:metadata:local_smartnoticespro:title'] = 'Título del aviso.';
$string['privacy:metadata:local_smartnoticespro:message'] = 'Contenido del aviso.';
$string['privacy:metadata:local_smartnoticespro:userid'] = 'ID del usuario creador.';
$string['privacy:metadata:local_smartnoticespro:timecreated'] = 'Marca de tiempo de creación.';
$string['privacy:metadata:local_smartnoticespro:timemodified'] = 'Marca de tiempo de última actualización.';

$string['managenotices'] = 'Gestionar Smart Notices Pro';
$string['notices'] = 'Avisos';
$string['addnotice'] = 'Agregar aviso';
$string['editnotice'] = 'Editar aviso';
$string['deletenotice'] = 'Eliminar aviso';
$string['confirmdelete'] = '¿Deseas eliminar este aviso?';
$string['nonotices'] = 'No se encontraron avisos.';

$string['title'] = 'Título';
$string['hidetitle'] = 'Ocultar título';
$string['confirmenabled'] = 'Habilitar botón de confirmación';
$string['message'] = 'Mensaje';
$string['active'] = 'Activo';
$string['inactive'] = 'Inactivo';
$string['status'] = 'Estado';
$string['scope'] = 'Alcance';
$string['scopeglobal'] = 'Global';
$string['scopecourse'] = 'Específico de curso';
$string['course'] = 'Curso';
$string['targetroles'] = 'Rol objetivo';
$string['targetgroup'] = 'Grupo objetivo';
$string['allgroups'] = 'Todos los grupos';
$string['targetrole_select'] = 'Selecciona un rol objetivo';
$string['targetrole_all'] = 'Todos los usuarios';
$string['targetrole_student'] = 'Estudiantes';
$string['targetrole_teacher'] = 'Docentes';
$string['targetrole_manager'] = 'Gestores/administradores';
$string['locations'] = 'Ubicaciones de visualización';
$string['location_login'] = 'Página de acceso';
$string['location_frontpage'] = 'Página principal';
$string['location_dashboard'] = 'Área personal';
$string['location_mycourses'] = 'Mis cursos';
$string['location_course'] = 'Dentro de un curso';
$string['startdate'] = 'Fecha de inicio';
$string['enddate'] = 'Fecha de fin';

$string['savechanges'] = 'Guardar cambios';
$string['gotocourse'] = 'Ir al curso';
$string['noticecreated'] = 'Aviso creado correctamente.';
$string['noticeupdated'] = 'Aviso actualizado correctamente.';
$string['noticedeleted'] = 'Aviso eliminado correctamente.';
$string['error:nolocations'] = 'Selecciona al menos una ubicación de visualización.';
$string['error:dateinvalid'] = 'La fecha de fin debe ser mayor o igual a la fecha de inicio.';
$string['error:courseidrequired'] = 'Debes seleccionar un curso para avisos de curso.';
$string['error:invalidscope'] = 'El alcance seleccionado no es válido.';
$string['error:invalidtargetrole'] = 'El rol objetivo seleccionado no es válido.';
$string['error:targetrolerequired'] = 'Debes seleccionar un rol objetivo para avisos globales.';
$string['error:invalidlocation'] = 'La ubicación seleccionada no es válida.';
$string['error:grouprequired'] = 'Debes seleccionar un grupo.';
$string['error:invalidgroup'] = 'El grupo seleccionado no es válido.';
$string['error:noticemissing'] = 'El aviso solicitado no existe.';
$string['error:cannotmanagecourse'] = 'No tienes permiso para gestionar avisos en este curso.';
$string['error:cannotmanageglobal'] = 'No tienes permiso para gestionar avisos globales.';

$string['table:title'] = 'Título';
$string['table:id'] = 'ID del aviso';
$string['table:scope'] = 'Alcance';
$string['table:course'] = 'Curso';
$string['table:group'] = 'Grupo';
$string['table:locations'] = 'Ubicaciones';
$string['table:targetrole'] = 'Rol objetivo';
$string['table:status'] = 'Estado';
$string['table:dates'] = 'Fechas';
$string['table:impressions'] = 'Impresiones';
$string['table:closes'] = 'Cierres';
$string['table:confirmations'] = 'Confirmaciones';
$string['table:ctr'] = 'CTR';
$string['table:actions'] = 'Acciones';
$string['table:reports'] = 'Reportes';

$string['report:title'] = 'Reporte del aviso';
$string['report:heading'] = 'Reporte: {$a}';
$string['report:nodata'] = 'No se encontraron datos para este aviso.';
$string['report:exportcsv'] = 'Exportar CSV';
$string['report:user'] = 'Usuario';
$string['report:email'] = 'Correo';
$string['report:action'] = 'Acción';
$string['report:courseid'] = 'ID de curso';
$string['report:pageurl'] = 'Página';
$string['report:date'] = 'Fecha y hora';


$string['capability:manageglobalnotices'] = 'Gestionar avisos globales Smart Notices Pro';
$string['capability:managecoursenotices'] = 'Gestionar avisos Smart Notices Pro en curso';
$string['capability:viewnotices'] = 'Ver avisos Smart Notices Pro';

$string['modal:close'] = 'Cerrar aviso';
$string['modal:confirm'] = 'Entendido';
$string['modal:dialoglabel'] = 'Aviso del sitio';
$string['coursenotices'] = 'Avisos del curso';
