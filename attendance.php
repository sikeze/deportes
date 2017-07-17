<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 *
*
* @package    local
* @subpackage deportes
* @copyright  2017	Mark Michaelsen (mmichaelsen678@gmail.com)
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once(dirname(dirname(dirname(__FILE__))) . "/config.php");
require_once($CFG->dirroot."/local/deportes/locallib.php");
require_once($CFG->libdir . "/tablelib.php");
global $CFG, $DB, $OUTPUT, $PAGE, $USER;

// User must be logged in.
require_login();
if (isguestuser()) {
	die();
}

$context = context_system::instance();

$url = new moodle_url("/local/deportes/attendance.php");
$PAGE->navbar->add(get_string("nav_title", "local_deportes"));
$PAGE->navbar->add(get_string("attendance", "local_deportes"), $url);
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout("standard");
$PAGE->set_title(get_string("page_title", "local_deportes"));
$PAGE->set_heading(get_string("page_heading", "local_deportes"));

$curl = curl_init();
$url = "http://webapi.uai.cl/webcursos/asistenciasAlumno";
$token = "0e5f3b2d4b974aa0b1835b6dc756a696dQoQ01";
$fields = array(
		"token" => $token,
		"email" => "mscalvini@alumnos.uai.cl"
);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_POST, TRUE);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($fields));
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
$result = json_decode(curl_exec($curl));
curl_close($curl);

$table = new html_table("p");

$table->head = array(
		get_string("month", "local_deportes"),
		get_string("week", "local_deportes"),
		get_string("date", "local_deportes"),
		get_string("sport", "local_deportes"),
		get_string("t_start", "local_deportes"),
		get_string("t_end", "local_deportes"),
		get_string("attendance", "local_deportes")
);

$table->size = array(
		"7%",
		"7%",
		"20%",
		"20%",
		"15%",
		"15%",
		"16%"
);

$data = $result->asistencias->asistencias;

foreach($data as $attendance) {
	$attendanceinfo = array(
			date('F', mktime(0, 0, 0, $attendance->Mes, 10)),
			$attendance->Semana,
			$attendance->Dia."-".$attendance->Mes,
			$attendance->Deporte,
			substr($attendance->HoraInicio, -8, 5),
			substr($attendance->HoraTermino, -8, 5),
			$attendance->Asistencia
	);
	
	$table->data[] = $attendanceinfo;
}



echo $OUTPUT->header();
echo $OUTPUT->heading("DeportesUAI");
echo $OUTPUT->tabtree(deportes_tabs(), "attendance");
echo html_writer::table($table);
echo $OUTPUT->footer();