Homework Management System
1. Login & Registration
Login Page

Elements:

Text field: Username

Password field: Password

Button: Login

Link: Register

Functionality:

User logs in with username and password

Role (user/admin) is detected automatically

Registration Page

Elements:

Text field: Username

Password field: Password

Password field: Repeat password

Button: Register

Functionality:

Creates a new user account

Password is stored hashed

2. User Area – Homework Overview
“My Homework” Page

Elements:

Button: “Create Homework”

Dropdown: Filter by subject (optional)

Table with homework entries

Homework table columns:

Subject

Title

Due date

Status (open / done)

Actions

Row color rules (due date):

≥ 14 days remaining → no color

< 14 and ≥ 7 days → blue

< 7 and ≥ 1 day → yellow

< 1 day → red

Actions per homework:

View

Edit (only if not done)

Mark as done

3. Create / Edit Homework
Homework Form

Elements:

Dropdown: Subject

Text field: Title

Text area: Description

Date/time picker: Due date

Button: Save

Button: Cancel

Rules:

Homework belongs to the logged-in user

If homework is marked as done:

Form is read-only

No editing allowed

4. Admin Area
Admin Dashboard

Accessible only by users with role admin.

Functions:

Manage subjects (CRUD)

Manage teachers (create, edit, set inactive)

Assign teachers to subjects

Subject Management

Elements:

List of subjects

Create subject

Edit subject

Delete subject

Teacher Management

Elements:

First name

Last name

Active / inactive checkbox

Subject assignment (multi-select)

Rules:

Teachers cannot be deleted

Each teacher must be assigned to at least one subject

5. Permissions Overview
Role	Permissions
User	Create, view, edit own homework
Admin	Manage subjects and teachers
6. Mockup Purpose

This mockup defines the structure and functionality of the user interface and serves as a planning document before implementation in Yii2.
