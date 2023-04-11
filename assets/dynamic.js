var key = "IAMRACCOON";

function getLocation() {
    return new Promise((resolve, reject) => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => resolve({
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                }),
                error => reject(error)
            );
        } else {
            reject("Geolocation is not supported by this browser.");
        }
    });
}

async function getNearestTownName(lat, lng) {
    const url = `../location.php?lat=${lat}&lng=${lng}`;
    const response = await fetch(url);
    const data = await response.json();

    if (data.status === "OK") {
        const town = data.results
            .flatMap(result => result.address_components)
            .find(component => component.types.includes("administrative_area_level_2"));

        return town ? town.long_name : "Unknown";
    } else {
        return "Unknown";
    }
}



async function updateLocation() {
    const maxRetries = 3;
    let retries = 0;

    while (retries < maxRetries) {
        try {
            const permissionStatus = await navigator.permissions.query({
                name: 'geolocation'
            });

            if (permissionStatus.state === 'prompt') {
                alert('Please allow access to your location to find your nearest town.');
            }

            const location = await getLocation();
            townName = await getNearestTownName(location.lat, location.lng);

            if (townName !== "Unknown") {
                break;
            }
        } catch (error) {
            console.error("Error getting location:", error);
        }
        retries++;
    }

    document.querySelector(".town-name").textContent = townName;
}


async function generateOrderId(phoneNumber) {
    try {
        const location = await getLocation();
        const randomStr = Math.random().toString(36).substring(2, 10).toUpperCase();
        return "#" + phoneNumber + "_" + location.lat + "_" + location.lng + "_" + randomStr;
    } catch (error) {
        console.error("Error getting location:", error);
        return "#" + phoneNumber + "_" + "NOLOC" + "_" + randomStr;
    }
}

async function updateOrderId() {

    const phoneNumber = document.getElementById("cardnumber").value;
    if (phoneNumber.length === 10) {
        const orderId = await generateOrderId(phoneNumber);
        document.getElementById("orderNo").value = orderId;

        key = "IAMRACCOON";

        // Encrypt the order data
        const location = await getLocation();
        const townName = await getNearestTownName(location.lat, location.lng);

        //................
        const encryptedData = encryptOrderData(orderId, townName, key);
        console.log("Encrypted data:", encryptedData);
        document.getElementById("orderNo").value = encryptedData;

        const decryptedData = decryptOrderData(encryptedData, key);
        console.log("this.Decrypted data:", decryptedData);
    }
}


//.....................

function xor(str, key) {
    let result = "";
    for (let i = 0; i < str.length; i++) {
        const xorValue = str.charCodeAt(i) ^ key.charCodeAt(i % key.length);
        result += String.fromCharCode(xorValue);
    }
    return result;
}

function encryptOrderData(orderId, location, key) {
    const orderData = `${orderId}:${location}`;
    return xor(orderData, key);
}

function decryptOrderData(encryptedData, key) {
    const decryptedData = xor(encryptedData, key);
    const [orderId, location] = decryptedData.split(':');
    return {
        orderId,
        location
    };
}
// This should be a random string that you generate and keep secure.

// Encrypt the order data
const encryptedData = encryptOrderData("0758481320", "Chuka", "8DJ2D4F7", key);
console.log("Encrypted data:", encryptedData);

// Decrypt the order data
const decryptedData = decryptOrderData(encryptedData, key);
console.log("Decrypted data:", decryptedData);


/// ....................................

updateLocation();


async function waitForLocation() {
    const submitButton = document.getElementById("submitButton");
    const loadingMessages = document.querySelectorAll(".loadingMessage");

    // Disable the button and show the loading message
    submitButton.disabled = true;
    loadingMessages.forEach(loadingMessage => {
        loadingMessage.style.display = "inline";
    });

    // Wait for the location to be found
    await updateLocation();

    // Re-enable the button and hide the loading message
    submitButton.disabled = false;
    //change the text of the submit button
    submitButton.textContent = "Submit Request";
    loadingMessages.forEach(loadingMessage => {
        loadingMessage.style.display = "none";
    });
}

// Call the waitForLocation function when the page loads
waitForLocation();