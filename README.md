# 🎬 YouTube Clone API

A RESTful API backend for a YouTube-like video-sharing platform, built with Laravel and MySQL.

---

## 🚀 Features

- User authentication and authorization
- Video upload, streaming, and management
- Channel creation and subscription
- Commenting and liking system
- Playlist management
- Search functionality
- API documentation

---

## 🛠️ Technologies Used

- **Framework**: Laravel
- **Language**: PHP
- **Database**: MySQL
- **Web Server**: Nginx
- **Containerization**: Docker
- **Package Manager**: Composer

---

## 📁 Project Structure

```
├── app/                 # Application core
├── bootstrap/           # Application bootstrap files
├── config/              # Configuration files
├── database/            # Migrations and seeders
├── public/              # Publicly accessible files
├── resources/           # Views and assets
├── routes/              # API and web routes
├── storage/             # Logs and file storage
├── tests/               # Test cases
├── Dockerfile           # Docker configuration
├── .env.example         # Environment variables example
├── composer.json        # PHP dependencies
├── package.json         # JavaScript dependencies
└── README.md            # Project documentation
```

---

## 📦 Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/AHMED1CB/youtube-clone-api.git
   cd youtube-clone-api
   ```

2. **Copy the example environment file and configure it:**
   ```bash
   cp .env.example .env
   ```

3. **Install PHP dependencies:**
   ```bash
   composer install
   ```

4. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

5. **Run database migrations:**
   ```bash
   php artisan migrate
   ```

6. **Start the development server:**
   ```bash
   php artisan serve
   ```

---

## 🐳 Docker Setup

1. **Build and run the Docker containers:**
   ```bash
   docker-compose up -d --build
   ```

2. **Access the application:**
   The API will be available at `http://localhost:8000`.

---

## 🔌 API Endpoints

### 🔐 Authentication:
- `POST /api/auth/register` - Register a new user
- `POST /api/auth/login` - Authenticate a user
- `POST /api/auth/logout` - Logout the authenticated user
- `POST /api/auth` - Get Profile Details

### 📹 Videos And Shorts:
- `POST /api/videos` - Retrieve all videos
- `POST /api/videos/upload` - Upload a new video
- `POST /api/videos/{slug}` - Retrieve a specific video
- `POST /api/videos/{id}/delete` - Delete a video
- `POST /api/videos/{id}/comment` - Comment on Video
- `POST /api/videos/{id}/savedata` - Add View To Video And Save To User History
- `POST /api/videos/{id}/react` - React on Video

### 📺 Channels:
- `POST /api/channels/{channel}/subscribe` - Subscribe Channel
- `POST /api/channels/{username}` - get Channel Data

### 💬 Comments:
- `POST /api/comments/` - get All user Comments
- `POST /api/comments/{comment}/update` - Update Comment
- `POST /api/comments/{comment}/delete` - Delete a comment
---

### 💬 History:
- `POST /api/` - get All History Videos Of User
- `POST /api/history/changestate` - Pause or Start Saving History Videos
- `POST /api/history/clear` - Clear History of user
---

## 🗄️ Database Configuration

Ensure your `.env` file is configured with the correct database settings:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

---

## 🧪 Running Tests

Execute the test suite using PHPUnit:

```bash
php artisan test
```

---

## 📄 License

This project is licensed under the **MIT License**. See the [LICENSE](LICENSE) file for details.

---

## 🤝 Contributing

Contributions are welcome! Please fork the repository and submit a pull request.

---

## 📫 Contact

For support or inquiries, open an issue on [GitHub](https://github.com/AHMED1CB/youtube-clone-api/issues).

---
