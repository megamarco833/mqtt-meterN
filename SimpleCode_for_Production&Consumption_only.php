#!/usr/bin/php
<?php
if (isset($_SERVER['REMOTE_ADDR'])) {
    die('Direct access not permitted');
}

// sudo chmod +x mqtt_energy.php
// sudo chown www-data\: mqtt_energy.php
// sudo ln -s /var/www/comapps/mqtt_energy.php /usr/bin/mqtt_energy

$frequenza = 10; // seconds for loop

function mqtt($arr)
{
    $msg = json_encode($arr);
    //$CMD = "mosquitto_pub -d -h '192.168.0.105' -t 'domoticz/in' -m '$msg'";
   $CMD = "timeout --kill-after=15s 10s mosquitto_pub -d -h '192.168.0.105' -t 'domoticz/in' -m '$msg'";
    exec($CMD, $output);
    //$return = implode(PHP_EOL, $output);
    //echo $return;
}

// IDX Produzione = 4 ; prelievi = 5 ; autoconsumo = 6 ; fascia f1 = 7 ; fascia f23 = 8 ; immissioni = 9 ; consumi = 10
// domoticz device = dummy device type electrical: instanteo + contatore
//device type = dummy device: electrical instanteno + contatore
//os command:
//sudo mosquitto_pub -d -h 192.168.0.105 -t 'domoticz/in' -m "{"idx": 196, "nvalue": 0, "svalue":"1200.3;12345" }"
// domoticz/in {"idx": 196, "nvalue": 0, "svalue":"1200.3;1" }
$ID_prod   = 4;   //idx del dummy device: electrical instanteno + contatore = 4
$ID_cons   = 10;  //idx del dummy device: electrical instanteno + contatore = 10


while (true) {
    // qui devi modificare in base alla tua configurazione del file json in /dev/shm che crea meterN
    if (file_exists('/dev/shm/mN_LIVEMEMORY.json') && file_exists('/dev/shm/mN_ILIVEMEMORY.json') && file_exists('/dev/shm/mN_MEMORY.json')) {
       
        $data_mN_LIVEM      = file_get_contents('/dev/shm/mN_LIVEMEMORY.json');
        $data_mN_ILIVEM = file_get_contents('/dev/shm/mN_ILIVEMEMORY.json');
        $data_mN_MEMORY     = file_get_contents('/dev/shm/mN_MEMORY.json');
        $memarray_mN_LIVEM  = json_decode($data_mN_LIVEM, true);
        $memarray_mN_ILIVEM = json_decode($data_mN_ILIVEM, true);
        $memarray_mN_MEMORY = json_decode($data_mN_MEMORY, true);
   

// qui devi mettere i dati che ci soon nel json MEMORY e LIVEMEMORY, nel mio caso produzione è il meter n°2 e CONSUMI il metern n°1   
        $prod_KWH   = $memarray_mN_MEMORY["Last2"]; //Last2 = Produzione Wh   METERN2=produzione quindi LAST2
        $cons_KWH   = $memarray_mN_MEMORY["Last1"]; //Last1  = Consumi Wh    METERN1=consumi quindi LAST1
        $consumiW   = $memarray_mN_LIVEM["Consumi1"]; //Consumi1 = consumi W    questi sono i nomi dei miei meter scritti esattamente così !!!
        $prodW      = $memarray_mN_LIVEM["Produzione2"]; //Produzinoe2 = produzione W   questi sono i nomi dei miei meter scritti esattamente così !!!
   
       
        // produzione:
        $svalue = "$prodW;$prod_KWH";
        $arr    = array(
            'idx' => $ID_prod,
            'nvalue' => 0,
            'svalue' => $svalue
        );
        $out    = mqtt($arr);
        // consumi:
        $svalue = "$consumiW;$cons_KWH";
        $arr    = array(
            'idx' => $ID_cons, // Da usare un idx per ogni valore
            'nvalue' => 0,
            'svalue' => $svalue
        );
       mqtt($arr);
       
    } else { // ain't running
        //die("Aborting: no file \n");
        echo "No file\n";
        sleep(3);
    }
   
    sleep($frequenza);
}
?>