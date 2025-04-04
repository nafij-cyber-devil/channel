<?php
session_start();

// Simple authentication
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Function to get all chat files
function getAllChatFiles() {
    $chatFiles = [];
    foreach (glob("chat_*.json") as $filename) {
        $data = json_decode(file_get_contents($filename), true);
        $lastMessage = end($data['messages']);
        $chatFiles[] = [
            'file' => $filename,
            'user_name' => $data['user_name'] ?? 'Unknown',
            'user_phone' => $data['user_phone'] ?? '',
            'last_message' => $lastMessage['text'] ?? '',
            'last_time' => $lastMessage['timestamp'] ?? 0
        ];
    }
    
    // Sort by last message time (newest first)
    usort($chatFiles, function($a, $b) {
        return $b['last_time'] <=> $a['last_time'];
    });
    
    return $chatFiles;
}

// Function to get chat history
function getChatHistory($chatId) {
    if (file_exists($chatId)) {
        $data = json_decode(file_get_contents($chatId), true);
        return $data['messages'] ?? [];
    }
    return [];
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    if (isset($_POST['load_chat'])) {
        $chatId = $_POST['chat_id'];
        $messages = getChatHistory($chatId);
        $data = json_decode(file_get_contents($chatId), true);
        echo json_encode([
            'success' => true,
            'messages' => $messages,
            'user_name' => $data['user_name'] ?? 'Unknown',
            'user_phone' => $data['user_phone'] ?? ''
        ]);
        exit;
    }
    
    if (isset($_POST['send_reply'])) {
        $chatId = $_POST['chat_id'];
        $reply = $_POST['reply_message'];
        
        $data = json_decode(file_get_contents($chatId), true);
        $data['messages'][] = [
            'sender' => 'admin',
            'text' => $reply,
            'timestamp' => time()
        ];
        
        file_put_contents($chatId, json_encode($data));
        
        echo json_encode(['success' => true]);
        exit;
    }
    
    if (isset($_POST['check_updates'])) {
        $chatFiles = getAllChatFiles();
        echo json_encode([
            'success' => true,
            'chats' => $chatFiles,
            'current_time' => time()
        ]);
        exit;
    }
}

// Normal page load
$chatFiles = getAllChatFiles();
$currentChat = $_GET['chat'] ?? ($chatFiles[0]['file'] ?? '');
$messages = [];
$userName = '';
$userPhone = '';

