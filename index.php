<?php
    require "vendor/autoload.php";

    $url = parse_url(getenv("CLEARDB_DATABASE_URL"));

    $server = $url["host"];
    $username = $url["user"];
    $password = $url["pass"];
    $db = substr($url["path"], 1);

    //Instantiate connection
    $conn = mysqli_connect($server, $username, $password, $db);
    $conn->set_charset("utf8");

    //The action act as POST for the URL
    $action = $_GET['action'];

    switch($action){
        case "addProduct":
            $prodName = $_GET['product_name'];
            $prodSKU = $_GET['product_SKU'];
            $prodAvail = $_GET['product_availability'];
            $prodStock = $_GET['product_stock'];
            $prodPrice = $_GET['product_price'];
            $prodImg = $_GET['product_img'];
            $query = "INSERT INTO product (product_name, product_SKU, product_availability, product_stock, product_price, product_img) VALUES ('$prodName', '$prodSKU', '$prodAvail', '$prodStock', '$prodPrice', '$prodImg')";
            mysqli_query($conn, $query);
            break;
        case "updateProduct":
            $prodID = $_GET['product_id'];
            $prodName = $_GET['product_name'];
            $prodSKU = $_GET['product_SKU'];
            $prodAvail = $_GET['product_availability'];
            $prodStock = $_GET['product_stock'];
            $prodPrice = $_GET['product_price'];
            $prodImg = $_GET['product_img'];
            $query = "UPDATE product SET product_name='$prodName', product_SKU = '$prodSKU', product_availability = '$prodAvail', product_stock = '$prodStock', product_price = '$prodPrice', product_img = '$prodImg' WHERE product_id = '$prodID'";
            mysqli_query($conn, $query);
            break;
        case "deleteProduct":
            $prodID = $_GET['product_id'];
            $query = "DELETE FROM product WHERE product_id = '$prodID'";
            mysqli_query($conn, $query);
            break;
        case "readProductAll":
            $query = "SELECT * FROM product ORDER BY product_id ASC";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
                echo json_encode($data);
            }
            break;
        case "readProductById":
            $prodID = $_GET['product_id'];
            $query = "SELECT * FROM product WHERE product_id = '$prodID'";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
                echo json_encode($data);
            }
            break;
        case "addQuote":
            $userID = $_GET['user_id'];
            $total = $_GET['total'];
            $quoteStatus = $_GET['quote_status'];
            $query = "INSERT INTO quote (user_id, total, quote_status) VALUES ('$userID', '$total', '$quoteStatus')";
            mysqli_query($conn, $query);
            break;
        case "getQuote":
            $userID = $_GET['user_id'];
            $query = "SELECT quote_id FROM quote WHERE user_id = '$userID' AND quote_status = 0";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
                echo json_encode($data);
            }
            break;
        case "updateQuoteStatus":
            $quoteID = $_GET['quote_id'];
            $query = "UPDATE quote SET quote_status = 1 WHERE quote_id = '$quoteID'";
            mysqli_query($conn, $query);
            break;
        case "deleteQuote":
            $quoteID = $_GET['quote_id'];
            $query = "DELETE FROM quote WHERE quote_id = '$quoteID'";
            mysqli_query($conn, $query);
            break;
        case "readQuoteByUser":
            $userID = $_GET['user_id'];
            $query = "SELECT * FROM quote q WHERE user_id = '$userID' AND quote_status = 0";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
                echo json_encode($data);
            }
            break;
        case "readQuoteItemByQuote":
            $quoteID = $_GET['quote_id'];
            $query = "SELECT qi.*, q.quote_status FROM quote_item qi 
                        INNER JOIN quote q
                        ON qi.quote_id = q.quote_id
                        WHERE qi.quote_id = '$quoteID' AND q.quote_status = 0";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
                echo json_encode($data);
            }
            else{
                echo "";
            }
            break;
        case "checkIfUserExist":
            $userID = $_GET['user_id'];
            $isUserExist = false;
            $query = "SELECT * FROM quote WHERE user_id = '$userID'";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                $isUserExist = true;
            }
            if (!$isUserExist){
                $query = "INSERT INTO quote (user_id, total, quote_status) VALUES ('$userID', '0.00', '0')";
                mysqli_query($conn, $query);
            }
            break;
        case "updateQuoteRecal":
            $quoteID = $_GET['quote_id'];
            $amount = 0.0;
            $query = "SELECT SUM(product_quantity*product_price) as total FROM quote_item WHERE quote_id = '$quoteID'";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    $amount = $amount + $row['total'];
                }
            }
            $query = "UPDATE quote SET total = '$amount' WHERE quote_id = '$quoteID'";
            mysqli_query($conn, $query);
            break;
        case "addQuoteItem":
            $quoteID = $_GET['quote_id'];
            $prodName = $_GET['product_name'];
            $prodSKU = $_GET['product_SKU'];
            $prodQuantity = $_GET['product_quantity'];
            $prodPrice = $_GET['product_price'];
            $prodImg = $_GET['product_img'];

            $quoteItemID = 0;
            $checkIfItemExist = "SELECT quote_item_id, product_quantity FROM quote_item WHERE quote_id = '$quoteID' AND product_SKU = '$prodSKU'";
            $isExist = mysqli_query($conn, $checkIfItemExist);
            if (mysqli_num_rows($isExist) > 0) {
                while($row = mysqli_fetch_assoc($isExist)){
                    $prodQuantity = $prodQuantity + $row['product_quantity'];
                    $quoteItemID = $row['quote_item_id'];
                }
                $query = "UPDATE quote_item SET product_quantity = '$prodQuantity' WHERE quote_item_id = '$quoteItemID'";
                mysqli_query($conn, $query);
            }
            else{
                $query = "INSERT INTO quote_item (quote_id, product_name, product_SKU, product_quantity, product_price, product_img) VALUES ('$quoteID', '$prodName', '$prodSKU', '$prodQuantity', '$prodPrice', '$prodImg')";
                mysqli_query($conn, $query);
            }
            break;
        case "updateQuoteItem":
            $quoteItemID = $_GET['quote_item_id'];            
            $quoteID = $_GET['quote_id'];
            $prodName = $_GET['product_name'];
            $prodSKU = $_GET['product_SKU'];
            $prodQuantity = $_GET['product_quantity'];
            $prodPrice = $_GET['product_price'];
            $prodImg = $_GET['product_img'];
            $query = "UPDATE quote_item SET quote_id = '$quoteID', product_name='$prodName', product_SKU = '$prodSKU', product_quantity = '$prodQuantity', product_price = '$prodPrice', product_img = '$prodImg' WHERE quote_item_id = '$quoteItemID'";
            mysqli_query($conn, $query);
            break;
        case "deleteQuoteItem":
            $quoteItemID = $_GET['quote_item_id'];            
            $query = "DELETE FROM quote_item WHERE quote_item_id = '$quoteItemID'";
            mysqli_query($conn, $query);
            break;
        case "addAddress":
            $userID = $_GET['user_id'];
            $addRecipient = $_GET['address_recipient'];
            $addContact = $_GET['address_contact'];
            $addLine1 = $_GET['address_line1'];
            $addLine2 = $_GET['address_line2'];
            $addCode = $_GET['address_code'];
            $addCity = $_GET['address_city'];
            $addState = $_GET['address_state'];  
            $addCountry = $_GET['address_country'];   
            $isDefault = $_GET['is_default'];       
            $query = "INSERT INTO address_user (user_id, address_recipient, address_contact, address_line1, address_line2, address_code, address_city, address_state, address_country, is_default) VALUES ('$userID', '$addRecipient', '$addContact', '$addLine1', '$addLine2', '$addCode', '$addCity', '$addState', '$addCountry', '$isDefault')";
            mysqli_query($conn, $query);
            break;
        case "updateAddress":
            $addID = $_GET['address_id'];
            $userID = $_GET['user_id'];
            $addRecipient = $_GET['address_recipient'];
            $addContact = $_GET['address_contact'];
            $addLine1 = $_GET['address_line1'];
            $addLine2 = $_GET['address_line2'];
            $addCode = $_GET['address_code'];
            $addCity = $_GET['address_city'];
            $addState = $_GET['address_state'];  
            $addCountry = $_GET['address_country'];       
            $isDefault = $_GET['is_default'];    
            $query = "UPDATE address_user SET user_id = '$userID', address_recipient = '$addRecipient', address_contact = '$addContact', address_line1 = '$addLine1', address_line2 = '$addLine2', address_code = '$addCode', address_city = '$addCity', address_state = '$addState', address_country = '$addCountry', is_default = '$isDefault' WHERE address_id = '$addID'";
            mysqli_query($conn, $query);
            break;
        case "deleteAddress":
            $addID = $_GET['address_id'];         
            $query = "DELETE FROM address_user WHERE address_id = '$addID'";
            mysqli_query($conn, $query);
            break;
        case "readAddressByUser":
            $userID = $_GET['user_id'];
            $query = "SELECT * FROM address_user WHERE user_id = '$userID'";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
                echo json_encode($data);
            }
            break;
        case "readAddressDefault":
            $userID = $_GET['user_id'];
            $query = "SELECT * FROM address_user WHERE user_id = '$userID' AND is_default = 1";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
                echo json_encode($data);
            }
            break;
        case "addOrder":
            $userID = $_GET['user_id'];
            $orderDate = $_GET['order_date'];
            $total = $_GET['total'];
            $orderStatus = $_GET['order_status'];   
            $ref = orderIDGenerator();
			//If order ID existed, rerun generator until the order ID is unique
			$orderRef = orderIDValidator($conn, $ref);   
            
            //echo "Begin Insert"."<br>";

            $query = "INSERT INTO orders (user_id, order_reference, order_date, order_total, order_status) VALUES ('$userID', '$orderRef', '$orderDate', '$total', '$orderStatus')";
            mysqli_query($conn, $query);

            /*
            if(mysqli_query($conn, $query)){
                echo "insert success"."<br>";
            }else{
                echo "insert fail"."<br>";
            }
            */

            //echo "Inserted"."<br>";

            //echo "start to select"."<br>";

            //echo $orderRef."<br>";

            $getOrderID = "SELECT order_id FROM orders WHERE order_reference = '$orderRef'";
            $result = mysqli_query($conn, $getOrderID);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
                echo json_encode($data);
            }

            //echo "selected"."<br>";
            break;
        case "readOrderRefAll":
            $query = "SELECT order_reference FROM orders";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
                echo json_encode($data);
            }
            break;
        case "readOrderAll":
            $query = "SELECT * FROM orders o 
                    LEFT JOIN order_item oi
                    ON oi.order_item_id = o.order_id 
                    LEFT JOIN order_address oa
                    ON oa.order_address_id = o.order_id 
                    LEFT JOIN order_payment op
                    ON op.order_payment_id = o.order_id                     
                    GROUP BY o.order_id";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
                echo json_encode($data);
            }
            break;
        case "readOrderByUser":
            $userID = $_GET['user_id'];
            $data = array();
            $query = "SELECT * FROM orders o 
                    LEFT JOIN order_item oi
                    ON oi.order_item_id = o.order_id 
                    LEFT JOIN order_address oa
                    ON oa.order_address_id = o.order_id 
                    LEFT JOIN order_payment op
                    ON op.order_payment_id = o.order_id                     
                    WHERE user_id = '$userID'
                    GROUP BY o.order_id";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    $orderID = $row['order_id'];
                    $query1 = "SELECT COUNT(*) AS total_items FROM order_item oi
                                LEFT JOIN orders o
                                ON o.order_id = oi.order_id
                                WHERE o.order_id = '$orderID'";
                    $result1 = mysqli_query($conn, $query1);
                    if (mysqli_num_rows($result1) > 0) {
                        while($row1 = mysqli_fetch_assoc($result1)){
                            $row = json_encode($row);
                            $row = json_decode($row, TRUE);
                            array_push($row, ["total_items" => $row1['total_items']]);
                            $data[] = $row;
                        }
                    }
                }
                echo json_encode($data);
            }
            break;
        case "updateOrderStatus":
            $orderID = $_GET['order_id'];
            $orderStatus = $_GET['order_status'];       
            $query = "UPDATE orders SET order_status = '$orderStatus' WHERE order_id = '$orderID'";
            mysqli_query($conn, $query);
            break;
        case "addOrderItem":
            $orderID = $_GET['order_id'];
            $quoteID = $_GET['quote_id'];
            $query = "SELECT * FROM quote_item WHERE quote_id = '$quoteID'";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    $prodName = $row['product_name'];
                    $prodSKU = $row['product_SKU'];
                    $prodQuantity = $row['product_quantity'];
                    $prodPrice = $row['product_price'];
                    $prodImg = $row['product_img'];
                    $addOrderItem = "INSERT INTO order_item (order_id, product_name, product_SKU, product_quantity, product_price, product_img) VALUES ('$orderID', '$prodName', '$prodSKU', '$prodQuantity', '$prodPrice', '$prodImg')";
                    mysqli_query($conn, $addOrderItem);
                }
            }  
            break;
        case "addOrderAddress":
            $orderID = $_GET['order_id'];
            $addRecipient = $_GET['address_recipient'];
            $addContact = $_GET['address_contact'];
            $addLine1 = $_GET['address_line1'];
            $addLine2 = $_GET['address_line2'];
            $addCode = $_GET['address_code'];
            $addCity = $_GET['address_city'];
            $addState = $_GET['address_state'];  
            $addCountry = $_GET['address_country'];  
            $query = "INSERT INTO order_address (order_id, address_recipient, address_contact, address_line1, address_line2, address_code, address_city, address_state, address_country) VALUES ('$orderID', '$addRecipient', '$addContact', '$addLine1', '$addLine2', '$addCode', '$addCity', '$addState', '$addCountry')";
            mysqli_query($conn, $query);
            break;
        case "addOrderPayment":
            $orderID = $_GET['order_id'];
            $payType = $_GET['payment_type'];
            $payID = $_GET['payment_id'];
            $query = "INSERT INTO order_payment (order_id, payment_type, payment_id) VALUES ('$orderID', '$payType', '$payID')";
            mysqli_query($conn, $query);
            break;
        case "readOrderItem":
            $userID = $_GET['user_id'];
            $query = "SELECT * FROM order_item LEFT JOIN orders ON orders.order_id = order_item.order_id WHERE orders.user_id = '$userID'";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
                echo json_encode($data);
            }
            break;
        case "readOrderItemTop1":
            $userID = $_GET['user_id'];
            $query = "SELECT * FROM order_item LEFT JOIN orders ON orders.order_id = order_item.order_id WHERE orders.user_id = '$userID' GROUP BY order_item.order_id";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
                echo json_encode($data);
            }
            break;
    }

    //Generate unique order id number
    function orderIDGenerator(){
        $length = 15;
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $orderID= '';

        for ($i = 0; $i < $length; $i++) {
            $orderID .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return "AE_".$orderID;
    }
    
    //Validate order id number
    function orderIDValidator($conn, $orderID){
        $checkID = "SELECT * FROM orders WHERE order_reference='$orderID'";
        $getID = mysqli_query($conn, $checkID);
        if (mysqli_num_rows($getID) > 0) {
            while($row = mysqli_fetch_assoc($getID)){
                $orderIDOld = $row['order_id'];
                while ($orderID == $orderIDOld){
                    $orderID = orderIDGenerator();
                }
            }
        }
        return $orderID;    
    }
?>