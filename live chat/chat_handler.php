<?php
header('Content-Type: application/json');

// Function to generate random chat ID
function generateChatId($phone) {
    return 'chat_' . md5($phone) . '.json';
}

// Function to find existing chat by phone number
function findChatByPhone($phone) {
    $chatId = generateChatId($phone);
    if (file_exists($chatId)) {
        return $chatId;
    }
    return false;
}

// Handle different actions
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'check_chat':
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        $existingChat = findChatByPhone($phone);
        if ($existingChat) {
            echo json_encode(['exists' => true, 'chat_id' => $existingChat]);
        } else {
            echo json_encode(['exists' => false]);
        }
        break;
        
    case 'send_message':
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $message = $_POST['message'] ?? '';
        
        $chatId = generateChatId($phone);
        $isFirstMessage = !file_exists($chatId);
        
        if ($isFirstMessage) {
            // Create new chat file
            $data = [
                'user_name' => $name,
                'user_phone' => $phone,
                'messages' => []
            ];
        } else {
            // Load existing chat
            $data = json_decode(file_get_contents($chatId), true);
        }
        
        // Add new message
        $data['messages'][] = [
            'sender' => 'user',
            'text' => $message,
            'timestamp' => time()
        ];
        
        // Save chat
        file_put_contents($chatId, json_encode($data));
        
        echo json_encode([
            'success' => true,
            'is_first_message' => $isFirstMessage
        ]);
        break;
        
    case 'load_chat':
        $chatId = $_POST['chat_id'] ?? '';
        if (file_exists($chatId)) {
            $data = json_decode(file_get_contents($chatId), true);
            echo json_encode(['messages' => $data['messages'] ?? []]);
        } else {
            echo json_encode(['error' => 'Chat not found']);
        }
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>