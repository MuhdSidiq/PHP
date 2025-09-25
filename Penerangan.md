# PHP Project Files Explanation

## Core Application Files

### `config.php`
- **Purpose**: Database connection configuration
- **Contains**: PDO connection setup with error handling
- **Usage**: Included by all database-related files
- **Security**: Contains database credentials (host, username, password, database name)

### `setup.php`
- **Purpose**: Automated database initialization
- **Functions**:
  - `createDatabase()` - Creates MySQL database
  - `createTables()` - Creates tables with foreign keys
  - `populateData()` - Inserts sample data
  - `setupDatabase()` - Runs complete setup process
- **UI**: Bootstrap interface to run setup with one click
- **Usage**: Run once to initialize the entire database system

## CRUD (Create, Read, Update, Delete) Files

### `add.php`
- **Purpose**: Create new product records
- **Features**:
  - Bootstrap form with validation
  - Dropdown for categories (populated from database)
  - Form fields: name, price, category, stock
  - Success message after insertion
- **Database**: INSERT query to products table

### `view.php`
- **Purpose**: Display all products in a table
- **Features**:
  - Responsive Bootstrap table
  - Shows product details with category names (JOIN query)
  - Color-coded stock badges (green/red)
  - Edit/Delete action buttons
  - "Add New Product" button
- **Database**: SELECT with LEFT JOIN between products and categories

### `edit.php`
- **Purpose**: Update existing product records
- **Features**:
  - Pre-filled form with current product data
  - Same form structure as add.php but with UPDATE functionality
  - Category dropdown with current selection highlighted
  - Validation to ensure product exists
- **Database**: SELECT to fetch current data, UPDATE to save changes

### `delete.php`
- **Purpose**: Remove product records
- **Features**:
  - Simple deletion script (no UI)
  - Takes product ID from URL parameter
  - Immediately redirects back to view.php
  - JavaScript confirmation handled in view.php
- **Database**: DELETE query by product ID

## Demo/Learning Files

### `index.php`
- **Purpose**: Original PHP learning demonstration
- **Contains**: Basic PHP variables, Bootstrap card layout (commented out)
- **Variables**: Product details (name, brand, size, price, discount, stock)
- **Styling**: Bootstrap + custom CSS

### `demo.php`
- **Purpose**: Simple PHP array and loop demonstration
- **Contains**: Student data array with foreach loop
- **Output**: Displays student information (name, age, gender)

### `style.css`
- **Purpose**: Custom CSS styles
- **Contains**:
  - `.title` class - Red text with blue background
  - `#greetings` ID - Green text with blue background
- **Usage**: Linked by all HTML pages

## Database Schema Files

### `database.sql` (Safe to delete)
- **Purpose**: Original SQL schema file
- **Status**: Replaced by `setup.php` functions
- **Contains**: CREATE statements and sample INSERT queries

### `linux-notes.md` (Safe to delete)
- **Purpose**: Linux command reference documentation
- **Status**: Not related to PHP application
- **Contains**: Basic Linux commands with Malay descriptions

## PHP Includes Explanation

### What are PHP Includes?
PHP includes allow you to insert the content of one PHP file into another. This promotes code reusability and organization.

### Types of Include Statements:
- `include 'file.php'` - Includes file, continues if file not found (warning only)
- `require 'file.php'` - Includes file, stops execution if file not found (fatal error)
- `include_once` - Includes file only once, prevents duplicate includes
- `require_once` - Same as require but only once

### How Includes Work in This Project:

```php
include 'config.php';  // Used in add.php, view.php, edit.php
```

**What happens:**
1. When `add.php` runs, it first executes `config.php`
2. This creates the `$pdo` database connection variable
3. `add.php` can then use `$pdo` for database operations
4. Variables and functions from `config.php` become available in `add.php`

**Benefits:**
- **DRY Principle**: Don't repeat database connection code in every file
- **Centralized Config**: Change database settings in one place
- **Code Organization**: Separate concerns (connection vs. functionality)
- **Maintenance**: Easier to update database credentials

**Example Flow:**
```
User visits add.php
  ↓
include 'config.php' runs
  ↓
$pdo connection is established
  ↓
add.php uses $pdo for INSERT query
```

This pattern is used throughout the CRUD system to share the database connection across all pages.