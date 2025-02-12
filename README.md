# Quickbites Backend

This project is a **Laravel environment only API**.

## Requirements
- [AWS SDK for PHP](https://github.com/aws/aws-sdk-php)
- [League Flysystem AWS S3](https://github.com/thephpleague/flysystem-aws-s3-v3)
- [Pusher PHP Server](https://github.com/pusher/pusher-http-php)
- [DigitalOcean API v2](https://github.com/toin0u/DigitalOceanV2)

## How to Run Locally

### Setup Environment
1. Clone the repository:
   ```sh
   git clone https://github.com/QuickBites-Capstone/quickbites-backend.git
   cd quickbites-backend
   ```

2. Install dependencies:
   ```sh
   composer install
   ```

3. Copy the `.env.example` file to `.env`:
   ```sh
   cp .env.example .env
   ```

4. Generate the application key:
   ```sh
   php artisan key:generate
   ```

5. Configure the database in `.env`:
   ```sh
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. Run database migrations:
   ```sh
   php artisan migrate
   ```

7. Start the development server:
   ```sh
   php artisan serve
   ```

Your Laravel API should now be running locally.

---
## Contributing
Feel free to contribute by creating issues or submitting pull requests.

## License
This project is licensed under the MIT License.

---
This backend serves as the API for the Quickbites school canteen pre-order system.
If you need further customization or additional sections, let me know!
