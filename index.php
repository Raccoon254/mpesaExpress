<?php
  include('mpesaExpress.php');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PayMent API</title>
    <link rel="stylesheet" href="./assets/style.css">
    <script src="https://kit.fontawesome.com/af6aba113a.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">

</head>

<body>

    <div class="container">
        <form action='<?php echo $_SERVER['PHP_SELF'] ?>' method='POST'>
            <h1>Payment Authentication</h1>
            <div class="card_container">
                <p class="details">i. Enter the <b>phone number</b> and press " <b>Confirm
                        and Pay</b>"</br>ii. You will receive a popup on your phone. Enter your <b>MPESA PIN</b>
                </p>
                <?php if ($errmsg != ''): ?>
                <p class="err" style="    background: #cc2a24;padding: .8rem;color: #ffffff;"><?php echo $errmsg; ?>
                </p>
                <?php endif; ?>
                <div class="formData">
                    <input type="hidden" id="orderNo" name="orderNo" value="" />
                    <label for="cardnumber"></label>
                    <input id="cardnumber" type="text" name="phone_number" maxlength="10" placeholder="07---" o
                        oninput="updateOrderId()" />

                    <button id="submitButton" type="submit">
                        <span class="loadingMessage" style="display:none;">
                            <i class="fa-solid fa-gear fa-spin fa-spin-reverse" style="color: #ffffff;"></i>
                        </span>
                        <span class="btnText">
                            Loading...
                        </span>
                    </button>
                </div>
            </div>
        </form>

        <div class="location">Your Location is : <span class="town-name"></span> <span class="loadingMessage"
                style="display:none;">
                <i class="fa-solid fa-gear fa-spin fa-spin-reverse" style="color: #ffffff;"></i>
            </span> <span class="city-name"></span>
        </div>


        <div class="footer-copyrights">

            <p class="mr-5"> Copyright &copy;
                <script>
                document.write(new Date().getFullYear());
                </script> All rights reserved | <i class="icon-heart" aria-hidden="true"></i> powered by <a
                    href="https://stevosoro.xyz" target="_blank">Raccoon254</a>
            </p>
        </div>
        </footer>
    </div>
    <script src="./assets/dynamic.js"></script>
</body>

</html>