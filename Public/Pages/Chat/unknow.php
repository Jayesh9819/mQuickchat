<!doctype html>
<html lang="en" dir="ltr">

<head>
    <?php
    ob_start();
    include("./Public/Pages/Common/header.php");
    // include "./Public/Pages/Common/auth_user.php";

    // Function to echo the script for toastr
    function echoToastScript($type, $message)
    {
        echo "<script type='text/javascript'>document.addEventListener('DOMContentLoaded', function() { toastr['$type']('$message'); });</script>";
    }


    if (isset($_SESSION['toast'])) {
        $toast = $_SESSION['toast'];
        echoToastScript($toast['type'], $toast['message']);
        unset($_SESSION['toast']); // Clear the toast message from session
    }

    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    // Display error message if available
    if (isset($_SESSION['login_error'])) {
        echo '<p class="error">' . $_SESSION['login_error'] . '</p>';
        unset($_SESSION['login_error']); // Clear the error message
    }
    if (isset($_SESSION['username'])) {
        # database connection file
        include 'app/db.conn.php';
        // include './Public/Pages/Chat/./Public/Pages/Chat/app/';
        include 'app/helpers/user.php';
        include 'app/helpers/conversations.php';
        include 'app/helpers/timeAgo.php';
        include 'app/helpers/last_chat.php';
        // Fetch online agents in the same page
        $sql = "SELECT * FROM user WHERE role = 'Agent' AND last_seen(last_seen) COLLATE utf8mb4_unicode_ci  = 'Active' ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // $user = getUser($_SESSION['username'], $conn);
        $conversations = getConversation($_SESSION['id'], $conn);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
                $conversations1 = getConversation($user_id, $conn);
                echo json_encode($conversations1);
            } else {
                echo json_encode([]);
            }
            exit;
        }
    }else{
        header('Location: ../index.php/Login_to_CustCount'); // Redirect to the login page

    }


    ?>
    <style>
        .vh-100 {
            min-height: 100vh;
        }

        .w-400 {
            width: 800px;
        }

        .fs-xs {
            font-size: 1rem;
        }

        .w-10 {
            width: 10%;
        }

        a {
            text-decoration: none;
        }

        .fs-big {
            font-size: 5rem !important;
        }

        .online {
            width: 10px;
            height: 10px;
            background: green;
            border-radius: 50%;
        }

        .w-15 {
            width: 10%;
        }

        .fs-sm {
            font-size: 2rem;
        }

        .display-4 {
            font-size: 1.5rem !important;
        }

        small {
            color: #bbb;
            font-size: 0.7rem;
            text-align: right;
        }

        .chat-box {
            overflow-y: auto;
            overflow-x: hidden;
            max-height: 50vh;
        }

        .rtext {
            width: 65%;
            background: #f8f9fa;
            color: #444;
        }

        .ltext {
            width: 65%;
            background: #3289c8;
            color: #fff;
        }

        /* width */
        *::-webkit-scrollbar {
            width: 3px;
        }

        /* Track */
        *::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Handle */
        *::-webkit-scrollbar-thumb {
            background: #aaa;
        }

        /* Handle on hover */
        *::-webkit-scrollbar-thumb:hover {
            background: #3289c8;
        }

        textarea {
            resize: none;
        }

        /*message_status*/
        /* Custom CSS styles */
        .chat-box {
            max-width: 750px;
            max-height: 300px;
            /* Limit the height of the chat box */
            overflow-y: auto;
            /* Enable vertical scrolling */
        }

        .chat-box p {
            margin: 5px 0;
            /* Add spacing between chat messages */
        }

        .chat-input-group {
            position: relative;
            /* Set position to relative for proper alignment */
        }

        #message {
            border-radius: 20px;
            /* Adjust border radius for message input */
            resize: none;
            /* Disable resizing of textarea */
        }

        #sendBtn {
            position: absolute;
            /* Position the send button */
            right: 10px;
            bottom: 10px;
        }

        .ltext {
            background-color: blueviolet;
            color: black;
            max-width: 50%;
            font-size: large;
        }

        .rtext {
            background-color: blue;
            color: aliceblue;
            max-width: 50%;
            font-size: large;


        }


        .emoji-picker {
            position: absolute;
            bottom: 60px;
            /* Adjust based on your layout */
            border: 1px solid #ddd;
            padding: 5px;
            background-color: white;
            width: 400px;
            /* Adjust as necessary */
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            /* Adjust column count based on preference */
            gap: 5px;
            overflow-y: auto;
            max-height: 400px;
        }

        .unread-messages {
            display: inline-block;
            background-color: green;
            color: white;
            font-size: 0.8em;
            border-radius: 50%;
            padding: 2px 6px;
            margin-left: 5px;
            vertical-align: top;
            float: right;
            /* Add float right to move it to the right side */
            margin-right: 10px;
            /* Adjust as needed for spacing from the right edge */
        }

        .d-flex.align-items-center {
            flex-grow: 1;
            /* Ensure the container takes all available space */
        }
    </style>



