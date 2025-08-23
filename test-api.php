<?php

// Test API endpoints

$baseUrl = 'http://127.0.0.1:8000/api';

// Test credentials
$adminEmail = 'admin@mdagang.com';
$adminPassword = 'admin123';

// Colors for output
$green = "\033[32m";
$red = "\033[31m";
$yellow = "\033[33m";
$reset = "\033[0m";

echo "\n{$yellow}=== MDagang API Test ==={$reset}\n\n";

// 1. Test Health Check
echo "1. Testing Health Check... ";
$response = file_get_contents($baseUrl . '/health');
$data = json_decode($response, true);
if ($data['status'] === 'ok') {
    echo "{$green}✓ PASSED{$reset}\n";
} else {
    echo "{$red}✗ FAILED{$reset}\n";
}

// 2. Test Login
echo "2. Testing Login... ";
$loginData = json_encode([
    'email' => $adminEmail,
    'password' => $adminPassword
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => $loginData
    ]
]);

$response = @file_get_contents($baseUrl . '/auth/login', false, $context);
if ($response) {
    $data = json_decode($response, true);
    if (isset($data['data']['access_token'])) {
        $token = $data['data']['access_token'];
        echo "{$green}✓ PASSED{$reset}\n";
        echo "   Token: " . substr($token, 0, 20) . "...\n";
        
        // 3. Test Authenticated Endpoint
        echo "3. Testing Authenticated User Endpoint... ";
        $authContext = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "Authorization: Bearer $token\r\n"
            ]
        ]);
        
        $response = @file_get_contents($baseUrl . '/auth/user', false, $authContext);
        if ($response) {
            $userData = json_decode($response, true);
            if (isset($userData['data']['email'])) {
                echo "{$green}✓ PASSED{$reset}\n";
                echo "   User: {$userData['data']['name']} ({$userData['data']['role']})\n";
            } else {
                echo "{$red}✗ FAILED{$reset}\n";
            }
        } else {
            echo "{$red}✗ FAILED{$reset}\n";
        }
        
        // 4. Test Dashboard Stats
        echo "4. Testing Dashboard Stats... ";
        $response = @file_get_contents($baseUrl . '/dashboard/stats', false, $authContext);
        if ($response) {
            $statsData = json_decode($response, true);
            if (isset($statsData['success']) && $statsData['success']) {
                echo "{$green}✓ PASSED{$reset}\n";
            } else {
                echo "{$red}✗ FAILED{$reset}\n";
            }
        } else {
            echo "{$red}✗ FAILED{$reset}\n";
        }
        
        // 5. Test Categories Endpoint
        echo "5. Testing Categories List... ";
        $response = @file_get_contents($baseUrl . '/categories', false, $authContext);
        if ($response) {
            $categoriesData = json_decode($response, true);
            if (isset($categoriesData['success']) && $categoriesData['success']) {
                echo "{$green}✓ PASSED{$reset}\n";
            } else {
                echo "{$red}✗ FAILED{$reset}\n";
            }
        } else {
            echo "{$red}✗ FAILED{$reset}\n";
        }
        
        // 6. Test Customers Endpoint
        echo "6. Testing Customers List... ";
        $response = @file_get_contents($baseUrl . '/sales/customers', false, $authContext);
        if ($response) {
            $customersData = json_decode($response, true);
            if (isset($customersData['success']) && $customersData['success']) {
                echo "{$green}✓ PASSED{$reset}\n";
            } else {
                echo "{$red}✗ FAILED{$reset}\n";
            }
        } else {
            echo "{$red}✗ FAILED{$reset}\n";
        }
        
    } else {
        echo "{$red}✗ FAILED - No token received{$reset}\n";
    }
} else {
    echo "{$red}✗ FAILED - Could not connect{$reset}\n";
}

echo "\n{$yellow}=== Test Complete ==={$reset}\n\n";