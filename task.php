<?php
// Set session save path (optional kalau error permission)
session_save_path("C:/xampp/htdocs/todolist_kelompok/tmp_session"); // ubah sesuai folder lokal kamu
session_start();

// Class Kategori
class Kategori {
    public static function getAllCategories() {
        return [
            "Kuliah",
            "Kerja",
            "Pribadi",
            "Organisasi",
            "Lainnya"
        ];
    }
}

// Class abstrak Task
abstract class Task {
    protected $id;
    protected $title;
    protected $isDone = false;
    protected $category;

    public function __construct($id, $title, $type, $category) {
        $this->id = $id;
        $this->title = $title;
        $this->type = $type;
        $this->category = $category;
    }

    public function markAsDone() {
        $this->isDone = true;
    }

    public function isDone() {
        return $this->isDone;
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getCategory() {
        return $this->category;
    }

    abstract public function getType();
    abstract public function getInfo();
}

// Regular Task
class RegularTask extends Task {
    private $deadline;

    public function __construct($id, $title, $type, $deadline, $category) {
        parent::__construct($id, $title, $type, $category);
        $this->deadline = $deadline;
    }

    public function getReminderStatus() {
        $today = date("Y-m-d");
        $deadlineDate = $this->deadline;

        if ($deadlineDate < $today) {
            return "overdue"; // merah
        } elseif ($deadlineDate == $today) {
            return "today"; // kuning
        } else {
            return "normal"; // biasa
        }
    }

    public function getType() {
        return "Regular";
    }

    public function getDeadline() {
        return $this->deadline;
    }

    public function getInfo() {
        $formatted = date("d-m-Y", strtotime($this->deadline));
        return "Deadline: " . $formatted;
    }
}

// Urgent Task
class UrgentTask extends Task {
    private $priority;

    public function __construct($id, $title, $priority, $category) {
        parent::__construct($id, $title, $priority, $category);
        $this->priority = $priority;
    }

    public function getType() {
        return "Urgent";
    }

    public function getInfo() {
        return "Priority: " . $this->priority;
    }
}
?>
