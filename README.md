# Simple Library Management System (Web-based)

## Overview
A lightweight web application to manage a library catalog, allowing librarians and users to handle books, borrowing, and returns.

## Features
- **Book Management:** Add, edit, or remove books.  
- **Catalog Interaction:** Browse and search the catalog.  
- **Borrowing & Returns:** Borrow books and manage returns.

## Objectives
- Apply web development concepts (HTML, CSS, JS, PHP).  
- Perform CRUD operations with MySQL.  
- Collaborate using Git and GitHub.  
- Practice version control: branching, merging, pull requests, resolving conflicts.

## Git Workflow
1. **Setup:** Clone the repo and create a `develop` branch.  
2. **Feature Branches:** Each member works on a separate feature.  
3. **Commit & Push:** Regularly commit changes and push feature branches.  
4. **Pull Requests:** Review by teammates before merging into `develop`.  
5. **Finalize:** Merge `develop` into `main` for production-ready code.

## Merge Conflict Resolution
1. Merge your branch: `git merge <branch_name>`  
2. Identify conflicts marked by Git.  
3. Edit files to resolve conflicts, removing markers.  
4. Complete the merge:
   ```bash
   git add <file_name>
   git commit -m "Resolve merge conflict"
   git push origin develop
