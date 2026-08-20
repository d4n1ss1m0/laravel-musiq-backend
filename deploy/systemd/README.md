# Systemd services

## Queue worker

Install the queue worker service on the server:

```bash
sudo cp deploy/systemd/musiq-queue.service /etc/systemd/system/musiq-queue.service
sudo systemctl daemon-reload
sudo systemctl enable --now musiq-queue
```

Check status and logs:

```bash
sudo systemctl status musiq-queue
sudo journalctl -u musiq-queue -f
```

After deploy, restart workers gracefully so they pick up the new code:

```bash
php artisan queue:restart
```

The service assumes:

- project path: `/var/www/musiq`
- PHP binary: `/usr/bin/php`
- system user/group: `www-data`
- queues: `tracks,default`

Adjust `deploy/systemd/musiq-queue.service` if the production server uses different values.
