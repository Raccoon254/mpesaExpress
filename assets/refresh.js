function updateStatus() {
    var shouldUpdate = true;
    if (!shouldUpdate) return;

    const xhr = new XMLHttpRequest();
    xhr.open("GET", "transactionStatus.php", true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            const data = JSON.parse(xhr.responseText);
            let statusText = "";
            const repayButton = document.getElementById("repayButton");

            if (data.error) {
                statusText = data.error;
                shouldUpdate = false;
                repayButton.style.display = "inline"; // Show the Repay button
            } else if (data.status === 'pending') {
                statusText = "Transaction Pending. Please wait...";
            } else if (data.status === 'completed') {
                statusText = "Transaction is completed. ❤️‍🔥 Receipt number: " + data.OrderNo + ", Amount: " + data.Amount;
                shouldUpdate = false;
            } else {
                statusText = "Transaction status Failed or is unknown.";
                shouldUpdate = false;
                repayButton.style.display = "inline"; // Show the Repay button
            }

            document.getElementById("status").innerText = statusText;
        }
    }
    xhr.send();
}

window.onload = function() {
    updateStatus();
    setInterval(updateStatus, 2000); // Update status every 2 seconds
}
