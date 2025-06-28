<?php
// manager.php
session_start(); // Selalu mulai session di awal

// Menggunakan db_config.php untuk koneksi database
require_once 'db_config.php';

// Memuat kelas-kelas pengguna dan manajemennya
require_once 'User.php';
require_once 'UserManager.php';

// --- Definisi Kelas-kelas Tugas ---
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
    protected $type; // Tambahkan properti type
    protected $userId; // Tambahkan properti userId

    public function __construct($id, $title, $type, $category, $userId) {
        $this->id = $id;
        $this->title = $title;
        $this->type = $type;
        $this->category = $category;
        $this->userId = $userId;
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

    public function getType() {
        return $this->type; // Mengembalikan tipe yang sudah disimpan
    }

    public function getUserId() {
        return $this->userId;
    }

    abstract public function getInfo();
}

// Regular Task
class RegularTask extends Task {
    private $deadline;

    public function __construct($id, $title, $type, $deadline, $category, $userId) {
        parent::__construct($id, $title, $type, $category, $userId);
        $this->deadline = $deadline;
    }

    public function getReminderStatus() {
        $today = date("Y-m-d");
        $deadlineDate = $this->deadline;

        if ($this->isDone()) {
            return "completed"; // Tambahkan status completed
        } elseif ($deadlineDate < $today) {
            return "overdue"; // merah
        } elseif ($deadlineDate == $today) {
            return "today"; // kuning
        } else {
            return "normal"; // biasa
        }
    }

    public function getDeadline() {
        return $this->deadline;
    }

    public function getInfo() {
        $formatted = ($this->deadline) ? date("d M Y", strtotime($this->deadline)) : "No deadline";
        return "Deadline: " . $formatted;
    }
}

// Urgent Task
class UrgentTask extends Task {
    private $priority;

    public function __construct($id, $title, $type, $priority, $category, $userId) {
        parent::__construct($id, $title, $type, $category, $userId);
        $this->priority = $priority;
    }

    public function getPriority() {
        return $this->priority;
    }

    public function getInfo() {
        return "Prioritas: " . $this->priority;
    }
}
// --- Akhir Definisi Kelas-kelas Tugas ---


// Inisialisasi UserManager
$userManager = new UserManager($conn);
$currentUser = $userManager->getCurrentUser();
// Redirect ke halaman login jika pengguna belum login
if ($currentUser === null) {
    header("Location: login.php");
    exit();
}

$tasks = []; // Inisialisasi array tasks

// --- Logika Penambahan Tugas ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title']) && $currentUser !== null) {
    $title = trim($_POST['title']);
    $type = $_POST['type'];
    $category = $_POST['category'];
    $extra = trim($_POST['extra']); // Ini bisa jadi deadline atau priority
    $user_id = $currentUser->getId(); // Ambil user ID dari objek User yang login

    $stmt = null; // Inisialisasi stmt

    if ($type === 'regular') {
        $stmt = $conn->prepare("INSERT INTO tasks (task_name, type, deadline, category, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $title, $type, $extra, $category, $user_id);
    } elseif ($type === 'urgent') {
        $stmt = $conn->prepare("INSERT INTO tasks (task_name, type, priority, category, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $title, $type, $extra, $category, $user_id);
    }

    if ($stmt && $stmt->execute()) {
        // Berhasil, tidak perlu pesan, langsung load ulang tugas dengan redirect
        header("Location: index.php"); // Redirect ke index.php setelah berhasil tambah
        exit();
    } else {
        // Echo alert hanya jika ada error
        echo "<script>alert('Gagal menambah tugas: " . (isset($stmt) ? $stmt->error : "Unknown error") . "');</script>";
    }
    if (isset($stmt)) $stmt->close();
}

// --- Logika Tandai Selesai ---
if (isset($_GET['done']) && $currentUser !== null) {
    $taskId = (int)$_GET['done'];
    $user_id = $currentUser->getId();
    $stmt = $conn->prepare("UPDATE tasks SET is_completed = TRUE WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $taskId, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php"); // Redirect untuk merefresh halaman
    exit();
}

// --- Logika Hapus Tugas ---
if (isset($_GET['delete']) && $currentUser !== null) {
    $taskId = (int)$_GET['delete'];
    $user_id = $currentUser->getId();
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $taskId, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php"); // Redirect untuk merefresh halaman
    exit();
}

// --- Ambil Semua Tugas (Hanya untuk User yang Login) ---
if ($currentUser !== null) {
    $user_id = $currentUser->getId();
    $sql = "SELECT id, task_name, type, deadline, priority, is_completed, category FROM tasks WHERE user_id = ? ORDER BY is_completed ASC, FIELD(type, 'urgent', 'regular'), deadline ASC, id DESC"; // Sorting lebih baik
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $task_obj = null;
            // Pastikan parameter userId diteruskan ke constructor
            if ($row['type'] === 'regular') {
                $task_obj = new RegularTask($row['id'], $row['task_name'], $row['type'], $row['deadline'], $row['category'], $user_id);
            } elseif ($row['type'] === 'urgent') {
                $task_obj = new UrgentTask($row['id'], $row['task_name'], $row['type'], $row['priority'], $row['category'], $user_id);
            }

            if ($task_obj) {
                if ($row['is_completed']) {
                    $task_obj->markAsDone();
                }
                $tasks[] = $task_obj;
            }
        }
    }
    $stmt->close();
}

$conn->close(); // Tutup koneksi setelah selesai semua operasi
?>