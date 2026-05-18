<?php

function fetchRecentActivities(PDO $conn, string $role, int $userId = 0, int $limit = 5): array
{
    $limit = max(1, $limit);

    $baseSql = "
        SELECT
            a.audit_id,
            a.action,
            a.actor_role,
            a.remarks,
            a.created_at,
            i.ipcrf_id,
            i.evaluation_period,
            u.full_name AS employee_name,
            actor.full_name AS actor_name
        FROM audit_trail a
        JOIN ipcrf i ON a.ipcrf_id = i.ipcrf_id
        JOIN users u ON i.user_id = u.user_id
        LEFT JOIN users actor ON a.actor_id = actor.user_id
    ";

    $conditions = [];
    $params = [];

    switch ($role) {
        case 'Employee':
            $conditions[] = "i.user_id = ?";
            $params[] = $userId;
            break;
        case 'Supervisor':
            $conditions[] = "a.actor_role = 'Supervisor'";
            break;
        case 'Auditor':
            $conditions[] = "a.actor_role = 'Auditor'";
            break;
        case 'HR':
            // HR sees recent actions across the workflow.
            break;
        default:
            return [];
    }

    if ($conditions) {
        $baseSql .= " WHERE " . implode(" AND ", $conditions);
    }

    $baseSql .= " ORDER BY a.created_at DESC LIMIT $limit";

    $stmt = $conn->prepare($baseSql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
