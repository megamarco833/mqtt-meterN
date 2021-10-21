# mqtt-metern for domoticz
this is a PHP script that read data from json created by metern (/dev/shm/) and send using mqtt send meterN data to MQTT using Mosquitto in domoticz format.

# Thanks to Jean Marc and Gianfranco Di Prinzio

reference for meterN: https://metern.org

# how to intsall
1) copy the script in /comapps
make it executible:
sudo chmod +x mqtt_energy.php

2) modify php script according to your: IDXs, names (refer also to example code with only production and consumption)

example modify the idx according to dummy devices create in domoticz:

```
$ID_prod   = 4;  //idx of  dummy device: electrical instantenous + counter = 4
$ID_prel   = 5;
$ID_autoc  = 6;
$ID_f1     = 7;
$ID_f23    = 8;
$ID_imm    = 9;
$ID_cons   = 10;
$ID_boiler = 130;
```

and then modify this part according to your needs:

```while (true) {
   
    if (file_exists('/dev/shm/mN_LIVEMEMORY.json') && file_exists('/dev/shm/mN_ILIVEMEMORY.json') && file_exists('/dev/shm/mN_MEMORY.json')) {
       
        $data_mN_LIVEM      = file_get_contents('/dev/shm/mN_LIVEMEMORY.json');
        $data_mN_ILIVEM = file_get_contents('/dev/shm/mN_ILIVEMEMORY.json');
        $data_mN_MEMORY     = file_get_contents('/dev/shm/mN_MEMORY.json');
        $memarray_mN_LIVEM  = json_decode($data_mN_LIVEM, true);
        $memarray_mN_ILIVEM = json_decode($data_mN_ILIVEM, true);
        $memarray_mN_MEMORY = json_decode($data_mN_MEMORY, true);
       
        $prod_KWH   = $memarray_mN_MEMORY["Last2"]; //Last2 = Produzione Wh
        $cons_KWH   = $memarray_mN_MEMORY["Last1"]; //Last1  = Consumi Wh
        $consumiW   = $memarray_mN_LIVEM["Consumi1"]; //Consumi1 = consumi W
        $prodW      = $memarray_mN_LIVEM["Produzione2"]; //Produzinoe2 = produzione W
        $prel_KWH   = $memarray_mN_MEMORY["Last3"]; //Last3 = Prelievi Wh
        $prelW      = $memarray_mN_LIVEM["Prelievi3"]; //Prelievi2 = prelievi W
        $imm_KWH    = $memarray_mN_MEMORY["Last4"];
        $immW       = $memarray_mN_LIVEM["Immissioni4"];
        $auto_KWH   = $memarray_mN_MEMORY["Last5"];
        $autoW      = $memarray_mN_LIVEM["Autoconsumo5"];
        $f1_KWH     = $memarray_mN_MEMORY["Last8"];
        $f1W        = $memarray_mN_LIVEM["PrelieviF18"];
        $f23_KWH    = $memarray_mN_MEMORY["Last9"];
        $f23W       = $memarray_mN_LIVEM["PrelieviF239"];
        $boiler_KWH = $memarray_mN_MEMORY["Last12"];
        $boilerW    = $memarray_mN_LIVEM["Boiler12"];
        $temp       = $memarray_mN_LIVEM["temperatura6"];
        $humi       = $memarray_mN_LIVEM["Umidità7"];
      $V   = $memarray_mN_ILIVEM["Voltage1"]; //Voltage1 = Volt
        $A   = $memarray_mN_ILIVEM["Corrente2"]; //Corrente2 = Ampere
        $h2o = $memarray_mN_ILIVEM["ACQUA7"] * 1000; //ACQUA7 = acqua in m3
```

modify the update time: every X seconds to send data from metern to domoticz:
`$frequenza = 10; // seconds for loop`


3) create symlink:
`sudo ln -s /var/www/comapps/mqtt_energy.php /usr/bin/mqtt_energy`

4) create a systemd service to run it

esample of systemd:

`sudo nano /etc/systemd/system/mqtt_energy.service`


```
[Unit]
Description=mqtt_energy
Requires=network.target
After=network.target nginx.service php-fpm.service

[Service]
Type=oneshot
ExecStart=/usr/bin/php /var/www/comapps/mqtt_energy.php
#ExecStartPre=/bin/sleep 30

[Install]
WantedBy=default.target
```

then activate the systemd

`sudo systemctl enable mqtt_energy.service`
`sudo systemctl start mqtt_energy.service`
