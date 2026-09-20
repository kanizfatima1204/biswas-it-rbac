<?php

function testLoginAndRoutes($email, $password, $roleName) {
    echo "\n--------------------------------------------------\n";
    echo "Testing User: $email ($roleName)\n";
    echo "--------------------------------------------------\n";

    // 1. GET /login with headers
    $ch = curl_init('http://127.0.0.1:8000/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $rawResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($rawResponse, 0, $headerSize);
    curl_close($ch);

    // Extract XSRF-TOKEN and biswas_it_firm_rbac_session
    preg_match('/Set-Cookie:\s*XSRF-TOKEN=([^;]+)/', $headers, $xsrfMatch);
    preg_match('/Set-Cookie:\s*biswas_it_firm_rbac_session=([^;]+)/', $headers, $sessMatch);

    $rawXsrf = $xsrfMatch[1] ?? '';
    $xsrfToken = urldecode($rawXsrf);
    $session = $sessMatch[1] ?? '';

    echo "1. GET /login => HTTP $httpCode (Session: " . substr($session, 0, 15) . "...)\n";

    $cookieHeader = "Cookie: XSRF-TOKEN=$rawXsrf; biswas_it_firm_rbac_session=$session";

    // 2. POST /login with credentials
    $ch = curl_init('http://127.0.0.1:8000/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'email' => $email,
        'password' => $password,
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        $cookieHeader,
        'X-XSRF-TOKEN: ' . $xsrfToken,
        'Accept: text/html, application/xhtml+xml',
    ]);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    $postRaw = curl_exec($ch);
    $loginCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $postHeaderSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $postHeaders = substr($postRaw, 0, $postHeaderSize);
    $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);

    // Capture updated session after authentication
    if (preg_match('/Set-Cookie:\s*biswas_it_firm_rbac_session=([^;]+)/', $postHeaders, $newSessMatch)) {
        $session = $newSessMatch[1];
    }
    if (preg_match('/Set-Cookie:\s*XSRF-TOKEN=([^;]+)/', $postHeaders, $newXsrfMatch)) {
        $rawXsrf = $newXsrfMatch[1];
        $xsrfToken = urldecode($rawXsrf);
    }
    $authCookieHeader = "Cookie: XSRF-TOKEN=$rawXsrf; biswas_it_firm_rbac_session=$session";

    echo "2. POST /login => HTTP $loginCode (Redirect: $redirectUrl)\n";

    // 3. GET /dashboard as standard browser request
    $ch = curl_init('http://127.0.0.1:8000/dashboard');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        $authCookieHeader,
        'Accept: text/html,application/xhtml+xml',
    ]);
    $dashRes = curl_exec($ch);
    $dashCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $component = 'Unknown';
    if (preg_match('/data-page="([^"]+)"/', $dashRes, $pageMatch)) {
        $pageData = json_decode(htmlspecialchars_decode($pageMatch[1]), true);
        $component = $pageData['component'] ?? 'Unknown';
    }
    echo "3. GET /dashboard => HTTP $dashCode | Component: $component\n";

    // 4. GET /admin-only
    $ch = curl_init('http://127.0.0.1:8000/admin-only');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        $authCookieHeader,
        'Accept: application/json',
    ]);
    $adminRes = curl_exec($ch);
    $adminCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "4. GET /admin-only => HTTP $adminCode (Response: $adminRes)\n";

    // 5. GET /team-only
    $ch = curl_init('http://127.0.0.1:8000/team-only');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        $authCookieHeader,
        'Accept: application/json',
    ]);
    $teamRes = curl_exec($ch);
    $teamCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "5. GET /team-only => HTTP $teamCode (Response: $teamRes)\n";

    return [
        'login' => $loginCode,
        'dashboard' => $dashCode,
        'component' => $component,
        'admin_only' => $adminCode,
        'team_only' => $teamCode,
    ];
}

echo "====================================================\n";
echo "   BISWAS IT RBAC LIVE HTTP INTEGRATION TEST        \n";
echo "====================================================\n";

$admin = testLoginAndRoutes('admin@biswasit.test', 'password', 'Admin');
$team = testLoginAndRoutes('team@biswasit.test', 'password', 'Team Member');
$client = testLoginAndRoutes('client@biswasit.test', 'password', 'Client');

echo "\n====================================================\n";
echo "                   TEST RESULTS                     \n";
echo "====================================================\n";

$success = true;

// Check Admin
if ($admin['dashboard'] === 200 && $admin['component'] === 'Dashboard/Admin' && $admin['admin_only'] === 200 && $admin['team_only'] === 200) {
    echo "[PASS] Admin: Correct component (Dashboard/Admin), full access to admin (200) and team (200) routes.\n";
} else {
    echo "[FAIL] Admin check failed!\n";
    $success = false;
}

// Check Team
if ($team['dashboard'] === 200 && $team['component'] === 'Dashboard/TeamMember' && $team['admin_only'] === 403 && $team['team_only'] === 200) {
    echo "[PASS] Team Member: Correct component (Dashboard/TeamMember), blocked from admin (403), access to team (200).\n";
} else {
    echo "[FAIL] Team Member check failed!\n";
    $success = false;
}

// Check Client
if ($client['dashboard'] === 200 && $client['component'] === 'Dashboard/Client' && $client['admin_only'] === 403 && $client['team_only'] === 403) {
    echo "[PASS] Client: Correct component (Dashboard/Client), blocked from admin (403), blocked from team (403).\n";
} else {
    echo "[FAIL] Client check failed!\n";
    $success = false;
}

if ($success) {
    echo "\n>>> ALL 3 ROLES + ROUTE PROTECTIONS VERIFIED 100% OPERATIONAL! <<<\n";
    exit(0);
} else {
    echo "\n>>> VERIFICATION FAILED! <<<\n";
    exit(1);
}
