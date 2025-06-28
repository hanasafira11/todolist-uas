// script.js

document.addEventListener('DOMContentLoaded', () => {
    const toggleSidebarBtn = document.querySelector('.toggle-sidebar');
    const sidebar = document.querySelector('.sidebar');
    const agencyLogoText = document.querySelector('.agency-logo span');
    const navSectionTitles = document.querySelectorAll('.nav-section h4');
    const navItems = document.querySelectorAll('.nav-item');
    const subProjectLists = document.querySelectorAll('.sub-projects');
    const invitePeople = document.querySelector('.invite-people');
    const userBadges = document.querySelectorAll('.nav-item .badge'); // Select all badges

    if (toggleSidebarBtn && sidebar) {
        toggleSidebarBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');

            // Toggle visibility of text elements
            const isCollapsed = sidebar.classList.contains('collapsed');

            if (isCollapsed) {
                agencyLogoText.style.display = 'none';
                navSectionTitles.forEach(title => {
                    title.querySelector('.add-icon').style.display = 'none';
                    title.childNodes[0].nodeValue = ''; // Clear text content
                });
                navItems.forEach(item => {
                    // Hide text, keep icon
                    item.childNodes[1].nodeValue = ''; // Clear text content after icon
                });
                subProjectLists.forEach(list => list.style.display = 'none');
                invitePeople.style.display = 'none';
                userBadges.forEach(badge => badge.style.display = 'none'); // Hide badges
            } else {
                agencyLogoText.style.display = 'inline';
                navSectionTitles.forEach(title => {
                    title.querySelector('.add-icon').style.display = 'inline';
                    // Restore text content (you might need to store original text or hardcode)
                    if (title.innerHTML.includes('Teams')) {
                         title.innerHTML = '<i class="fas fa-users"></i> Teams <span class="add-icon">+</span>';
                    } else if (title.innerHTML.includes('Projects')) {
                         title.innerHTML = '<i class="fas fa-project-diagram"></i> Projects <span class="add-icon">+</span>';
                    }
                });
                navItems.forEach(item => {
                    // Restore text content (this is a simplified example)
                    if (item.classList.contains('active')) {
                        item.innerHTML = '<i class="fas fa-grip-horizontal"></i> My work';
                    } else if (item.innerHTML.includes('Schedule')) {
                        item.innerHTML = '<i class="fas fa-calendar-alt"></i> Schedule';
                    } else if (item.innerHTML.includes('Messages')) {
                        item.innerHTML = '<i class="fas fa-envelope"></i> Messages <span class="badge">12</span>';
                    } else if (item.innerHTML.includes('Tasks')) {
                        item.innerHTML = '<i class="fas fa-clipboard-list"></i> Tasks';
                    } else if (item.innerHTML.includes('History')) {
                        item.innerHTML = '<i class="fas fa-history"></i> History';
                    } else if (item.innerHTML.includes('Reports')) {
                        item.innerHTML = '<i class="fas fa-file-alt"></i> Reports';
                    }
                });
                subProjectLists.forEach(list => list.style.display = 'block'); // Or 'flex', 'grid'
                invitePeople.style.display = 'block'; // Or 'flex'
                userBadges.forEach(badge => badge.style.display = 'inline-block'); // Show badges
            }
        });
    }
});