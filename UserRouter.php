<?php
    include ('server/Utils.php');
    include ('server/controller/UserController.php');
    include ('server/Response.php');
    include ('server/Environment.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        header("Content-Type: application/json; charset=UTF-8");
        try {
            if (array_key_exists('action', $_POST) && trim($_POST['action']) != "") {
                $action = trim($_POST['action']);
                unset($_POST['action']);
                $user_controller = new UserController();

                if (!method_exists($user_controller, $action)) {
                    throw new ResponseException(ExceptionMSG::INVALID_ACTION, 404);
                }
                
                $response = $user_controller->$action(array_merge($_POST, $_FILES));
                $status_code = $response->getStatusCode();
                header("X-PHP-Response-Code: $status_code", true, $status_code);
                echo $response->getMessage();
            }
            else {
                throw new ResponseException(ExceptionMSG::INVALID_ACTION, 404);
            }
        }
        catch (ResponseException $exp) {
            $status_code = $exp->getStatusCode();
            header("X-PHP-Response-Code: $status_code", true, $status_code);
            echo '{"error": "'.$exp->getMessage().'"}';
        }
        catch (Exception $exp) {
            header('HTTP/1.1 500 Internal Server Error');
            echo '{"InternalError": "'.$exp->getMessage().'"}';
        }
    }
    else {
        header("HTTP/1.0 404 Not Found");
    }
?>
