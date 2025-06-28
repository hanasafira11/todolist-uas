<?php
// index.php
require_once 'manager.php'; // Ini akan menjalankan semua logika PHP dan mengisi $tasks

// Jika user tidak null (berarti sudah login), ambil username-nya
$currentUsername = ($currentUser !== null) ? $currentUser->getUsername() : '';

// Mengelompokkan tugas berdasarkan status untuk tampilan Kanban
$newTaskTasks = [];
$scheduledTasks = [];
$inProgressTasks = [];
$completedTasks = [];

foreach ($tasks as $task) {
    if ($task->isDone()) {
        $completedTasks[] = $task;
    } else {
        // Tentukan status berdasarkan logika aplikasi Anda
        // Ini adalah contoh sederhana, Anda mungkin perlu logika yang lebih kompleks
        // Misal: New Task (no deadline/priority set yet), Scheduled (deadline future), In Progress (started manually), etc.
        // Untuk demo ini, kita akan membuat asumsi sederhana:
        // RegularTask dengan deadline future = Scheduled
        // UrgentTask = In Progress (atau kategori lain sesuai kebutuhan)
        // Jika tidak ada kondisi di atas, dianggap New Task
        
        if ($task instanceof RegularTask) {
            $status = $task->getReminderStatus();
            if ($status === 'normal' || $status === 'today') {
                $scheduledTasks[] = $task;
            } elseif ($status === 'overdue') {
                // Bisa diletakkan di 'In Progress' jika masih harus dikerjakan
                $inProgressTasks[] = $task;
            }
        } elseif ($task instanceof UrgentTask) {
            $inProgressTasks[] = $task; // Asumsi urgent task sedang dalam pengerjaan
        } else {
            $newTaskTasks[] = $task; // Default: new task
        }
    }
}

