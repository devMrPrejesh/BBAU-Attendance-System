<?php
    include ('server/repository/StudentRepository.php');
    include ('server/repository/TeacherRepository.php');
    include ('server/repository/AdminRepository.php');
    include ('server/repository/UserRepository.php');
    
    class AdminService {

        public function insertStudent(int $student_id, string $name, string $class, int $teacher_id, string $email): void {
            $student_repository = new StudentRepository();
            $user_repository = new UserRepository();
            $teacher_repository = new TeacherRepository();

            if ($user_repository->existById($email)) {
                throw new ResponseException(ExceptionMSG::INVALID_NEW_EMAIL, 406);
            }
            elseif ($teacher_repository->findById($teacher_id) == NULL) {
                $message = Utils::constructMSG(ExceptionMSG::INVALID_DATA, "Teacher ID");
                throw new ResponseException($message, 406);
            }
            else {
                $period_size = $student_repository->checkNewStudent($student_id, $class);
                $student_repository->save($student_id, $name, $class, $period_size, $teacher_id);
                $password = Utils::generatePassword();
                $user_repository->save($email, $password, UserRole::STUDENT, $student_id, explode(" ", $name)[0]);
                $body = "You have successfully registered in BBAU Attendance System.\n Your temporary password is ".$password;
                if (!Utils::sendMail($email, "Registration Completed", $body)) {
                    throw new ResponseException(ExceptionMSG::MAIL_FAILURE, 501);
                }
            }
        }

        public function insertTeacher(int $teacher_id, string $name, int $period_size, string $email, string $class=NULL): void {
            $user_repository = new UserRepository();
            $teacher_repository = new TeacherRepository();

            if ($user_repository->existById($email)) {
                throw new ResponseException(ExceptionMSG::INVALID_NEW_EMAIL, 406);
            }
            elseif ($teacher_repository->findById($teacher_id) != NULL) {
                $message = Utils::constructMSG(ExceptionMSG::USER_EXIST, "Teacher", $teacher_id);
                throw new ResponseException($message, 400);
            }
            else {
                $teacher_repository->save($teacher_id, $name, $period_size, $class);
                $password = Utils::generatePassword();
                $user_repository->save($email, $password, UserRole::TEACHER, $teacher_id, explode(" ", $name)[0]);
                $body = "You have successfully registered in BBAU Attendance System.\n Your temporary password is ".$password;
                if (!Utils::sendMail($email, "Registration Completed", $body)) {
                    throw new ResponseException(ExceptionMSG::MAIL_FAILURE, 501);
                }
            }
        }

    }
?>
