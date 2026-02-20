<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NirapodPoth | Homepage</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap');

    * {
      margin: 0;
      padding: 0;
      scroll-behavior: smooth;
      box-sizing: border-box;
    }

    body {
      font-family: 'Roboto', sans-serif;
    }

    .wrapper {
      background: url('road.jpg') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
      position: relative;
      z-index: 1;
    }

    /* Overlay for contrast */
    .wrapper::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      background-color: rgba(0, 0, 0, 0.5); /* dark transparent overlay */
      z-index: 2;
    }

.navbar {
  position: fixed;
  top: 0;
  width: 100%;
  height: 80px;
  background-color: rgba(0,0,0,0.5); /* initial semi-transparent */
  transition: background-color 0.4s ease;
  z-index: 10;
}

.navbar.scrolled {
  background-color: transparent; /* NO background on scroll */
}

  .navbar {
      position: fixed;
      top: 0;
      width: 100%;
      height: 80px;
      background-color: rgba(0,0,0,0.5); /* initial semi-transparent */
      transition: background-color 0.4s ease, color 0.4s ease;
      z-index: 10;
         display: flex;           /* changed to flexbox */
    align-items: center;     /* vertical center */
    justify-content: space-between;  /* space between logo and menu */
    padding: 0 20px;         /* some horizontal padding */
    }

    .navbar .logo,
    .navbar ul li a {
      color: white;
      transition: color 0.4s ease;
    }

    .navbar.scrolled {
      background-color: transparent; /* no bg on scroll */
    }

    .navbar.scrolled .logo,
    .navbar.scrolled ul li a {
      color: black; /* text turns black on scroll */
    }

    .navbar .logo {
      float: left;
      padding: 20px 100px;
      color: white;
      font-size: 24px;
    }
      .navbar .logo img {
    height: 70px;  /* adjust size as you want */
    vertical-align: middle;
  }

    .navbar ul {
      float: right;
      margin-right: 20px;
    }

    .navbar ul li {
      display: inline-block;
      line-height: 80px;
      margin: 0 8px;
    }

    .navbar ul li a {
      color: white;
      padding: 6px 13px;
      font-size: 18px;
      text-decoration: none;
      transition: 0.3s;
    }

    

    .center {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
      z-index: 3;
      color: white;
    }

    .center h1 {
      font-size: 60px;
      margin-bottom: 20px;
      text-shadow: 1px 1px 4px black;
    }

    .center h3 {
      font-size: 24px;
      text-shadow: 1px 1px 4px black;
    }

    .buttons {
      margin-top: 30px;
    }

 .buttons button {
  height: 50px;
  width: 150px;
  font-size: 18px;
  font-weight: 600;
  color: #000000ff;
  background: #FDF3E7;
  outline: none;
  cursor: pointer;
  border: 2px solid #F39C12;
  border-radius: 25px;
  transition: 0.3s ease;
  margin: 0 10px;
}

.buttons button:hover {
  background: #F39C12;
  color: #ffffff;
}



    section {
      padding: 100px 60px;
      background: #f5f5f5;
      color: #333;
    }

    section:nth-child(even) {
      background: #e9e9e9;
    }

    section h2 {
      font-size: 32px;
      margin-bottom: 20px;
      
    }

    section p {
      font-size: 18px;
      line-height: 1.6;
    }
    body {
  font-family: 'Poppins', sans-serif;
}

section h2 {
  font-weight: 700;
}
.navbar .logo img {
  vertical-align: middle;
}

section p {
  font-weight: 500;
}

  </style>
</head>
<body>

<div class="wrapper" id="home">
  <div class="navbar">
  <div class="logo">
    <img src="logo.png" alt="NirapodPoth Logo" style="height: 100px; vertical-align: middle;">
  </div>
  <ul>
    <li><a href="#home" class="active">Home</a></li>
    <li><a href="#about">About</a></li>
    <li><a href="#benefits">Benefits</a></li>
    <li><a href="login.php">Login</a></li>
    <li><a href="register.php">Register</a></li>
  </ul>
</div>


  <div class="center">
    <h1>Welcome to NirapodPoth</h1>
    <h3>Your Safety, Our Priority</h3>
    <div class="buttons">
      <button onclick="location.href='login.php'">Login</button>
      <button onclick="location.href='register.php'">Register</button>
    </div>
  </div>