// Anda mungkin perlu menyesuaikan logika pengelompokan di atas
// agar sesuai dengan definisi "New task", "Scheduled", "In progress", "Completed"
// dari gambar yang Anda berikan. Contoh di atas adalah interpretasi sederhana.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban Dashboard - GM Agency</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="agency-logo">
                    <img src="https://via.placeholder.com/30" alt="GM Agency Logo">
                    <span>GM Agency</span>
                </div>
                <button class="toggle-sidebar" aria-label="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-item active"><i class="fas fa-grip-horizontal"></i> My work</div>
                    <div class="nav-item"><i class="fas fa-calendar-alt"></i> Schedule</div>
                    <div class="nav-item"><i class="fas fa-envelope"></i> Messages <span class="badge">12</span></div>
                    <div class="nav-item"><i class="fas fa-clipboard-list"></i> Tasks</div>
                    <div class="nav-item"><i class="fas fa-history"></i> History</div>
                    <div class="nav-item"><i class="fas fa-file-alt"></i> Reports</div>
                </div>

                <div class="nav-section">
                    <h4><i class="fas fa-users"></i> Teams <span class="add-icon">+</span></h4>
                    <ul>
                        <li>Marketing</li>
                        <li>Design</li>
                        <li>Development</li>
                        <li>Employee Training</li>
                        <li>Video Recording</li>
                    </ul>
                </div>

                <div class="nav-section">
                    <h4><i class="fas fa-project-diagram"></i> Projects <span class="add-icon">+</span></h4>
                    <ul>
                        <li class="active-project">New Website <i class="fas fa-chevron-down"></i>
                            <ul class="sub-projects">
                                <li>Web Development</li>
                                <li>Sales Funnel</li>
                                <li>Mobile App</li>
                                <li>CRM Integration</li>
                                <li>Webinar</li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="invite-people">
                <i class="fas fa-plus"></i> Invite people
            </div>
            <div class="user-info">
                <?php if ($currentUser): ?>
                    <p>Logged in as: <br><strong><?= htmlspecialchars($currentUsername); ?></strong></p>
                    <a href="logout.php" class="logout-btn">Logout</a>
                <?php endif; ?>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <div class="board-controls">
                        <button class="btn-primary" onclick="document.getElementById('task-form-container').scrollIntoView({ behavior: 'smooth' });"><i class="fas fa-plus"></i> Add new</button>
                        <button class="btn-secondary"><i class="fas fa-table"></i> Table view</button>
                        <button class="btn-secondary active"><i class="fas fa-th-large"></i> Kanban board</button>
                    </div>
                </div>
                <div class="topbar-right">
                    <div class="search-bar">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search...">
                    </div>
                    <div class="topbar-icons">
                        <i class="fas fa-bell"></i>
                        <i class="fas fa-filter"></i> Filter
                        <div class="user-group">
                            <img src="https://via.placeholder.com/25/ff6347/fff" alt="User 1" class="user-avatar">
                            <img src="https://via.placeholder.com/25/4682b4/fff" alt="User 2" class="user-avatar">
                            <img src="https://via.placeholder.com/25/3cb371/fff" alt="User 3" class="user-avatar">
                            <span class="user-count">+3</span>
                        </div>
                    </div>
                    <div class="notifications">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">15</span>
                    </div>
                </div>
            </header>

            <section class="kanban-board">
                <div class="kanban-column">
                    <div class="column-header">
                        <h3>New task <span class="task-count"><?= count($newTaskTasks); ?></span></h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="task-list">
                        <?php foreach ($newTaskTasks as $task): ?>
                            <div class="task-card">
                                <h4><?= htmlspecialchars($task->getTitle()); ?></h4>
                                <p class="task-meta"><?= htmlspecialchars($task->getInfo()); ?></p>
                                <div class="task-tags">
                                    <span class="tag tag-feedback"><?= htmlspecialchars($task->getCategory()); ?></span>
                                </div>
                                <div class="task-actions">
                                    <a href="?done=<?= $task->getId() ?>" class="action-done" title="Mark as Done"><i class="fas fa-check-circle"></i></a>
                                    <a href="?delete=<?= $task->getId() ?>" class="action-delete" title="Delete Task" onclick="return confirm('Yakin ingin menghapus tugas ini?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($newTaskTasks)): ?>
                            <p class="no-tasks-in-column">No new tasks</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="kanban-column">
                    <div class="column-header">
                        <h3>Scheduled <span class="task-count"><?= count($scheduledTasks); ?></span></h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="task-list">
                        <?php foreach ($scheduledTasks as $task): ?>
                            <?php
                            $statusClass = '';
                            if ($task instanceof RegularTask) {
                                $status = $task->getReminderStatus();
                                if ($status === 'overdue') $statusClass = 'task-overdue';
                                elseif ($status === 'today') $statusClass = 'task-today';
                            }
                            ?>
                            <div class="task-card <?= $statusClass; ?>">
                                <h4><?= htmlspecialchars($task->getTitle()); ?></h4>
                                <p class="task-meta"><?= htmlspecialchars($task->getInfo()); ?></p>
                                <div class="task-tags">
                                    <span class="tag tag-feedback"><?= htmlspecialchars($task->getCategory()); ?></span>
                                </div>
                                <div class="task-actions">
                                    <a href="?done=<?= $task->getId() ?>" class="action-done" title="Mark as Done"><i class="fas fa-check-circle"></i></a>
                                    <a href="?delete=<?= $task->getId() ?>" class="action-delete" title="Delete Task" onclick="return confirm('Yakin ingin menghapus tugas ini?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($scheduledTasks)): ?>
                            <p class="no-tasks-in-column">No scheduled tasks</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="kanban-column">
                    <div class="column-header">
                        <h3>In progress <span class="task-count"><?= count($inProgressTasks); ?></span></h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="task-list">
                        <?php foreach ($inProgressTasks as $task): ?>
                            <div class="task-card <?= ($task instanceof UrgentTask) ? 'task-urgent' : ''; ?>">
                                <h4><?= htmlspecialchars($task->getTitle()); ?></h4>
                                <p class="task-meta"><?= htmlspecialchars($task->getInfo()); ?></p>
                                <div class="task-tags">
                                    <span class="tag tag-feedback"><?= htmlspecialchars($task->getCategory()); ?></span>
                                </div>
                                <div class="task-actions">
                                    <a href="?done=<?= $task->getId() ?>" class="action-done" title="Mark as Done"><i class="fas fa-check-circle"></i></a>
                                    <a href="?delete=<?= $task->getId() ?>" class="action-delete" title="Delete Task" onclick="return confirm('Yakin ingin menghapus tugas ini?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($inProgressTasks)): ?>
                            <p class="no-tasks-in-column">No tasks in progress</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="kanban-column">
                    <div class="column-header">
                        <h3>Completed <span class="task-count"><?= count($completedTasks); ?></span></h3>
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="task-list">
                        <?php foreach ($completedTasks as $task): ?>
                            <div class="task-card task-completed">
                                <h4><?= htmlspecialchars($task->getTitle()); ?></h4>
                                <p class="task-meta"><?= htmlspecialchars($task->getInfo()); ?></p>
                                <div class="task-tags">
                                    <span class="tag tag-feedback"><?= htmlspecialchars($task->getCategory()); ?></span>
                                </div>
                                <div class="task-actions">
                                    <a href="?delete=<?= $task->getId() ?>" class="action-delete" title="Delete Task" onclick="return confirm('Yakin ingin menghapus tugas ini?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($completedTasks)): ?>
                            <p class="no-tasks-in-column">No completed tasks</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <div class="bottom-section">
                <div class="add-task-form-container" id="task-form-container">
                    <h1>📝 Buat Tugas Baru</h1>
                    <form method="POST">
                        <label for="title">Judul Tugas:</label>
                        <input type="text" name="title" placeholder="Judul tugas" required>

                        <label for="type-select">Tipe Tugas:</label>
                        <select name="type" id="type-select" onchange="updateExtraField()" required>
                            <option value="regular">Regular</option>
                            <option value="urgent">Urgent</option>
                        </select>

                        <label for="extra-input" id="extra-label">Deadline:</label>
                        <input type="date" name="extra" id="extra-input" required>

                        <label for="category">Kategori:</label>
                        <select name="category" id="category" required>
                            <?php
                            $categories = Kategori::getAllCategories();
                            foreach ($categories as $cat) {
                                echo "<option value='" . htmlspecialchars($cat) . "'>" . htmlspecialchars($cat) . "</option>";
                            }
                            ?>
                        </select>

                        <button type="submit">Tambah Tugas</button>
                    </form>
                </div>

                <div class="mobile-preview-container">
                    <div class="mobile-phone">
                        <div class="phone-screen">
                            <div class="phone-status-bar">
                                <span>9:41</span>
                                <div class="status-icons">
                                    <i class="fas fa-signal"></i>
                                    <i class="fas fa-wifi"></i>
                                    <i class="fas fa-battery-full"></i>
                                </div>
                            </div>
                            <div class="phone-header">
                                <i class="fas fa-bars"></i>
                                <h3>Website Development</h3>
                                <i class="fas fa-ellipsis-h"></i>
                            </div>
                            <div class="phone-nav-tabs">
                                <span class="active">New task <span><?= count($newTaskTasks); ?></span></span>
                                <span>Scheduled <span><?= count($scheduledTasks); ?></span></span>
                                <span>In progress <span><?= count($inProgressTasks); ?></span></span>
                                <span>Completed <span><?= count($completedTasks); ?></span></span>
                            </div>
                            <div class="phone-task-list">
                                <?php
                                // Gabungkan semua task untuk tampilan mobile, bisa disesuaikan lagi
                                $allTasksForMobile = array_merge($newTaskTasks, $scheduledTasks, $inProgressTasks, $completedTasks);
                                usort($allTasksForMobile, function($a, $b) {
                                    // Urutkan berdasarkan status selesai, lalu tipe, lalu ID
                                    if ($a->isDone() !== $b->isDone()) return $a->isDone() ? 1 : -1;
                                    if ($a->getType() === 'urgent' && $b->getType() !== 'urgent') return -1;
                                    if ($a->getType() !== 'urgent' && $b->getType() === 'urgent') return 1;
                                    return $a->getId() - $b->getId();
                                });
                                ?>
                                <?php foreach ($allTasksForMobile as $task): ?>
                                <div class="task-card-mobile <?= $task->isDone() ? 'task-completed' : ''; ?>">
                                    <h4><?= htmlspecialchars($task->getTitle()); ?></h4>
                                    <p class="task-meta"><?= htmlspecialchars($task->getInfo()); ?></p>
                                    <div class="task-tags">
                                        <span class="tag tag-feedback"><?= htmlspecialchars($task->getCategory()); ?></span>
                                    </div>
                                    <?php if ($task instanceof RegularTask && !$task->isDone()): ?>
                                        <p class="task-meta" style="font-style: italic; font-size: 0.75em; color: <?= ($task->getReminderStatus() === 'overdue' || $task->getReminderStatus() === 'today') ? 'red' : 'inherit'; ?>">
                                            <?php
                                            if ($task->getReminderStatus() === 'overdue') echo "Deadline Overdue!";
                                            elseif ($task->getReminderStatus() === 'today') echo "Deadline Today!";
                                            ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                                <button class="add-task-mobile-btn"><i class="fas fa-plus"></i></button>
                            </div>
                            <div class="phone-bottom-nav">
                                <div class="nav-item-phone"><i class="fas fa-calendar-alt"></i> Schedule</div>
                                <div class="nav-item-phone active"><i class="fas fa-home"></i> Home</div>
                                <div class="nav-item-phone"><i class="fas fa-plus"></i> New</div>
                                <div class="nav-item-phone"><i class="fas fa-tasks"></i> Tasks</div>
                                <div class="nav-item-phone"><i class="fas fa-ellipsis-h"></i> More</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> </main>
    </div>

    <script src="script.js"></script>
    <script>
    // Fungsi untuk mengubah tipe input 'extra' dan labelnya
    function updateExtraField() {
        const select = document.getElementById('type-select');
        const input = document.getElementById('extra-input');
        const label = document.getElementById('extra-label');

        if (select.value === "regular") {
            input.type = "date";
            input.placeholder = "Deadline (YYYY-MM-DD)";
            label.textContent = "Deadline:";
            input.required = true;
        } else { // urgent
            input.type = "text";
            input.placeholder = "Prioritas (High/Medium/Low)";
            label.textContent = "Prioritas:";
            input.required = true;
        }
    }

    // Panggil saat halaman dimuat untuk inisialisasi awal form
    document.addEventListener('DOMContentLoaded', updateExtraField);
    </script>
</body>
</html>