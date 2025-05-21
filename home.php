<?php

session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>MCG Creamline - Home</title>
    <link rel="stylesheet" href="Style/style.css">
</head>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    padding: 20px 100px;
    background: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 99;
}
.logo {
    font-size: 2em;
    color: black;
    user-select: none;
}
.navigation a {
    position: relative;
    font-size: 1.1em;
    color: black;
    text-decoration: none;
    font-weight: 500;
    margin-left: 40px;
}
.navigation a::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -6px;
    width: 100%;
    height: 3px;
    background: black;
    border-radius: 5px;
    transform-origin: right;
    transform: scaleX(0);
    transition: transform .5s;
}
.navigation a:hover::after {
    transform: scaleX(1);
    transform-origin: left;
}
.hero {
    height: 90vh;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 60px 20px;
    background: rgba(255, 255, 255, 0.7);
    margin-top: 80px;
}

.hero-content h1 {
    font-size: 3rem;
    color: #e17055;
}

.hero-content p {
    font-size: 1.2rem;
    margin-top: 10px;
}
.about {
    padding: 8px 20px;
    text-align: center;
    background-color: rgba(255, 255, 255, 0.8);
}

.about h2 {
    font-size: 2.5em;
    margin-bottom: 30px;
    color: #d35400;
}

/* Card Layout */
.card-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px;
    padding: 20px;
}
.box {
    background-color: white;
    border-radius: 15px;
    max-width: 280px;
    padding: 20px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    transition: transform 0.3s;
}

.box:hover {
    transform: translateY(-5px);
}

.box img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 15px;
}
.box p {
    font-size: 1rem;
    color: #333;
}
</style>
<body>

    <header>
        <h2 class="logo">MCG Creamline</h2>
        <nav class="navigation">
            <a href="signup.php">Sign-up</a>
            <a href="login.php">Login</a>
        </nav>
    </header> 
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to MCG Creamline</h1>
            <p>Delicious moments in every scoop 🍦</p>
        </div>
    </section>

    <section class="about">
        <h2>About Creamline</h2>
        <div class="card-container">
            <div class="box">
                <img src="images/w1.jpg" alt="This is ice cream 1">
                <p>Creamline is your go-to brand for fresh, creamy ice creams loved by kids and adults alike!</p>
            </div>
            <div class="box">
                <img src="images/w2.jpg" alt="This is ice cream 2">
                <p>Made from the finest ingredients to bring sweetness to your day.</p>
            </div>
            <div class="box">
                <img src="images/w3.png" alt="This is ice cream 3">
                <p>Perfect for parties, dates, and weekend indulgence!</p>
            </div>
        </div>
    </section>

</body>
</html>
