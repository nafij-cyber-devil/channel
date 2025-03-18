<?php
// fetch channel fredflixceo server hit api json data
$jsonURL = "https://its-ferdos-alom.top/fredflix.fun/ayna/api.json"; // Update JSON URL here
$channelsData = file_get_contents($jsonURL);
$channels = json_decode($channelsData, true) ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AynaOTT</title>
<link rel="icon" href="https://i.postimg.cc/rmw0zVvB/logo-2.png" />
    <link rel="shortcut icon" href="https://i.postimg.cc/rmw0zVvB/logo-2.png" />
    <style>
        :root {
            --logo-size: 50px;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Header styling */
        header {
            background-color: black;
            padding: 5px 20px;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo img {
            height: var(--logo-size);
            width: auto;
            margin-right: 10px;
        }

        /* Search Bar */
        .search-bar {
            max-width: 600px;
            margin: 1rem auto 2rem;
            display: flex;
            align-items: center;
            background: rgba(30, 30, 30, 0.8);
            padding: 0.5rem;
            border-radius: 50px;
        }

        .search-bar input {
            flex: 1;
            padding: 0.5rem;
            background: transparent;
            color: white;
            border: none;
            outline: none;
        }

        .search-bar button {
            padding: 0.5rem 1rem;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        /* Channel Grid */
        .channel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .channel {
            text-align: center;
            background-color: #f9f9f9;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .channel img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .channel h3 {
            font-size: 16px;
            margin: 10px 0;
        }

        .channel button {
            padding: 10px 15px;
            font-size: 14px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .channel button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <a href="#" class="logo">
            <img src="https://i.postimg.cc/rmw0zVvB/logo-2.png" alt="Logo">
        </a>
    </header>

    <!-- Search Bar -->
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search channels...">
        <button onclick="filterChannels()">Search</button>
    </div>

    <!-- Channel Grid -->
    <div id="channelGrid" class="channel-grid">
        <?php
        if (!empty($channels)) {
            foreach ($channels as $channel) {
                $channelId = htmlspecialchars($channel['channelId']);
                $channelName = htmlspecialchars($channel['channelName'] ?? 'Unnamed Channel');
                $channelLogo = isset($channel['channelLogo']) && !empty($channel['channelLogo'])
                    ? htmlspecialchars($channel['channelLogo'])
                    : "https://its-ferdos-alom.top/fredflix.fun/ayna/file-HV6zPjQeouXzddFhUb8jn3.webp";

                echo "
                    <div class='channel'>
                        <img src='$channelLogo' alt='$channelName'>
                        <h3>$channelName</h3>
                        <button onclick='openChannel(\"$channelId\")'>Watch</button>
                    </div>
                ";
            }
        } else {
            echo "<p>No channels available.</p>";
        }
        ?>
    </div>

    <script>
        // Filter channels based on search input
    function filterChannels() {
        const searchInput = document.getElementById("searchInput").value.toLowerCase();
        const channels = document.querySelectorAll(".channel");

        let found = false;

        channels.forEach(channel => {
            const title = channel.querySelector("h3").textContent.toLowerCase();
            if (title.includes(searchInput) || searchInput === "") {
                channel.style.display = "";
                found = true;
            } else {
                channel.style.display = "none";
            }
        });

        // Jodi kono result na thake, tahole message show korbo
        const channelGrid = document.getElementById("channelGrid");
        let noResultMessage = document.getElementById("noResultMessage");

        if (!found) {
            if (!noResultMessage) {
                noResultMessage = document.createElement("p");
                noResultMessage.id = "noResultMessage";
                noResultMessage.textContent = "No channels found.";
                noResultMessage.style.textAlign = "center";
                noResultMessage.style.fontWeight = "bold";
                channelGrid.appendChild(noResultMessage);
            }
        } else {
            if (noResultMessage) {
                noResultMessage.remove();
            }
        }
    }

    // Search box clear korle sob channel abar show hobe
    document.getElementById("searchInput").addEventListener("input", function() {
        if (this.value === "") {
            filterChannels();
        }
    });

        // Redirect to play.php with channelId
        function openChannel(id) {
            window.location.href = `play.php?id=${id}`;
        }
    </script>

</body>
</html>