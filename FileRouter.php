<?php
    include ('server/Utils.php');
    include ('server/Response.php');
    include ('server/Environment.php');

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        try {
            $result = null;
            if (array_key_exists('UserProfile', $_GET)) {
                include ('server/controller/UserController.php');
                $user_controller = new UserController();
                $result = $user_controller->getProfilePath();
            }
            elseif (array_key_exists('Leave', $_GET) && trim($_GET['Leave']) != "" && array_key_exists('Role', $_GET) && 
            trim($_GET['Role']) != "") {
                $leave_id = trim($_GET['Leave']);
                $role = trim($_GET['Role']);
                if ($role === UserRole::STUDENT) {
                    include ('server/controller/StudentController.php');
                    $student_controller = new StudentController();
                    $result = $student_controller->getAttachment($leave_id);
                }
                elseif ($role === UserRole::TEACHER) {
                    include ('server/controller/TeacherController.php');
                    $teacher_controller = new TeacherController();
                    $result = $teacher_controller->getStudentAttachment($leave_id);
                }
                else {
                    throw new ResponseException(ExceptionMSG::FORBIDDEN);
                }
            }
            else {
                throw new ResponseException(ExceptionMSG::INVALID_ACTION, 404);
            }
            
            $path = $result['path'];
            $content_type = $result['content-type'];
            header("Content-Type: ".$content_type);
            readfile($path);
        }
        catch (ResponseException $exp) {
            $status_code = $exp->getStatusCode();
            header("Content-Type: application/json; charset=UTF-8");
            header("X-PHP-Response-Code: $status_code", true, $status_code);
            echo '{"error": "'.$exp->getMessage().'"}';
        }
        catch (Exception $exp) {
            header("Content-Type: application/json; charset=UTF-8");
            header('HTTP/1.1 500 Internal Server Error');
            echo '{"InternalError": "'.$exp->getMessage().'"}';
        }
    }
    else {
        header("HTTP/1.0 404 Not Found");
    }
?>
