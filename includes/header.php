<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once __DIR__ . "/activity_feed_helper.php";

/*
|--------------------------------------------------------------------------
| Dynamic title + navigation control
|--------------------------------------------------------------------------
| $title        = Page title (optional)
| $showBack     = true/false (optional)
| $dashboardUrl = where "Back to Dashboard" should go
*/

$title = $title ?? 'Dashboard';
$showBack = $showBack ?? false;

$dashboardUrl = '../dashboard.php';
$isEmployee = false;
$isSupervisor = false;
$isAuditor = false;
$isHr = false;
$quickAccessLinks = [];
$recentActivities = [];
$employeeIpcrfOpen = false;
$employeeNotificationUrl = '../employee/ipcrf_form.php';
$employeeNotificationTitle = 'IPCRF Form Open';
$employeeNotificationMessage = 'HR has opened the IPCRF form. You can now submit or update your objectives.';
$supervisorPendingCount = 0;
$supervisorNotificationUrl = '../head/pending_submissions.php';
$supervisorNotificationTitle = 'Pending Employee Evaluations';
$supervisorNotificationMessage = 'Employees have submitted IPCRFs that need your evaluation.';
$auditorPendingCount = 0;
$auditorNotificationUrl = '../auditor/dashboard.php';
$auditorNotificationTitle = 'Pending IPCRF Audits';
$auditorNotificationMessage = 'Department heads have forwarded IPCRFs that are ready for audit.';
$hrPendingCount = 0;
$hrNotificationUrl = '../hr/pending_hr.php';
$hrNotificationTitle = 'Pending IPCRF Certifications';
$hrNotificationMessage = 'There are IPCRFs waiting for HR certification.';