if ($currentChat && file_exists($currentChat)) {
    $data = json_decode(file_get_contents($currentChat), true);
    $messages = $data['messages'] ?? [];
    $userName = $data['user_name'] ?? 'Unknown';
    $userPhone = $data['user_phone'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | TCS Support</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #27ae60;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .admin-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .admin-title {
            font-size: 20px;
            font-weight: bold;
        }
        
        .logout-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 14px;
        }
        
        .admin-container {
            flex: 1;
            display: flex;
            overflow: hidden;
        }
        
        .sidebar {
            width: 300px;
            background-color: white;
            border-right: 1px solid #ddd;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }
        
        .sidebar-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
            background-color: var(--primary-color);
            color: white;
        }
        
        .chat-list {
            list-style: none;
        }
        
        .chat-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .chat-item:hover {
            background-color: #f9f9f9;
        }
        
        .chat-item.active {
            background-color: var(--light-color);
        }
        
        .chat-user {
            font-weight: bold;
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
        }
        
        .chat-preview {
            font-size: 13px;
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .chat-time {
            font-size: 12px;
            color: #999;
        }
        
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .chat-header {
            padding: 15px;
            background-color: white;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .chat-title {
            font-size: 18px;
            font-weight: bold;
        }
        
        .chat-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background-color: #fafafa;
        }
        
        .message {
            margin-bottom: 15px;
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 18px;
            position: relative;
            animation: fadeIn 0.3s ease;
            word-wrap: break-word;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .user-message {
            background-color: var(--secondary-color);
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 5px;
        }
        
        .admin-message {
            background-color: #e3e3e3;
            margin-right: auto;
            border-bottom-left-radius: 5px;
        }
        
        .message-time {
            font-size: 10px;
            color: rgba(255,255,255,0.8);
            margin-top: 5px;
            text-align: right;
        }
        
        .admin-message .message-time {
            color: #777;
        }
        
        .chat-input {
            padding: 10px 15px;
            background-color: white;
            border-top: 1px solid #eee;
        }
        
        .input-group {
            display: flex;
            align-items: center;
        }
        
        #reply-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 30px;
            outline: none;
            font-size: 14px;
        }
        
        #send-button {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-left: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .no-chat-selected {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 20px;
            text-align: center;
        }
        
        .no-chat-icon {
            font-size: 50px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .menu-toggle {
            display: none;
            position: fixed;
            left: 10px;
            top: 60px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 100;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 60px;
                bottom: 0;
                width: 280px;
                transform: translateX(-100%);
                z-index: 90;
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .menu-toggle {
                display: block;
            }
            
            .message {
                max-width: 90%;
            }
        }
        
        @media (max-width: 480px) {
            .admin-title {
                font-size: 18px;
            }
            
            .chat-title {
                font-size: 16px;
            }
            
            .sidebar {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="admin-title">TCS Support Admin</div>
        <a href="admin_logout.php" class="logout-btn">Logout</a>
    </div>
    
    <button class="menu-toggle" id="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <div class="admin-container">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                Active Chats
            </div>
            <ul class="chat-list" id="chat-list">
                <?php foreach ($chatFiles as $chat): ?>
                    <li class="chat-item <?php echo $chat['file'] === $currentChat ? 'active' : ''; ?>" 
                        data-chat-id="<?php echo htmlspecialchars($chat['file']); ?>">
                        <div class="chat-user">
                            <span><?php echo htmlspecialchars($chat['user_name']); ?></span>
                            <span class="chat-time"><?php echo date('H:i', $chat['last_time']); ?></span>
                        </div>
                        <div class="chat-preview"><?php echo htmlspecialchars($chat['last_message']); ?></div>
                    </li>
                <?php endforeach; ?>
                
                <?php if (empty($chatFiles)): ?>
                    <li style="padding: 20px; text-align: center;">
                        No active chats found
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        
        <div class="main-content">
            <?php if ($currentChat): ?>
                <div class="chat-header">
                    <div class="chat-title">Chat with <?php echo htmlspecialchars($userName); ?> (<?php echo htmlspecialchars($userPhone); ?>)</div>
                </div>
                
                <div class="chat-messages" id="chat-messages">
                    <?php foreach ($messages as $message): ?>
                        <div class="message <?php echo $message['sender'] === 'user' ? 'user-message' : 'admin-message'; ?>">
                            <div><?php echo htmlspecialchars($message['text']); ?></div>
                            <div class="message-time">
                                <?php echo date('H:i', $message['timestamp']); ?>
                                <?php if ($message['sender'] === 'admin'): ?>
                                    <i class="fas fa-check-double" style="margin-left: 5px;"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="chat-input">
                    <div class="input-group">
                        <input type="text" id="reply-input" placeholder="Type your reply...">
                        <button id="send-button">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-chat-selected">
                    <div class="no-chat-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>No Chat Selected</h3>
                    <p>Select a chat from the sidebar to view and reply to messages</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // DOM elements
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.getElementById('menu-toggle');
        const chatList = document.getElementById('chat-list');
        const chatMessages = document.getElementById('chat-messages');
        const replyInput = document.getElementById('reply-input');
        const sendButton = document.getElementById('send-button');
        
        // Current chat state
        let currentChatId = '<?php echo $currentChat; ?>';
        let lastUpdateTime = <?php echo time(); ?>;
        let pollingInterval = null;
        
        // Toggle sidebar on mobile
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
        
        // Load chat when clicked in sidebar
        chatList.addEventListener('click', (e) => {
            const chatItem = e.target.closest('.chat-item');
            if (chatItem) {
                const chatId = chatItem.dataset.chatId;
                loadChat(chatId);
                
                // Close sidebar on mobile
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                }
            }
        });
        
        // Send reply
        function sendReply() {
            const message = replyInput.value.trim();
            if (message && currentChatId) {
                // Display admin message immediately
                displayMessage(message, 'admin-message', new Date());
                replyInput.value = '';
                
                // Send to server
                fetch('admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `ajax=1&send_reply=1&chat_id=${encodeURIComponent(currentChatId)}&reply_message=${encodeURIComponent(message)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert('Failed to send reply');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to send reply');
                });
            }
        }
        
        // Event listeners for sending
        sendButton.addEventListener('click', sendReply);
        replyInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendReply();
            }
        });
        
        // Load chat function
        function loadChat(chatId) {
            currentChatId = chatId;
            
            // Update active state in sidebar
            document.querySelectorAll('.chat-item').forEach(item => {
                item.classList.remove('active');
                if (item.dataset.chatId === chatId) {
                    item.classList.add('active');
                }
            });
            
            // Update URL without reloading
            history.pushState(null, null, `?chat=${encodeURIComponent(chatId)}`);
            
            // Load chat messages
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `ajax=1&load_chat=1&chat_id=${encodeURIComponent(chatId)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update chat header
                    document.querySelector('.chat-title').textContent = `Chat with ${data.user_name} (${data.user_phone})`;
                    
                    // Display messages
                    chatMessages.innerHTML = '';
                    data.messages.forEach(msg => {
                        const date = new Date(msg.timestamp * 1000);
                        const className = msg.sender === 'user' ? 'user-message' : 'admin-message';
                        displayMessage(msg.text, className, date);
                    });
                    
                    // Scroll to bottom
                    setTimeout(() => {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }, 100);
                } else {
                    alert('Failed to load chat');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load chat');
            });
        }
        
        // Display message in chat
        function displayMessage(text, className, date) {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message', className);
            
            const messageText = document.createElement('div');
            messageText.textContent = text;
            
            const messageTime = document.createElement('div');
            messageTime.classList.add('message-time');
            messageTime.textContent = formatTime(date);
            
            if (className === 'admin-message') {
                const checkIcon = document.createElement('i');
                checkIcon.classList.add('fas', 'fa-check-double');
                checkIcon.style.marginLeft = '5px';
                messageTime.appendChild(checkIcon);
            }
            
            messageDiv.appendChild(messageText);
            messageDiv.appendChild(messageTime);
            chatMessages.appendChild(messageDiv);
            
            // Scroll to bottom
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
        
        // Format time
        function formatTime(date) {
            if (!(date instanceof Date)) {
                return '';
            }
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
        
        // Check for updates in chat list
        function checkForUpdates() {
            fetch('admin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `ajax=1&check_updates=1&last_checked=${lastUpdateTime}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update last update time
                    lastUpdateTime = data.current_time;
                    
                    // Update chat list
                    const chatList = document.getElementById('chat-list');
                    chatList.innerHTML = '';
                    
                    if (data.chats.length === 0) {
                        chatList.innerHTML = '<li style="padding: 20px; text-align: center;">No active chats found</li>';
                    } else {
                        data.chats.forEach(chat => {
                            const chatItem = document.createElement('li');
                            chatItem.className = `chat-item ${chat.file === currentChatId ? 'active' : ''}`;
                            chatItem.dataset.chatId = chat.file;
                            chatItem.innerHTML = `
                                <div class="chat-user">
                                    <span>${chat.user_name}</span>
                                    <span class="chat-time">${new Date(chat.last_time * 1000).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                                </div>
                                <div class="chat-preview">${chat.last_message}</div>
                            `;
                            chatList.appendChild(chatItem);
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error checking updates:', error);
            });
        }
        
        // Start polling for updates
        function startPolling() {
            // Clear any existing interval
            if (pollingInterval) {
                clearInterval(pollingInterval);
            }
            
            // Check immediately
            checkForUpdates();
            
            // Then set interval
            pollingInterval = setInterval(checkForUpdates, 5000);
        }
        
        // Handle popstate (back/forward navigation)
        window.addEventListener('popstate', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const chatParam = urlParams.get('chat');
            
            if (chatParam && chatParam !== currentChatId) {
                loadChat(chatParam);
            }
        });
        
        // Initialize
        startPolling();
    </script>
</body>
</html>