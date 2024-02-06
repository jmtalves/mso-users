# User Microservice

The User Microservice is a standalone service handling user-related operations within a distributed system.

## Overview

The User Microservice provides CRUD (Create, Read, Update, Delete) operations for managing user entities. It is designed as a RESTful API, supporting standard HTTP methods.

## Endpoints

### 1. Get User Information

- **Method:** GET
- **Endpoint:** `/api/user/{userEmail}`
- **Description:** Retrieve user information based on the user email or id.
- **Example:** `GET /api/user/john.doe@example.com`

### 2. Create a New User

- **Method:** POST
- **Endpoint:** `/api/user`
- **Description:** Create a new user.
- **Example Request:**
  ```json
  {
    "name": "john_doe",
    "email": "john.doe@example.com",
    "type": "0",
    "password": "secure_password"
  }

### 3. Update User Information

- **Method:** PUT
- **Endpoint:** `/api/user/{userEmail}`
- **Description:** update a user.
- **Example Request:**
  ```json
  {
    "name": "john_doe_2",
    "type": "0",
    "password": "secure_password"
  }

### 4. Delete User
- **Method:** DELETE
- **Endpoint:** `/api/user/{userEmail}`
- **Description:** Delete a user based on the user email or id.
- **Example:** `DELETE /api/user/john.doe@example.com`