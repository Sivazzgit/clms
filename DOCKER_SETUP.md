# ContractLabour Application - Docker Setup

This directory contains the Docker configuration for running the ContractLabour application locally with **PHP 8.2.31**, Apache, and **MySQL 5.7.44-48** (Percona Server).

## Prerequisites

- **Docker Desktop** installed and running
- **Docker Compose** installed

## Quick Start

1. **Start the environment**:
   ```bash
   chmod +x *.sh
   ./docker-up.sh
   ```
   *The startup script will automatically create a `./certs/` folder and generate secure self-signed SSL certificates for you.*

2. **Access the Application**:
   - **HTTPS**: [https://localhost:5002](https://localhost:5002) (Highly Recommended)
   - **HTTP**: [http://localhost:8082](http://localhost:8082)

3. **HTTPS Certificate Verification**:
   Since we're using a self-signed certificate for local development, your browser will show a security warning on the first visit. This is completely expected:
   - **Chrome**: Click "Advanced" → "Proceed to localhost"
   - **Firefox**: Click "Advanced" → "Accept the Risk and Continue"
   - **Safari**: Click "Show Details" → "visit this website"

## Port Mapping Details

To prevent conflicts with other services or the previous `speed` containers, the following host-to-container port mappings are configured:

| Service | Host Port | Container Port | Purpose |
| :--- | :--- | :--- | :--- |
| **Apache SSL** | **5002** | `5000` | Secure HTTPS Web Server |
| **Apache HTTP** | **8082** | `80` | Standard HTTP Web Server |
| **MySQL (Percona)** | **3307** | `3306` | Database Client Connection |

## Database Information

- **Host (internal container network)**: `mysql`
- **Host (from your host computer)**: `localhost`
- **Port**: `3307`
- **Username**: `anahaw`
- **Password**: `anahaw`
- **Default Schema**: `anahaw`
- **Alternative Schema**: `catlmain`

Database data is persistent and stored in the Docker volume `labour_mysql_data`.

### Database Auto-Importing
If you have a database archive named `catlmain.sql.gz` or similar, you can automatically import it when the container initializes:
1. Place your `catlmain.sql.gz` file directly into `/Users/sivaprasad/contractLabour/catlmain.sql.gz`
2. Start the container using `./docker-up.sh` (or `docker-compose up --build` if modifying the configuration).
3. The MySQL container will automatically extract and import this schema on its very first run.

*Note: Database initialization only runs if the volume does not yet exist. To wipe the volume and force a clean re-initialization with an updated import file, run:*
```bash
docker-compose down -v
./docker-up.sh
```

## Configuration File Structure

```
contractLabour/
├── Dockerfile                    # PHP 8.2.31 + Apache image definition
├── docker-compose.yml            # Multi-container orchestration settings
├── docker-up.sh                  # One-click startup script (handles SSL certs)
├── docker-down.sh                # Graceful teardown script
├── docker-logs.sh                # Interactive real-time log viewer
├── docker-shell.sh               # Terminal access to the web container
├── docker-mysql.sh               # Console client to Percona MySQL 5.7
├── index.php                     # Clean diagnostics dashboard
├── docker/
│   ├── apache.conf              # HTTP virtual host settings (port 80)
│   ├── apache-ssl.conf          # HTTPS SSL virtual host settings (port 5000)
│   ├── php.ini                  # Optimized PHP 8.2 configurations (OPcache active)
│   ├── init.sql                 # Primary database initialization script
│   └── docker-entrypoint.sh     # Container synchronization check script
└── certs/
    ├── server.crt               # SSL public certificate (auto-generated)
    └── server.key               # SSL private key (auto-generated)
```

## Live Updates

All PHP code located in this workspace directory is mounted directly into `/var/www/html` in the container. Any changes you save in your editor will take effect immediately upon reloading your browser. No rebuild is necessary!
