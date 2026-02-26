Homework Management System - Mockup v2

Goal
- Define page structure and behavior for the Yii2 app.
- Focus on functionality, not final visual styling.

Main Navigation
- Guest: Login, Register
- Logged-in user: My Homework
- Admin: My Homework, Admin Panel

User Flow
1. User logs in.
2. User is redirected to "My Homework".
3. User creates homework, views own homework, edits open homework, marks homework as done.
4. Admin additionally manages users, teachers, subjects, and teacher-subject links.

1. Login And Registration

Login Page
- Fields: Username, Password
- Actions: Login, link to Register
- Rules:
  - Role is taken from User table automatically.
  - Inactive teachers cannot log in.

Registration Page
- Fields: Username, Password, Repeat Password
- Actions: Register, link to Login
- Rules:
  - Creates regular user account.
  - Password is stored hashed.

2. My Homework Page

Purpose
- Show only homework of the logged-in user.

Elements
- Button: Create Homework
- Filter: Subject dropdown (optional)
- Homework table

Homework Table Columns
- Subject
- Teacher
- Title
- Due Date
- Status (Open / Done)
- Actions

Actions Per Row
- View
- Edit (only if status is Open)
- Mark Done (only if status is Open)

Due Date Color Rules
- >= 14 days remaining: default row color
- < 14 days and >= 7 days: blue
- < 7 days and >= 1 day: yellow
- < 1 day: red
- Done: muted/neutral style

3. Create Or Edit Homework

Form Elements
- Subject dropdown
- Teacher dropdown (depends on selected subject)
- Title text field
- Description textarea (multiline)
- Due date/time picker
- Buttons: Save, Cancel

Rules
- Homework owner is always the logged-in user.
- Selected teacher must be linked to selected subject.
- Only active teachers can be selected for new/edited homework.
- If homework is done, editing is blocked.

4. Homework Details Page

Elements
- Back button
- Edit button (only if open)
- Mark as done button (only if open)
- Read-only details table:
  - Subject
  - Teacher
  - Due date
  - Status
  - Description (preserves line breaks)

5. Admin Panel

Access
- Only users with role admin.

5.1 User Management
- Table of all users with:
  - Username
  - Role selector (user/teacher/admin)
  - Status selector (active/inactive only for teachers)
  - Save action

Rules
- Admin cannot remove own admin role.
- Admin cannot deactivate own account.
- At least one active admin must remain.

5.2 Teacher Management
- Create teacher form:
  - Username
  - Password
  - Repeat password
  - Active checkbox
  - Create action

Rules
- Teacher uses username-based account (no first/last name split).
- Password is hashed through existing user model logic.

5.3 Subject Management
- Create subject form (name)
- Subject list with:
  - Linked teacher count
  - Rename action
  - Delete action

Delete Rules
- Subject cannot be deleted if:
  - linked to homework, or
  - linked to any teacher.

5.4 Teacher-Subject Linking
- Link form:
  - Teacher dropdown
  - Subject dropdown
  - Link action
- Existing links table:
  - Teacher
  - Teacher status
  - Subject
  - Unlink action

Linking Rules
- User must have role teacher to be linked.
- Duplicate link is not allowed.
- Unlink is blocked if homework already uses that exact teacher-subject pair.
- Inactive teacher stays linked until manually unlinked.

6. Permissions Overview

- User:
  - Create, view, edit own open homework
  - Mark own homework as done
- Admin:
  - Everything user can do
  - Manage users and teacher active state
  - Create teachers
  - Create/edit/delete subjects (with safety checks)
  - Link/unlink teachers and subjects

7. Mockup Scope

- This document is a functional low-fidelity mockup.
- It defines required UI blocks, actions, and constraints for implementation/testing in Yii2.
