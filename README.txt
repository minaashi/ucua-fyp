===============================================================================
                    UCUA SAFETY REPORTING SYSTEM - README
===============================================================================

PROJECT OVERVIEW
================

The UCUA Safety Reporting System is a comprehensive web-based application 
designed for managing workplace safety incidents, violations, and corrective 
actions. The system facilitates multi-level reporting, investigation, and 
resolution workflows with role-based access control and automated warning 
letter management.

SYSTEM DESCRIPTION
==================

This Laravel-based application serves as a centralized platform for:
- Safety incident reporting and tracking
- Multi-departmental investigation workflows  
- Automated warning letter generation and approval
- User management with role-based permissions
- Real-time notifications and deadline tracking
- Comprehensive audit trails and reporting

KEY FEATURES
============

* Multi-User Authentication System
  - Regular Users: Standard employees for incident reporting
  - Admin: System administrators with full access
  - UCUA Officers: Safety officers for report review and management
  - Department Users: Department heads for investigation and resolution

* Safety Reporting System
  - Anonymous reporting options with privacy protection
  - Separate tracking for unsafe conditions vs unsafe acts
  - Standardized report ID format (RPT-001, RPT-002, etc.)
  - File attachment support for evidence
  - Past date/time restrictions to prevent future-dated reports
  - Auto-population of employee details from user profiles

* Warning Letter Management
  - Three-tier severity system (minor, moderate, severe)
  - Automated escalation rules (3 warnings in 3 months)
  - UCUA suggestion workflow with admin approval
  - Email delivery for internal violators
  - Manual delivery tracking for external violators
  - Standardized warning ID format (WL-001, WL-002, etc.)

* Department Investigation Workflow
  - Report assignment with deadline tracking
  - department remarks system
  - Violator identification for unsafe acts
  - Resolution notes visible to report submitters
  - Status tracking (pending → in_progress → resolved)

* Security & Privacy Features
  - Multi-guard authentication (web and department guards)
  - OTP verification for all login attempts
  - Email verification for regular users
  - Anonymous reporting with internal user tracking
  - Role-based access control using Spatie Laravel Permission
  - CSRF protection and input validation

USER ROLES & AUTHENTICATION
============================

Regular Users (/login)
- Account registration with email verification required
- OTP verification during login (6-character, 5-minute expiration)
- Submit safety reports with anonymous options
- Track own report status and view resolution notes
- Access role-specific help documentation

Admin (/admin/login)  
- Full system access and user management
- Department creation and management
- Warning letter approval workflow
- System settings configuration
- Skip email verification, OTP verification enabled
- Credentials: nursyahminabintimosdy@gmail.com / Admin@UCUA03

UCUA Officer (/ucua/login)
- Report review and investigation management
- Department assignment with deadline setting
- Warning letter suggestion workflow
- Priority guidelines and response monitoring
- Skip email verification, OTP verification enabled
- Credentials: nazzreezahar@gmail.com / TestPassword123! (Worker ID: UCUA001)

Department (/department/login)
- View and manage assigned reports
- Add confidential department remarks
- Violator identification for unsafe acts
- Resolution note submission
- OTP verification enabled
- Sample Credentials: Security@Port25 (PSD department)

INSTALLATION & SETUP
=====================

Prerequisites:
- PHP 8.2 or higher
- Composer
- MySQL database
- Node.js and npm
- Web server (Apache/Nginx)

Installation Steps:

1. Clone the repository:
   git clone [repository-url]
   cd ucua-fyp

2. Install PHP dependencies:
   composer install

3. Install JavaScript dependencies:
   npm install

4. Environment Configuration:
   - Copy .env.example to .env
   - Configure database connection
   - Set mail configuration for OTP and notifications
   - Configure timezone to Asia/Kuala_Lumpur

5. Database Setup:
   php artisan key:generate
   php artisan migrate
   php artisan db:seed

6. Build frontend assets:
   npm run build

7. Start the development server:
   php artisan serve

KEY FUNCTIONALITIES
===================

Report Submission Workflow:
- Form validation with auto-populated employee details
- Category selection (unsafe_condition OR unsafe_act)
- Optional file attachments (max 5MB)
- Anonymous reporting checkbox
- Automatic RPT-XXX ID generation
- Status tracking through resolution

Investigation Process:
- UCUA officer review and department assignment
- Deadline setting with automated reminders
- Department investigation with confidential remarks
- Violator identification for unsafe acts only
- Resolution notes for report closure

Warning Letter System:
- UCUA officer suggests warnings for unsafe acts
- Admin approval workflow with edit capabilities
- Three severity levels with escalation rules
- Internal violator email delivery with HOD CC
- Manual delivery tracking for external violators
- Escalation reset after 6 months violation-free

Department Management:
- Department creation with unique constraints
- User assignment to departments (one-to-many relationship)
- Department-specific login and dashboard
- Report assignment and deadline tracking
- Notification system for pending actions

TECHNOLOGY STACK
=================

Backend Framework:
- Laravel 11.x (PHP 8.2+)
- MySQL database with Eloquent ORM
- Spatie Laravel Permission for role management
- Laravel UI for authentication scaffolding
- Laravel Sanctum for API authentication

Frontend Technologies:
- Blade templating engine
- Vue.js 3.2 components
- Bootstrap 5.2.3 for responsive design
- Tailwind CSS for utility classes
- Font Awesome 6.0 for icons
- Axios for HTTP requests

Build Tools & Development:
- Vite for asset bundling and hot reloading
- Composer for PHP dependency management
- npm for JavaScript package management
- Laravel Pint for code formatting
- PHPUnit for testing

Security Features:
- Multi-guard authentication system
- OTP verification with 5-minute expiration
- CSRF protection on all forms
- Input validation and sanitization
- Role-based middleware protection
- Email verification for user accounts

SYSTEM ARCHITECTURE
====================

The system follows a multi-layered architecture:

Frontend Layer:
- Responsive web interface using Blade templates
- Vue.js components for interactive features
- Bootstrap and Tailwind CSS for styling
- JavaScript utilities for enhanced UX

Backend Layer:
- Laravel MVC architecture
- RESTful routing with middleware protection
- Service layer for business logic
- Repository pattern for data access

Database Layer:
- MySQL with normalized table structure
- Eloquent ORM for model relationships
- Migration system for version control
- Seeder classes for initial data

Authentication Layer:
- Multi-guard system (web and department)
- Role-based access control
- OTP verification system
- Session management

External Services:
- Email delivery for notifications and OTP
- File storage for report attachments
- Real-time notification system

HELP SYSTEM
============

Comprehensive role-based help documentation is available through:
- Sidebar navigation "Help" menu
- Role-specific content and procedures
- Search functionality within help pages
- Step-by-step guides for all major workflows
- Technical documentation and troubleshooting

PRIVACY & CONFIDENTIALITY
==========================

The system implements multiple privacy protection measures:
- Anonymous reporting hides user identity in UI while maintaining internal tracking
- Department remarks are confidential and not visible to regular users
- Resolution notes are only visible to report submitters
- Violator identification applies only to unsafe_act violations
- Audit trails maintain data integrity without compromising privacy

SUPPORT & MAINTENANCE
======================

For technical support or system maintenance:
- Refer to the comprehensive help system within the application
- Review implementation documentation in markdown files
- Contact system administrators for access issues

The system is designed for scalability and can be extended with additional
features such as video tutorials, interactive tours, multi-language support,
and advanced analytics as needed.

===============================================================================
                              END OF README
===============================================================================
