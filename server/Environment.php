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
        public const NOT_CLASS_TEACHER = "You have no class assigned.";
        public const NON_ACCESS_TEACHER = "You are not a subject or class teacher for student.";
        public const CLASS_NA = "It is your free lecture, no Class Available.";
        public const WEEKEND = "Today is a weekend.";
        public const DUPLICATE_ATTENDANCE = "Duplicate attendance records found.";
        public const LEAVE_STATUS_OVERWRITE = "You cannot overwrite your previous decision.";
        public const USER_EXIST = "[%s] ID [%s] already exists.";
    }

    abstract class ResponseMSG extends Enum {
        public const REDIRECT_URL = "{\"redirect\": \"[%s]\"}";
        public const RESET_PASSWORD = "{\"success\": \"Your password has been reset.<br>Please check your mail.\"}";
        public const CHANGE_PASSWORD = "{\"success\": \"Password updated successfully.\"}";
        public const CHANGE_EMAIL = "{\"success\": \"Email address updated successfully.\"}";
        public const ATTENDANCE_ADDED = "{\"success\": \"Attendance added successfully.\"}";
        public const LEAVE_DECIDE = "{\"success\": \"Leave [%s] successfully.\"}";
        public const USER_ADDED = "{\"success\": \"[%s] added successfully.\"}";
    }

    abstract class MailTemplate extends Enum {
        public const RESET_PASSWORD = "server\\mail_templates\\reset_password_template.html";
    }
    
    abstract class RedirectUrl extends Enum {
        public const DOMAIN = "http://localhost/attendance/";
        public const LOGIN = "index.php";
        public const RESET_PASSWORD = "index.php?redirect=password";
        public const STUDENT = "student.php";
        public const TEACHER = "teacher.php";
        public const ADMIN = "admin.php";
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
        public const ADMIN = "ADMIN";
    }

    abstract class LeaveDecide extends Enum {
        public const INITIAL_STATUS = array("INITIATED", "READ");
        public const FINAL_STATUS = array("APPROVED","REJECTED");
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
?>