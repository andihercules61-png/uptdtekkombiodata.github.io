<?php
/**
 * Session Management - Permanent Login System with LocalStorage Backup
 * 
 * This system keeps users logged in permanently until they manually logout.
 * Features:
 * - Session never expires automatically
 * - Cookie persists until browser is closed or logout
 * - LocalStorage backup for session data (survives browser restart)
 * - Auto-login from localStorage when website is reopened
 * - Real-time session duration tracking
 * - Manual logout required to end session
 * - Works even when database/terminal is closed
 */

// Configure session to last forever
ini_set('session.gc_maxlifetime', 0);
ini_set('session.cookie_lifetime', 0);
ini_set('session.use_strict_mode', 1);

// Start session
session_start();

// Check if user just registered
$welcomeName = '';

// Check for logout action
if (isset($_GET['logout'])) {
    // Clear session data
    session_unset();
    session_destroy();
    
    // Clear cookies
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Redirect to clear URL with clear parameter to clear localStorage
    header('Location: index.php?clear=1');
    exit();
}

        // Check if user is logged in via session
        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
            $welcomeName = $_SESSION['user_name'] ?? '';
            $userData = $_SESSION['user_data'] ?? null;
        } else if (isset($_GET['welcome']) && !empty($_GET['welcome'])) {
            // New login - save to session
            $welcomeName = htmlspecialchars($_GET['welcome']);
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_name'] = $welcomeName;
            $_SESSION['login_time'] = time();
            
            // Check if login is late (after 8:30 AM)
            $currentHour = (int)date('H', $_SESSION['login_time']);
            $currentMinute = (int)date('i', $_SESSION['login_time']);
            $isLate = ($currentHour > 8) || ($currentHour == 8 && $currentMinute > 30);
            $_SESSION['is_late'] = $isLate;
            
            // Get user data from database if available
            if (isset($_GET['user_id'])) {
                $_SESSION['user_id'] = htmlspecialchars($_GET['user_id']);
            }
            
            // Set session cookie to last forever (until logout)
            setcookie(session_name(), session_id(), 0, '/');
    
    // Debug: Tampilkan informasi session
    if (isset($_GET['debug'])) {
        echo "<div style='background: #f8f9fa; padding: 20px; margin: 20px; border-radius: 10px; font-family: monospace;'>";
        echo "<h3>🔍 Debug Session Info</h3>";
        echo "<strong>PHP Session:</strong><br>";
        echo "Session ID: " . session_id() . "<br>";
        echo "Session Name: " . session_name() . "<br>";
        echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "<br>";
        echo "User Logged In: " . (isset($_SESSION['user_logged_in']) ? 'Yes' : 'No') . "<br>";
        echo "User Name: " . ($welcomeName ?? 'Not set') . "<br>";
        echo "Login Time: " . (isset($_SESSION['login_time']) ? date('Y-m-d H:i:s', $_SESSION['login_time']) : 'Not set') . "<br>";
        echo "Session Duration: " . (isset($_SESSION['login_time']) ? round((time() - $_SESSION['login_time']) / 60, 2) . ' minutes' : 'N/A') . "<br>";
        echo "<br><strong>Session Data:</strong><br>";
        echo "<pre>" . print_r($_SESSION, true) . "</pre>";
        echo "<br><strong>Cookies:</strong><br>";
        echo "<pre>" . print_r($_COOKIE, true) . "</pre>";
        echo "<br><strong>GET Parameters:</strong><br>";
        echo "<pre>" . print_r($_GET, true) . "</pre>";
        echo "<br><a href='index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Kembali</a>";
        echo "</div>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPTD TEKKOM - Sistem Informasi Biodata</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #2d5016 0%, #1a3d0e 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            background-image: url('rainforest.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Background Image */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('rainforest.jpg') no-repeat center center;
            background-size: cover;
            background-position: center;
            z-index: -1;
        }

        /* Welcome Message Animation */
        .welcome-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(76, 175, 80, 0.9);
            color: white;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            z-index: 1001;
            animation: slideDown 0.5s ease-out;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        /* Page Load Animation */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #2d5016 0%, #1a3d0e 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            animation: fadeOut 1s ease-out 2s forwards;
        }

        .loader-content {
            text-align: center;
            color: white;
        }

        .loader-logo {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            animation: logoFloat 2s ease-in-out infinite;
        }

        .loader-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #4CAF50;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        .loader-text {
            font-size: 1.2rem;
            font-weight: 500;
            opacity: 0.9;
            animation: textPulse 1.5s ease-in-out infinite;
        }

        /* Hero Animation */
        .hero-headline {
            opacity: 0;
            transform: translateX(-100px);
        }

        .hero-subtitle {
            opacity: 0;
            transform: translateX(-100px);
        }

        .hero-description {
            opacity: 0;
            transform: translateX(-100px);
        }

        .learn-more-btn {
            opacity: 0;
            transform: translateX(-100px);
        }

        /* Widget Animation */
        .booking-widget {
            opacity: 0;
            transform: translateX(100px);
        }

        /* Clock Animation */
        .real-time-clock {
            animation: clockPulse 2s ease-in-out infinite;
        }

        .time {
            animation: timeGlow 3s ease-in-out infinite;
        }

        /* Data Animation */
        .data-summary {
            opacity: 0;
            transform: translateY(50px);
        }

        .summary-item {
            opacity: 0;
            transform: translateY(30px);
        }

        .summary-item:nth-child(2) {
            animation-delay: 0.2s;
        }

        /* Weather Animation */
        .weather-icon {
            animation: weatherFloat 3s ease-in-out infinite;
        }

        /* Background Animation */
        body::before {
            animation: backgroundShift 20s ease-in-out infinite;
        }

        /* Floating Elements */
        .floating-element {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .floating-element:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }

        .floating-element:nth-child(3) {
            bottom: 30%;
            left: 20%;
            animation-delay: 4s;
        }



        @keyframes slideDown {
            from {
                transform: translateX(-50%) translateY(-100px);
                opacity: 0;
            }
            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }

        .welcome-message.fade-out {
            animation: fadeOut 0.5s ease-out forwards;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateX(-50%) translateY(-50px);
            }
        }

        /* Animation Keyframes */
        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes textPulse {
            0%, 100% { opacity: 0.9; }
            50% { opacity: 1; }
        }

        @keyframes heroSlideIn {
            from {
                transform: translateX(-100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes widgetSlideIn {
            from {
                transform: translateX(100px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes clockPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }

        @keyframes timeGlow {
            0%, 100% { text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3); }
            50% { text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3), 0 0 20px rgba(255, 255, 255, 0.5); }
        }

        @keyframes dataSlideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes itemBounce {
            0% {
                transform: translateY(30px);
                opacity: 0;
            }
            60% {
                transform: translateY(-10px);
                opacity: 1;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes backgroundShift {
            0%, 100% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.05) rotate(1deg); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        @keyframes weatherFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Header Navigation */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s ease;
        }

        .nav-links a:hover {
            opacity: 0.8;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .country-selector {
            color: white;
            font-weight: 500;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-btn {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Main Content */
        .main-content {
            padding-top: 100px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
        }

        .content-left {
            flex: 1;
            padding: 2rem 4rem 2rem 2rem;
            color: white;
            max-width: calc(80% - 400px);
            min-height: 0;
           
            flex-direction: column;
            justify-content: center;
            box-sizing: border-box;
            overflow-wrap: break-word;
            word-wrap: break-word;
            overflow: hidden;
        }

        .hero-headline {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
            max-width: 100%;
            width: 100%;
            box-sizing: border-box;
            white-space: normal;
            word-break: break-word;
            overflow: hidden;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            font-weight: 500;
            margin-bottom: 1rem;
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
            max-width: 100%;
            width: 100%;
            box-sizing: border-box;
        }

        .hero-description {
            font-size: 1.1rem;
            font-weight: 400;
            margin-bottom: 2rem;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            max-width: 400px;
            width: 100%;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            word-wrap: break-word;
            overflow-wrap: break-word;
            box-sizing: border-box;
        }

        .learn-more-btn {
            display: inline-block;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 1rem 2rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 3rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .learn-more-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }



        /* Real-time Clock */
        .real-time-clock {
            text-align: center;
            margin-bottom: 1.2rem;
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .time {
            font-size: 1.7rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.4rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .date {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }

        /* Booking Widget */
        .booking-widget {
            position: absolute;
            top: 10%;
            right: 8rem;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 1.5rem;
            width: 380px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Smaller booking widget for logged in users */
        .booking-widget.logged-in {
            width: 320px;
            padding: 1.2rem;
            right: 6rem;
        }

        .booking-widget.logged-in .widget-title {
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .booking-widget.logged-in .data-summary {
            margin-bottom: 1rem;
            padding: 0.6rem;
        }

        .booking-widget.logged-in .summary-item {
            gap: 0.8rem;
        }

        .booking-widget.logged-in .summary-value {
            font-size: 1.3rem;
        }

        .booking-widget.logged-in .recent-title {
            font-size: 1rem;
            margin-bottom: 0.6rem;
        }

        .booking-widget.logged-in .recent-data {
            margin-top: 1rem;
            padding: 0.6rem;
        }

        .booking-widget.logged-in .current-data {
            padding: 1rem;
        }

        .booking-widget.logged-in .detail-item {
            padding: 0.3rem 0;
            gap: 0.5rem;
        }

        .booking-widget.logged-in .detail-label {
            font-size: 0.8rem;
            min-width: 80px;
        }

        .booking-widget.logged-in .detail-value {
            font-size: 0.8rem;
        }

        .booking-widget.logged-in .data-date {
            font-size: 0.7rem;
            margin-top: 0.6rem;
            padding-top: 0.5rem;
        }

        .widget-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: white;
            margin-bottom: 1.2rem;
            text-align: center;
        }

        .data-summary {
            display: flex;
            justify-content: space-around;
            margin-bottom: 1.2rem;
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .summary-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .summary-label {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 0.25rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .summary-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .recent-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
            color: white;
            text-align: center;
        }

        .recent-data {
            margin-top: 1.2rem;
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .current-data {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1.2rem;
        }

        .data-name {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.95);
            font-weight: 600;
            margin-bottom: 0.6rem;
            text-align: center;
        }

        .data-details {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.4rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
            min-width: 90px;
        }

        .detail-value {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        .data-date {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.6);
            font-style: italic;
            text-align: center;
            margin-top: 0.8rem;
            padding-top: 0.6rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .no-data {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-style: italic;
            padding: 2rem;
        }

        /* Weather specific styles */
        .weather-icon {
            font-size: 2rem;
            margin-bottom: 0.4rem;
            text-align: center;
            animation: weatherFloat 3s ease-in-out infinite;
        }

        @keyframes weatherFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .weather-temp {
            font-size: 1.5rem;
            font-weight: 700;
            color: #FFD700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .weather-temp:hover {
            transform: scale(1.1);
        }

        .weather-condition {
            color: #87CEEB;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .weather-condition:hover {
            color: #5FB6E5;
        }

        .weather-humidity {
            color: #98FB98;
            transition: all 0.3s ease;
        }

        .weather-humidity:hover {
            color: #50C878;
        }

        .weather-wind {
            color: #DDA0DD;
            transition: all 0.3s ease;
        }

        .weather-wind:hover {
            color: #BA55D3;
        }

        /* Weather animation classes */
        .weather-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Weather icon animations */
        .weather-sun {
            color: #FFD700;
            animation: rotateSun 10s linear infinite;
        }

        @keyframes rotateSun {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .weather-cloud {
            color: #87CEEB;
            animation: floatCloud 5s ease-in-out infinite;
        }

        @keyframes floatCloud {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(10px); }
        }

        .weather-rain {
            color: #4682B4;
            animation: rain 1s linear infinite;
        }

        @keyframes rain {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(10px) rotate(10deg); }
        }

        .weather-wind-icon {
            color: #B0C4DE;
            animation: wind 2s ease-in-out infinite;
        }

        @keyframes wind {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1) translateX(5px); }
        }

        /* Session and logout styles */
        .logout-btn {
            background: rgba(220, 53, 69, 0.8) !important;
            border-color: rgba(220, 53, 69, 0.5) !important;
        }

        .logout-btn:hover {
            background: rgba(220, 53, 69, 0.9) !important;
            border-color: rgba(220, 53, 69, 0.7) !important;
        }

        .session-info {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .session-duration {
            color: #FFD700;
            font-weight: 600;
        }
        /* Responsive Design */
        @media (max-width: 1200px) {
            .booking-widget {
                position: static;
                transform: none;
                margin: 2rem auto;
                width: 100%;
                max-width: 380px;
            }
            
            .main-content {
                flex-direction: column;
                padding-top: 120px;
            }
            
            .content-left {
                padding: 2rem;
                max-width: 800px;
                margin: 0 auto;
                width: 100%;
                overflow: hidden;
            }
            
            .hero-headline {
                font-size: 2.5rem;
                line-height: 1.3;
                max-width: 100%;
                word-break: break-word;
                white-space: normal;
                overflow: hidden;
            }
            
            .hero-subtitle {
                font-size: 1.3rem;
                line-height: 1.4;
                max-width: 100%;
                word-break: break-word;
            }
            
            .hero-description {
                font-size: 1rem;
                max-width: 100%;
                word-break: break-word;
            }

            .data-summary {
                flex-direction: column;
                gap: 1rem;
            }

            .summary-item {
                justify-content: center;
            }

            .current-data {
                padding: 1.2rem;
            }

            .detail-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.3rem;
                padding: 0.4rem 0;
            }

            .detail-label {
                min-width: auto;
                font-weight: 600;
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .content-left {
                padding: 1rem;
                max-width: 100%;
                width: 100%;
                overflow: hidden;
            }
            
            .hero-headline {
                font-size: 2rem;
                line-height: 1.4;
                word-break: break-word;
                max-width: 100%;
                white-space: normal;
                overflow: hidden;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
                line-height: 1.5;
                word-break: break-word;
                max-width: 100%;
            }
            
            .hero-description {
                font-size: 0.9rem;
                max-width: 100%;
                word-break: break-word;
            }

            .booking-widget {
                margin: 1rem;
                padding: 1.2rem;
                width: calc(100% - 2rem);
            }

            .widget-title {
                font-size: 1.3rem;
            }

            .summary-value {
                font-size: 1.5rem;
            }

            .recent-title {
                font-size: 1.1rem;
            }

            .time {
                font-size: 1.5rem;
            }

            .date {
                font-size: 0.8rem;
            }

            .data-name {
                font-size: 1rem;
            }

            .detail-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.2rem;
                padding: 0.3rem 0;
            }

            .detail-label {
                min-width: auto;
                font-size: 0.85rem;
            }

            .detail-value {
                font-size: 0.85rem;
            }

            .weather-icon {
                font-size: 1.5rem;
            }

            .weather-temp {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 480px) {
            .hero-headline {
                font-size: 1.8rem;
                line-height: 1.5;
                max-width: 100%;
                word-break: break-word;
                white-space: normal;
                overflow: hidden;
            }
            

            
            .hero-subtitle {
                font-size: 1rem;
                line-height: 1.6;
                max-width: 100%;
                word-break: break-word;
            }
            
            .welcome-message {
                font-size: 14px;
                padding: 12px 20px;
                max-width: 90%;
            }

            .learn-more-btn {
                padding: 0.7rem 1.2rem;
                font-size: 0.85rem;
                margin-bottom: 1.5rem;
                width: 100%;
                max-width: 200px;
            }

            .content-left {
                padding: 0.5rem;
                max-width: 100%;
                width: 100%;
                overflow: hidden;
            }

            .booking-widget {
                padding: 0.8rem;
                margin: 0.5rem;
                width: calc(100% - 1rem);
            }

            .data-summary {
                flex-direction: column;
                gap: 0.5rem;
            }

            .summary-item {
                padding: 0.8rem;
            }

            .summary-value {
                font-size: 1.3rem;
            }

            .time {
                font-size: 1.3rem;
            }

            .date {
                font-size: 0.7rem;
            }

            .current-data {
                padding: 1rem;
            }

            .data-name {
                font-size: 0.9rem;
                margin-bottom: 0.6rem;
            }

            .detail-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.2rem;
                padding: 0.3rem 0;
                margin-bottom: 0.2rem;
            }

            .detail-label {
                min-width: auto;
                font-size: 0.8rem;
                font-weight: 600;
            }

            .detail-value {
                font-size: 0.8rem;
            }

            .data-date {
                font-size: 0.7rem;
                margin-top: 0.8rem;
                padding-top: 0.6rem;
            }

            .weather-icon {
                font-size: 1.3rem;
            }

            .weather-temp {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Page Loader -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-content">
            <div class="loader-logo">
                <i class="fas fa-building"></i> UPTD TEKKOM
            </div>
            <div class="loader-spinner"></div>
            <div class="loader-text">Memuat sistem...</div>
        </div>
    </div>

    <!-- Floating Elements -->
    <div class="floating-element">
        <i class="fas fa-cloud" style="font-size: 2rem; color: rgba(255, 255, 255, 0.3);"></i>
    </div>
    <div class="floating-element">
        <i class="fas fa-leaf" style="font-size: 1.5rem; color: rgba(255, 255, 255, 0.2);"></i>
    </div>
    <div class="floating-element">
        <i class="fas fa-tree" style="font-size: 2.5rem; color: rgba(255, 255, 255, 0.25);"></i>
    </div>

    <?php if (!empty($welcomeName)): ?>
    <div class="welcome-message" id="welcomeMessage">
        <i class="fas fa-check-circle"></i> Selamat datang, <?= $welcomeName ?>! 🎉
        <?php if (isset($_GET['auto_login'])): ?>
            <span style="margin-left: 10px; background: rgba(40, 167, 69, 0.2); color: #28a745; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                <i class="fas fa-sync-alt"></i> Auto-login
            </span>
        <?php endif; ?>
       
    </div>
    <?php endif; ?>

    <!-- Header Navigation -->
    <!-- <header class="header">
                        <div class="logo">UPTD TEKKOM</div>
        <nav>
            <ul class="nav-links">
                <li><a href="#">Book</a></li>
                <li><a href="#">Manage</a></li>
                <li><a href="#">Experience</a></li>
                <li><a href="#">Loyalty</a></li>
                <li><a href="#">Help</a></li>
            </ul>
        </nav>
        <div class="nav-right">
            <div class="country-selector">CA</div>
            <a href="#" class="login-btn">Login</a>
        </div>
    </header> -->

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-left">
            <h1 class="hero-headline">
                <?php if (!empty($welcomeName)): ?>
                    Selamat datang, <?= $welcomeName ?>! di UPTD TEKKOM
                <?php else: ?>
                    Selamat Datang di UPTD TEKKOM
                <?php endif; ?>
            </h1>
            <p class="hero-subtitle">Unit Pelaksana Teknis Daerah Teknologi dan Komunikasi</p>
            <p class="hero-description">Melayani kebutuhan teknologi informasi dan komunikasi untuk kemajuan daerah</p>
            <?php if (!empty($welcomeName)): ?>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="absensi.php" class="learn-more-btn">Isi Absensi</a>
                    <a href="mydata.php" class="learn-more-btn">Dashboard</a>
                </div>
            <?php else: ?>
                <a href="biodata.php" class="learn-more-btn">Isi Biodata</a>
            <?php endif; ?>
        </div>
    </main>

    <!-- Data Widget -->
    <div class="booking-widget<?php echo !empty($welcomeName) ? ' logged-in' : ''; ?>">
        <div class="real-time-clock">
            <div class="time" id="current-time">00:00:00</div>
            <div class="date" id="current-date">Loading...</div>
        </div>
        <h3 class="widget-title" id="widgetTitle">
            <?php if (!empty($welcomeName)): ?>
                Biodata Saya
            <?php else: ?>
                Informasi Cuaca
            <?php endif; ?>
        </h3>
        
        <?php if (!empty($welcomeName)): ?>
        <!-- Biodata Section for logged in users -->
        <div class="data-summary">
            <div class="summary-item">
                <div class="summary-label">Total Data</div>
                <div class="summary-value" id="totalData">0</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Hari Ini</div>
                <div class="summary-value" id="todayData">0</div>
            </div>
        </div>
        
        <h4 class="recent-title">Data & Status User</h4>
        <div class="recent-data">
            <div class="current-data" id="currentDataDisplay">
                <div class="data-name">Memuat data...</div>
                <div class="data-details">
                    <div class="detail-item">
                        <span class="detail-label">Tanggal Lahir:</span>
                        <span class="detail-value">Loading...</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Asal Sekolah:</span>
                        <span class="detail-value">Loading...</span>
                    </div>
                </div>
                <div class="data-date">Loading...</div>
            </div>
        </div>
        
        <!-- Weather Info for logged in users -->
        <h4 class="recent-title">🌤️ Cuaca Hari Ini</h4>
        <div class="recent-data">
            <div class="current-data">
                <div class="data-name">
                    <i class="fas fa-cloud-sun" style="color: #ffd700; margin-right: 0.5rem;"></i>
                    <span id="weatherLocation">UPTD TEKKOM</span>
                </div>
                <div class="data-details">
                    <div class="detail-item">
                        <span class="detail-label">Suhu:</span>
                        <span class="detail-value" id="weatherTemp">Loading...</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Kelembaban:</span>
                        <span class="detail-value" id="weatherHumidity">Loading...</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Kondisi:</span>
                        <span class="detail-value" id="weatherCondition">Loading...</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Angin:</span>
                        <span class="detail-value" id="weatherWind">Loading...</span>
                    </div>
                </div>
                <div class="data-date" id="weatherTime">Update: Loading...</div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div style="text-align: center; margin-top: 1rem;">
            <button onclick="refreshWeather()" style="
                background: rgba(40, 167, 69, 0.8);
                color: white;
                border: none;
                padding: 0.5rem 1.5rem;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                margin-right: 0.5rem;
            " onmouseover="this.style.background='rgba(40, 167, 69, 0.9)'" 
               onmouseout="this.style.background='rgba(40, 167, 69, 0.8)'">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>

            <a href="?logout=1" style="
                background: rgba(220, 53, 69, 0.8);
                color: white;
                text-decoration: none;
                padding: 0.5rem 1.5rem;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 500;
                border: 1px solid rgba(220, 53, 69, 0.5);
                transition: all 0.3s ease;
                display: inline-block;
            " onmouseover="this.style.background='rgba(220, 53, 69, 0.9)'" 
               onmouseout="this.style.background='rgba(220, 53, 69, 0.8)'">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
        
        <!-- Weather Test Section -->

        <?php else: ?>
        <!-- Weather Section for non-logged in users -->
        <div class="data-summary">
            <div class="summary-item">
                <div class="summary-label">Lokasi</div>
                <div class="summary-value" id="weatherLocation">Mendeteksi...</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Update</div>
                <div class="summary-value" id="weatherUpdate">Live</div>
            </div>
        </div>
        <div style="text-align: center; margin-bottom: 1rem;">
            <button id="refreshWeatherBtn" style="
                background: rgba(255, 255, 255, 0.1);
                color: white;
                border: 1px solid rgba(255, 255, 255, 0.3);
                padding: 0.5rem 1rem;
                border-radius: 15px;
                cursor: pointer;
                font-size: 0.8rem;
                transition: all 0.3s ease;
                margin-right: 0.5rem;
            " onmouseover="this.style.background='rgba(255, 255, 255, 0.2)'" 
               onmouseout="this.style.background='rgba(255, 255, 255, 0.1)'">
                <i class="fas fa-sync-alt"></i> Refresh Cuaca
            </button>
            <button id="testSamarindaBtn" style="
                background: rgba(255, 255, 255, 0.1);
                color: white;
                border: 1px solid rgba(255, 255, 255, 0.3);
                padding: 0.5rem 1rem;
                border-radius: 15px;
                cursor: pointer;
                font-size: 0.8rem;
                transition: all 0.3s ease;
            " onmouseover="this.style.background='rgba(255, 255, 255, 0.2)'" 
               onmouseout="this.style.background='rgba(255, 255, 255, 0.1)'">
                <i class="fas fa-map-marker-alt"></i> Test Samarinda
            </button>
        </div>
        
        <h4 class="recent-title">Kondisi Cuaca Saat Ini</h4>
        <div class="recent-data">
            <div class="current-data" id="weatherDisplay">
                <div class="data-name">Mendeteksi lokasi...</div>
                <div class="data-details">
                    <div class="detail-item">
                        <span class="detail-label">Suhu:</span>
                        <span class="detail-value" id="weatherTemp">Mendapatkan data...</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Kelembaban:</span>
                        <span class="detail-value" id="weatherHumidity">Mendapatkan data...</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Kondisi:</span>
                        <span class="detail-value" id="weatherCondition">Mendapatkan data...</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Kecepatan Angin:</span>
                        <span class="detail-value" id="weatherWind">Mendapatkan data...</span>
                    </div>
                </div>
                <div class="data-date" id="weatherTime">Mendeteksi lokasi Anda...</div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Page Loader Control
        window.addEventListener('load', function() {
            setTimeout(function() {
                const pageLoader = document.getElementById('pageLoader');
                if (pageLoader) {
                    pageLoader.style.animation = 'fadeOut 1s ease-out forwards';
                    setTimeout(function() {
                        pageLoader.style.display = 'none';
                    }, 1000);
                }
            }, 2000);
        });

        // Welcome message auto-hide
        <?php if (!empty($welcomeName)): ?>
        setTimeout(function() {
            const welcomeMessage = document.getElementById('welcomeMessage');
            if (welcomeMessage) {
                welcomeMessage.classList.add('fade-out');
                setTimeout(function() {
                    welcomeMessage.style.display = 'none';
                }, 500);
            }
        }, 5000); // Hide after 5 seconds
        <?php endif; ?>

        // Real-time clock function
        function updateClock() {
            const now = new Date();
            const timeElement = document.getElementById('current-time');
            const dateElement = document.getElementById('current-date');
            
            // Format time
            const timeString = now.toLocaleTimeString('id-ID', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            
            // Format date
            const dateString = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            timeElement.textContent = timeString;
            dateElement.textContent = dateString;
        }

        // Update clock every second
        updateClock();
        setInterval(updateClock, 1000);

        // Check if user is logged in
        const isLoggedIn = <?php echo !empty($welcomeName) ? 'true' : 'false'; ?>;
        const userName = '<?php echo htmlspecialchars($welcomeName ?? ''); ?>';
        const loginTime = '<?php echo date('d/m/Y H:i', $_SESSION['login_time'] ?? time()); ?>';
        const isLate = <?php echo isset($_SESSION['is_late']) && $_SESSION['is_late'] ? 'true' : 'false'; ?>;
        
        // Simple late warning alert
        <?php if (isset($_SESSION['is_late']) && $_SESSION['is_late']): ?>
        setTimeout(function() {
            const loginTime = '<?php echo date('H:i', $_SESSION['login_time']); ?>';
            const lateMinutes = Math.floor((<?php echo (int)date('H', $_SESSION['login_time']); ?> - 8) * 60 + (<?php echo (int)date('i', $_SESSION['login_time']); ?> - 30));
            
            alert('⚠️ PERINGATAN KETERLAMBATAN!\n\n' +
                  'Anda login terlambat!\n' +
                  'Waktu login: ' + loginTime + '\n' +
                  'Keterlambatan: ' + lateMinutes + ' menit\n' +
                  'Batas waktu: 08:30 pagi\n\n' +
                  'Silakan hubungi supervisor untuk konfirmasi.');
        }, 2000);
        <?php endif; ?>
        
        // Session management (Permanent until logout)
        function checkSessionStatus() {
            if (isLoggedIn) {
                console.log('✅ User logged in (Permanent):', userName);
                console.log('🕒 Login time:', loginTime);
                console.log('🔒 Session type: Permanent (until manual logout)');
                
                // Check localStorage status
                const storedSession = loadSessionFromStorage();
                if (storedSession) {
                    console.log('💾 LocalStorage backup found:', storedSession);
                    console.log('📅 Last activity:', new Date(storedSession.lastActivity).toLocaleString());
                } else {
                    console.log('⚠️ No LocalStorage backup found');
                }
                
                // Update session info
                updateSessionInfo();
                
                // Set up session refresh
                setInterval(() => {
                    refreshSessionData();
                }, 60000); // Refresh every minute
            } else {
                console.log('❌ User not logged in');
                
                // Check if there's stored session data
                const storedSession = loadSessionFromStorage();
                if (storedSession) {
                    console.log('📱 Found stored session data:', storedSession);
                    console.log('🔄 Auto-login available');
                } else {
                    console.log('📱 No stored session data found');
                }
            }
        }
        
        // Update session duration in real-time
        function updateSessionDuration() {
            if (isLoggedIn) {
                const sessionDurationElement = document.getElementById('sessionDuration');
                if (sessionDurationElement) {
                    const now = new Date();
                    const loginDate = new Date('<?php echo date('Y-m-d H:i:s', $_SESSION['login_time'] ?? time()); ?>');
                    const timeDiff = Math.floor((now - loginDate) / 1000 / 60); // minutes
                    
                    // Calculate days, hours, minutes
                    const days = Math.floor(timeDiff / 1440);
                    const hours = Math.floor((timeDiff % 1440) / 60);
                    const minutes = timeDiff % 60;
                    
                    let durationText = '';
                    if (days > 0) {
                        durationText = `${days} hari, ${hours} jam, ${minutes} menit`;
                    } else if (hours > 0) {
                        durationText = `${hours} jam, ${minutes} menit`;
                    } else {
                        durationText = `${minutes} menit`;
                    }
                    
                    sessionDurationElement.textContent = durationText;
                }
            }
        }
        
        function updateSessionInfo() {
            const sessionInfo = document.querySelector('.session-info');
            if (sessionInfo) {
                const now = new Date();
                const loginDate = new Date('<?php echo date('Y-m-d H:i:s', $_SESSION['login_time'] ?? time()); ?>');
                const timeDiff = Math.floor((now - loginDate) / 1000 / 60); // minutes
                
                // Calculate days, hours, minutes
                const days = Math.floor(timeDiff / 1440);
                const hours = Math.floor((timeDiff % 1440) / 60);
                const minutes = timeDiff % 60;
                
                let durationText = '';
                if (days > 0) {
                    durationText = `${days} hari, ${hours} jam, ${minutes} menit`;
                } else if (hours > 0) {
                    durationText = `${hours} jam, ${minutes} menit`;
                } else {
                    durationText = `${minutes} menit`;
                }
                
                sessionInfo.innerHTML = `
                    <div style="text-align: center; margin-bottom: 0.5rem;">
                        <i class="fas fa-user-check" style="color: #28a745; margin-right: 0.5rem;"></i>
                        <span style="color: white; font-weight: 600;">Status Login Permanen</span>
                    </div>
                    <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.9);">
                        <div style="margin-bottom: 0.3rem;">
                            <i class="fas fa-user"></i> User: ${userName}
                        </div>
                        <div style="margin-bottom: 0.3rem;">
                            <i class="fas fa-clock"></i> Login: ${loginTime}
                        </div>
                        <div style="margin-bottom: 0.3rem;">
                            <i class="fas fa-hourglass-half"></i> Durasi: ${durationText}
                        </div>
                        <div style="margin-bottom: 0.3rem;">
                            <i class="fas fa-shield-alt"></i> Session: Aktif (Permanen)
                        </div>
                        <div style="margin-bottom: 0.3rem;">
                            <i class="fas fa-database"></i> Storage: LocalStorage + Session
                        </div>
                        <div>
                            <i class="fas fa-info-circle"></i> Auto-login: Aktif (30 hari)
                        </div>
                    </div>
                `;
            }
        }
        
        function refreshSessionData() {
            if (isLoggedIn) {
                console.log('🔄 Refreshing session data...');
                // Refresh biodata stats
                loadDataStats();
                loadRecentData();
                // Update session info
                updateSessionInfo();
            }
        }
        
        // Enhanced LocalStorage management for permanent login
        function saveSessionToStorage() {
            if (isLoggedIn) {
                const sessionData = {
                    userName: userName,
                    loginTime: loginTime,
                    timestamp: Date.now(),
                    isLoggedIn: true,
                    permanent: true,
                    sessionId: '<?php echo session_id(); ?>',
                    lastActivity: Date.now()
                };
                localStorage.setItem('userSession', JSON.stringify(sessionData));
                console.log('💾 Session data saved to localStorage (Permanent)');
                
                // Also save to sessionStorage for immediate access
                sessionStorage.setItem('userSession', JSON.stringify(sessionData));
            }
        }
        
        function loadSessionFromStorage() {
            // Try sessionStorage first (for current session)
            let sessionData = sessionStorage.getItem('userSession');
            if (!sessionData) {
                // Fallback to localStorage (for persistent storage)
                sessionData = localStorage.getItem('userSession');
            }
            
            if (sessionData) {
                const data = JSON.parse(sessionData);
                console.log('📱 Session data found in storage:', data);
                
                // Check if session is still valid (not expired)
                const now = Date.now();
                const lastActivity = data.lastActivity || data.timestamp;
                const sessionAge = now - lastActivity;
                
                // Session is valid if less than 30 days old
                if (sessionAge < (30 * 24 * 60 * 60 * 1000)) {
                    return data;
                } else {
                    console.log('⏰ Session expired, clearing storage');
                    clearSessionStorage();
                }
            }
            return null;
        }
        
        function clearSessionStorage() {
            localStorage.removeItem('userSession');
            sessionStorage.removeItem('userSession');
            console.log('🗑️ Session data cleared from all storage');
        }
        
        // Auto-login function
        function attemptAutoLogin() {
            const storedSession = loadSessionFromStorage();
            if (storedSession && storedSession.isLoggedIn) {
                console.log('🔄 Attempting auto-login for:', storedSession.userName);
                
                // Check if we're not already logged in via PHP session
                if (!isLoggedIn) {
                    // Redirect to login with stored data
                    const loginUrl = `index.php?welcome=${encodeURIComponent(storedSession.userName)}&auto_login=1`;
                    console.log('🔄 Redirecting to auto-login:', loginUrl);
                    window.location.href = loginUrl;
                    return true;
                }
            }
            return false;
        }
        
        // Update session activity
        function updateSessionActivity() {
            if (isLoggedIn) {
                const storedSession = loadSessionFromStorage();
                if (storedSession) {
                    storedSession.lastActivity = Date.now();
                    localStorage.setItem('userSession', JSON.stringify(storedSession));
                    sessionStorage.setItem('userSession', JSON.stringify(storedSession));
                }
            }
        }

        // Load data statistics (only for logged in users)
        function loadDataStats() {
            if (!isLoggedIn) return;
            
            fetch('../config/app.php?action=stats')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('totalData').textContent = data.stats.total_records || 0;
                        document.getElementById('todayData').textContent = data.stats.today_registrations || 0;
                    }
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                });
        }

        // Load recent data (only for logged in users)
        function loadRecentData() {
            if (!isLoggedIn) return;
            
            fetch('../config/app.php?action=get_all')
                .then(response => response.json())
                .then(data => {
                    const currentDataDisplay = document.getElementById('currentDataDisplay');
                    if (data.status === 'success' && data.data && data.data.length > 0) {
                        const latestData = data.data[0]; // Get the latest entry
                        currentDataDisplay.innerHTML = `
                            <div class="data-name">${latestData.nama_lengkap}</div>
                            <div class="data-details">
                                <div class="detail-item">
                                    <span class="detail-label">Tanggal Lahir:</span>
                                    <span class="detail-value">${formatDate(latestData.tanggal_lahir)}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Asal Sekolah:</span>
                                    <span class="detail-value">${latestData.asal_sekolah}</span>
                                </div>
                            </div>
                            <div class="data-date">Didaftarkan ${getTimeAgo(new Date(latestData.created_at))}</div>
                        `;
                    } else {
                        currentDataDisplay.innerHTML = '<div class="data-name">Belum ada data</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading recent data:', error);
                    document.getElementById('currentDataDisplay').innerHTML = '<div class="data-name">Error loading data</div>';
                });
        }

        // Load weather data (only for non-logged in users)
        function loadWeatherData() {
            if (isLoggedIn) return;
            
            console.log('Loading weather data...');
            
            // Show loading state
            const weatherDisplay = document.getElementById('weatherDisplay');
            if (weatherDisplay) {
                weatherDisplay.innerHTML = `
                    <div class="data-name">Mendeteksi lokasi...</div>
                    <div class="data-details">
                        <div class="detail-item">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value">Meminta izin lokasi...</span>
                        </div>
                    </div>
                    <div class="data-date">Mohon izinkan akses lokasi</div>
                `;
            }
            
            // Try to get user's location
            if (navigator.geolocation) {
                console.log('Geolocation supported, requesting location...');
                
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        console.log('✅ Location obtained:', position.coords);
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        
                        // Check if location is near Samarinda (within ~50km radius)
                        const samarindaLat = -0.5021;
                        const samarindaLon = 117.1536;
                        const distance = calculateDistance(lat, lon, samarindaLat, samarindaLon);
                        
                        console.log('Distance from Samarinda:', distance, 'km');
                        
                        if (distance < 50) {
                            console.log('✅ Location is near Samarinda, using exact coordinates');
                            fetchWeatherByLocation(lat, lon);
                        } else {
                            console.log('📍 Location is far from Samarinda, using Samarinda coordinates');
                            fetchWeatherByLocation(samarindaLat, samarindaLon);
                        }
                    },
                    function(error) {
                        console.log('❌ Location error:', error);
                        console.log('Error code:', error.code);
                        console.log('Error message:', error.message);
                        handleLocationError(error);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 60000
                    }
                );
            } else {
                console.log('❌ Geolocation not supported, using Samarinda coordinates');
                // Use Samarinda coordinates directly
                const samarindaLat = -0.5021;
                const samarindaLon = 117.1536;
                fetchWeatherByLocation(samarindaLat, samarindaLon);
            }
        }

        // Calculate distance between two coordinates in kilometers
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius of the Earth in kilometers
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                     Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                     Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            const distance = R * c;
            return distance;
        }

        // Fetch weather by coordinates
        function fetchWeatherByLocation(lat, lon) {
            // Using OpenWeatherMap API with Indonesian language
            const apiKey = '53c68c49b9cacdebc760e0be2fbb6b51';
            const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${apiKey}&units=metric&lang=id`;
            
            console.log('Fetching weather from:', url);
            
            // Show loading state
            const weatherDisplay = document.getElementById('weatherDisplay');
            if (weatherDisplay) {
                weatherDisplay.innerHTML = `
                    <div class="data-name">Mengambil data cuaca...</div>
                    <div class="data-details">
                        <div class="detail-item">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value">Menghubungi server...</span>
                        </div>
                    </div>
                    <div class="data-date">Koordinat: ${lat.toFixed(4)}, ${lon.toFixed(4)}</div>
                `;
            }
            
            fetch(url)
                .then(response => {
                    console.log('Weather API response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Weather API data:', data);
                    if (data.cod === 200) {
                        updateWeatherDisplay(data);
                        // Get city name from coordinates
                        getLocationName(lat, lon);
                    } else {
                        console.error('Weather API error:', data);
                        // Fallback to mock data if API fails
                        const mockWeatherData = generateWeatherByLocation(lat, lon);
                        updateWeatherDisplay(mockWeatherData);
                    }
                })
                .catch(error => {
                    console.error('Error fetching weather:', error);
                    // Show error state
                    if (weatherDisplay) {
                        weatherDisplay.innerHTML = `
                            <div class="data-name">Error Koneksi</div>
                            <div class="data-details">
                                <div class="detail-item">
                                    <span class="detail-label">Status:</span>
                                    <span class="detail-value">Gagal mengambil data</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Error:</span>
                                    <span class="detail-value">${error.message}</span>
                                </div>
                            </div>
                            <div class="data-date">Menggunakan data simulasi...</div>
                        `;
                    }
                    // Fallback to mock data if API fails
                    setTimeout(() => {
                        const mockWeatherData = generateWeatherByLocation(lat, lon);
                        updateWeatherDisplay(mockWeatherData);
                    }, 2000);
                });
        }

        // Fetch weather by city name
        function fetchWeatherByCity(city) {
            // Using OpenWeatherMap API with Indonesian language
            const apiKey = '53c68c49b9cacdebc760e0be2fbb6b51';
            const url = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric&lang=id`;
            
            console.log('🌍 Fetching weather for city:', city);
            
            // Check if user is logged in
            if (isLoggedIn) {
                // For logged in users, update the weather section directly
                console.log('👤 User is logged in, updating weather section...');
                
                // Show loading state for logged in users
                const weatherLocation = document.getElementById('weatherLocation');
                const weatherTemp = document.getElementById('weatherTemp');
                const weatherHumidity = document.getElementById('weatherHumidity');
                const weatherCondition = document.getElementById('weatherCondition');
                const weatherWind = document.getElementById('weatherWind');
                const weatherTime = document.getElementById('weatherTime');
                
                if (weatherLocation) weatherLocation.textContent = 'Mengambil data...';
                if (weatherTemp) weatherTemp.textContent = 'Loading...';
                if (weatherHumidity) weatherHumidity.textContent = 'Loading...';
                if (weatherCondition) weatherCondition.textContent = 'Loading...';
                if (weatherWind) weatherWind.textContent = 'Loading...';
                if (weatherTime) weatherTime.textContent = 'Update: Loading...';
            } else {
                // For non-logged in users, show loading state in weatherDisplay
                const weatherDisplay = document.getElementById('weatherDisplay');
                if (weatherDisplay) {
                    weatherDisplay.innerHTML = `
                        <div class="data-name">Mengambil data cuaca ${city}...</div>
                        <div class="data-details">
                            <div class="detail-item">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value">Menghubungi server...</span>
                            </div>
                        </div>
                        <div class="data-date">Menggunakan data kota ${city}</div>
                    `;
                }
            }
            
            fetch(url)
                .then(response => {
                    console.log('🌤️ Weather API response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('🌤️ Weather API data for city:', data);
                    if (data.cod === 200) {
                        if (isLoggedIn) {
                            // Update weather for logged in users
                            updateWeatherForLoggedInUser(data);
                        } else {
                            // Update weather for non-logged in users
                            updateWeatherDisplay(data);
                        }
                    } else {
                        console.error('❌ Weather API error:', data);
                        // Fallback to mock data if API fails
                        const mockWeatherData = {
                            main: {
                                temp: Math.floor(Math.random() * 15) + 25, // 25-40°C
                                humidity: Math.floor(Math.random() * 30) + 60 // 60-90%
                            },
                            weather: [{
                                description: getRandomWeatherCondition()
                            }],
                            wind: {
                                speed: Math.floor(Math.random() * 10) + 5 // 5-15 km/h
                            },
                            name: city
                        };
                        
                        if (isLoggedIn) {
                            updateWeatherForLoggedInUser(mockWeatherData);
                        } else {
                            updateWeatherDisplay(mockWeatherData);
                        }
                    }
                })
                .catch(error => {
                    console.error('❌ Error fetching weather:', error);
                    
                    if (isLoggedIn) {
                        // Show error state for logged in users
                        const weatherLocation = document.getElementById('weatherLocation');
                        const weatherTemp = document.getElementById('weatherTemp');
                        const weatherHumidity = document.getElementById('weatherHumidity');
                        const weatherCondition = document.getElementById('weatherCondition');
                        const weatherWind = document.getElementById('weatherWind');
                        const weatherTime = document.getElementById('weatherTime');
                        
                        if (weatherLocation) weatherLocation.textContent = 'Error koneksi';
                        if (weatherTemp) weatherTemp.textContent = 'Error';
                        if (weatherHumidity) weatherHumidity.textContent = 'Error';
                        if (weatherCondition) weatherCondition.textContent = 'Error';
                        if (weatherWind) weatherWind.textContent = 'Error';
                        if (weatherTime) weatherTime.textContent = 'Update: Error';
                    } else {
                        // Show error state for non-logged in users
                        const weatherDisplay = document.getElementById('weatherDisplay');
                        if (weatherDisplay) {
                            weatherDisplay.innerHTML = `
                                <div class="data-name">Error Koneksi</div>
                                <div class="data-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Status:</span>
                                        <span class="detail-value">Gagal mengambil data</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Error:</span>
                                        <span class="detail-value">${error.message}</span>
                                    </div>
                                </div>
                                <div class="data-date">Menggunakan data simulasi...</div>
                            `;
                        }
                    }
                    
                    // Fallback to mock data if API fails
                    setTimeout(() => {
                        const mockWeatherData = {
                            main: {
                                temp: Math.floor(Math.random() * 15) + 25, // 25-40°C
                                humidity: Math.floor(Math.random() * 30) + 60 // 60-90%
                            },
                            weather: [{
                                description: getRandomWeatherCondition()
                            }],
                            wind: {
                                speed: Math.floor(Math.random() * 10) + 5 // 5-15 km/h
                            },
                            name: city
                        };
                        
                        if (isLoggedIn) {
                            updateWeatherForLoggedInUser(mockWeatherData);
                        } else {
                            updateWeatherDisplay(mockWeatherData);
                        }
                    }, 2000);
                });
        }

        // Generate weather data based on location coordinates
        function generateWeatherByLocation(lat, lon) {
            // Simple algorithm to generate realistic weather based on location
            const temp = Math.floor(Math.random() * 20) + 15; // 15-35°C
            const humidity = Math.floor(Math.random() * 40) + 50; // 50-90%
            const windSpeed = Math.floor(Math.random() * 15) + 3; // 3-18 km/h
            
            // Determine weather condition based on location and time
            const hour = new Date().getHours();
            let condition;
            
            if (hour >= 6 && hour <= 18) {
                // Daytime
                if (temp > 30) {
                    condition = 'Cerah Berawan';
                } else if (temp > 25) {
                    condition = 'Cerah';
                } else {
                    condition = 'Berawan';
                }
            } else {
                // Nighttime
                condition = 'Mendung';
            }
            
            // Add some randomness
            if (Math.random() > 0.7) {
                condition = 'Hujan Ringan';
            }
            
            const weatherData = {
                main: {
                    temp: temp,
                    humidity: humidity
                },
                weather: [{
                    description: condition
                }],
                wind: {
                    speed: windSpeed
                },
                name: 'Lokasi Anda'
            };
            
            // Update weather display based on login status
            if (isLoggedIn) {
                updateWeatherForLoggedInUser(weatherData);
            } else {
                // For non-logged in users, this will be handled by the calling function
                console.log('🌤️ Generated mock weather data:', weatherData);
            }
            
            return weatherData;
        }

        // Get location name from coordinates using reverse geocoding
        function getLocationName(lat, lon) {
            const apiKey = '53c68c49b9cacdebc760e0be2fbb6b51';
            
            fetch(`https://api.openweathermap.org/geo/1.0/reverse?lat=${lat}&lon=${lon}&limit=1&appid=${apiKey}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const location = data[0];
                        const cityName = location.name || 'Lokasi Anda';
                        const weatherLocation = document.getElementById('weatherLocation');
                        if (weatherLocation) {
                            weatherLocation.textContent = cityName;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error getting location name:', error);
                    // Keep default name if reverse geocoding fails
                });
            
            return 'Lokasi Anda'; // Default return value
        }

        // Handle location permission errors
        function handleLocationError(error) {
            console.log('Location error:', error);
            const weatherDisplay = document.getElementById('weatherDisplay');
            
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    weatherDisplay.innerHTML = `
                        <div class="data-name">Izin Lokasi Ditolak</div>
                        <div class="data-details">
                            <div class="detail-item">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value">Menggunakan data Samarinda</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Pesan:</span>
                                <span class="detail-value">Izinkan akses lokasi untuk cuaca yang lebih akurat</span>
                            </div>
                        </div>
                        <div class="data-date">Menggunakan data Samarinda sebagai fallback</div>
                    `;
                    break;
                case error.POSITION_UNAVAILABLE:
                    weatherDisplay.innerHTML = `
                        <div class="data-name">Lokasi Tidak Tersedia</div>
                        <div class="data-details">
                            <div class="detail-item">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value">Menggunakan data Samarinda</span>
                            </div>
                        </div>
                        <div class="data-date">Menggunakan data Samarinda sebagai fallback</div>
                    `;
                    break;
                case error.TIMEOUT:
                    weatherDisplay.innerHTML = `
                        <div class="data-name">Timeout Mendeteksi Lokasi</div>
                        <div class="data-details">
                            <div class="detail-item">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value">Menggunakan data Samarinda</span>
                            </div>
                        </div>
                        <div class="data-date">Menggunakan data Samarinda sebagai fallback</div>
                    `;
                    break;
                default:
                    weatherDisplay.innerHTML = `
                        <div class="data-name">Error Mendeteksi Lokasi</div>
                        <div class="data-details">
                            <div class="detail-item">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value">Menggunakan data Samarinda</span>
                            </div>
                        </div>
                        <div class="data-date">Menggunakan data Samarinda sebagai fallback</div>
                    `;
            }
            
            // Fallback to Samarinda weather instead of Jakarta
            setTimeout(() => {
                fetchWeatherByCity('Samarinda');
            }, 2000);
        }

        // Helper function to get random weather condition
        function getRandomWeatherCondition() {
            const conditions = [
                'Cerah Berawan', 'Berawan', 'Hujan Ringan', 'Hujan Sedang', 
                'Cerah', 'Mendung', 'Berkabut', 'Angin Kencang'
            ];
            return conditions[Math.floor(Math.random() * conditions.length)];
        }

        // Update weather display
        function updateWeatherDisplay(weatherData) {
            const weatherDisplay = document.getElementById('weatherDisplay');
            const weatherLocation = document.getElementById('weatherLocation');
            if (!weatherDisplay) return;
            
            console.log('Updating weather display with data:', weatherData);
            
            // Check if weatherData has required properties
            if (!weatherData || !weatherData.weather || !weatherData.weather[0] || !weatherData.main) {
                console.error('Invalid weather data:', weatherData);
                weatherDisplay.innerHTML = `
                    <div class="data-name">Data Cuaca Tidak Valid</div>
                    <div class="data-details">
                        <div class="detail-item">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value">Error format data</span>
                        </div>
                    </div>
                    <div class="data-date">Silakan refresh halaman</div>
                `;
                return;
            }
            
            const weatherIcon = getWeatherIcon(weatherData.weather[0].description);
            
            // Update location display
            if (weatherLocation) {
                weatherLocation.textContent = weatherData.name || 'Lokasi Anda';
            }
            
            // Format temperature
            const temp = weatherData.main.temp ? Math.round(weatherData.main.temp) : 'N/A';
            const humidity = weatherData.main.humidity || 'N/A';
            const condition = weatherData.weather[0].description || 'Tidak diketahui';
            const windSpeed = weatherData.wind && weatherData.wind.speed ? Math.round(weatherData.wind.speed * 3.6) : 'N/A';
            
            weatherDisplay.innerHTML = `
                <div class="weather-icon">${weatherIcon}</div>
                <div class="data-name">${weatherData.name || 'Lokasi Anda'}</div>
                <div class="data-details">
                    <div class="detail-item">
                        <span class="detail-label">Suhu:</span>
                        <span class="detail-value weather-temp">${temp}°C</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Kelembaban:</span>
                        <span class="detail-value weather-humidity">${humidity}%</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Kondisi:</span>
                        <span class="detail-value weather-condition">${condition}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Kecepatan Angin:</span>
                        <span class="detail-value weather-wind">${windSpeed} km/h</span>
                    </div>
                </div>
                <div class="data-date">Update terakhir: ${new Date().toLocaleTimeString('id-ID')}</div>
            `;
            
            console.log('Weather display updated successfully');
        }

        // Helper function to get weather icon with animation
        function getWeatherIcon(condition) {
            const conditionLower = condition.toLowerCase();
            
            // Handle Indonesian weather descriptions from API with animations
            if (conditionLower.includes('cerah') || conditionLower.includes('clear')) {
                return '<i class="fas fa-sun weather-sun"></i>';
            } else if (conditionLower.includes('berawan') || conditionLower.includes('clouds')) {
                if (conditionLower.includes('sedikit') || conditionLower.includes('scattered')) {
                    return '<i class="fas fa-cloud-sun weather-cloud"></i>';
                } else {
                    return '<i class="fas fa-cloud weather-cloud"></i>';
                }
            } else if (conditionLower.includes('mendung') || conditionLower.includes('overcast')) {
                return '<i class="fas fa-cloud weather-cloud"></i>';
            } else if (conditionLower.includes('hujan') || conditionLower.includes('rain') || conditionLower.includes('drizzle')) {
                if (conditionLower.includes('ringan') || conditionLower.includes('light')) {
                    return '<i class="fas fa-cloud-rain weather-rain"></i>';
                } else if (conditionLower.includes('deras') || conditionLower.includes('heavy')) {
                    return '<i class="fas fa-cloud-showers-heavy weather-rain"></i>';
                } else {
                    return '<i class="fas fa-cloud-rain weather-rain"></i>';
                }
            } else if (conditionLower.includes('berkabut') || conditionLower.includes('mist') || conditionLower.includes('fog')) {
                return '<i class="fas fa-smog weather-cloud"></i>';
            } else if (conditionLower.includes('angin') || conditionLower.includes('wind')) {
                return '<i class="fas fa-wind weather-wind-icon"></i>';
            } else if (conditionLower.includes('badai') || conditionLower.includes('thunderstorm')) {
                return '<div class="weather-fade-in"><i class="fas fa-bolt"></i></div>';
            } else if (conditionLower.includes('salju') || conditionLower.includes('snow')) {
                return '<i class="fas fa-snowflake weather-fade-in"></i>';
            } else {
                return '<i class="fas fa-cloud weather-cloud"></i>';
            }
        }
        
        // Function to get weather background color based on temperature
        function getWeatherColor(temp) {
            if (temp >= 30) {
                return '#FF6B6B'; // Hot
            } else if (temp >= 25) {
                return '#FFD93D'; // Warm
            } else if (temp >= 20) {
                return '#6BCB77'; // Pleasant
            } else if (temp >= 15) {
                return '#4D96FF'; // Cool
            } else {
                return '#B4B4B8'; // Cold
            }
        }

        // Helper function to get time ago
        function getTimeAgo(date) {
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) {
                return 'Baru saja';
            } else if (diffInSeconds < 3600) {
                const minutes = Math.floor(diffInSeconds / 60);
                return `${minutes} menit yang lalu`;
            } else if (diffInSeconds < 86400) {
                const hours = Math.floor(diffInSeconds / 3600);
                return `${hours} jam yang lalu`;
            } else {
                const days = Math.floor(diffInSeconds / 86400);
                return `${days} hari yang lalu`;
            }
        }

        // Helper function to format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        }

        // Test API key function
        function testAPIKey() {
            const apiKey = '53c68c49b9cacdebc760e0be2fbb6b51';
            const testUrl = `https://api.openweathermap.org/data/2.5/weather?q=Samarinda&appid=${apiKey}&units=metric&lang=id`;
            
            console.log('Testing API key with Samarinda URL:', testUrl);
            
            fetch(testUrl)
                .then(response => {
                    console.log('API test response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('API test data for Samarinda:', data);
                    if (data.cod === 200) {
                        console.log('✅ API key is working with Samarinda!');
                    } else {
                        console.error('❌ API key error:', data);
                    }
                })
                .catch(error => {
                    console.error('❌ API test failed:', error);
                });
        }

        // Check for clear parameter (logout)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('clear') === '1') {
            console.log('🧹 Clearing localStorage due to logout');
            clearSessionStorage();
        }
        
        // Function to get weather data for logged in users
        function refreshWeather() {
            console.log('🔄 Refreshing weather for logged in user...');
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        const apiKey = '53c68c49b9cacdebc760e0be2fbb6b51';
                        const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${apiKey}&units=metric&lang=id`;
                        
                        console.log('📍 Location obtained:', lat, lon);
                        
                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                console.log('✅ Weather data received:', data);
                                
                                if (data.cod === 200) {
                                    // Update weather display for logged in users
                                    updateWeatherForLoggedInUser(data);
                                } else {
                                    console.error('❌ Weather API error:', data);
                                    // Fallback to mock data
                                    const mockData = generateWeatherByLocation(lat, lon);
                                    updateWeatherForLoggedInUser(mockData);
                                }
                            })
                            .catch(error => {
                                console.error('❌ Error fetching weather:', error);
                                // Fallback to Samarinda weather if error
                                fetchWeatherByCity('Samarinda');
                            });
                    },
                    function(error) {
                        console.error('❌ Location error:', error);
                        // Fallback to Samarinda weather if error
                        fetchWeatherByCity('Samarinda');
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 60000
                    }
                );
            } else {
                console.log('❌ Geolocation not supported, using Samarinda coordinates');
                // Fallback to Samarinda weather
                fetchWeatherByCity('Samarinda');
            }
        }

        // Function to update weather display for logged in users
        function updateWeatherForLoggedInUser(weatherData) {
            console.log('🔄 Updating weather display for logged in user:', weatherData);
            
            try {
                // Validate weather data
                if (!weatherData || !weatherData.weather || !weatherData.weather[0] || !weatherData.main) {
                    console.error('❌ Invalid weather data structure:', weatherData);
                    return;
                }
                
                // Get weather icon and color
                const weatherIcon = getWeatherIcon(weatherData.weather[0].description);
                const temp = Math.round(weatherData.main.temp);
                const weatherColor = getWeatherColor(temp);
                
                console.log('🌤️ Weather details:', {
                    temp: temp,
                    humidity: weatherData.main.humidity,
                    condition: weatherData.weather[0].description,
                    wind: weatherData.wind?.speed,
                    icon: weatherIcon,
                    color: weatherColor
                });
                
                // Update weather icon with animation
                const iconElement = document.querySelector('.fa-cloud-sun');
                if (iconElement) {
                    iconElement.style.color = weatherColor;
                    // Extract the class name from the weather icon HTML
                    const iconClass = weatherIcon.match(/class="([^"]+)"/);
                    if (iconClass && iconClass[1]) {
                        iconElement.className = iconClass[1];
                        console.log('🎨 Icon updated to:', iconClass[1]);
                    }
                } else {
                    console.warn('⚠️ Weather icon element not found');
                }
                
                // Update location with fade animation
                const locationElement = document.getElementById('weatherLocation');
                if (locationElement) {
                    locationElement.style.opacity = '0';
                    setTimeout(() => {
                        locationElement.textContent = weatherData.name || 'Lokasi Anda';
                        locationElement.style.opacity = '1';
                        console.log('📍 Location updated to:', weatherData.name || 'Lokasi Anda');
                    }, 300);
                } else {
                    console.warn('⚠️ Weather location element not found');
                }
                
                // Update temperature with color
                const tempElement = document.getElementById('weatherTemp');
                if (tempElement) {
                    tempElement.style.color = weatherColor;
                    tempElement.textContent = `${temp}°C`;
                    console.log('🌡️ Temperature updated to:', `${temp}°C`);
                } else {
                    console.warn('⚠️ Weather temperature element not found');
                }
                
                // Update other elements with fade animation
                const elements = {
                    'weatherHumidity': `${weatherData.main.humidity}%`,
                    'weatherCondition': weatherData.weather[0].description,
                    'weatherWind': `${Math.round(weatherData.wind.speed * 3.6)} km/h`,
                    'weatherTime': `Update: ${new Date().toLocaleTimeString('id-ID')}`
                };
                
                Object.entries(elements).forEach(([id, value], index) => {
                    setTimeout(() => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.classList.add('weather-fade-in');
                            element.textContent = value;
                            setTimeout(() => element.classList.remove('weather-fade-in'), 500);
                            console.log(`✅ Updated ${id} to:`, value);
                        } else {
                            console.warn(`⚠️ Element ${id} not found`);
                        }
                    }, index * 100);
                });
                
                // Add loading animation to refresh button
                const refreshButton = document.querySelector('button[onclick="refreshWeather()"]');
                if (refreshButton) {
                    refreshButton.style.pointerEvents = 'none';
                    refreshButton.innerHTML = '<i class="fas fa-sync-alt fa-spin"></i> Memperbarui...';
                    setTimeout(() => {
                        refreshButton.style.pointerEvents = 'auto';
                        refreshButton.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
                    }, 1000);
                }
                
                console.log('✅ Weather display updated successfully for logged in user');
                
            } catch (error) {
                console.error('❌ Error updating weather display:', error);
                // Show error message
                const locationElement = document.getElementById('weatherLocation');
                if (locationElement) {
                    locationElement.textContent = 'Error update cuaca';
                }
            }
        }

        // Load data when page loads
        if (isLoggedIn) {
            console.log('👤 User is logged in, initializing...');
            
            // Add login success animation
            setTimeout(() => {
                const heroHeadline = document.querySelector('.hero-headline');
                const heroSubtitle = document.querySelector('.hero-subtitle');
                const heroDescription = document.querySelector('.hero-description');
                const learnMoreBtns = document.querySelectorAll('.learn-more-btn');
                const bookingWidget = document.querySelector('.booking-widget');
                const dataSummary = document.querySelector('.data-summary');
                const summaryItems = document.querySelectorAll('.summary-item');
                
                if (heroHeadline) heroHeadline.style.animation = 'heroSlideIn 1s ease-out both';
                if (heroSubtitle) heroSubtitle.style.animation = 'heroSlideIn 1s ease-out 0.2s both';
                if (heroDescription) heroDescription.style.animation = 'heroSlideIn 1s ease-out 0.4s both';
                
                learnMoreBtns.forEach((btn, index) => {
                    btn.style.animation = `heroSlideIn 1s ease-out ${0.6 + (index * 0.1)}s both`;
                });
                
                if (bookingWidget) bookingWidget.style.animation = 'widgetSlideIn 1s ease-out 0.8s both';
                if (dataSummary) dataSummary.style.animation = 'dataSlideUp 0.8s ease-out 1s both';
                
                summaryItems.forEach((item, index) => {
                    item.style.animation = `itemBounce 0.6s ease-out ${1.2 + (index * 0.1)}s both`;
                });
            }, 2500);
            
            loadDataStats();
            loadRecentData();
            // Initialize session management
            checkSessionStatus();
            // Save session to localStorage
            saveSessionToStorage();
            
            // Get initial weather data immediately
            console.log('🌤️ Loading initial weather data for logged in user...');
            
            // Debug: Check if weather elements exist
            console.log('🔍 Checking weather elements...');
            const weatherElements = {
                'weatherLocation': document.getElementById('weatherLocation'),
                'weatherTemp': document.getElementById('weatherTemp'),
                'weatherHumidity': document.getElementById('weatherHumidity'),
                'weatherCondition': document.getElementById('weatherCondition'),
                'weatherWind': document.getElementById('weatherWind'),
                'weatherTime': document.getElementById('weatherTime')
            };
            
            Object.entries(weatherElements).forEach(([id, element]) => {
                if (element) {
                    console.log(`✅ Found ${id}:`, element.textContent);
                } else {
                    console.warn(`❌ Missing ${id}`);
                }
            });
            
            // Try to get weather data immediately if geolocation is available
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        console.log('📍 Initial location obtained:', lat, lon);
                        
                        // Try to get weather data immediately
                        const apiKey = '53c68c49b9cacdebc760e0be2fbb6b51';
                        const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${apiKey}&units=metric&lang=id`;
                        
                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                if (data.cod === 200) {
                                    console.log('✅ Initial weather data loaded successfully');
                                    updateWeatherForLoggedInUser(data);
                                } else {
                                    console.log('⚠️ Weather API returned error, using fallback');
                                    const mockData = generateWeatherByLocation(lat, lon);
                                    updateWeatherForLoggedInUser(mockData);
                                }
                            })
                            .catch(error => {
                                console.log('⚠️ Initial weather fetch failed, using fallback');
                                const mockData = generateWeatherByLocation(lat, lon);
                                updateWeatherForLoggedInUser(mockData);
                            });
                    },
                    function(error) {
                        console.log('⚠️ Initial location failed, using Samarinda fallback');
                        fetchWeatherByCity('Samarinda');
                    },
                    {
                        enableHighAccuracy: false,
                        timeout: 10000,
                        maximumAge: 300000
                    }
                );
            } else {
                console.log('⚠️ Geolocation not supported, using Samarinda fallback');
                fetchWeatherByCity('Samarinda');
            }
            
            // Also call refreshWeather as backup
            setTimeout(() => {
                refreshWeather();
            }, 2000);
            
            // Update weather every 5 minutes
            setInterval(refreshWeather, 300000); // Every 5 minutes
        } else {
            // Add non-login animation
            setTimeout(() => {
                const heroHeadline = document.querySelector('.hero-headline');
                const heroSubtitle = document.querySelector('.hero-subtitle');
                const heroDescription = document.querySelector('.hero-description');
                const learnMoreBtn = document.querySelector('.learn-more-btn');
                const bookingWidget = document.querySelector('.booking-widget');
                
                if (heroHeadline) heroHeadline.style.animation = 'heroSlideIn 1s ease-out both';
                if (heroSubtitle) heroSubtitle.style.animation = 'heroSlideIn 1s ease-out 0.2s both';
                if (heroDescription) heroDescription.style.animation = 'heroSlideIn 1s ease-out 0.4s both';
                if (learnMoreBtn) learnMoreBtn.style.animation = 'heroSlideIn 1s ease-out 0.6s both';
                if (bookingWidget) bookingWidget.style.animation = 'widgetSlideIn 1s ease-out 0.8s both';
            }, 2500);
            
            // Check localStorage for session data and attempt auto-login
            const storedSession = loadSessionFromStorage();
            if (storedSession && storedSession.isLoggedIn) {
                console.log('📱 Found stored session, attempting auto-login...');
                if (!attemptAutoLogin()) {
                    console.log('❌ Auto-login failed or not needed');
                }
            }
            
            // Test API first, then load weather
            testAPIKey();
            setTimeout(() => {
                loadWeatherData();
            }, 1000);
        }

        // Add event listeners for weather buttons and logout
        document.addEventListener('DOMContentLoaded', function() {
            const refreshBtn = document.getElementById('refreshWeatherBtn');
            const testSamarindaBtn = document.getElementById('testSamarindaBtn');
            
            // Add logout confirmation
            const logoutBtn = document.querySelector('a[href*="logout"]');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    if (!confirm('Apakah Anda yakin ingin logout? Session akan dihapus dan Anda harus login ulang.')) {
                        e.preventDefault();
                    } else {
                        // Clear localStorage before logout
                        clearSessionStorage();
                        console.log('🚪 User logging out, session cleared');
                        
                        // Show logout message
                        const logoutMessage = document.createElement('div');
                        logoutMessage.style.cssText = `
                            position: fixed;
                            top: 20px;
                            left: 50%;
                            transform: translateX(-50%);
                            background: rgba(220, 53, 69, 0.9);
                            color: white;
                            padding: 15px 30px;
                            border-radius: 25px;
                            font-weight: 600;
                            font-size: 16px;
                            z-index: 1002;
                            animation: slideDown 0.5s ease-out;
                        `;
                        logoutMessage.innerHTML = '<i class="fas fa-sign-out-alt"></i> Logging out...';
                        document.body.appendChild(logoutMessage);
                        
                        // Remove message after 2 seconds
                        setTimeout(() => {
                            logoutMessage.remove();
                        }, 2000);
                    }
                });
            }
            
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function() {
                    if (!isLoggedIn) {
                        console.log('Refresh weather button clicked');
                        
                        // Show loading state
                        const weatherDisplay = document.getElementById('weatherDisplay');
                        if (weatherDisplay) {
                            weatherDisplay.innerHTML = `
                                <div class="data-name">Memperbarui cuaca...</div>
                                <div class="data-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Status:</span>
                                        <span class="detail-value">Mendeteksi lokasi...</span>
                                    </div>
                                </div>
                                <div class="data-date">Memperbarui data cuaca...</div>
                            `;
                        }
                        
                        // Test API first, then reload weather data
                        testAPIKey();
                        setTimeout(() => {
                            loadWeatherData();
                        }, 1000);
                    }
                });
            }
            
            if (testSamarindaBtn) {
                testSamarindaBtn.addEventListener('click', function() {
                    if (!isLoggedIn) {
                        console.log('Test Samarinda button clicked');
                        
                        // Show loading state
                        const weatherDisplay = document.getElementById('weatherDisplay');
                        if (weatherDisplay) {
                            weatherDisplay.innerHTML = `
                                <div class="data-name">Testing Samarinda...</div>
                                <div class="data-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Status:</span>
                                        <span class="detail-value">Mengambil data Samarinda...</span>
                                    </div>
                                </div>
                                <div class="data-date">Testing API connection...</div>
                            `;
                        }
                        
                        // Test with Samarinda directly
                        fetchWeatherByCity('Samarinda');
                    }
                });
            }
        });

        // Refresh data every 30 seconds
        setInterval(() => {
            if (isLoggedIn) {
                loadDataStats();
                loadRecentData();
            } else {
                loadWeatherData();
            }
        }, 30000);

        // Handle page visibility changes (when tab is closed/reopened)
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                console.log('📱 Page became visible, checking session...');
                if (isLoggedIn) {
                    updateSessionActivity();
                } else {
                    // Check if we should auto-login
                    const storedSession = loadSessionFromStorage();
                    if (storedSession && storedSession.isLoggedIn) {
                        console.log('🔄 Page reopened, attempting auto-login...');
                        attemptAutoLogin();
                    }
                }
            } else {
                console.log('📱 Page hidden, saving session state...');
                if (isLoggedIn) {
                    updateSessionActivity();
                }
            }
        });
        
        // Handle beforeunload event (when page is about to be closed)
        window.addEventListener('beforeunload', function() {
            if (isLoggedIn) {
                console.log('📱 Page closing, saving session...');
                updateSessionActivity();
            }
        });


        
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Add some interactive effects
        document.querySelectorAll('.offer-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Parallax effect for background
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('body::before');
            if (parallax) {
                const speed = scrolled * 0.5;
                parallax.style.transform = `translateY(${speed}px)`;
            }
        });
        
    </script>
</body>
</html> 