FutureMap Backend - AI-Powered Career Architect
Welcome to the FutureMap Backend, the server-side component of an intelligent career guidance platform designed to help students, mentors, and institutions forge personalized career paths. This backend, built with PHP and MySQL, powers the FutureMap application by providing APIs for user authentication, flashcards, peer groups, and data management, enabling features like career path building, mentor connections, and institutional dashboards.
Table of Contents

Features
Tech Stack
Installation
Prerequisites
Backend Setup
Database Setup
Running the Backend


Project Structure
Usage
Contributing
Team
License
Contact

Features
The FutureMap backend supports the following key functionalities:

API Endpoints:

/flashcards_api.php: Handles flashcards retrieval, career path saving, and user data management.
/get_peers.php: Provides peer group data for community features.
User authentication and session management using JWT tokens.


Career Path Management:

Stores and retrieves career paths, including courses, exams, skills, and internships.
Manages connections between career path elements.


Mentor & Community Hub Support:

Manages mentor profiles, session scheduling, and live session data.
Supports peer group creation and interaction.


Institute Control Center:

Provides endpoints for managing courses, applications, student data, and real-time stats.


Mentor Dashboard:

Tracks mentorship sessions, student interactions, and earnings.


Schedule Management:

Handles events, deadlines, and reminders for the Schedule Manager.



Tech Stack

Backend: PHP (8.1+) with custom APIs for handling flashcards, user authentication, and data management.
Database: MySQL (8.0+) for storing user data, career paths, mentor sessions, and institutional records.
Dependencies:
Composer for PHP dependency management.




Installation
Prerequisites
Ensure you have the following installed on your system:

PHP (v8.1 or higher) for the backend.
MySQL (v8.0 or higher) for the database.
Composer (PHP dependency manager).
Git for cloning the repository.
A web server like Apache or Nginx (e.g., via XAMPP, MAMP, or a local PHP server).
A code editor like VS Code.

Backend Setup

Clone the Repository:
git clone https://github.com/your-username/futuremap.git
cd futuremap/backend




Set Up Apache/Nginx:

Place the backend folder in your web server's root directory (e.g., htdocs for XAMPP).
Ensure the server is configured to point to the backend folder.
For Apache, enable mod_rewrite and configure .htaccess if needed. Example .htaccess:RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]





Database Setup

Create a MySQL Database:
CREATE DATABASE futuremap;


Import the Database Schema:

The database.sql file is provided in the backend directory.
Import it into MySQL:mysql -u your_username -p futuremap < database.sql


This file sets up tables for users, career paths, flashcards, mentors, sessions, and more.


Verify Database Connection:

Ensure the .env credentials match your MySQL setup.
Test the connection by running a PHP script or hitting an API endpoint (e.g., http://localhost/Futuremap-backend/flashcards_api.php?action=getFlashcards).



Running the Backend

Start the Backend Server:

If using XAMPP, start Apache and MySQL.
Alternatively, use PHP's built-in server:cd backend
php -S localhost:8000




Test API Endpoints:

Use a tool like Postman or cURL to test the APIs.
Example: Retrieve flashcards (requires authentication token):curl -H "Authorization: Bearer your_jwt_token" http://localhost:8000/flashcards_api.php?action=getFlashcards


Example: Get peer groups:curl -H "Authorization: Bearer your_jwt_token" http://localhost:8000/get_peers.php




Integration with Frontend:

Ensure the frontend (running on http://localhost:5173) is configured to communicate with the backend at http://localhost:8000. The frontend .env should have:VITE_API_BASE_URL=http://localhost:8000





Project Structure
backend/
├── flashcards_api.php    # API for flashcards, career paths, and user data
├── get_peers.php         # API for peer group data
├── .env.example          # Example environment file
├── .env                  # Environment file (create from .env.example)
├── composer.json         # PHP dependencies
├── composer.lock         # Composer lock file
├── database.sql          # MySQL schema
└── README.md             # This file

Usage
The backend provides RESTful APIs to support the FutureMap frontend. Key endpoints include:

Flashcards API (flashcards_api.php):

GET /flashcards_api.php?action=getFlashcards: Retrieves flashcards based on user education level.
POST /flashcards_api.php: Saves career paths (cards and connections).
Requires Authorization: Bearer <token> header.


Peer Groups API (get_peers.php):

GET /get_peers.php: Retrieves peer group data for community features.
Requires Authorization: Bearer <token> header.


Authentication:

Assumes a login endpoint (e.g., /auth.php) to generate JWT tokens. Implement if not present.
Tokens are stored in the frontend's localStorage as authToken.



To use the backend:

Start the server as described in Running the Backend.
Authenticate a user to obtain a JWT token.
Use the token to access protected endpoints.
Integrate with the frontend to enable features like career path building and mentor connections.

Contributing
We welcome contributions to the FutureMap backend! To contribute:

Fork the repository.
Create a feature branch:git checkout -b feature/your-feature


Commit your changes:git commit -m "Add your feature"


Push to your branch:git push origin feature/your-feature


Open a pull request with a clear description of your changes.

Please follow our code of conduct and ensure your code adheres to the project's style guidelines (e.g., PSR-12 for PHP).
Team
Meet the team behind FutureMap:

Deepak Mishra - Full Stack Developer - [LinkedIn Profile](https://www.linkedin.com/in/ddevguru/) 
Vrushali Nanavati - Product Designer×Frontend Developer - [LinkedIn Profile ](https://www.linkedin.com/in/vrushali-nanavati-3ba606208)
Amulya Ambre - Full Stack Developer - [LinkedIn Profile ](http://www.linkedin.com/in/amulya-ambre)
Sargam Sharma - UI/UX Designer - [LinkedIn Profile](http://www.linkedin.com/in/sargam-sharma-9664b1301)

License
This project is licensed under the MIT License.
Contact
For questions, feedback, or support, reach out to:

Email: deepakm7778@gmail.com
GitHub Issues: Create an issue

Power your career guidance platform with the FutureMap backend!
