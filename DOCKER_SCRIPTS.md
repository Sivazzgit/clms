# Docker Control Scripts

We have provided a set of wrapper shell scripts to simplify managing your development stack.

## Command Reference

### 🚀 `./docker-up.sh`
**Starts the local server stack.**

This script handles all pre-flight checks and initialization steps automatically:
- Checks if the Docker engine is running.
- Verifies if self-signed SSL certificates exist in `./certs/`. If they are missing, it automatically creates them using `openssl`.
- Performs a container build and boots up both the web and Percona database containers.
- Wait-polls for a few seconds to let MySQL boot.
- Displays a formatted console summary of access URLs, ports, and databases.

```bash
./docker-up.sh
```

---

### 🛑 `./docker-down.sh`
**Stops and removes the containers gracefully.**

This shuts down active instances without deleting your database data.
- Database entries and structures are fully preserved inside the `labour_mysql_data` Docker volume.

```bash
./docker-down.sh
```

*To wipe your local database volume and start entirely fresh:*
```bash
docker-compose down -v
```

---

### 📋 `./docker-logs.sh`
**Streams real-time log outputs.**

Keeps your console focused on standard output and standard errors for troubleshooting:
- `web`: Stream logs from Apache and PHP.
- `mysql`: Stream logs from Percona Server.
- `all`: Stream combined logs from both containers.

```bash
# View Apache/PHP server output (Default)
./docker-logs.sh web

# View database query logs or startup details
./docker-logs.sh mysql

# View all containers combined
./docker-logs.sh all
```

*Press `Ctrl+C` to exit.*

---

### 🐚 `./docker-shell.sh`
**Launches a shell session inside the active PHP 8.2 container.**

Gives you direct terminal access to run commands such as `composer`, check installed extensions via `php -m`, inspect files inside `/var/www/html`, or execute one-off scripts.

```bash
./docker-shell.sh
```

*Type `exit` to disconnect.*

---

### 🗄️ `./docker-mysql.sh`
**Opens the MySQL terminal client inside the container.**

Launches an interactive SQL console pre-authenticated as the `anahaw` user.

```bash
./docker-mysql.sh
```

*Type `exit` or `quit` to disconnect.*
