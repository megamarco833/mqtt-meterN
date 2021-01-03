# mqtt-metern
this is a PHP script that read data from json created by metern (/dev/shm/) and send using mqtt send meterN data to MQTT using Mosquitto in domoticz format
thanks to Jean Marc and Gianfranco Di Prinzio

reference for meterN: https://metern.org

# how to intsall
1) copy the script in /comapps
make it executible:
sudo chmod +x mqtt_energy.php

2) modify php script according to your: IDXs, names

3) create symlink:
sudo ln -s /var/www/comapps/mqtt_energy.php /usr/bin/mqtt_energy

4) create a systemd service to run it
