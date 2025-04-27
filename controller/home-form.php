<?php 
if(isset($_POST['submit'])){
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $subject = $_POST['subject'];

    $conn = mysqli_connect("localhost", "pohoopmy_constructionhelps", "]TpJx7^p3hHs", "pohoopmy_constructionhelps");

    mysqli_query($conn, "INSERT INTO homeform(username, email, phone, subject) VALUES('$username','$email','$phone','$subject')");

    $to = "emailWazid@gmail.com";
    $subject = "You have received an Enquiry from cscs ";
    $message = "
        Name : $username  <br>
        Email : $email  <br>
        Phone: $phone <br>
        Subject: $subject
    ";
    $headers  = "From: Construction Helps <constructionhelps@gmail.com>\n";
    $headers .= 'X-Mailer: PHP/' . phpversion();
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=iso-8859-1\n";

    if(mail($to, $subject, $message, $headers)){
        echo "<script language='JAVASCRIPT'>
            window.alert('Thank you for contacting us! Our Representative Will Call You Shortly!');
            window.location.href='../index';
            </script>";
    }
}


?>