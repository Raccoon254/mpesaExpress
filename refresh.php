<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Status</title>
    <script>
        function updateStatus() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "transactionStatus.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const data = JSON.parse(xhr.responseText);
                    let statusText = "";

                    if (data.error) {
                        statusText = data.error;
                    } else if (data.status === 'pending') {
                        statusText = "Transaction is still pending. Please wait...";
                    } else if (data.status === 'completed') {
                        statusText = "Transaction is completed. Receipt number: " + data.OrderNo + ", Amount: " + data.Amount;
                    } else {
                        statusText = "Transaction status is unknown.";
                    }

                    document.getElementById("status").innerText = statusText;
                }
            }
            xhr.send();
        }

        window.onload = function() {
            updateStatus();
            setInterval(updateStatus, 2000); // Update status every 5 seconds
        }
    </script>
</head>
<body>
    <div id="status">Loading...</div>
</body>
</html>
