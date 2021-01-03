# mqtt-metern
this is a PHP script that read data from json created by metern and send using mqtt
thanks to Jean Marc and Gianfranco Di Prinzio

reference for meterN: https://metern.org

how to intsall
copy the script in /comapps
make it executible:
sudo chmod +x mqtt_energy.php

create symlink:
sudo ln -s /var/www/comapps/mqtt_energy.php /usr/bin/mqtt_energy

create a systemd service to run it