if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'Employee':
            $isEmployee = true;
            $dashboardUrl = '../employee/dashboard.php';
            $quickAccessLinks = [
                ['label' => 'My Dashboard', 'href' => '../employee/dashboard.php', 'description' => 'Go to employee home'],
                ['label' => 'Open IPCRF', 'href' => '../employee/ipcrf_form.php', 'description' => 'Open or update your IPCRF'],
                ['label' => 'IPCRF Status', 'href' => '../employee/ipcrf_status.php', 'description' => 'Track your IPCRF progress'],
                ['label' => 'Performance History', 'href' => '../employee/performance_history.php', 'description' => 'View previous records'],
            ];

            if (isset($conn) && isset($_SESSION['user_id'])) {
                $ipcrfOpenStmt = $conn->prepare("
                    SELECT status
                    FROM ipcrf
                    WHERE user_id = ?
                    ORDER BY ipcrf_id DESC
                    LIMIT 1
                ");
                $ipcrfOpenStmt->execute([(int) $_SESSION['user_id']]);
                $employeeIpcrfOpen = ($ipcrfOpenStmt->fetchColumn() === 'Open');
                $recentActivities = fetchRecentActivities($conn, 'Employee', (int) $_SESSION['user_id'], 5);
            }
            break;
        case 'Supervisor':
            $isSupervisor = true;
            $dashboardUrl = '../head/dashboard.php';
            $quickAccessLinks = [
                ['label' => 'Head Dashboard', 'href' => '../head/dashboard.php', 'description' => 'Go to department head home'],
                ['label' => 'Pending Submissions', 'href' => '../head/pending_submissions.php', 'description' => 'Review submitted IPCRFs'],
                ['label' => 'Rate Employee', 'href' => '../head/employees.php', 'description' => 'Open employee evaluation list'],
                ['label' => 'Performance Summary', 'href' => '../head/performance_summary.php', 'description' => 'View department summary'],
                ['label' => 'Employee Performance', 'href' => '../head/employee_performance_summary.php', 'description' => 'View all employee ratings'],
            ];

            if (isset($conn)) {
                $supervisorPendingStmt = $conn->prepare("
                    SELECT COUNT(*)
                    FROM ipcrf
                    WHERE
                        core_status IN ('Pending', 'Returned')
                        OR strategic_status IN ('Pending', 'Returned')
                        OR support_status IN ('Pending', 'Returned')
                ");
                $supervisorPendingStmt->execute();
                $supervisorPendingCount = (int) $supervisorPendingStmt->fetchColumn();
                $recentActivities = fetchRecentActivities($conn, 'Supervisor', (int) ($_SESSION['user_id'] ?? 0), 5);
            }
            break;
        case 'Auditor':
            $isAuditor = true;
            $dashboardUrl = '../auditor/dashboard.php';
            $quickAccessLinks = [
                ['label' => 'Auditor Dashboard', 'href' => '../auditor/dashboard.php', 'description' => 'Go to auditor home'],
                ['label' => 'Audit Queue', 'href' => '../auditor/dashboard.php', 'description' => 'Review IPCRFs for audit'],
                ['label' => 'Compliance Reports', 'href' => '../auditor/compliance_reports.php', 'description' => 'View audit compliance reports'],
                ['label' => 'Audit Logs', 'href' => '../auditor/audit_logs.php', 'description' => 'View recent audit actions'],
            ];

            if (isset($conn)) {
                $auditorPendingStmt = $conn->prepare("
                    SELECT COUNT(*)
                    FROM ipcrf
                    WHERE status = 'For Audit'
                ");
                $auditorPendingStmt->execute();
                $auditorPendingCount = (int) $auditorPendingStmt->fetchColumn();
                $recentActivities = fetchRecentActivities($conn, 'Auditor', (int) ($_SESSION['user_id'] ?? 0), 5);
            }
            break;
        case 'HR':
            $isHr = true;
            $dashboardUrl = '../hr/dashboard.php';
            $quickAccessLinks = [
                ['label' => 'HR Dashboard', 'href' => '../hr/dashboard.php', 'description' => 'Go to HR home'],
                ['label' => 'Pending HR Approvals', 'href' => '../hr/pending_hr.php', 'description' => 'Review pending certifications'],
                ['label' => 'Manage IPCRF', 'href' => '../hr/manage_ipcrf.php', 'description' => 'Open or close the IPCRF period'],
                ['label' => 'Certified IPCRFs', 'href' => '../hr/certified_list.php', 'description' => 'View certified outputs'],
                ['label' => 'Overall Ranking', 'href' => '../hr/overall_ranking.php', 'description' => 'View certified rankings'],
                ['label' => 'Activity Log', 'href' => '../hr/audit_employees.php', 'description' => 'View employee activity logs'],
                ['label' => 'Create User', 'href' => '../hr/create_employee.php', 'description' => 'Create new system users'],
            ];

            if (isset($conn)) {
                $hrPendingStmt = $conn->prepare("
                    SELECT COUNT(*)
                    FROM ipcrf
                    WHERE status = 'For HR'
                ");
                $hrPendingStmt->execute();
                $hrPendingCount = (int) $hrPendingStmt->fetchColumn();
                $recentActivities = fetchRecentActivities($conn, 'HR', (int) ($_SESSION['user_id'] ?? 0), 5);
            }
            break;
    }
}
?>

<style>
    .aepes-header {
        width: 100%;
        background: #0b4dbb;
        color: #fff;
        padding: 16px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-sizing: border-box;
    }

    .aepes-header .logo {
        height: 40px;
        margin-right: 12px;
    }

    .aepes-header h1 {
        margin: 0;
        font-size: 22px;
        font-weight: bold;
        display: flex;
        align-items: center;
    }

    .aepes-nav-btn {
        background: #ffd500;
        color: #000;
        padding: 10px 18px;
        border-radius: 6px;
        font-weight: bold;
        text-decoration: none;
    }

    .aepes-nav-btn:hover {
        background: #e6c200;
    }

    .aepes-right-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .aepes-utility-wrap {
        position: relative;
    }

    .aepes-utility-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 46px;
        padding: 0 14px;
        border-radius: 999px;
        border: 0;
        background: #ffffff;
        color: #0b4dbb;
        font-weight: bold;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
    }

    .aepes-utility-btn:hover {
        background: #f3f7ff;
    }

    .aepes-notification-bell {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: #ffffff;
        color: #0b4dbb;
        text-decoration: none;
        font-size: 22px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
        border: 0;
        cursor: pointer;
    }

    .aepes-notification-bell:hover {
        background: #f3f7ff;
    }

    .aepes-notification-bell.is-active {
        color: #d90429;
        box-shadow: 0 4px 14px rgba(217, 4, 41, 0.28);
    }

    .aepes-notification-badge {
        position: absolute;
        top: 7px;
        right: 7px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #ff3b30;
        border: 2px solid #fff;
    }

    .aepes-notification-wrap {
        position: relative;
    }

    .aepes-notification-panel {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        width: 320px;
        background: #fff;
        color: #1d2129;
        border-radius: 14px;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
        overflow: hidden;
        display: none;
        z-index: 1000;
    }

    .aepes-notification-panel.is-open {
        display: block;
    }

    .aepes-notification-panel::before {
        content: '';
        position: absolute;
        top: -8px;
        right: 18px;
        width: 16px;
        height: 16px;
        background: #fff;
        transform: rotate(45deg);
    }

    .aepes-notification-head {
        position: relative;
        padding: 14px 16px 10px;
        font-size: 16px;
        font-weight: 700;
        border-bottom: 1px solid #e8edf5;
    }

    .aepes-notification-body {
        position: relative;
        padding: 12px;
    }

    .aepes-notification-item {
        display: block;
        text-decoration: none;
        color: inherit;
        background: #f6f9ff;
        border: 1px solid #dbe6ff;
        border-radius: 12px;
        padding: 14px;
        transition: background .2s ease, transform .2s ease;
    }

    .aepes-notification-item:hover {
        background: #edf4ff;
        transform: translateY(-1px);
    }

    .aepes-notification-item.is-empty {
        background: #fff;
        border-style: dashed;
        color: #5f6b7a;
    }

    .aepes-notification-item-title {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
        font-weight: 700;
        color: #0b4dbb;
    }

    .aepes-notification-item-text {
        margin: 0;
        font-size: 14px;
        line-height: 1.45;
    }

    .aepes-notification-item-cta {
        display: inline-block;
        margin-top: 10px;
        font-size: 13px;
        font-weight: 700;
        color: #d90429;
    }

    .aepes-quick-panel {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        width: 360px;
        background: #fff;
        color: #1d2129;
        border-radius: 14px;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
        overflow: hidden;
        display: none;
        z-index: 1000;
    }

    .aepes-quick-panel.is-open {
        display: block;
    }

    .aepes-quick-panel::before {
        content: '';
        position: absolute;
        top: -8px;
        right: 22px;
        width: 16px;
        height: 16px;
        background: #fff;
        transform: rotate(45deg);
    }

    .aepes-quick-head {
        position: relative;
        padding: 14px 16px 10px;
        border-bottom: 1px solid #e8edf5;
    }

    .aepes-quick-head strong {
        display: block;
        margin-bottom: 10px;
        font-size: 16px;
    }

    .aepes-quick-search {
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #c8d5eb;
        border-radius: 10px;
        padding: 11px 12px;
        font-size: 14px;
        outline: none;
    }

    .aepes-quick-search:focus {
        border-color: #0b4dbb;
        box-shadow: 0 0 0 3px rgba(11, 77, 187, 0.12);
    }

    .aepes-quick-list {
        max-height: 320px;
        overflow-y: auto;
        padding: 10px;
    }

    .aepes-quick-link {
        display: block;
        padding: 12px 14px;
        border-radius: 10px;
        text-decoration: none;
        color: inherit;
        background: #f6f9ff;
        border: 1px solid #dbe6ff;
        margin-bottom: 10px;
    }

    .aepes-quick-link:hover {
        background: #edf4ff;
    }

    .aepes-quick-link:last-child {
        margin-bottom: 0;
    }

    .aepes-quick-link-title {
        display: block;
        color: #0b4dbb;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .aepes-quick-link-desc {
        display: block;
        color: #556070;
        font-size: 13px;
        line-height: 1.4;
    }

    .aepes-quick-empty {
        display: none;
        padding: 12px 14px;
        color: #5f6b7a;
        font-size: 14px;
    }

    .aepes-activity-panel {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        width: 360px;
        background: #fff;
        color: #1d2129;
        border-radius: 14px;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.22);
        overflow: hidden;
        display: none;
        z-index: 1000;
    }

    .aepes-activity-panel.is-open {
        display: block;
    }

    .aepes-activity-panel::before {
        content: '';
        position: absolute;
        top: -8px;
        right: 22px;
        width: 16px;
        height: 16px;
        background: #fff;
        transform: rotate(45deg);
    }

    .aepes-activity-head {
        position: relative;
        padding: 14px 16px 10px;
        border-bottom: 1px solid #e8edf5;
    }

    .aepes-activity-head strong {
        display: block;
        font-size: 16px;
    }

    .aepes-activity-list {
        max-height: 320px;
        overflow-y: auto;
        padding: 10px;
    }

    .aepes-activity-entry {
        padding: 12px 14px;
        background: #f6f9ff;
        border: 1px solid #dbe6ff;
        border-radius: 10px;
        margin-bottom: 10px;
    }

    .aepes-activity-entry:last-child {
        margin-bottom: 0;
    }

    .aepes-activity-entry p {
        margin: 0 0 6px;
    }

    .aepes-activity-meta {
        color: #666;
        font-size: 13px;
        line-height: 1.45;
    }

    .aepes-activity-empty {
        padding: 12px 14px;
        color: #5f6b7a;
        font-size: 14px;
    }