</head>

<body>
    <!-- loader Start -->
    <?php
    // include("./Public/Pages/Common/loader.php");

    ?>
    <!-- loader END -->

    <!-- sidebar  -->
    <?php

    ?>

    <main class="main-content">
        <?php
        ?>


        <div class="content-inner container-fluid pb-0" id="page_layout">
            <div class="p-2 w-100
                rounded shadow">
                <div>
                    <h3>Online Agents Available for Chat</h3>
                    <ul>
                        <?php foreach ($agents as $agent) { ?>
                            <a href="./UNC?user=<?= $agent['username'] ?>" class="d-flex
	    				          justify-content-between
	    				          align-items-center p-2">
                                <div class="d-flex
	    					            align-items-center">
                                    <img src="../uploads/profile/<?= !empty($chatWith['p_p']) ? $chatWith['p_p'] : '07.png' ?>" class="w-15 rounded-circle">
                                    <h3 class="fs-xs m-2">
                                        <?= $agent['username'] ?><br>
                                    </h3>
                                </div>
                                </li>
                            <?php } ?>
                    </ul>
                </div>

                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <script>
                    function fetchConversations() {
                        $.ajax({
                            url: 'index.php', // Making an AJAX request to the same file
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                console.log(data); // Process and display the conversations
                                // Update the HTML based on returned data
                                var html = '';
                                data.forEach(function(conv) {
                                    html += '<div>' + conv.username + ' - Unread messages: ' + conv.unread_messages + '</div>';
                                });
                                document.getElementById('conversation-list').innerHTML = html;
                            },
                            error: function() {
                                console.error('Error fetching conversations.');
                            }
                        });
                    }

                    // Fetch conversations every 5 seconds
                    setInterval(fetchConversations, 1000);
                    // Also fetch them immediately on page load
                    fetchConversations();
                </script>
                <script>
                    $(document).ready(function() {

                        // Search
                        $("#searchText").on("input", function() {
                            var searchText = $(this).val();
                            if (searchText == "") return;
                            $.post('../Public/Pages/Chat/app/ajax/search.php', {
                                    key: searchText
                                },
                                function(data, status) {
                                    $("#chatList").html(data);
                                });
                        });

                        // Search using the button
                        $("#serachBtn").on("click", function() {
                            var searchText = $("#searchText").val();
                            if (searchText == "") return;
                            $.post('../Public/Pages/Chat/app/ajax/search.php', {
                                    key: searchText
                                },
                                function(data, status) {
                                    $("#chatList").html(data);
                                });
                        });

                        let lastSeenUpdate = function() {
                            $.get('../Public/Pages/Chat/app/ajax/update_last_seen.php')
                                .done(function(data) {
                                    console.log('Success:', data); // Successful response handling
                                })
                                .fail(function(jqXHR, textStatus, errorThrown) {
                                    console.error('AJAX Error:', textStatus); // Error handling
                                });
                        };

                        lastSeenUpdate(); // Initial call
                        setInterval(lastSeenUpdate, 10000); // Set to run every 10 seconds
                    });
                </script>




            </div>






            <?
            include("./Public/Pages/Common/footer.php");

            ?>

    </main>
    <!-- Wrapper End-->
    <!-- Live Customizer start -->
    <!-- Setting offcanvas start here -->
    <?php
    include("./Public/Pages/Common/theme_custom.php");

    ?>

    <!-- Settings sidebar end here -->

    <?php
    include("./Public/Pages/Common/settings_link.php");

    ?>
    <?php
    include("./Public/Pages/Common/scripts.php");

    ?>

</body>

</html>