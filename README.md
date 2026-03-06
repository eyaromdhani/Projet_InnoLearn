# 🚀 Innolearn Platform

Innolearn is a comprehensive **educational platform** developed with **Symfony** (MVC architecture).  
It manages 6 core modules and integrates modern web features such as responsive design, light/dark theme, secure authentication and user-friendly dashboard interfaces.

**Developed at** Esprit School of Engineering – Tunisia  
**Academic Year** 2025–2026  
**Program** PIDEV – 3rd Year Engineering

## ✨ Features
👥 **User Management**: Multi-role system with customizable permissions
📅 **Event Management**: Interactive calendar with booking system
💳 **Subscription Management**: Flexible plans with secure payments
💼 **Opportunity Management**: Sales pipeline with conversion tracking
📚 **Course Management**: Rich content creation and student progress tracking
🏢 **Project Management**: Team collaboration with task management

📱 **Responsive Design**: Fully responsive across all devices
🌓 **Light/Dark Theme**: Toggle between themes with user preference persistence
⚡ **Modern UI/UX**: Clean, intuitive interface with smooth interactions
🔐 **Secure Authentication**: Symfony security with role-based access control

## 📁 Project Structure

    innolearn/
    ├── src/
    │   ├── Controller/
    │   │   ├── UserController.php         # 👥 User management logic
    │   │   ├── EventController.php        # 📅 Event handling
    │   │   ├── SubscriptionController.php # 💳 Subscription logic
    │   │   ├── OpportunityController.php  # 💼 Business opportunities
    │   │   ├── CourseController.php       # 📚 Course operations
    │   │   └── ProjectController.php      # 🏢 Project management
    │   │
    │   ├── Entity/                        # 🧩 Data models
    │   │   ├── User.php                   # User entity
    │   │   ├── Event.php                  # Event entity
    │   │   ├── Subscription.php           # Subscription entity
    │   │   ├── Opportunity.php            # Opportunity entity
    │   │   ├── Course.php                 # Course entity
    │   │   └── Project.php                # Project entity
    │   │
    │   └── Repository/                    # Data access layer
    │
    ├── templates/                         # 🎨 Views (Twig templates)
    │   ├── user/                          # User-related views
    │   ├── event/                         # Event-related views
    │   ├── subscription/                  # Subscription views
    │   ├── opportunity/                   # Opportunity views
    │   ├── course/                        # Course views
    │   ├── project/                       # Project views
    │   └── dashboard/                     # Dashboard views
    │
    ├── public/                            # 🌐 Public assets
    │   ├── css/                           # Stylesheets
    │   ├── js/                            # JavaScript files
    │   └── assets/                        # Images, fonts, etc.
    │
    ├── config/                            # ⚙️ Configuration files
    └── migrations/                        # 📊 Database migrations



## Tech Stack

### Frontend
- Twig (templating engine)
- HTML5 / CSS3 (avec variables CSS pour les thèmes)
- JavaScript (interactions dynamiques)
- Responsive design (mobile-first)

### Backend
- PHP 8+
- Symfony (framework principal – MVC)
- Doctrine ORM
- MySQL / MariaDB

## Architecture
Application classique **Symfony MVC** :
- **Entities** → Modèles de données (User, Event, Subscription, Opportunity, Course, Project)
- **Controllers** → Logique métier et gestion des requêtes
- **Twig Templates** → Vues modulaires par entité
- **Repository** → Couche d’accès aux données


## Contributors
- Eya Allah Romdhani
- Myriam ben Azzoun
- Rayen Sboui
- Mohamed Aziz Mesalmani
- Zied Ibrahim
- Alae Naoui

## Academic Context
This project was realized as part of the **PIDEV** course (Projet d'Intégration et Développement) in the **3rd year engineering cycle** at **Esprit School of Engineering**, Tunisia — Academic Year **2025–2026**.



