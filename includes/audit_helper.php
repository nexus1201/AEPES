<?php

function logAudit(PDO $conn, int $ipcrf_id, string $action, string $remarks = null)
{
    if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
        return;
    }

    $stmt = $conn->prepare("
        INSERT INTO audit_trail
            (ipcrf_id, actor_id, actor_role, action, remarks)
        VALUES
            (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $ipcrf_id,
        $_SESSION['user_id'],
        $_SESSION['role'],
        $action,
        $remarks
    ]);
}
