
# Train Management - Ticketing and Administration Application
University project developed at [Università degli studi Guglielmo Marconi](https://www.unimarconi.it/) implementing a comprehensive train management system with three user levels, designed to optimize railway planning and ticketing.

## User Functionalities:
### User

- System registration and login
- Available route selection
- Ticket purchases through PaySteam integration 

### Administrative User

- Complete route visualization
- Email communication with exercise users
- System monitoring and supervision

### Exercise User

- Train composition and cancellation
- Creating new routes
- Modifying and deleting existing routes

## E-R Diagram
<p align=center>
    <img src="docs/E-R scheme.jpg">
</p>

## Layout Directories
```bash
├── administrative 
│   ├── administrative_office.php
│   ├── mail_exercise.php
│   └── mail_sent.html
├── exercise
│   ├── create_convoy.php
│   ├── create_route_home.php
│   ├── create_route.php
│   ├── delete_convoy.php
│   ├── edit_route_home.php
│   ├── edit_route.php
│   ├── exercise_office.php
│   └── route_details.php
├── home
│   ├── convoy_history.php
│   ├── itineraries.php
│   ├── login.php
│   ├── registration.php
│   └── route.php
├── images
│   ├── home_navbar.jpg
│   ├── landscapes.png
│   ├── track.png
│   ├── train.png
│   └── trains
├── index.php
├── paysteam
│   ├── card_registration.php
│   ├── login_paysteam.php
│   ├── payment_accepted.php
│   ├── paysteam_registration.php
│   ├── profile.php
│   └── purchase_confirmation.php
├── scripts
│   ├── book_trip_functions.php
│   ├── convoy_functions.php
│   ├── create_route_functions.php
│   ├── db_connection.php
│   ├── delete_route_functions.php
│   ├── login_management.php
│   ├── logout.php
│   ├── office_pagination.php
│   ├── pagination.php
│   ├── paysteam_login_management.php
│   ├── profile_functions.php
│   ├── purchase_functions.php
│   ├── register_card.php
│   ├── register_paysteam.php
│   └── register_user.php
├── styles_css
│   ├── profile.css
│   ├── style.css
│   ├── style_route.css
│   └── trains.css
└── user
    ├── book_trip.php
    └── registered_user.php

