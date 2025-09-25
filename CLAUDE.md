# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a simple PHP learning project containing basic PHP examples and demonstrations. The codebase consists of:

- `index.php` - Main file demonstrating PHP variables, HTML integration with Bootstrap styling, and product display card (currently commented out)
- `demo.php` - Simple PHP array and foreach loop demonstration with student data
- `style.css` - Custom CSS styles for title and greetings elements
- `linux-notes.md` - Linux command reference documentation (not part of the PHP codebase)

## Development Environment

This project uses vanilla PHP with HTML/CSS and Bootstrap framework. No build tools, package managers, or testing frameworks are configured.

## Running the Project

To run the PHP files:
```bash
php -S localhost:8000
```
Then visit:
- http://localhost:8000/index.php
- http://localhost:8000/demo.php

## Code Architecture

- **Frontend**: HTML with Bootstrap 5.3.2 CSS framework
- **Backend**: Plain PHP with embedded HTML
- **Styling**: Bootstrap + custom CSS in `style.css`
- **No database or complex architecture** - this is a beginner PHP learning project

## File Structure

- PHP files contain mixed HTML and PHP code using embedded PHP tags
- CSS is separated into `style.css` for custom styling
- Bootstrap is loaded via CDN for responsive design