</style>

<div class="aepes-header">
    <div class="left-group" style="display:flex; align-items:center;">
        <img class="logo" src="/aepes/assets/logo.png" alt="AEPES logo">
        <h1>AEPES - <?= htmlspecialchars($title) ?></h1>
    </div>

    <div class="right-group aepes-right-actions">
        <img class="logo" src="/aepes/assets/Mandaluyong_logo.png" alt="Mandaluyong logo" style="margin-right:12px;">
        <?php if (!empty($quickAccessLinks)): ?>
            <div class="aepes-utility-wrap">
                <button
                    type="button"
                    class="aepes-utility-btn"
                    aria-expanded="false"
                    aria-controls="headerQuickAccessPanel"
                    data-quick-toggle
                >
                    <span aria-hidden="true">&#128269;</span>
                    <span>Quick Access</span>
                </button>

                <div class="aepes-quick-panel" id="headerQuickAccessPanel" data-quick-panel>
                    <div class="aepes-quick-head">
                        <strong>Search / Quick Access</strong>
                        <input
                            type="text"
                            class="aepes-quick-search"
                            placeholder="Search pages or actions"
                            data-quick-search
                        >
                    </div>

                    <div class="aepes-quick-list" data-quick-list>
                        <?php foreach ($quickAccessLinks as $link): ?>
                            <a
                                class="aepes-quick-link"
                                href="<?= htmlspecialchars($link['href']) ?>"
                                data-quick-item
                                data-search="<?= htmlspecialchars(strtolower($link['label'] . ' ' . $link['description'])) ?>"
                            >
                                <span class="aepes-quick-link-title"><?= htmlspecialchars($link['label']) ?></span>
                                <span class="aepes-quick-link-desc"><?= htmlspecialchars($link['description']) ?></span>
                            </a>
                        <?php endforeach; ?>
                        <div class="aepes-quick-empty" data-quick-empty>No matching shortcuts found.</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($isEmployee || $isSupervisor || $isAuditor || $isHr): ?>
            <div class="aepes-utility-wrap">
                <button
                    type="button"
                    class="aepes-utility-btn"
                    aria-expanded="false"
                    aria-controls="headerRecentActivityPanel"
                    data-activity-toggle
                >
                    <span aria-hidden="true">&#128337;</span>
                    <span>Recent Activity</span>
                </button>

                <div class="aepes-activity-panel" id="headerRecentActivityPanel" data-activity-panel>
                    <div class="aepes-activity-head">
                        <strong>Recent Activity</strong>
                    </div>

                    <div class="aepes-activity-list">
                        <?php if (empty($recentActivities)): ?>
                            <div class="aepes-activity-empty">No recent activity yet.</div>
                        <?php else: ?>
                            <?php foreach ($recentActivities as $activity): ?>
                                <div class="aepes-activity-entry">
                                    <p>
                                        <strong><?= htmlspecialchars($activity['action']) ?></strong><br>
                                        <?php if ($isEmployee): ?>
                                            <?= htmlspecialchars($activity['actor_name'] ?? $activity['actor_role']) ?>
                                            (<?= htmlspecialchars($activity['actor_role']) ?>)
                                        <?php else: ?>
                                            <?= htmlspecialchars($activity['employee_name']) ?>
                                        <?php endif; ?>
                                    </p>
                                    <div class="aepes-activity-meta">
                                        Period: <?= htmlspecialchars($activity['evaluation_period']) ?><br>
                                        <?php if (!$isEmployee): ?>
                                            Actor: <?= htmlspecialchars($activity['actor_name'] ?? $activity['actor_role']) ?>
                                            (<?= htmlspecialchars($activity['actor_role']) ?>)<br>
                                        <?php endif; ?>
                                        <?= date("F d, Y h:i A", strtotime($activity['created_at'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($isEmployee || $isSupervisor || $isAuditor || $isHr): ?>
            <div class="aepes-notification-wrap">
                <button
                    type="button"
                    class="aepes-notification-bell<?= ($employeeIpcrfOpen || $supervisorPendingCount > 0 || $auditorPendingCount > 0 || $hrPendingCount > 0) ? ' is-active' : '' ?>"
                    title="Notifications"
                    aria-label="Notifications"
                    aria-expanded="false"
                    aria-controls="headerNotificationPanel"
                    data-notification-toggle
                >
                    <span aria-hidden="true">&#128276;</span>
                    <?php if ($employeeIpcrfOpen || $supervisorPendingCount > 0 || $auditorPendingCount > 0 || $hrPendingCount > 0): ?>
                        <span class="aepes-notification-badge"></span>
                    <?php endif; ?>
                </button>

                <div class="aepes-notification-panel" id="headerNotificationPanel" data-notification-panel>
                    <div class="aepes-notification-head">Notifications</div>
                    <div class="aepes-notification-body">
                        <?php if ($employeeIpcrfOpen): ?>
                            <a class="aepes-notification-item" href="<?= $employeeNotificationUrl ?>">
                                <div class="aepes-notification-item-title">
                                    <span aria-hidden="true">&#128276;</span>
                                    <span><?= htmlspecialchars($employeeNotificationTitle) ?></span>
                                </div>
                                <p class="aepes-notification-item-text"><?= htmlspecialchars($employeeNotificationMessage) ?></p>
                                <span class="aepes-notification-item-cta">Open IPCRF</span>
                            </a>
                        <?php elseif ($isSupervisor && $supervisorPendingCount > 0): ?>
                            <a class="aepes-notification-item" href="<?= $supervisorNotificationUrl ?>">
                                <div class="aepes-notification-item-title">
                                    <span aria-hidden="true">&#128276;</span>
                                    <span><?= htmlspecialchars($supervisorNotificationTitle) ?></span>
                                </div>
                                <p class="aepes-notification-item-text">
                                    <?= htmlspecialchars($supervisorNotificationMessage) ?>
                                    <?= $supervisorPendingCount ?> pending <?= $supervisorPendingCount === 1 ? 'IPCRF needs' : 'IPCRFs need' ?> review.
                                </p>
                                <span class="aepes-notification-item-cta">Review Pending Submissions</span>
                            </a>
                        <?php elseif ($isAuditor && $auditorPendingCount > 0): ?>
                            <a class="aepes-notification-item" href="<?= $auditorNotificationUrl ?>">
                                <div class="aepes-notification-item-title">
                                    <span aria-hidden="true">&#128276;</span>
                                    <span><?= htmlspecialchars($auditorNotificationTitle) ?></span>
                                </div>
                                <p class="aepes-notification-item-text">
                                    <?= htmlspecialchars($auditorNotificationMessage) ?>
                                    <?= $auditorPendingCount ?> pending <?= $auditorPendingCount === 1 ? 'IPCRF needs' : 'IPCRFs need' ?> audit.
                                </p>
                                <span class="aepes-notification-item-cta">Review Audit Queue</span>
                            </a>
                        <?php elseif ($isHr && $hrPendingCount > 0): ?>
                            <a class="aepes-notification-item" href="<?= $hrNotificationUrl ?>">
                                <div class="aepes-notification-item-title">
                                    <span aria-hidden="true">&#128276;</span>
                                    <span><?= htmlspecialchars($hrNotificationTitle) ?></span>
                                </div>
                                <p class="aepes-notification-item-text">
                                    <?= htmlspecialchars($hrNotificationMessage) ?>
                                    <?= $hrPendingCount ?> pending <?= $hrPendingCount === 1 ? 'record needs' : 'records need' ?> review.
                                </p>
                                <span class="aepes-notification-item-cta">Review Pending IPCRFs</span>
                            </a>
                        <?php else: ?>
                            <div class="aepes-notification-item is-empty">
                                <div class="aepes-notification-item-title">
                                    <span aria-hidden="true">&#10003;</span>
                                    <span>No new notifications</span>
                                </div>
                                <p class="aepes-notification-item-text">You are all caught up for now.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($showBack): ?>
            <a class="aepes-nav-btn" href="<?= $dashboardUrl ?>">&larr; Back to Dashboard</a>
        <?php else: ?>
            <a class="aepes-nav-btn" href="../logout.php">Logout</a>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($quickAccessLinks) || $isEmployee || $isSupervisor || $isAuditor || $isHr): ?>
<script>
    (function () {
        var quickToggle = document.querySelector('[data-quick-toggle]');
        var quickPanel = document.querySelector('[data-quick-panel]');
        var quickSearch = document.querySelector('[data-quick-search]');
        var quickItems = Array.prototype.slice.call(document.querySelectorAll('[data-quick-item]'));
        var quickEmpty = document.querySelector('[data-quick-empty]');
        var activityToggle = document.querySelector('[data-activity-toggle]');
        var activityPanel = document.querySelector('[data-activity-panel]');
        var toggle = document.querySelector('[data-notification-toggle]');
        var panel = document.querySelector('[data-notification-panel]');

        function closeQuickPanel() {
            if (!quickPanel || !quickToggle) {
                return;
            }
            quickPanel.classList.remove('is-open');
            quickToggle.setAttribute('aria-expanded', 'false');
        }

        function closePanel() {
            if (!panel || !toggle) {
                return;
            }
            panel.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        }

        function closeActivityPanel() {
            if (!activityPanel || !activityToggle) {
                return;
            }
            activityPanel.classList.remove('is-open');
            activityToggle.setAttribute('aria-expanded', 'false');
        }

        function filterQuickAccess() {
            if (!quickSearch || !quickItems.length) {
                return;
            }

            var query = quickSearch.value.toLowerCase().trim();
            var visibleCount = 0;

            quickItems.forEach(function (item) {
                var haystack = item.getAttribute('data-search') || '';
                var matches = haystack.indexOf(query) !== -1;
                item.style.display = matches ? 'block' : 'none';
                if (matches) {
                    visibleCount += 1;
                }
            });

            if (quickEmpty) {
                quickEmpty.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }

        if (quickToggle && quickPanel) {
            quickToggle.addEventListener('click', function (event) {
                event.stopPropagation();
                closePanel();
                closeActivityPanel();
                var isOpen = quickPanel.classList.toggle('is-open');
                quickToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                if (isOpen && quickSearch) {
                    quickSearch.focus();
                    filterQuickAccess();
                }
            });

            quickPanel.addEventListener('click', function (event) {
                event.stopPropagation();
            });
        }

        if (quickSearch) {
            quickSearch.addEventListener('input', filterQuickAccess);
        }

        if (activityToggle && activityPanel) {
            activityToggle.addEventListener('click', function (event) {
                event.stopPropagation();
                closePanel();
                closeQuickPanel();
                var isOpen = activityPanel.classList.toggle('is-open');
                activityToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            activityPanel.addEventListener('click', function (event) {
                event.stopPropagation();
            });
        }

        if (toggle && panel) {
            toggle.addEventListener('click', function (event) {
                event.stopPropagation();
                closeQuickPanel();
                closeActivityPanel();
                var isOpen = panel.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            panel.addEventListener('click', function (event) {
                event.stopPropagation();
            });
        }

        document.addEventListener('click', function () {
            closePanel();
            closeQuickPanel();
            closeActivityPanel();
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closePanel();
                closeQuickPanel();
                closeActivityPanel();
            }
        });
    })();
</script>
<?php endif; ?>
