<?php
    abstract class Enum {
        public static function isValid($value): bool {
            $callingClass = get_called_class();
            $reflection = new ReflectionClass($callingClass);
            $values = $reflection->getConstants();
            return in_array($value, $values, true);
        }
    }
    
    abstract class UserRole extends Enum {
        public const STUDENT = "STUDENT";
        public const TEACHER = "TEACHER";
    }
    
    abstract class LeaveAttachment extends Enum {
        public const SIZE = 16777216;
        public const CONTENTTYPE = array("image/png", "image/jpg", "image/jpeg", "application/pdf");
    }

    abstract class EditableStudentDetails extends Enum {
        public const EMAIL = "email";
        //public const STUDENTNAME = "student_name";
    }

    abstract class EditableTeacherDetails extends Enum {
        public const EMAIL = "email";
        //public const TEACHERNAME = "teacher_name";
    }
?>