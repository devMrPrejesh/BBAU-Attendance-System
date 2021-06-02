<?php
    abstract class Enum {
        public static function isValid($value): bool {
            $callingClass = get_called_class();
            $reflection = new ReflectionClass($callingClass);
            $values = $reflection->getConstants();
            return in_array($value, $values, true);
        }
    }

    abstract class ExceptionMSG extends Enum {
        public const INVALID_ACTION = "Invalid action.";
        public const ALREADY_LOGGED = "You have already logged in [%s].";
        public const INVALID_DATA = "Invalid [%s].";
        public const INCOMPLETE_DATA = "Incomplete [%s] data.";
        public const AUTHENTICATION_REQUIRED = "You are unauthenticated.";
        public const FORBIDDEN = "You are unauthorized.";
        public const INVALID_OLD_PASSWORD = "Invalid Old Password.";
        public const INVALID_NEW_EMAIL = "Email address already exists.";
        public const MAIL_FAILURE = "Your password can't be sent via mail.";
        public const FILE_NOT_MOVED = "Your file cannot be uploaded.";
        public const LEAVE_FAILURE = "Your leave cannot be added.";
    }

    abstract class ResponseMSG extends Enum {
        public const REDIRECT_URL = "{\"redirect\": \"[%s]\"}";
        public const RESET_PASSWORD = "{\"success\": \"Your password has been reset.<br>Please check your mail.\"}";
        public const CHANGE_PASSWORD = "{\"success\": \"Password updated successfully.\"}";
        public const CHANGE_EMAIL= "{\"success\": \"Email address updated successfully.\"}";
    }

    abstract class MailTemplate extends Enum {
        public const RESET_PASSWORD = "server\\mail_templates\\reset_password_template.html";
    }
    
    abstract class RedirectUrl extends Enum {
        public const LOGIN = "http://localhost/attendance/index.php";
        public const RESET_PASSWORD = "http://localhost/attendance/index.php?redirect=password";
    }
    
    abstract class UserProfile extends Enum {
        public const PATH = "server\\upload\\";
        public const SIZE = 4194304;
        public const DEFAULT_PROFILE = "server\\upload\\default_user_profile.png";
        public const CONTENT_TYPE = array(
            "png" => "image/png", 
            "jpg" => "image/jpg", 
            "jpeg" => "image/jpeg"
        );
    }
    
    abstract class UserRole extends Enum {
        public const STUDENT = "STUDENT";
        public const TEACHER = "TEACHER";
    }
    
    abstract class LeaveAttachment extends Enum {
        public const PATH = "server\\upload\\";
        public const SIZE = 10485760;
        public const CONTENT_TYPE = array(
            "png" => "image/png", 
            "jpg" => "image/jpg", 
            "jpeg" => "image/jpeg", 
            "pdf" => "application/pdf"
        );
    }

    abstract class EditableStudentDetails extends Enum {
        //public const STUDENTNAME = "student_name";
    }

    abstract class EditableTeacherDetails extends Enum {
        //public const TEACHERNAME = "teacher_name";
    }
?>