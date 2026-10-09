# Training Enrollment System

## Project Description

The Training Enrollment System is a web-based application developed using PHP, PDO, and MySQL. It manages courses, class sections, student records, and training enrollments.

The system supports course management, class management, student registration, enrollment processing, enrollment cancellation, and enrollment reports. It uses database transactions to help maintain data consistency during enrollment operations.

## Technologies Used

- PHP
- PDO (PHP Data Objects)
- MySQL
- HTML and CSS
- XAMPP
- Git and GitHub

## Project Setup Instructions

### Prerequisites

Install the following software before running the application:

- XAMPP
- Visual Studio Code
- Web browser
- Git (optional, for version control)

### 1. Clone or Download the Repository

Clone the repository using Git:

```bash
git clone https://github.com/Jovielynz/training-enrollment-system.git
```

Alternatively, download the repository as a ZIP file from GitHub and extract it into the XAMPP `htdocs` directory.

### 2. Place the Project in XAMPP

Make sure the project folder is located at:

`C:\xampp\htdocs\training_enrollment`

### 3. Start XAMPP

Open the XAMPP Control Panel and start:

- Apache
- MySQL

### 4. Create the Database

1. Open `http://localhost/phpmyadmin/`.
2. Create or import the database named `training_db`.
3. Import the provided SQL schema or database file, if included in the repository.

### 5. Configure the Database Connection

Open `config/db.php` and check the database host, database name, username, and password.

Update the settings to match your local MySQL configuration.

### 6. Run the Application

Open the following URL in your browser:

`http://localhost/training_enrollment/`

## Design Pattern

### Singleton Pattern

The Singleton Pattern is used in the database connection component to provide a single shared instance of the database connection within the application.

Instead of repeatedly creating new database connection objects, the application can reuse the existing PDO connection. This helps centralize database access and configuration.

### Repository Pattern

The Repository Pattern is used to separate database operations from the application's presentation and workflow logic. The enrollment repository handles enrollment-related operations, including retrieving enrollment details and processing enrollment transactions.

This separation makes the code easier to organize, maintain, and test.

### Transaction Management

Database transactions are used for operations that involve multiple related changes. For example, enrolling a student may require creating an enrollment record and reducing the number of available class slots. If an operation fails, the transaction can be rolled back to prevent incomplete updates.

## Main Features

- Course management
- Class section management
- Student registration
- Student enrollment
- Enrollment cancellation
- Available-slot tracking
- Enrollment reports
- PDO-based database operations

## Author

Developed as part of the IPT – Integrative Programming and Technology laboratory activity.