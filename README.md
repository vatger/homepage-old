> **Warning** 
> 
> This repository is no longer actively maintained! Whilst some security updates (if applicable) are applied, no new features will be implemented. If you wish to receive access to the Homepage V4, please check the project overview page [here](https://github.com/vatger/project-overview).

### VATSIM Germany Webservice
Dieses Repository enthält den Quellcode der VATSIM Germany Webservices.


#### Installation

1. Klone das Repository
2. Führe die folgenden Konsolenbefehle aus:

    1. `composer update` (Installiert die notwendigen Abhängigkeiten via Composer)

    2. `npm install` (Installiert die notwendigen Abhängigkeiten via NPM)

    3. `npm run dev` (Um .css und .js Dateien zu generieren)

    4. Jetzt muss die `.env` Datei angepasst werden:

        1. `cp .env.example .env`

        2. `nano .env`
    
        3. Wenn alle Einstellungen in der .env Datei an das lokale System angepasst wurden, die Datei speichern und schließen

    5. `php artisan migrate && php artisan db:seed`
        Hiermit wird die Datenbank initialisiert und mit anfänglichen Daten bestückt
    6. `sudo crontab -e`

        1. Füge folgenden Crontab hinzu `* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1`

    7. Vorbereiten der "Echtzeit" Mitteilungen

        1. `npm install -g laravel-echo-server`
    
        2. Nur ausführen, wenn KEINE `laravel-echo-server.json` Datei mitgeliefert wurde: `laravel-echo-server init`
    
        3. `nano laravel-echo-server.json` und die Datei dem lokalen System anpassen
    
    8. Datenautomatisierung
    
        1. `sudo nano /etc/supervisorctl/conf.d/vatsim-germany-worker.conf`
    
        2. Der Queue-Worker
            ```lang-bash
            [program:vatsim-germany-worker]
            process_name=%(program_name)s_%(process_num)02d
            command=php /path/to/project/artisan queue:work --sleep=3 --tries=3
            autostart=true
            autorestart=true
            user=vagrant
            numprocs=4
            redirect_stderr=true
            stdout_logfile=/path/to/project/storage/logs/worker.log```
    
        3. `sudo nano /etc/supervisorctl/conf.d/vatsim-germany-echo-worker.conf`
    
        4. Der Echo-Server Worker

            ```lang-bash
            [program:vatsim-germany-echo-worker]
            directory=/path/to/project
            command=laravel-echo-server start
            autostart=true
            autorestart=true
            user=vagrant
            redirect_stderr=true
            stdout_logfile=/path/to/project/storage/logs/echo.log```

    
        5. `sudo supervisorctl reread`
    
        6. `sudo supervisorctl reload`
    
        7. `sudo supervisorctl start vatsim-germany-worker:*`
    
        8. `sudo supervisorctl start vatsim-germany-echo-worker:*`

3. Konfiguriere deinen Webserver so, dass es auf das public Verzeichnis verweist.
    ```lang-bash
    server {
        listen 80;
        server_name yoururl.com;
        root /path/to/project/public;

        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-XSS-Protection "1; mode=block";
        add_header X-Content-Type-Options "nosniff";

        index index.php;

        charset utf-8;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location = /favicon.ico { access_log off; log_not_found off; }
        location = /robots.txt  { access_log off; log_not_found off; }

        error_page 404 /index.php;

        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
            include fastcgi_params;
        }

        location ~ /\.(?!well-known).* {
            deny all;
        }
    }
    ```
