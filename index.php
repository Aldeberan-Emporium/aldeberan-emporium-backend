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
    $action = $_POST['action'];

    switch($action){
        case "addProduct":
            $prodName = $_POST['product_name'];
            $prodSKU = $_POST['product_SKU'];
            $prodAvail = $_POST['product_availability'];
            $prodStock = $_POST['product_stock'];
            $prodPrice = $_POST['product_price'];
            $prodImg = $_POST['product_img'];
            $query = "INSERT INTO product (product_name, product_SKU, product_availability, product_stock, product_price, product_img) VALUES ('$prodName', '$prodSKU', '$prodAvail', '$prodStock', '$prodPrice', '$prodImg')";
            mysqli_query($conn, $query);
            break;
        case "updateProduct":
            $prodID = $_POST['product_id'];
            $prodName = $_POST['product_name'];
            $prodSKU = $_POST['product_SKU'];
            $prodAvail = $_POST['product_availability'];
            $prodStock = $_POST['product_stock'];
            $prodPrice = $_POST['product_price'];
            $prodImg = $_POST['product_img'];
            $query = "UPDATE product SET product_name='$prodName', product_SKU = '$prodSKU', product_availability = '$prodAvail', product_stock = '$prodStock', product_price = '$prodPrice', product_img = '$prodImg' WHERE product_id = '$prodID'";
            mysqli_query($conn, $query);
            break;
        case "deleteProduct":
            $prodID = $_POST['product_id'];
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
            $prodID = $_POST['product_id'];
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
            $userID = $_POST['user_id'];
            $subtotal = $_POST['subtotal'];
            $total = $_POST['total'];
            $createdAt = $_POST['created_at'];
            $updatedAt = $_POST['updated_at'];
            $quoteStatus = $_POST['quote_status'];
            $query = "INSERT INTO quote (user_id, subtotal, total, created_at, updated_at, quote_status) VALUES ('$userID', '$subtotal', '$total', '$createdAt', '$updatedAt', '$quoteStatus')";
            mysqli_query($conn, $query);
            break;
        case "updateQuote":
            $quoteID = $_POST['quote_id'];
            $userID = $_POST['user_id'];
            $subtotal = $_POST['subtotal'];
            $total = $_POST['total'];
            $createdAt = $_POST['created_at'];
            $updatedAt = $_POST['updated_at'];
            $quoteStatus = $_POST['quote_status'];
            $query = "UPDATE quote SET user_id = '$userID', subtotal = '$subtotal', total = '$total', created_at = '$createdAt', updated_at = '$updatedAt', quote_status = '$quoteStatus' WHERE quote_id = '$quoteID'";
            mysqli_query($conn, $query);
            break;
        case "deleteQuote":
            $quoteID = $_POST['quote_id'];
            $query = "DELETE FROM quote WHERE quote_id = '$quoteID'";
            mysqli_query($conn, $query);
            break;
        case "readQuoteByUser":
            $userID = $_POST['user_id'];
            $query = "SELECT q.*, qi.* FROM quote q
                        INNER JOIN quote_item qi
                        ON qi.quote_id = q.quote_id
                        WHERE user_id = '$userID' AND quote_status = 0";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
                echo json_encode($data);
            }
            break;
        case "addQuoteItem":
            $quoteID = $_POST['quote_id'];
            $prodName = $_POST['product_name'];
            $prodSKU = $_POST['product_SKU'];
            $prodQuantity = $_POST['product_quantity'];
            $prodPrice = $_POST['product_price'];
            $prodImg = $_POST['product_img'];
            $query = "INSERT INTO quote_item (quote_id, product_name, product_SKU, product_quantity, product_price, product_img) VALUES ('$quoteID', '$prodName', '$prodSKU', '$prodQuantity', '$prodPrice', '$prodImg')";
            mysqli_query($conn, $query);
            break;
        case "updateQuoteItem":
            $quoteItemID = $_POST['quote_item_id'];            
            $quoteID = $_POST['quote_id'];
            $prodName = $_POST['product_name'];
            $prodSKU = $_POST['product_SKU'];
            $prodQuantity = $_POST['product_quantity'];
            $prodPrice = $_POST['product_price'];
            $prodImg = $_POST['product_img'];
            $query = "UPDATE quote_item SET quote_id = '$quoteID', product_name='$prodName', product_SKU = '$prodSKU', product_quantity = '$prodQuantity', product_price = '$prodPrice', product_img = '$prodImg' WHERE quote_item_id = '$quoteItemID'";
            mysqli_query($conn, $query);
            break;
        case "deleteQuoteItem":
            $quoteItemID = $_POST['quote_item_id'];            
            $query = "DELETE FROM quote_item WHERE quote_item_id = '$quoteItemID'";
            mysqli_query($conn, $query);
            break;
        case "addAddress":
            $userID = $_POST['user_id'];
            $addRecipient = $_POST['address_recipient'];
            $addContact = $_POST['address_contact'];
            $addLine1 = $_POST['address_line1'];
            $addLine2 = $_POST['address_line2'];
            $addCode = $_POST['address_code'];
            $addCity = $_POST['address_city'];
            $addState = $_POST['address_state'];  
            $addCountry = $_POST['address_country'];          
            $query = "INSERT INTO address_user (user_id, address_recipient, address_contact, address_line1, address_line2, address_code, address_city, address_state, address_country) VALUES ('$userID', '$addRecipient', '$addContact', '$addLine1', '$addLine2', '$addCode', '$addCity', '$addState', '$addCountry')";
            mysqli_query($conn, $query);
            break;
        case "updateAddress":
            $addID = $_POST['address_id'];
            $userID = $_POST['user_id'];
            $addRecipient = $_POST['address_recipient'];
            $addContact = $_POST['address_contact'];
            $addLine1 = $_POST['address_line1'];
            $addLine2 = $_POST['address_line2'];
            $addCode = $_POST['address_code'];
            $addCity = $_POST['address_city'];
            $addState = $_POST['address_state'];  
            $addCountry = $_POST['address_country'];          
            $query = "UPDATE address_user SET user_id = '$userID', address_recipient = '$addRecipient', address_contact = '$addContact', address_line1 = '$addLine1', address_line2 = '$addLine2', address_code = '$addCode', address_city = '$addCity', address_state = '$addState', address_country = '$addCountry' WHERE address_id = '$addID'";
            mysqli_query($conn, $query);
            break;
        case "deleteAddress":
            $addID = $_POST['address_id'];         
            $query = "DELETE FROM address_user WHERE address_id = '$addID'";
            mysqli_query($conn, $query);
            break;
        case "readAddressByUser":
            $userID = $_POST['user_id'];
            $query = "SELECT * FROM address_user WHERE user_id = '$userID'";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
                echo json_encode($data);
            }
            break;
        case "addOrder":
            $userID = $_POST['user_id'];
            $orderRef = $_POST['order_reference'];
            $orderDate = $_POST['order_date'];
            $subtotal = $_POST['order_subtotal'];
            $total = $_POST['order_total'];
            $orderStatus = $_POST['order_status'];       
            $query = "INSERT INTO orders (user_id, order_reference, order_date, order_subtotal, order_total, order_status) VALUES ('$userID', '$orderRef', '$orderDate', '$subtotal', '$total', '$orderStatus')";
            mysqli_query($conn, $query);
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
            $query = "SELECT DISTINCT oi.*, oa.*, op.* FROM orders o 
                        INNER JOIN order_item oi
                        ON oi.order_id = o.order_id 
                        INNER JOIN order_address oa
                        ON oa.order_id = o.order_id 
                        INNER JOIN order_payment op
                        ON op.order_id = o.order_id                     
                        ORDER BY o.order_id ASC
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
            $userID = $_POST['user_id'];
            $query = "SELECT DISTINCT oi.*, oa.*, op.* FROM orders o 
                        INNER JOIN order_item oi
                        ON oi.order_id = o.order_id 
                        INNER JOIN order_address oa
                        ON oa.order_id = o.order_id 
                        INNER JOIN order_payment op
                        ON op.order_id = o.order_id                     
                        WHERE user_id = '$userID'
                        GROUP BY o.order_id";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
                echo json_encode($data);
            }
            break;
        case "addOrderItem":
            $orderID = $_POST['order_id'];
            $prodName = $_POST['product_name'];
            $prodSKU = $_POST['product_SKU'];
            $prodQuantity = $_POST['product_quantity'];
            $prodPrice = $_POST['product_price'];
            $prodImg = $_POST['product_img'];     
            $query = "INSERT INTO order_item (order_id, product_name, product_SKU, product_quantity, product_price, product_img) VALUES ('$orderID', '$prodName', '$prodSKU', '$prodQuantity', '$prodPrice', '$prodImg')";
            mysqli_query($conn, $query);
            break;
        case "addOrderAddress":
            $orderID = $_POST['order_id'];
            $addRecipient = $_POST['address_recipient'];
            $addContact = $_POST['address_contact'];
            $addLine1 = $_POST['address_line1'];
            $addLine2 = $_POST['address_line2'];
            $addCode = $_POST['address_code'];
            $addCity = $_POST['address_city'];
            $addState = $_POST['address_state'];  
            $addCountry = $_POST['address_country'];  
            $query = "INSERT INTO order_address (order_id, address_recipient, address_contact, address_line1, address_line2, address_code, address_city, address_state, address_country) VALUES ('$orderID', '$addRecipient', '$addContact', '$addLine1', '$addLine2', '$addCode', '$addCity', '$addState', '$addCountry')";
            mysqli_query($conn, $query);
            break;
        case "addOrderPayment":
            $orderID = $_POST['order_id'];
            $payType = $_POST['payment_type'];
            $payID = $_POST['payment_id'];
            $query = "INSERT INTO order_payment (order_id, payment_type, payment_id) VALUES ('$orderID', '$payType', '$payID')";
            mysqli_query($conn, $query);
            break;
    }
?>