</div>

<section id="about" style="background-color: #ffffff; padding: 60px 20px; max-width: 900px; margin: 40px auto; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); border-radius: 12px;">
  <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
    About NirapodPoth
  </h2>

  <p style="font-size: 18px; line-height: 1.8; color: #333;">
    NirapodPoth is a digital safety and incident reporting platform built to empower everyday citizens. Whether it's a road accident, suspicious behavior, or hazardous condition, the platform lets users report events in real-time. Through this collective reporting system, NirapodPoth creates awareness, enhances transparency, and supports authorities in making our roads and communities safer.
  </p>
</section>



<section id="benefits" style="background-color: #ffffff; padding: 60px 20px; max-width: 900px; margin: 40px auto; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); border-radius: 12px;">
  <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
    What Will It Do?
  </h2>

  <p style="font-size: 18px; line-height: 1.8; margin-bottom: 30px; color: #333;">
    NirapodPoth empowers users to actively participate in public safety. By providing simple reporting tools and access to valuable information, the platform serves both individuals and communities in the following ways:
  </p>

  <ul style="font-size: 17px; line-height: 1.8; margin-left: 20px; margin-bottom: 30px; color: #444;">
    <li style="margin-bottom: 12px;">
      <strong>Easy Incident Reporting:</strong> Users can quickly report accidents, suspicious activity, or road hazards.
    </li>
    <li style="margin-bottom: 12px;">
      <strong>Live Risk Area View:</strong> Stay informed about dangerous or high-risk zones based on real-time data.
    </li>
    <li style="margin-bottom: 12px;">
      <strong>Anonymous Submissions:</strong> Report without revealing your identity, ensuring safety for every voice.
    </li>
    <li style="margin-bottom: 12px;">
      <strong>Blackspot Highlighting:</strong> Frequent incident locations are marked to alert other users and authorities.
    </li>
    <li style="margin-bottom: 12px;">
      <strong>Support for Authorities:</strong> Collected data helps organizations plan preventive actions and safety measures.
    </li>
  </ul>

  <p style="font-size: 18px; line-height: 1.8; color: #333;">
    This feature set ensures a proactive approach to safety, helping people stay aware, informed, and protected.
  </p>
</section>

<section id="future-plans" style="background-color: #ffffff; padding: 60px 20px; max-width: 900px; margin: 40px auto; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); border-radius: 12px;">
  <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
    Future Plans & Community Benefits
  </h2>

  <p style="font-size: 18px; line-height: 1.8; margin-bottom: 30px; color: #333;">
    As NirapodPoth continues to grow, we are committed to building a smarter, safer future for everyone. Our upcoming features aim to enhance both user experience and public safety through technology and collaboration.
  </p>

  <ul style="font-size: 17px; line-height: 1.8; margin-left: 20px; margin-bottom: 30px; color: #444;">
    <li style="margin-bottom: 12px;">
      <strong>Real-Time Risk Maps:</strong> Interactive maps displaying accident-prone areas, updated based on live reports.
    </li>
    <li style="margin-bottom: 12px;">
      <strong>Collaboration with Authorities:</strong> Working with law enforcement and emergency services to ensure quick response and action.
    </li>
    <li style="margin-bottom: 12px;">
      <strong>AI-Based Predictions:</strong> Using data analysis to predict potential high-risk zones and times for preventive measures.
    </li>
    <li style="margin-bottom: 12px;">
      <strong>Emergency Alerts:</strong> Sending instant alerts to users in or near affected locations for timely awareness.
    </li>
    <li style="margin-bottom: 12px;">
      <strong>Community Engagement:</strong> Involving citizens through safety tips, feedback systems, and local awareness programs.
    </li>
  </ul>

  <p style="font-size: 18px; line-height: 1.8; color: #333;">
    These future upgrades will help NirapodPoth become an essential tool for public safety and smart city development, benefiting individuals and communities alike.
  </p>
</section>
  <script>
    window.addEventListener('scroll', function() {
      const navbar = document.querySelector('.navbar');
      if (window.scrollY > window.innerHeight) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });
  </script>






</body>
</html>
